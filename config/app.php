<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Tritium parameters
    |--------------------------------------------------------------------------
    |
    */

    'tritium_galaxy' => env('TRITIUM_GALAXY', 'Rigel'),
    'tritium_construction_speed' => env('TRITIUM_CONSTRUCTION_SPEED', 10),
    'tritium_research_speed' => env('TRITIUM_RESEARCH_SPEED', 1),
    'tritium_production_speed' => env('TRITIUM_PRODUCTION_SPEED', 10),
    'tritium_travel_speed' => env('TRITIUM_TRAVEL_SPEED', 300),
    'tritium_stage_speed' => env('TRITIUM_STAGE_SPEED', 10),
    'tritium_energy_base' => env('TRITIUM_ENERGY_BASE', 50),
    'tritium_energy_workers_by_level' => env('TRITIUM_ENERGY_WORKERS_BY_LEVEL', 50),
    'tritium_metal_base' => env('TRITIUM_METAL_BASE', 20),
    'tritium_uranium_base' => env('TRITIUM_URANIUM_BASE', 10),
    'tritium_crystal_base' => env('TRITIUM_CRYSTAL_BASE', 10),
    'tritium_humanoid_base' => env('TRITIUM_HUMANOID_BASE', 10),
    'tritium_humanoid_price' => env('TRITIUM_HUMANOID_PRICE', 500),
    'tritium_transportship_base' => env('TRITIUM_TRANSPORTSHIP_BASE', 100),
    'tritium_transportship_price' => env('TRITIUM_TRANSPORTSHIP_PRICE', 2000),
    'tritium_transportship_capacity' => env('TRITIUM_TRANSPORTSHIP_CAPACITY', 500),
    'tritium_count_member_level_alliance' => env('TRITIUM_COUNT_MEMBER_LEVEL_ALIANCE', 5),
    'tritium_member_founder' => env('TRITIUM_MEMBER_FOUNDER', 1),
    'tritium_member_general' => env('TRITIUM_MEMBER_GENERAL', 2),
    'tritium_member_fleet_cap' => env('TRITIUM_MEMBER_FLEET_CAP', 3),
    'tritium_member_troop_cap' => env('TRITIUM_MEMBER_TROOP_CAP', 4),
    'tritium_member_diplomat' => env('TRITIUM_MEMBER_DIPLOMAT', 5),
    'tritium_member_corporal' => env('TRITIUM_MEMBER_CORPORAL', 6),
    'tritium_member_soldier' => env('TRITIUM_MEMBER_SOLDIER', 7),
    'tritium_chat_other_alliance' => env('TRITIUM_CHAT_OTHER_ALLIANCE', 2),
    'tritium_market_status_open' => env('TRITIUM_MARKET_STATUS_OPEN', 1),
    'tritium_market_status_canceled' => env('TRITIUM_MARKET_STATUS_CANCELED', 0),
    'tritium_market_status_pending' => env('TRITIUM_MARKET_STATUS_PENDING', 3),
    'tritium_market_status_finished' => env('TRITIUM_MARKET_STATUS_FINISHED', 2),
    'tritium_max_planet_player' => env('TRITIUM_MAX_PLANET_PLAYER', 7),
    'tritium_combat_stage_time' => env('TRITIUM_COMBAT_STAGE_TIME', 300),
    'tritium_weight_time_second' => env('TRITIUM_WEIGHT_TIME_SECOND', 18),
    'tritium_travel_mission_spionage_cost' => env('TRITIUM_TRAVEL_MISSION_SPIONAGE_COST', 1000),
    'tritium_charging_speed' => env('TRITIUM_CHARGING_SPEED', 100),
    'tritium_shield_force' => env('TRITIUM_SHIELD_FORCE'),
    'env_url_site' => env('ENV_URL_SITE', 'http://localhost:3000'),
    'recaptchav3_sitekey' => env('RECAPTCHAV3_SITEKEY', '6LcUEZ8pAAAAAG7qPJuMt7B67u4OoCg97VIujDhe'),
    'recaptchav3_secret' => env('RECAPTCHAV3_SECRET', '6LcUEZ8pAAAAAA3tFHx44zikQc2sGR3BNWHIty9O'),

];
