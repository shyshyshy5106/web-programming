<?php

require_once __DIR__ . '/database.php';

/**
 * EmailService Class
 * Handles email sending via SMTP with validation, logging, and retry logic
 * Supports Gmail, Mailtrap, SendGrid, and custom SMTP providers
 */
class EmailService extends Database {

    private $config;
    private $log_path;

    public function __construct() {
        $this->config = require __DIR__ . '/../config/email.php';
        $this->log_path = $this->config['log_path'];

        // Ensure log directory exists
        if (!is_dir($this->log_path)) {
            @mkdir($this->log_path, 0755, true);
        }

        // Attempt to load bundled PHPMailer classes from downloads/ if not already available
        if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            $downloadsDir = realpath(__DIR__ . '/../downloads');
            if ($downloadsDir !== false) {
                $exFile = $downloadsDir . DIRECTORY_SEPARATOR . 'Exception.php';
                $pmFile = $downloadsDir . DIRECTORY_SEPARATOR . 'PHPMailer.php';
                $smtpFile = $downloadsDir . DIRECTORY_SEPARATOR . 'SMTP.php';
                if (file_exists($exFile) && file_exists($pmFile) && file_exists($smtpFile)) {
                    // Require in correct order: Exception, PHPMailer, SMTP
                    require_once $exFile;
                    require_once $pmFile;
                    require_once $smtpFile;
                }
            }
        }

