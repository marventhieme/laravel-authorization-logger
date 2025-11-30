<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use MarvenThieme\LaravelAuthorizationLogger\Contracts\LogHandler;
use MarvenThieme\LaravelAuthorizationLogger\Objects\LogData;
use MarvenThieme\LaravelAuthorizationLogger\Objects\PolicyContextData;
use MarvenThieme\LaravelAuthorizationLogger\Objects\RequestContextData;
use MarvenThieme\LaravelAuthorizationLogger\Objects\UserContextData;
use RuntimeException;

class AuthorizationDenialLogger
{
    public function __construct(
        protected RequestBodySanitizer $sanitizer
    ) {}

    public function log(?Authenticatable $user, string $ability, ?bool $result, array $arguments): void
    {
        // Only log denials
        if ($result !== false) {
            return;
        }

        // Skip if disabled in config
        if (! config('authorization-logger.enabled', true)) {
            return;
        }

        // Skip configured HTTP methods (usually just UI permission checks)
        $skipMethods = config('authorization-logger.skip_methods', ['GET', 'HEAD']);
        if (in_array(request()->method(), $skipMethods)) {
            return;
        }

        // Skip if the authorization check comes from a Resource class
        if (config('authorization-logger.skip_resource_checks', true) && $this->isCalledFromResource()) {
            return;
        }

        $logData = new LogData(
            event: 'Authorization Denied',
            timestamp: now()->toIso8601String(),
            userContext: $this->getUserContext($user),
            policyContext: $this->getPolicyContext($ability, $arguments),
            requestContext: $this->getRequestContext()
        );

        $this->processHandlers($logData);
    }

    protected function processHandlers(LogData $logData): void
    {
        $handlers = config('authorization-logger.handlers', []);

        foreach ($handlers as $handlerClass) {
            if (! is_subclass_of($handlerClass, LogHandler::class)) {
                throw new RuntimeException("Log handler {$handlerClass} must implement LogHandler contract.");
            }

            try {
                /** @var LogHandler $handler */
                $handler = app($handlerClass);
                $handler->handle($logData);
            } catch (\Throwable $e) {
                if (config('app.debug')) {
                    logger()->debug("Authorization logger handler failed: {$handlerClass}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    protected function getUserContext(?Authenticatable $user): UserContextData
    {
        if ($user === null) {
            return new UserContextData(
                type: 'anonymous',
                ipAddress: request()->ip(),
                id: null,
            );
        }

        $context = [
            'id' => $user->id,
            'type' => 'authenticated',
            'email' => $user->email ?? null,
            'ip_address' => request()->ip(),
        ];

        if (method_exists($user, 'getAttribute')) {
            $context['name'] = $user->getAttribute('name') ?? null;
        }

        // Add roles if available (using Spatie's HasRoles trait)
        if (method_exists($user, 'getRoleNames')) {
            $context['roles'] = $user->getRoleNames()->toArray();
        }

        return new UserContextData(
            type: $context['type'],
            ipAddress: $context['ip_address'],
            id: $context['id'],
            email: $context['email'],
            name: $context['name'],
            roles: $context['roles'],
        );
    }

    protected function getPolicyContext(string $ability, array $arguments): PolicyContextData
    {
        if (empty($arguments)) {
            return new PolicyContextData(
                ability: $ability,
                policyClass: null,
                policyMethod: null,
                modelClass: null,
                modelId: null,
            );
        }

        $model = $arguments[0] ?? null;
        $policyClass = null;
        $policyMethod = null;
        $modelClass = null;
        $modelId = null;

        if (is_object($model)) {
            $modelClass = get_class($model);

            if (method_exists($model, 'getKey')) {
                $modelId = $model->getKey();
            }

            [$policyClass, $policyMethod] = $this->resolvePolicyInfo($model, $ability);
        } elseif (is_string($model)) {
            // For abilities like 'create' where class name is passed
            $modelClass = $model;
            [$policyClass, $policyMethod] = $this->resolvePolicyInfo($model, $ability);
        }

        return new PolicyContextData(
            ability: $ability,
            policyClass: $policyClass,
            policyMethod: $policyMethod,
            modelClass: $modelClass,
            modelId: $modelId,
        );
    }

    protected function resolvePolicyInfo(object|string $model, string $ability): array
    {
        $policyClass = null;
        $policyMethod = null;

        $policy = Gate::getPolicyFor($model);

        if ($policy !== null) {
            $policyClass = get_class($policy);
            $policyMethod = $ability;

            if (! method_exists($policy, $ability)) {
                $policyMethod = $ability.' [METHOD NOT FOUND]';
            }
        }

        return [$policyClass, $policyMethod];
    }

    protected function getRequestContext(): RequestContextData
    {
        $request = request();

        $routeName = null;
        $body = null;

        // Add route name if available
        if ($request->route()) {
            $routeName = $request->route()->getName();
        }

        // Add sanitized request body if present
        $requestBody = $request->all();
        if (! empty($requestBody)) {
            $sanitizedBody = $this->sanitizer->sanitize($requestBody);
            $bodyJson = json_encode($sanitizedBody);

            if ($this->sanitizer->exceedsMaxSize($bodyJson)) {
                $actualSize = strlen($bodyJson) / 1024;
                $maxSize = $this->sanitizer->getMaxSizeInKb();
                $body = "[Request body too large: {$actualSize}KB, limit: {$maxSize}KB]";
            } else {
                $body = $sanitizedBody;
            }
        }

        return new RequestContextData(
            method: $request->method(),
            url: $request->url(),
            endpoint: $request->path(),
            body: $body,
            routeName: $routeName,
        );
    }

    protected function isCalledFromResource(): bool
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);

        foreach ($trace as $frame) {
            if (isset($frame['class']) && str_ends_with($frame['class'], 'Resource')) {
                return true;
            }
        }

        return false;
    }
}
