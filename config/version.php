<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Version-Specific Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file allows you to set values that are specific to
    | different Laravel versions. This helps maintain compatibility across
    | Laravel 10, 11, 12, and 13.
    |
    */

    'version_specific' => [
        // Default configuration for all versions
        'default' => [
            'middleware_aliases' => true,
            'service_providers' => true,
            'routing_behavior' => 'standard',
            'queue_timeout' => 60,
            'cache_ttl' => 3600,
        ],

        // Laravel 10 specific settings
        'v10' => [
            'middleware_aliases' => true,
            'service_providers' => true,
            'routing_behavior' => 'standard',
            'queue_timeout' => 60,
            'cache_ttl' => 3600,
        ],

        // Laravel 11 specific settings
        'v11' => [
            'middleware_aliases' => true,
            'service_providers' => true,
            'routing_behavior' => 'standard',
            'queue_timeout' => 60,
            'cache_ttl' => 3600,
            // Add any Laravel 11+ specific configurations here
        ],

        // Laravel 12 specific settings
        'v12' => [
            'middleware_aliases' => true,
            'service_providers' => true,
            'routing_behavior' => 'standard',
            'queue_timeout' => 60,
            'cache_ttl' => 3600,
            // Add any Laravel 12+ specific configurations here
        ],

        // Laravel 13 specific settings
        'v13' => [
            'middleware_aliases' => true,
            'service_providers' => true,
            'routing_behavior' => 'standard',
            'queue_timeout' => 60,
            'cache_ttl' => 3600,
            // Add any Laravel 13+ specific configurations here
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Deprecated Features
    |--------------------------------------------------------------------------
    |
    | Monitor deprecated features across versions
    |
    */
    'deprecated_features' => [
        // None currently for this application
    ],

    /*
    |--------------------------------------------------------------------------
    | New Features by Version
    |--------------------------------------------------------------------------
    |
    | Track new features available in each version
    |
    */
    'new_features' => [
        'v11' => [
            'lazy_collections',
            'improved_error_reporting',
        ],
        'v12' => [
            // Add new features for v12
        ],
        'v13' => [
            // Add new features for v13
        ],
    ],
];