        // If SMTP username is configured and the configured from address is a local/placeholder
        // value (e.g. contains '.local' or 'your-email' or 'noreply'), override it at runtime
        // so the web UI and sending use the authenticated address as the visible From.
        $smtpUser = $this->config['smtp']['username'] ?? '';
        $fromAddr = $this->config['from']['address'] ?? '';
        if (!empty($smtpUser)) {
            $isPlaceholder = false;
            $lower = strtolower($fromAddr);
            if (empty($fromAddr)) $isPlaceholder = true;
            if (strpos($lower, '.local') !== false) $isPlaceholder = true;
            if (strpos($lower, 'your-email') !== false) $isPlaceholder = true;
            if (strpos($lower, 'noreply') !== false) $isPlaceholder = true;

            if ($isPlaceholder) {
                $this->config['from']['address'] = $smtpUser;
            }
        }
    }

        /**
         * Ensure email_logs table exists (auto-create if needed)
         */
        private function ensureTableExists() {
            try {
                $conn = $this->connect();
                $sql = "CREATE TABLE IF NOT EXISTS `email_logs` (
                            `email_log_id` INT PRIMARY KEY AUTO_INCREMENT,
                            `recipient` VARCHAR(255) NOT NULL,
                            `subject` VARCHAR(255) NOT NULL,
                            `message_preview` LONGTEXT,
                            `sender_email` VARCHAR(255) NOT NULL,
                            `sender_name` VARCHAR(255),
                            `status` ENUM('PENDING', 'SENT_SUCCESS', 'SENT_FAILED', 'TEST_MODE_SUCCESS') DEFAULT 'PENDING',
                            `notes` LONGTEXT,
                            `sent_by_user_id` INT,
                            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        
                            INDEX `idx_recipient` (`recipient`),
                            INDEX `idx_status` (`status`),
                            INDEX `idx_created_at` (`created_at`),
                            INDEX `idx_sender_email` (`sender_email`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                $conn->exec($sql);
            } catch (Exception $e) {
                // Table already exists or error creating it - log but don't fail
                $this->logToFile("Table creation check: " . $e->getMessage());
            }
        }
    /**
     * Validate email format
     */
    public function validateEmail($email) {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate email domain (optional whitelist check)
     */
    private function validateDomain($email) {
        if (empty($this->config['allowed_domains'])) {
            return true;
        }

        $domain = substr(strrchr($email, "@"), 1);
        return in_array($domain, $this->config['allowed_domains']);
    }

    /**
     * Send email via SMTP
     * 
     * @param string $recipient Email address
     * @param string $subject Email subject
     * @param string $message Email body (HTML or plain text)
     * @param string $sender_name Sender display name (optional, uses logged-in user)
     * @param bool $is_html Whether message is HTML (default: true)
     * @return array ['success' => bool, 'message' => string, 'email_log_id' => int|null]
     */
    public function send($recipient, $subject, $message, $sender_name = null, $is_html = true) {
        // Validate inputs
        $errors = [];

            // Ensure table exists
            $this->ensureTableExists();
        if (empty(trim($recipient))) {
            $errors[] = 'Recipient email is required';
        } elseif (!$this->validateEmail($recipient)) {
            $errors[] = 'Invalid recipient email format';
        } elseif (!$this->validateDomain($recipient)) {
            $errors[] = 'Recipient domain not in whitelist';
        }

        if (empty(trim($subject))) {
            $errors[] = 'Subject is required';
        }

        if (empty(trim($message))) {
            $errors[] = 'Message body is required';
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => implode('; ', $errors),
                'email_log_id' => null
            ];
        }

        // Sanitize inputs
        $recipient = trim(htmlspecialchars($recipient, ENT_QUOTES, 'UTF-8'));
        $subject = trim(htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'));

        // Get sender info from config (sender name can be passed in or use default)
        $from_address = $this->config['from']['address'];
        $from_name = $sender_name ?: $this->config['from']['name'];

        // Log the email attempt
        $log_id = $this->logEmailAttempt($recipient, $subject, $message, $from_address, $from_name);

        // Test mode: don't actually send
        if ($this->config['test_mode']) {
            $this->updateEmailLog($log_id, 'TEST_MODE_SUCCESS', 'Email logged in test mode (not sent)');
            return [
                'success' => true,
                'message' => 'Email logged successfully (test mode - not sent)',
                'email_log_id' => $log_id
            ];
        }

        // Attempt to send email with retry logic
        $retry_count = $this->config['retry_count'];
        $last_error = '';

        for ($attempt = 1; $attempt <= $retry_count; $attempt++) {
            try {
                $result = $this->sendViaSMTP($recipient, $subject, $message, $from_address, $from_name, $is_html);

                if ($result === true) {
                    $this->updateEmailLog($log_id, 'SENT_SUCCESS', 'Email sent successfully on attempt ' . $attempt);
                    return [
                        'success' => true,
                        'message' => 'Email sent successfully to ' . $recipient,
                        'email_log_id' => $log_id
                    ];
                } else {
                    $last_error = $result;
                }
            } catch (Exception $e) {
                $last_error = $e->getMessage();
            }

            // Wait before retry (except on last attempt)
            if ($attempt < $retry_count) {
                sleep($this->config['retry_delay']);
            }
        }

        // All attempts failed
        $this->updateEmailLog($log_id, 'SENT_FAILED', 'Failed after ' . $retry_count . ' attempts: ' . $last_error);
        return [
            'success' => false,
            'message' => 'Failed to send email after ' . $retry_count . ' attempts: ' . $last_error,
            'email_log_id' => $log_id
        ];
    }

    /**
     * Send email via SMTP (low-level implementation)
     * Supports native PHP mail() or PHPMailer if available
     */
    private function sendViaSMTP($recipient, $subject, $message, $from_address, $from_name, $is_html) {
        // Try using PHPMailer if available (preferred method)
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            return $this->sendViaPhpMailer($recipient, $subject, $message, $from_address, $from_name, $is_html);
        }

        // If PHPMailer is not installed, attempt a direct SMTP socket connection using configured SMTP settings
        $smtpResult = $this->sendViaSmtpSocket($recipient, $subject, $message, $from_address, $from_name, $is_html);
        if ($smtpResult === true) {
            return true;
        }

        // Fallback to native mail() function with proper headers
        return $this->sendViaPhpMail($recipient, $subject, $message, $from_address, $from_name, $is_html);
    }

    /**
     * Send email using PHPMailer (recommended for SMTP)
     */
    private function sendViaPhpMailer($recipient, $subject, $message, $from_address, $from_name, $is_html) {
        $mail = null;
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

            // SMTP configuration
            $smtpCfg = $this->config['smtp'] ?? [];
            $mail->isSMTP();
            $mail->Host = $smtpCfg['host'] ?? 'localhost';
            $mail->Port = $smtpCfg['port'] ?? 25;
            $mail->SMTPSecure = $smtpCfg['encryption'] ?? '';
            $mail->SMTPAutoTLS = isset($smtpCfg['auto_tls']) ? (bool)$smtpCfg['auto_tls'] : true;

            // Only enable SMTPAuth when credentials are provided
            $hasCreds = !empty($smtpCfg['username']) && !empty($smtpCfg['password']);
            $mail->SMTPAuth = $hasCreds;
            if ($hasCreds) {
                $mail->Username = $smtpCfg['username'];
                $mail->Password = $smtpCfg['password'];
            }

            // Allow local testing with self-signed certs if configured
            $allowSelfSigned = $this->config['allow_self_signed'] ?? true;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => !$allowSelfSigned,
                    'verify_peer_name' => !$allowSelfSigned,
                    'allow_self_signed' => $allowSelfSigned,
                ]
            ];

            // Email content
            // When SMTP credentials are present, set the From and Sender to the SMTP account
            // so the SMTP envelope (MAIL FROM) and the message From match the authenticated user.
            // Keep Reply-To pointing to the configured from address so replies go there.
            $originalFrom = $from_address;
            if ($hasCreds && !empty($smtpCfg['username'])) {
                $smtpUser = $smtpCfg['username'];
                $mail->setFrom($smtpUser, $from_name);
                $mail->Sender = $smtpUser;
                if ($originalFrom !== $smtpUser) {
                    $mail->addReplyTo($originalFrom, $from_name);
                }
            } else {
                $mail->setFrom($originalFrom, $from_name);
            }

            $mail->addAddress($recipient);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->isHTML($is_html);

            // Log current send configuration for diagnostics
            $logMsg = 'PHPMailer send: SMTPAuth=' . ($mail->SMTPAuth ? 'true' : 'false')
                . ', SMTPUser=' . ($mail->Username ?? '')
                . ', From=' . ($mail->From ?? '')
                . ', Sender=' . ($mail->Sender ?? '')
                . ', To=' . $recipient;
            $this->logToFile($logMsg);

            $mail->send();
            $this->logToFile('PHPMailer: send() completed successfully');
            return true;

        } catch (\PHPMailer\PHPMailer\Exception $e) {
            $err = 'PHPMailer Exception: ' . $e->getMessage();
            if ($mail !== null && property_exists($mail, 'ErrorInfo')) {
                $err .= ' | ErrorInfo: ' . $mail->ErrorInfo;
            }
            $this->logToFile($err);
            return $err;
        } catch (Exception $e) {
            $err = 'PHPMailer Error: ' . $e->getMessage();
            $this->logToFile($err);
            return $err;
        }
    }

    /**
     * Send email using native PHP mail() with SMTP headers
     * Note: This may not work with all SMTP providers; PHPMailer is preferred
     */
    private function sendViaPhpMail($recipient, $subject, $message, $from_address, $from_name, $is_html) {
        $headers = "From: " . $from_name . " <" . $from_address . ">\r\n";
        $headers .= "Reply-To: " . $from_address . "\r\n";
        $headers .= "Return-Path: " . $from_address . "\r\n";

        if ($is_html) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }

        $headers .= "X-Mailer: Gym Membership System\r\n";

        // Note: mail() doesn't use SMTP config; it uses system sendmail
        // For reliable SMTP delivery, use PHPMailer instead
        if (@mail($recipient, $subject, $message, $headers)) {
            return true;
        } else {
            return 'PHP mail() function failed. Ensure mail server is configured or use PHPMailer.';
        }
    }

    /**
     * Send email using SMTP socket (fallback when PHPMailer missing)
     */
    private function sendViaSmtpSocket($recipient, $subject, $message, $from_address, $from_name, $is_html) {
        $smtp = $this->config['smtp'];
        $host = $smtp['host'] ?? null;
        $port = $smtp['port'] ?? 25;
        $encryption = strtolower($smtp['encryption'] ?? '');
        $username = $smtp['username'] ?? null;
        $password = $smtp['password'] ?? null;

        if (empty($host) || empty($username) || empty($password)) {
            return 'SMTP host/credentials not configured';
        }

        $remote = ($encryption === 'ssl') ? 'ssl://' . $host . ':' . $port : $host . ':' . $port;
        $timeout = 30;

        $errno = 0;
        $errstr = '';
        $fp = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
        if (!$fp) {
            $this->logToFile('SMTP socket connect failed: ' . $errstr . ' (' . $errno . ')');
            return 'Failed to connect to SMTP server: ' . $errstr . ' (' . $errno . ')';
        }

        stream_set_timeout($fp, $timeout);
        $res = $this->getSmtpResponse($fp);
        if (strpos($res, '220') !== 0) {
            $this->logToFile('SMTP server greeting unexpected: ' . $res);
            fclose($fp);
            return 'SMTP server did not respond properly: ' . $res;
        }

        $this->sendSmtpCommand($fp, 'EHLO ' . gethostname());
        $res = $this->getSmtpResponse($fp);

        // Start TLS if requested and supported
        if ($encryption === 'tls') {
            // Check if server offers STARTTLS
            if (stripos($res, 'STARTTLS') !== false || stripos($res, '250-STARTTLS') !== false) {
                $this->sendSmtpCommand($fp, 'STARTTLS');
                $resStart = $this->getSmtpResponse($fp);
                if (strpos($resStart, '220') !== 0) {
                    $this->logToFile('STARTTLS failed: ' . $resStart);
                    fclose($fp);
                    return 'Failed to initiate STARTTLS: ' . $resStart;
                }
                // Enable crypto on socket
                if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    $this->logToFile('Failed to enable TLS on SMTP connection');
                    fclose($fp);
                    return 'Failed to enable TLS on SMTP connection';
                }
                // EHLO again after STARTTLS
                $this->sendSmtpCommand($fp, 'EHLO ' . gethostname());
                $res = $this->getSmtpResponse($fp);
            }
        }

        // Authenticate using AUTH LOGIN
        $this->sendSmtpCommand($fp, 'AUTH LOGIN');
        $res = $this->getSmtpResponse($fp);
        if (strpos($res, '334') !== 0) {
            $this->logToFile('SMTP AUTH not accepted: ' . $res);
            fclose($fp);
            return 'SMTP AUTH not accepted: ' . $res;
        }

        $this->sendSmtpCommand($fp, base64_encode($username));
        $res = $this->getSmtpResponse($fp);
        $this->sendSmtpCommand($fp, base64_encode($password));
        $res = $this->getSmtpResponse($fp);
        if (strpos($res, '235') !== 0) {
            $this->logToFile('SMTP authentication failed: ' . $res);
            fclose($fp);
            return 'SMTP authentication failed: ' . $res;
        }

        // MAIL FROM - use authenticated username as envelope if available
        $mailFrom = !empty($username) ? $username : $from_address;
        $this->sendSmtpCommand($fp, 'MAIL FROM: <' . $mailFrom . '>');
        $res = $this->getSmtpResponse($fp);
        if (strpos($res, '250') !== 0) {
            $this->logToFile('MAIL FROM rejected: ' . $res);
            fclose($fp);
            return 'MAIL FROM rejected: ' . $res;
        }

        // RCPT TO
        $this->sendSmtpCommand($fp, 'RCPT TO: <' . $recipient . '>');
        $res = $this->getSmtpResponse($fp);
        if (strpos($res, '250') !== 0 && strpos($res, '251') !== 0) {
            $this->logToFile('RCPT TO rejected: ' . $res);
            fclose($fp);
            return 'RCPT TO rejected: ' . $res;
        }

        // DATA
        $this->sendSmtpCommand($fp, 'DATA');
        $res = $this->getSmtpResponse($fp);
        if (strpos($res, '354') !== 0) {
            $this->logToFile('SMTP DATA not accepted: ' . $res);
            fclose($fp);
            return 'SMTP DATA not accepted: ' . $res;
        }

        // Build headers (From header can remain the original configured from address)
        $headers = [];
        $headers[] = 'From: ' . $from_name . ' <' . $originalFrom . '>';
        $headers[] = 'To: ' . $recipient;
        $headers[] = 'Subject: ' . $subject;
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = $is_html ? 'Content-Type: text/html; charset=UTF-8' : 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';
        $headers[] = 'Date: ' . date('r');

        $data = implode("\r\n", $headers) . "\r\n\r\n" . $message . "\r\n.\r\n";

        fwrite($fp, $data);
        $res = $this->getSmtpResponse($fp);
        if (strpos($res, '250') !== 0) {
            $this->logToFile('SMTP send failed: ' . $res);
            fclose($fp);
            return 'SMTP send failed: ' . $res;
        }

        // QUIT
        $this->sendSmtpCommand($fp, 'QUIT');
        fclose($fp);
        return true;
    }

    private function sendSmtpCommand($fp, $cmd) {
        fwrite($fp, $cmd . "\r\n");
    }

    private function getSmtpResponse($fp) {
        $data = '';
        while (!feof($fp)) {
            $line = fgets($fp, 515);
            if ($line === false) break;
            $data .= $line;
            // Lines that don't have a hyphen after the response code indicate end of response
            if (isset($line[3]) && $line[3] !== '-') break;
        }
        return $data;
    }

    /**
     * Get recipient suggestions from members database
     */
    public function getMemberEmails($search = '', $limit = 20) {
        $conn = $this->connect();
        $sql = "SELECT DISTINCT p.email, p.fname, p.lname FROM profile p 
                WHERE p.email IS NOT NULL AND p.email != ''";

        if (!empty($search)) {
            $search = '%' . $search . '%';
            $sql .= " AND (p.email LIKE :search OR CONCAT(p.fname, ' ', p.lname) LIKE :search)";
        }

        $sql .= " ORDER BY p.fname ASC LIMIT :limit";
        $stmt = $conn->prepare($sql);

        if (!empty($search)) {
            $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Log email attempt to database
     */
    private function logEmailAttempt($recipient, $subject, $message, $from_address, $from_name) {
        $conn = $this->connect();
        $sql = "INSERT INTO email_logs (recipient, subject, message_preview, sender_email, sender_name, status, created_at, updated_at)
                VALUES (:recipient, :subject, :preview, :sender_email, :sender_name, 'PENDING', NOW(), NOW())";

        $stmt = $conn->prepare($sql);
        $preview = substr($message, 0, 200) . (strlen($message) > 200 ? '...' : '');

        $stmt->bindParam(':recipient', $recipient, PDO::PARAM_STR);
        $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
        $stmt->bindParam(':preview', $preview, PDO::PARAM_STR);
        $stmt->bindParam(':sender_email', $from_address, PDO::PARAM_STR);
        $stmt->bindParam(':sender_name', $from_name, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $conn->lastInsertId();
        } catch (Exception $e) {
            // If table doesn't exist yet, log to file instead
            $this->logToFile("Failed to insert email log: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update email log with delivery status
     */
    private function updateEmailLog($log_id, $status, $notes = '') {
        if (empty($log_id)) {
            return;
        }

        $conn = $this->connect();
        $sql = "UPDATE email_logs SET status = :status, notes = :notes, updated_at = NOW() WHERE email_log_id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':notes', $notes, PDO::PARAM_STR);
        $stmt->bindParam(':id', $log_id, PDO::PARAM_INT);

        try {
            $stmt->execute();
        } catch (Exception $e) {
            $this->logToFile("Failed to update email log: " . $e->getMessage());
        }
    }

    /**
     * Log message to file (fallback if database is unavailable)
     */
    private function logToFile($message) {
        $log_file = $this->log_path . '/email_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
    }

    /**
     * Get email log history
     */
    public function getEmailLogs($limit = 50, $offset = 0) {
        $conn = $this->connect();
        $sql = "SELECT * FROM email_logs ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
