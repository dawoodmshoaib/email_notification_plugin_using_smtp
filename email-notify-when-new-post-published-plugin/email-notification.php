<?php  
/*  
Plugin Name: New Post Email Notification 
Description: Sends an email notification when a new post is published, using SMTP.  
Version: 1.3  
Author: Dawood M. Shoaib  
*/  

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include PHPMailer from WordPress Core
require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Function to send email when a new post is published
function send_new_post_notification($new_status, $old_status, $post) {
    if ($new_status !== 'publish' || $old_status === 'publish' || $post->post_type !== 'post') {
        return;
    }

    // Get settings from the database
    $use_custom_smtp = get_option('use_custom_smtp', 'no');
    $recipient_email = get_option('new_post_notification_email', get_option('admin_email'));
    $smtp_host = 'smtp.sendgrid.net'; // SendGrid SMTP Host
    $smtp_username = 'apikey'; // SendGrid requires 'apikey' as username
    $smtp_password = get_option('sendgrid_api_key', ''); // SendGrid API Key
    $smtp_port = 587;
    $smtp_encryption = PHPMailer::ENCRYPTION_STARTTLS;
    $from_email = get_option('admin_email'); // Sender email (admin email)

    if (!$recipient_email) {
        error_log('Email Notification Plugin: Recipient email is missing.');
        return;
    }

    // Initialize PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_encryption;
        $mail->Port = $smtp_port;

        // Set sender & recipient
        $mail->setFrom($from_email, get_bloginfo('name'));
        $mail->addAddress($recipient_email);

        // Email Subject & Body
        $mail->isHTML(true);
        $mail->Subject = 'New Post Published: ' . $post->post_title;
        $mail->Body = 'A new post has been published on your site: <br><a href="' . get_permalink($post->ID) . '">' . get_permalink($post->ID) . '</a>';

        // Send Email
        $mail->send();
    } catch (Exception $e) {
        error_log('Email Notification Plugin: Email failed - ' . $mail->ErrorInfo);
    }
}

// Hook into WordPress
add_action('transition_post_status', 'send_new_post_notification', 10, 3);

// =============================
// Admin Settings Page
// =============================
function new_post_notification_settings_page() {
    ?>
    <div class="wrap">
        <h1>Email Notification Settings (SendGrid)</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('new_post_notification_settings');
            do_settings_sections('new-post-notification');
            submit_button();
            ?>
        </form>

        <script>
            function toggleSMTPFields() {
                var smtpFields = document.getElementById('smtp_fields');
                var checkbox = document.getElementById('use_custom_smtp');
                smtpFields.style.display = checkbox.checked ? 'block' : 'none';
            }

            document.addEventListener("DOMContentLoaded", function() {
                toggleSMTPFields(); // Run on page load
            });
        </script>
    </div>
    <?php
}

// Register settings
function new_post_notification_register_settings() {
    add_option('new_post_notification_email', get_option('admin_email'));
    add_option('sendgrid_api_key', '');
    add_option('use_custom_smtp', 'no');

    register_setting('new_post_notification_settings', 'new_post_notification_email');
    register_setting('new_post_notification_settings', 'sendgrid_api_key');
    register_setting('new_post_notification_settings', 'use_custom_smtp');

    add_settings_section('email_settings_section', 'Email Settings', null, 'new-post-notification');

    add_settings_field('new_post_notification_email', 'Recipient Email', 'new_post_notification_email_field', 'new-post-notification', 'email_settings_section');
    add_settings_field('use_custom_smtp', 'Use SendGrid SMTP', 'use_custom_smtp_field', 'new-post-notification', 'email_settings_section');
}

// Callback functions for fields
function new_post_notification_email_field() {
    echo '<input type="email" name="new_post_notification_email" value="' . get_option('new_post_notification_email') . '" required />';
}
function use_custom_smtp_field() {
    $checked = get_option('use_custom_smtp') === 'yes' ? 'checked' : '';
    echo '<input type="checkbox" name="use_custom_smtp" id="use_custom_smtp" value="yes" ' . $checked . ' onclick="toggleSMTPFields()"/> Enable SendGrid SMTP';
    
    // SMTP fields (hidden by default)
    echo '<div id="smtp_fields" style="display: none; margin-top: 10px;">';
    echo '<label>SendGrid API Key: </label>';
    echo '<input type="password" name="sendgrid_api_key" value="' . get_option('sendgrid_api_key') . '" />';
    echo '</div>';
}

// Add menu item in WP Admin
function new_post_notification_add_menu() {
    add_options_page('New Post Notification', 'Post Notification', 'manage_options', 'new-post-notification', 'new_post_notification_settings_page');
}

// Hooks for settings
add_action('admin_menu', 'new_post_notification_add_menu');
add_action('admin_init', 'new_post_notification_register_settings');
?>
