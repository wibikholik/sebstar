<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // Pastikan semua jalur API tercakup
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    // Mengizinkan semua metode (GET, POST, PUT, DELETE, dll)
    'allowed_methods' => ['*'],

    // Mengizinkan semua origin. 
    // Jika ingin lebih aman di produksi, ganti '*' menjadi 'http://localhost:8081'
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    // Mengizinkan semua header, termasuk 'Authorization' dan 'Content-Type'
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // UBAH JADI TRUE jika kamu nanti menggunakan cookies/session sanctum.
    // Untuk saat ini (JWT/Bearer Token), false atau true tidak terlalu masalah,
    // tapi true lebih fleksibel untuk pengembangan ke depan.
    'supports_credentials' => true,

];