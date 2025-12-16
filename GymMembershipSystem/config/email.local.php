<?php
/**
 * Local email overrides (not committed to source control)
 * Copy this file to set local SMTP credentials for the web process.
 * Fill in your real values and keep this file private.
 * Example structure below — only include the keys you need to override.
 */

return [
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'sailelaprincess.rutherford2023@gmail.com',
        'password' => 'kasc ysmw plrm bajt',
    ],

    'from' => [
        'address' => 'sailelaprincess.rutherford2023@gmail.com',
        'name' => 'Gym Membership System',
    ],
];
