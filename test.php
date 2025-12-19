
<?php
// Load Composer's autoloader (recommended way)
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $body, $altBody = '', $attachments = []) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 0; // 0 = off (for production), 2 = verbose debug
        $mail->isSMTP(); // Use SMTP
        $mail->Host       = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth   = true; // Enable SMTP authentication
        $mail->Username   = 'abdo.el.kabli12@gmail.com'; // SMTP username
        $mail->Password   = 'qdxf pxgb guvi llak'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption: STARTTLS or PHPMailer::ENCRYPTION_SMTPS
        $mail->Port       = 587; // TCP port (587 for TLS, 465 for SSL)

        // Sender info
        $mail->setFrom('abdo.el.kabli12@gmail.com', 'apah');
        $mail->addReplyTo('abdo.el.kabli12@gmail.com', 'appaaah');

        // Recipient(s)
        if (is_array($to)) {
            foreach ($to as $recipient) {
                $mail->addAddress($recipient);
            }
        } else {
            $mail->addAddress($to);
        }

        // Attachments
        if (!empty($attachments)) {
            foreach ($attachments as $filePath) {
                if (file_exists($filePath)) {
                    $mail->addAttachment($filePath);
                }
            }
        }

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body; // HTML body
        $mail->AltBody = $altBody ?: strip_tags($body); // Plain text fallback

        // Send email
        $mail->send();
        return ['success' => true, 'message' => 'Email sent successfully.'];

    } catch (Exception $e) {
        return ['success' => false, 'message' => "Email could not be sent. Error: {$mail->ErrorInfo}"];
    }
}

// Example usage
$result = sendEmail(
    'abdo.el.kabli12@gmail.com',
    'Test Email',
    '<h1>Hello!</h1><p>This is a test email.</p>',
    'Hello! This is a test email in plain text.',
    ['path/to/file.pdf']
);

echo $result['message'];

?>