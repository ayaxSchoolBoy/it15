<?php
// config.php
// Central configuration for the API

return [
    // Database (override in api/db.php if needed)
    'db' => [
        'host' => 'localhost',
        'name' => 'gym_qr',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],

    // Email settings (SMTP)
    'mail_enabled' => true,
    'mail_from' => 'your-email@gmail.com',
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'username' => 'Kurtiax23@gmail.com',
        'password' => 'uwojgthwfcemyjhk',
        'encryption' => 'tls', // tls or ssl
        'port' => 587,
    ],

    // Base URL for generating public links
    // Example: http://localhost/it15
    'base_url' => 'http://localhost/it15',
];
