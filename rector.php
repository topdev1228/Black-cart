<?php

declare(strict_types=1);

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon as IlluminateCarbon;
use Illuminate\Support\Facades\Date;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\Rector\StaticCall\RenameStaticMethodRector;
use Rector\Renaming\ValueObject\RenameStaticMethod;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/lang',
        __DIR__ . '/public',
        __DIR__ . '/resources',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withConfiguredRule(RenameClassRector::class, [
        Carbon::class => CarbonImmutable::class,
        IlluminateCarbon::class => CarbonImmutable::class,
    ])
    ->withConfiguredRule(RenameStaticMethodRector::class, [
        new RenameStaticMethod(Carbon::class, 'now', Date::class, 'now'),
        new RenameStaticMethod(Carbon::class, 'create', Date::class, 'create'),
        new RenameStaticMethod(Carbon::class, 'createFromFormat', Date::class, 'createFromFormat'),
        new RenameStaticMethod(Carbon::class, 'parse', Date::class, 'parse'),
        new RenameStaticMethod(IlluminateCarbon::class, 'now', Date::class, 'now'),
        new RenameStaticMethod(IlluminateCarbon::class, 'create', Date::class, 'create'),
        new RenameStaticMethod(IlluminateCarbon::class, 'createFromFormat', Date::class, 'createFromFormat'),
        new RenameStaticMethod(IlluminateCarbon::class, 'parse', Date::class, 'parse'),
        new RenameStaticMethod(CarbonImmutable::class, 'createFromDate', Date::class, 'createFromDate'),
        new RenameStaticMethod(CarbonImmutable::class, 'setTestNow', Date::class, 'setTestNow'),
    ]);
