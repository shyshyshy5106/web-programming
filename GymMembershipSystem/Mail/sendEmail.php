<?php
session_start();
require_once '../classes/database.php';
require_once '../classes/email_service.php';

// Check authorization - only Admin and Staff can send emails
if (!isset($_SESSION["user"])) {
    header("Location: ../account/login.php");
    exit;
}

$user_role = $_SESSION["user"]["role"] ?? null;
if (!in_array($user_role, ['Admin', 'Staff'])) {
    $_SESSION['error_message'] = 'Unauthorized: Only Admin and Staff can send emails.';
    header("Location: ../index.php");
    exit;
}

$email_service = new EmailService();
$member_emails = [];
$sent_message = '';
$error_message = '';
// Safely derive sender name and email from session, with config fallbacks
$config = [];
if (file_exists(__DIR__ . '/../config/email.php')) {
    $config = require __DIR__ . '/../config/email.php';
}

// If SMTP username is configured and the configured from address looks like a placeholder
// (local, your-email, or noreply), prefer the SMTP username so the web UI shows a valid sender.
$smtpUser = $config['smtp']['username'] ?? '';
$fromAddr = $config['from']['address'] ?? '';
if (!empty($smtpUser)) {
    $lower = strtolower($fromAddr);
    if (empty($fromAddr) || strpos($lower, '.local') !== false || strpos($lower, 'your-email') !== false || strpos($lower, 'noreply') !== false) {
        $config['from']['address'] = $smtpUser;
    }
}

$sender_fname = $_SESSION["user"]["fname"] ?? $_SESSION["user"]["first_name"] ?? '';
$sender_lname = $_SESSION["user"]["lname"] ?? $_SESSION["user"]["last_name"] ?? '';
$sender_name = trim($sender_fname . ' ' . $sender_lname);
if (empty($sender_name)) {
    $sender_name = $config['from']['name'] ?? 'Gym Membership System';
}

