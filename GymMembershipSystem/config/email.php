<?php

/**
 * Email Configuration for Gym Membership System
 * Supports Gmail SMTP, Mailtrap, SendGrid, or custom SMTP providers
 * 
 * IMPORTANT: Store sensitive credentials securely. Never commit to version control.
 * Consider using environment variables or a secure vault in production.
 */

// Optional local overrides
$localOverrides = [];
if (file_exists(__DIR__ . '/email.local.php')) {
    $localOverrides = include __DIR__ . '/email.local.php';
    if (!is_array($localOverrides)) {
        $localOverrides = [];
    }
}

$config = [
    // Email service provider: 'gmail', 'mailtrap', 'sendgrid', or 'custom'
    'provider' => 'gmail', // Change to 'mailtrap', 'sendgrid', or 'custom' as needed
    
    // SMTP Configuration
    'smtp' => [
        'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com', // Gmail: smtp.gmail.com, Mailtrap: smtp.mailtrap.io
        'port' => (int)(getenv('MAIL_PORT') ?: 587), // 587 for TLS, 465 for SSL
        'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls', // 'tls' or 'ssl'
        'username' => getenv('MAIL_USERNAME') ?: '', // Your Gmail address or SMTP username
        'password' => getenv('MAIL_PASSWORD') ?: '', // Your Gmail app password (NOT regular password) or SMTP password
    ],
    
    // Default sender information
    'from' => [
        'address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@gymmembership.local',
        'name' => getenv('MAIL_FROM_NAME') ?: 'Gym Membership System',
    ],
    
    // Email logging directory (relative to project root)
    'log_path' => __DIR__ . '/../logs/emails',
    
    // Retry configuration
    'retry_count' => 3,
    'retry_delay' => 2, // seconds
    
    // Whitelist of allowed recipient domains (empty = allow all)
    'allowed_domains' => [],
    
    // Test mode: logs emails without actually sending (set to false in production)
    'test_mode' => false,
];

// Merge local overrides (email.local.php) into main config. Local overrides win.
if (!empty($localOverrides)) {
    $config = array_replace_recursive($config, $localOverrides);
}

return $config;

