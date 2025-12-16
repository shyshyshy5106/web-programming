<?php
/**
 * CLI test script for EmailService
 * Usage: php scripts/test_email.php recipient@example.com
 * Relies on `config/email.php` which may read environment variables `MAIL_USERNAME` and `MAIL_PASSWORD`.
 */
require_once __DIR__ . '/../classes/email_service.php';

$recipient = $argv[1] ?? null;
if (empty($recipient)) {
    echo "Usage: php scripts/test_email.php recipient@example.com\n";
    exit(1);
}

// Instantiate service
$svc = new EmailService();

$subject = 'GymMembershipSystem Test Email - ' . date('Y-m-d H:i:s');
$message = "This is a test email from GymMembershipSystem sent at " . date('c') . "\n\nIf you received this message, SMTP is working.\n";

$result = $svc->send($recipient, $subject, $message, 'System Test', false);

echo "Result:\n";
if (is_array($result)) {
    echo 'Success: ' . ($result['success'] ? 'true' : 'false') . "\n";
    echo 'Message: ' . ($result['message'] ?? '') . "\n";
    echo 'Log ID: ' . ($result['email_log_id'] ?? 'null') . "\n";
} else {
    // Some older codepaths may return string errors
    echo (string)$result . "\n";
}

exit(0);

?>