$sender_email = $_SESSION["user"]["email"] ?? $config['from']['address'] ?? 'noreply@gymmembership.local';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipient_email = $_POST['recipient_email'] ?? '';
    $manual_email = $_POST['manual_email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    // Use manual email if provided, otherwise use dropdown selection
    $final_recipient = !empty($manual_email) ? $manual_email : $recipient_email;

    // Validation
    $errors = [];
    if (empty($final_recipient)) {
        $errors[] = 'Please select or enter a recipient email address';
    } elseif (!filter_var($final_recipient, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }

    if (empty($message)) {
        $errors[] = 'Message body is required';
    }

    if (!empty($errors)) {
        $error_message = implode('<br>', $errors);
    } else {
        // Send email
        $result = $email_service->send(
            $final_recipient,
            $subject,
            $message,
            $sender_name,
            false // plain text (not HTML)
        );

        if ($result['success']) {
            $sent_message = 'Email sent successfully to ' . htmlspecialchars($final_recipient);
            // Clear form
            $recipient_email = '';
            $manual_email = '';
            $subject = '';
            $message = '';
        } else {
            $error_message = 'Failed to send email: ' . htmlspecialchars($result['message']);
        }
    }
}

// Get member email suggestions for dropdown
$member_emails = $email_service->getMemberEmails('', 50);
// Fetch recent email logs for history view
$recent_emails = $email_service->getEmailLogs(10, 0);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email - Gym Membership System</title>
    <link rel="stylesheet" href="../index.css">
    <style>
        .email-form-container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group textarea {
            min-height: 300px;
            resize: vertical;
            font-family: 'Courier New', monospace;
        }

        .recipient-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .recipient-section .form-group {
            margin-bottom: 0;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="email"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #710A14;
            box-shadow: 0 0 0 2px rgba(113, 10, 20, 0.1);
        }

        .sender-info {
            background: #f5f5f5;
            border-left: 4px solid #710A14;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .sender-info .info-row {
            margin-bottom: 8px;
        }

        .sender-info .info-row:last-child {
            margin-bottom: 0;
        }

        .sender-info .label {
            font-weight: 500;
            color: #555;
            display: inline-block;
            min-width: 100px;
        }

        .sender-info .value {
            color: #333;
        }

        .subject-section {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 15px;
        }

        .subject-section .form-group {
            margin-bottom: 0;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .form-actions button,
        .form-actions a {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }

        .btn-primary {
            background-color: #710A14;
            color: white;
        }

        .btn-primary:hover {
            background-color: #5a0a0f;
        }

        .btn-secondary {
            background-color: #ddd;
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #ccc;
        }

        .character-count {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .recipient-section,
            .subject-section {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .form-actions button,
            .form-actions a {
                width: 100%;
            }

            .email-form-container {
                margin: 10px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <div class="email-form-container">
        <h1 class="site-title">Send Email to Members</h1>

        <?php if (!empty($sent_message)): ?>
            <div class="alert alert-success">
                <?php echo $sent_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <!-- Sender Info Display -->
            <div class="sender-info">
                <div class="info-row">
                    <span class="label">From:</span>
                    <span class="value"><?php echo htmlspecialchars($sender_name); ?> &lt;<?php echo htmlspecialchars($sender_email); ?>&gt;</span>
                </div>
                <div class="info-row">
                    <span class="label">Role:</span>
                    <span class="value"><?php echo htmlspecialchars($user_role); ?></span>
                </div>
            </div>

            <form method="POST" class="form-content">
                <!-- Recipient Selection -->
                <div class="recipient-section">
                    <div class="form-group">
                        <label for="recipient_email">Select Member:</label>
                        <select name="recipient_email" id="recipient_email">
                            <option value="">-- Choose a member --</option>
                            <?php foreach ($member_emails as $member): ?>
                                <option value="<?php echo htmlspecialchars($member['email']); ?>">
                                    <?php echo htmlspecialchars($member['fname'] . ' ' . $member['lname'] . ' (' . $member['email'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="manual_email">Or Enter Email:</label>
                        <input 
                            type="email" 
                            name="manual_email" 
                            id="manual_email" 
                            placeholder="email@example.com"
                            value="<?php echo isset($_POST['manual_email']) ? htmlspecialchars($_POST['manual_email']) : ''; ?>"
                        >
                    </div>
                </div>

                <!-- Subject Selection & Edit -->
                <div class="subject-section">
                    <div class="form-group">
                        <label for="subject_template">Subject Template:</label>
                        <select id="subject_template" onchange="applySubjectTemplate(this.value)">
                            <option value="">-- Custom Subject --</option>
                            <option value="Membership Renewal Reminder">Membership Renewal Reminder</option>
                            <option value="Payment Due Notification">Payment Due Notification</option>
                            <option value="Membership Expiry Warning">Membership Expiry Warning</option>
                            <option value="Welcome to Our Gym">Welcome to Our Gym</option>
                            <option value="Special Promotion">Special Promotion</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="subject">Email Subject:</label>
                        <input 
                            type="text" 
                            name="subject" 
                            id="subject" 
                            placeholder="Enter email subject"
                            value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>"
                        >
                    </div>
                </div>

                <!-- Message Body -->
                <div class="form-group">
                    <label for="message">Message Body:</label>
                    <textarea 
                        name="message" 
                        id="message" 
                        placeholder="Enter your email message here..."
                    ><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    <div class="character-count">
                        <span id="char_count">0</span> characters
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="../index.php" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Send Email</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Sent Emails Section -->
    <div class="email-form-container" style="max-width:900px;">
        <h2>Recent Sent Emails</h2>
        <?php if (empty($recent_emails)): ?>
            <div class="alert alert-error">No sent emails recorded yet.</div>
        <?php else: ?>
            <div class="recent-emails">
                <div class="table-wrapper">
                <table class="data-table recent-emails-table">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>To</th>
                            <th>Subject</th>
                            <th>Preview</th>
                            <th>Sender</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_emails as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($log['recipient']); ?></td>
                                <td><?php echo htmlspecialchars($log['subject']); ?></td>
                                <td class="allow-wrap"><?php echo htmlspecialchars($log['message_preview']); ?></td>
                                <td><?php echo htmlspecialchars($log['sender_email']); ?></td>
                                <td><?php echo htmlspecialchars($log['status']); ?></td>
                                <td>
                                    <button class="btn-secondary" onclick="toggleDetails(<?php echo (int)$log['email_log_id']; ?>)">Details</button>
                                </td>
                            </tr>
                            <tr id="details-<?php echo (int)$log['email_log_id']; ?>" style="display:none;background:#fff;border-top:1px solid #f9f9f9">
                                <td colspan="7" style="padding:12px;border-top:1px solid #eee">
                                    <strong>Full Message Preview:</strong>
                                    <pre style="white-space:pre-wrap;word-break:break-word; background:#f7f7f7;padding:10px;border-radius:4px;margin-top:8px"><?php echo htmlspecialchars($log['message_preview']); ?></pre>
                                    <?php if (!empty($log['notes'])): ?>
                                        <div style="margin-top:8px"><strong>Notes:</strong> <?php echo htmlspecialchars($log['notes']); ?></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Update character count
        document.getElementById('message').addEventListener('input', function() {
            document.getElementById('char_count').textContent = this.value.length;
        });

        // Apply subject template
        function applySubjectTemplate(template) {
            if (template) {
                document.getElementById('subject').value = template;
                document.getElementById('subject_template').value = '';
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const recipient = document.getElementById('recipient_email').value;
            const manual = document.getElementById('manual_email').value;
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();

            if (!recipient && !manual) {
                e.preventDefault();
                alert('Please select or enter a recipient email address');
                return;
            }

            if (!subject) {
                e.preventDefault();
                alert('Subject is required');
                return;
            }

            if (!message) {
                e.preventDefault();
                alert('Message body is required');
                return;
            }
        });
    </script>
    <script>
        function toggleDetails(id) {
            var el = document.getElementById('details-' + id);
            if (!el) return;
            el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'table-row' : 'none';
        }
    </script>
</body>
</html>
