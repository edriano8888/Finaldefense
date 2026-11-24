<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PHPMailer Integration Library for Instroom by Armful Media
 * Delivers Password Resets & System Notifications via Gmail SMTP
 */
class Phpmailer_lib {
    
    private $CI;

    public function __construct() {
        $this->CI =& get_instance();
    }

    /**
     * Send password reset email via Gmail SMTP
     *
     * @param string $recipient_email
     * @param string $recipient_name
     * @param string $reset_link
     * @return bool
     */
    public function send_password_reset($recipient_email, $recipient_name, $reset_link) {
        $subject = 'Reset Your Instroom Password - Armful Media';
        
        $body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: "Plus Jakarta Sans", Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 24px; padding: 40px; box-shadow: 0 20px 40px rgba(0,0,0,0.06); border: 1px solid #f1f5f9; }
                .header { text-align: center; margin-bottom: 30px; }
                .badge { display: inline-block; padding: 6px 14px; background: #e6f4ed; color: #0f6b3e; border-radius: 50px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
                .title { font-size: 26px; font-weight: 900; color: #0f172a; margin-bottom: 15px; }
                .text { font-size: 15px; color: #475569; line-height: 1.6; margin-bottom: 25px; }
                .btn { display: inline-block; padding: 16px 36px; background: linear-gradient(135deg, #1fae5b 0%, #0f6b3e 100%); color: #ffffff !important; text-decoration: none; border-radius: 16px; font-weight: 800; font-size: 15px; box-shadow: 0 10px 20px rgba(31,174,91,0.25); }
                .footer { margin-top: 40px; padding-top: 25px; border-top: 1px solid #f1f5f9; text-align: center; font-size: 12px; color: #94a3b8; font-weight: 600; }
                .link-box { background: #f8fafc; padding: 14px; border-radius: 12px; font-size: 12px; word-break: break-all; color: #0f6b3e; margin-top: 15px; font-family: monospace; border: 1px solid #e2e8f0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="badge">Security Notification</div>
                    <div class="title">Password Reset Request</div>
                </div>
                <p class="text">Hi <strong>' . htmlspecialchars($recipient_name) . '</strong>,</p>
                <p class="text">We received a request to reset your password for your <strong>Instroom by Armful Media</strong> workspace account. Click the button below to set a new password:</p>
                <div style="text-align: center; margin: 35px 0;">
                    <a href="' . $reset_link . '" class="btn">Reset Password Now &rarr;</a>
                </div>
                <p class="text" style="font-size: 13px; margin-bottom: 10px;">If the button above does not work, copy and paste this link into your web browser:</p>
                <div class="link-box">' . $reset_link . '</div>
                <p class="text" style="font-size: 12px; color: #94a3b8; margin-top: 25px;">This password reset link will expire in <strong>1 hour</strong>. If you did not request this password reset, please ignore this email or contact support.</p>
                <div class="footer">
                    &copy; ' . date('Y') . ' Instroom by Armful Media. All rights reserved.
                </div>
            </div>
        </body>
        </html>
        ';

        return $this->send_mail($recipient_email, $subject, $body);
    }

    /**
     * High-performance Gmail SMTP Socket Transport Engine
     */
    public function send_mail($to, $subject, $body) {
        $this->CI->load->config('email');

        $smtp_host = $this->CI->config->item('smtp_host') ?: 'smtp.gmail.com';
        $smtp_port = $this->CI->config->item('smtp_port') ?: 587;
        $smtp_user = $this->CI->config->item('smtp_user') ?: 'hardenfaderon@gmail.com';
        $smtp_pass = $this->CI->config->item('smtp_pass') ?: 'mdjvvdkxwrnwmpkq';

        try {
            $socket = @fsockopen($smtp_host, $smtp_port, $errno, $errstr, 15);
            if (!$socket) {
                log_message('error', "Gmail SMTP Connection Error: $errstr ($errno)");
                return false;
            }

            $this->read_socket($socket);
            $this->send_cmd($socket, "EHLO localhost");
            $this->send_cmd($socket, "STARTTLS");
            
            @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT);

            $this->send_cmd($socket, "EHLO localhost");
            $this->send_cmd($socket, "AUTH LOGIN");
            $this->send_cmd($socket, base64_encode($smtp_user));
            $this->send_cmd($socket, base64_encode($smtp_pass));

            $this->send_cmd($socket, "MAIL FROM: <$smtp_user>");
            $this->send_cmd($socket, "RCPT TO: <$to>");
            $this->send_cmd($socket, "DATA");

            $headers  = "From: Instroom by Armful Media <$smtp_user>\r\n";
            $headers .= "To: <$to>\r\n";
            $headers .= "Subject: $subject\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "\r\n";

            fwrite($socket, $headers . $body . "\r\n.\r\n");
            $response = $this->read_socket($socket);
            
            $this->send_cmd($socket, "QUIT");
            fclose($socket);

            return (strpos($response, '250') !== false);
        } catch (Exception $e) {
            log_message('error', 'PHPMailer exception: ' . $e->getMessage());
            return false;
        }
    }

    private function send_cmd($socket, $cmd) {
        fwrite($socket, $cmd . "\r\n");
        return $this->read_socket($socket);
    }

    private function read_socket($socket) {
        $res = "";
        while ($str = fgets($socket, 515)) {
            $res .= $str;
            if (substr($str, 3, 1) == " ") break;
        }
        return $res;
    }
}
