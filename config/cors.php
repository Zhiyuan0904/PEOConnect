<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CORS Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". These settings determine what cross-origin operations
    | may execute in web browsers. Adjust these as needed for security.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Define API routes for CORS
    'allowed_methods' => ['*'],  // Allow all HTTP methods (GET, POST, etc.)
    'allowed_origins' => ['*'],  // Allow all domains (for development)
    'allowed_origins_patterns' => [], 
    'allowed_headers' => ['*'],  // Allow all headers
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false, // Change to true if using authentication cookies
];
