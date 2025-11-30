<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed()
    ->ignoring([
        'MarvenThieme\LaravelAuthorizationLogger\Handlers\DebugToRay',
    ]);
