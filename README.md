# New Post Email Notification Plugin

## Description
The **New Post Email Notification** plugin automatically sends an email notification whenever a new post is published on your WordPress site. It uses **SMTP (via SendGrid)** to ensure reliable email delivery.

## Features
- Sends an email notification when a new post is published.
- Uses **SendGrid SMTP** for secure and efficient email delivery.
- Configurable recipient email address.
- Admin settings page to manage SMTP options.
- Supports WordPress built-in PHPMailer.

## Installation
1. Download the plugin ZIP file or clone the repository.
2. Upload the plugin to the `/wp-content/plugins/` directory.
3. Activate the plugin through the WordPress **Plugins** menu.
4. Configure email settings under **Settings > Post Notification** in the WordPress admin panel.

## Configuration
- **Recipient Email**: Define the email address that receives notifications.
- **Enable SendGrid SMTP**: Option to use SendGrid for sending emails.
- **SendGrid API Key**: Securely enter your SendGrid API Key for authentication.

## SMTP Setup (SendGrid)
1. Sign up for a [SendGrid](https://sendgrid.com/) account.
2. Generate an API Key with **Full Access**.
3. Enter the API Key in the plugin settings under **SendGrid API Key**.
4. Enable the SMTP option for SendGrid to take effect.

## Hooks Used
- `transition_post_status`: Triggers email notification when a post is published.
- `admin_menu`: Adds a settings menu in the WordPress dashboard.
- `admin_init`: Registers plugin settings.

## Troubleshooting
- Ensure your **SendGrid API Key** is correct.
- Check your **spam folder** if emails are not received.
- Enable **WordPress debugging** (`WP_DEBUG`) for error logs.

## License
This plugin is open-source and licensed under the **GPL-2.0+** license.

## Author
Developed by **Dawood M. Shoaib**.

---
Feel free to contribute or report issues!

