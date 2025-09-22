<?php
// Load environment variables from .env file (for shared hosting)
require_once __DIR__ . '/env_loader.php';
// Production-Ready Email Service using PHPMailer
// Secure SMTP implementation with proper TLS and authentication
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $smtp_host;
    private $smtp_port;
    private $smtp_encryption;
    private $username;
    private $password;
    private $from_email;
    private $from_name;
    private $admin_email;
    private $reply_to;
    
    public function __construct() {
        // Configure SMTP settings from environment variables with fallback defaults
        $this->smtp_host = getenv('SMTP_HOST') ?: 'smtp.hostinger.com';
        $this->smtp_port = intval(getenv('SMTP_PORT') ?: '465');
        $this->smtp_encryption = getenv('SMTP_ENCRYPTION') ?: PHPMailer::ENCRYPTION_SMTPS;
        $this->username = getenv('SMTP_USERNAME');
        $this->password = getenv('SMTP_PASSWORD');
        $this->from_name = getenv('SMTP_FROM_NAME') ?: 'Ideovent Technologies';
        $this->admin_email = getenv('ADMIN_EMAIL') ?: 'acadify.online@gmail.com';
        
        // Set from email and reply-to (use SMTP username as sender email)
        $this->from_email = $this->username;
        $this->reply_to = getenv('SMTP_REPLY_TO') ?: $this->from_email;
        
        if (!$this->username || !$this->password) {
            throw new Exception('SMTP credentials not found. Please set SMTP_USERNAME and SMTP_PASSWORD environment variables.');
        }
    }
    
    /**
     * Create and configure a PHPMailer instance
     */
    private function createMailer() {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings with proper security
            $mail->isSMTP();
            $mail->Host = $this->smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = $this->smtp_encryption;
            $mail->Port = $this->smtp_port;
            
            // Enable TLS security with proper certificate verification for production
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'allow_self_signed' => false,
                    'cafile' => '/etc/ssl/certs/ca-certificates.crt'
                ]
            ];
            
            // Enable SMTP debugging for development (disable in production)
            if ($_ENV['SMTP_DEBUG'] ?? getenv('SMTP_DEBUG')) {
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->Debugoutput = 'error_log';
            }
            
            // Set sender information with proper headers
            $mail->setFrom($this->from_email, $this->from_name);
            if (!empty($this->reply_to)) {
                $mail->addReplyTo($this->reply_to, $this->from_name);
            }
            
            // Set additional headers for better deliverability
            $mail->XMailer = 'PHPMailer/' . PHPMailer::VERSION . ' (Ideovent Technologies)';
            $mail->addCustomHeader('X-Priority', '3');
            $mail->addCustomHeader('X-MSMail-Priority', 'Normal');
            
            return $mail;
            
        } catch (Exception $e) {
            error_log("PHPMailer configuration error: " . $e->getMessage());
            throw new Exception('Failed to configure email service: ' . $e->getMessage());
        }
    }
    
    /**
     * Send an email with both HTML and text versions
     */
    public function sendEmail($to, $subject, $text, $html = null, $cc = null, $attachments = null) {
        try {
            $mail = $this->createMailer();
            
            // Recipients
            $mail->addAddress($to);
            
            if ($cc) {
                if (is_array($cc)) {
                    foreach ($cc as $ccEmail) {
                        $mail->addCC($ccEmail);
                    }
                } else {
                    $mail->addCC($cc);
                }
            }
            
            // Content
            $mail->isHTML($html !== null);
            $mail->Subject = $subject;
            $mail->CharSet = 'UTF-8';
            
            if ($html !== null) {
                $mail->Body = $html;
                $mail->AltBody = $text;
            } else {
                $mail->Body = $text;
            }
            
            // Handle attachments if provided
            if ($attachments && is_array($attachments)) {
                foreach ($attachments as $attachment) {
                    if (is_array($attachment)) {
                        $mail->addAttachment($attachment['path'], $attachment['name'] ?? '');
                    } else {
                        $mail->addAttachment($attachment);
                    }
                }
            }
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            throw new Exception('Email sending failed: ' . $e->getMessage());
        }
    }
    
    public function sendContactNotificationToAdmin($contactData) {
        $subject = "New Contact Form Submission - " . htmlspecialchars($contactData['name'], ENT_QUOTES, 'UTF-8');
        
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #0d3269; color: white; padding: 20px; text-align: center;'>
                <h2 style='margin: 0;'>New Contact Form Submission</h2>
            </div>
            
            <div style='padding: 30px; background: #f8f9fa;'>
                <h3 style='color: #0d3269; margin-bottom: 20px;'>Contact Details:</h3>
                
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                    <tr style='background: white;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Name:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($contactData['name'], ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: #f8f9fa;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Email:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($contactData['email'], ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: white;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Phone:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($contactData['phone'] ?? 'Not provided', ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: #f8f9fa;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Subject:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($contactData['subject'] ?? 'General Inquiry', ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                </table>
                
                <h4 style='color: #0d3269; margin-bottom: 10px;'>Message:</h4>
                <div style='background: white; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>
                    " . nl2br(htmlspecialchars($contactData['message'])) . "
                </div>
                
                <div style='margin-top: 30px; padding: 15px; background: #e3f2fd; border-radius: 5px;'>
                    <p style='margin: 0; font-style: italic;'>Submitted on: " . date('F j, Y \a\t g:i A') . "</p>
                </div>
            </div>
        </div>";
        
        $text = "New Contact Form Submission\n\n" .
                "Name: {$contactData['name']}\n" .
                "Email: {$contactData['email']}\n" .
                "Phone: " . ($contactData['phone'] ?? 'Not provided') . "\n" .
                "Subject: " . ($contactData['subject'] ?? 'General Inquiry') . "\n\n" .
                "Message:\n{$contactData['message']}\n\n" .
                "Submitted on: " . date('F j, Y \a\t g:i A');
        
        return $this->sendEmail(
            $this->admin_email ?: 'acadify.online@gmail.com',
            $subject,
            $text,
            $html
        );
    }
    
    public function sendContactConfirmationToUser($contactData) {
        $subject = "Thank you for contacting Ideovent Technologies";
        
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #0d3269; color: white; padding: 30px; text-align: center;'>
                <h1 style='margin: 0; font-size: 28px;'>Thank You!</h1>
                <p style='margin: 10px 0 0 0; font-size: 16px;'>We've received your message</p>
            </div>
            
            <div style='padding: 40px 30px; background: #f8f9fa;'>
                <p style='font-size: 16px; color: #333; margin-bottom: 20px;'>Dear " . htmlspecialchars($contactData['name'], ENT_QUOTES, 'UTF-8') . ",</p>
                
                <p style='font-size: 16px; color: #333; line-height: 1.6; margin-bottom: 20px;'>
                    Thank you for reaching out to Ideovent Technologies. We've received your inquiry and appreciate your interest in our services.
                </p>
                
                <p style='font-size: 16px; color: #333; line-height: 1.6; margin-bottom: 30px;'>
                    Our team will review your message and get back to you within 24 hours with a detailed response. We're excited about the possibility of working together on your project.
                </p>
                
                <div style='background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px;'>
                    <h3 style='color: #0d3269; margin-bottom: 15px;'>Your Inquiry Details:</h3>
                    <p style='margin: 5px 0;'><strong>Subject:</strong> " . htmlspecialchars($contactData['subject'] ?? 'General Inquiry', ENT_QUOTES, 'UTF-8') . "</p>
                    <p style='margin: 5px 0;'><strong>Submitted:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                </div>
                
                <p style='font-size: 16px; color: #333; line-height: 1.6; margin-bottom: 20px;'>
                    In the meantime, feel free to explore our website to learn more about our services and past projects.
                </p>
                
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>
                    Best regards,<br>
                    <strong>The Ideovent Technologies Team</strong>
                </p>
            </div>
            
            <div style='background: #0d3269; color: white; padding: 20px; text-align: center; font-size: 14px;'>
                <p style='margin: 0;'>Ideovent Technologies - Transforming Ideas into Digital Reality</p>
            </div>
        </div>";
        
        $text = "Dear {$contactData['name']},\n\n" .
                "Thank you for reaching out to Ideovent Technologies. We've received your inquiry and appreciate your interest in our services.\n\n" .
                "Our team will review your message and get back to you within 24 hours with a detailed response. We're excited about the possibility of working together on your project.\n\n" .
                "Your Inquiry Details:\n" .
                "Subject: " . ($contactData['subject'] ?? 'General Inquiry') . "\n" .
                "Submitted: " . date('F j, Y \a\t g:i A') . "\n\n" .
                "In the meantime, feel free to explore our website to learn more about our services and past projects.\n\n" .
                "Best regards,\n" .
                "The Ideovent Technologies Team\n\n" .
                "Ideovent Technologies - Transforming Ideas into Digital Reality";
        
        return $this->sendEmail(
            $contactData['email'],
            $subject,
            $text,
            $html
        );
    }
    
    public function sendPartnerNotificationToAdmin($partnerData) {
        $subject = "New Partnership Inquiry - " . htmlspecialchars($partnerData['company_name'], ENT_QUOTES, 'UTF-8');
        
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #0d3269; color: white; padding: 20px; text-align: center;'>
                <h2 style='margin: 0;'>New Partnership Inquiry</h2>
            </div>
            
            <div style='padding: 30px; background: #f8f9fa;'>
                <h3 style='color: #0d3269; margin-bottom: 20px;'>Partnership Details:</h3>
                
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                    <tr style='background: white;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Company:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($partnerData['company_name'], ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: #f8f9fa;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Contact Person:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($partnerData['contact_person'], ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: white;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Email:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($partnerData['email'], ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: #f8f9fa;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Phone:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($partnerData['phone'] ?? 'Not provided', ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: white;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Partnership Type:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars(ucfirst(str_replace('-', ' ', $partnerData['business_type'])), ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                </table>
                
                <h4 style='color: #0d3269; margin-bottom: 10px;'>Company Description:</h4>
                <div style='background: white; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>
                    " . nl2br(htmlspecialchars($partnerData['message'])) . "
                </div>
                
                <div style='margin-top: 30px; padding: 15px; background: #e3f2fd; border-radius: 5px;'>
                    <p style='margin: 0; font-style: italic;'>Submitted on: " . date('F j, Y \a\t g:i A') . "</p>
                </div>
            </div>
        </div>";
        
        return $this->sendEmail(
            $this->admin_email ?: 'acadify.online@gmail.com',
            $subject,
            "New Partnership Inquiry from {$partnerData['company_name']}\n\nCompany: {$partnerData['company_name']}\nContact: {$partnerData['contact_person']}\nEmail: {$partnerData['email']}\nPartnership Type: " . ucfirst(str_replace('-', ' ', $partnerData['business_type'])) . "\n\nDescription:\n{$partnerData['message']}",
            $html
        );
    }
    
    public function sendPartnerConfirmationToUser($partnerData) {
        $subject = "Partnership Inquiry Received - Ideovent Technologies";
        
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #0d3269; color: white; padding: 30px; text-align: center;'>
                <h1 style='margin: 0; font-size: 28px;'>Partnership Inquiry Received</h1>
                <p style='margin: 10px 0 0 0; font-size: 16px;'>Thank you for your interest in partnering with us</p>
            </div>
            
            <div style='padding: 40px 30px; background: #f8f9fa;'>
                <p style='font-size: 16px; color: #333; margin-bottom: 20px;'>Dear " . htmlspecialchars($partnerData['contact_person'], ENT_QUOTES, 'UTF-8') . ",</p>
                
                <p style='font-size: 16px; color: #333; line-height: 1.6; margin-bottom: 20px;'>
                    Thank you for expressing interest in a partnership with Ideovent Technologies. We're excited about the potential collaboration opportunity with " . htmlspecialchars($partnerData['company_name'], ENT_QUOTES, 'UTF-8') . ".
                </p>
                
                <p style='font-size: 16px; color: #333; line-height: 1.6; margin-bottom: 30px;'>
                    Our partnerships team will review your inquiry and get back to you within 2-3 business days to discuss next steps and explore how we can work together.
                </p>
                
                <div style='background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px;'>
                    <h3 style='color: #0d3269; margin-bottom: 15px;'>Your Inquiry Summary:</h3>
                    <p style='margin: 5px 0;'><strong>Company:</strong> " . htmlspecialchars($partnerData['company_name'], ENT_QUOTES, 'UTF-8') . "</p>
                    <p style='margin: 5px 0;'><strong>Partnership Type:</strong> " . htmlspecialchars(ucfirst(str_replace('-', ' ', $partnerData['business_type'])), ENT_QUOTES, 'UTF-8') . "</p>
                    <p style='margin: 5px 0;'><strong>Submitted:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                </div>
                
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>
                    Best regards,<br>
                    <strong>The Ideovent Technologies Partnerships Team</strong>
                </p>
            </div>
        </div>";
        
        return $this->sendEmail(
            $partnerData['email'],
            $subject,
            "Dear {$partnerData['contact_person']},\n\nThank you for expressing interest in a partnership with Ideovent Technologies. We're excited about the potential collaboration opportunity with {$partnerData['company_name']}.\n\nOur partnerships team will review your inquiry and get back to you within 2-3 business days to discuss next steps.\n\nBest regards,\nThe Ideovent Technologies Partnerships Team",
            $html
        );
    }
    
    public function sendQuoteNotificationToAdmin($quoteData) {
        $subject = "New Quote Request - " . htmlspecialchars($quoteData['name'], ENT_QUOTES, 'UTF-8') . " (" . htmlspecialchars($quoteData['service_type'], ENT_QUOTES, 'UTF-8') . ")";
        
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #0d3269; color: white; padding: 20px; text-align: center;'>
                <h2 style='margin: 0;'>New Quote Request</h2>
            </div>
            
            <div style='padding: 30px; background: #f8f9fa;'>
                <h3 style='color: #0d3269; margin-bottom: 20px;'>Quote Request Details:</h3>
                
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                    <tr style='background: white;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Name:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($quoteData['name'], ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: #f8f9fa;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Email:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($quoteData['email'], ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: white;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Phone:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($quoteData['phone'] ?? 'Not provided', ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: #f8f9fa;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Company:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($quoteData['company'] ?? 'Not provided', ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: white;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Service:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars(ucfirst(str_replace('-', ' ', $quoteData['service_type'])), ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: #f8f9fa;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Budget:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($quoteData['project_budget'] ?? 'Not specified', ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                    <tr style='background: white;'>
                        <td style='padding: 12px; font-weight: bold; border: 1px solid #ddd;'>Timeline:</td>
                        <td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($quoteData['project_timeline'] ?? 'Not specified', ENT_QUOTES, 'UTF-8') . "</td>
                    </tr>
                </table>
                
                <h4 style='color: #0d3269; margin-bottom: 10px;'>Project Description:</h4>
                <div style='background: white; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>
                    " . nl2br(htmlspecialchars($quoteData['project_description'] ?? 'No description provided')) . "
                </div>
                
                <div style='margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 5px;'>
                    <p style='margin: 0; font-weight: bold; color: #856404;'>⚡ High Priority: Quote Request</p>
                    <p style='margin: 5px 0 0 0; font-style: italic; color: #856404;'>Submitted on: " . date('F j, Y \a\t g:i A') . "</p>
                </div>
            </div>
        </div>";
        
        return $this->sendEmail(
            $this->admin_email ?: 'acadify.online@gmail.com',
            $subject,
            "NEW QUOTE REQUEST - HIGH PRIORITY\n\nClient: {$quoteData['name']}\nService: " . ucfirst(str_replace('-', ' ', $quoteData['service_type'])) . "\nBudget: " . ($quoteData['project_budget'] ?? 'Not specified') . "\nTimeline: " . ($quoteData['project_timeline'] ?? 'Not specified') . "\n\nProject Description:\n" . ($quoteData['project_description'] ?? 'No description provided'),
            $html
        );
    }
    
    public function sendQuoteConfirmationToUser($quoteData) {
        $subject = "Quote Request Received - We'll Get Back to You Soon!";
        
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #0d3269; color: white; padding: 30px; text-align: center;'>
                <h1 style='margin: 0; font-size: 28px;'>Quote Request Received!</h1>
                <p style='margin: 10px 0 0 0; font-size: 16px;'>We're working on your proposal</p>
            </div>
            
            <div style='padding: 40px 30px; background: #f8f9fa;'>
                <p style='font-size: 16px; color: #333; margin-bottom: 20px;'>Dear " . htmlspecialchars($quoteData['name'], ENT_QUOTES, 'UTF-8') . ",</p>
                
                <p style='font-size: 16px; color: #333; line-height: 1.6; margin-bottom: 20px;'>
                    Thank you for requesting a quote for your <strong>" . htmlspecialchars(ucfirst(str_replace('-', ' ', $quoteData['service_type'])), ENT_QUOTES, 'UTF-8') . "</strong> project. We're excited about the opportunity to work with you!
                </p>
                
                <p style='font-size: 16px; color: #333; line-height: 1.6; margin-bottom: 30px;'>
                    Our team is reviewing your requirements and will provide you with a detailed proposal within 24 hours. We'll include project timeline, cost breakdown, and next steps.
                </p>
                
                <div style='background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px;'>
                    <h3 style='color: #0d3269; margin-bottom: 15px;'>Your Quote Request Summary:</h3>
                    <p style='margin: 5px 0;'><strong>Service:</strong> " . htmlspecialchars(ucfirst(str_replace('-', ' ', $quoteData['service_type'])), ENT_QUOTES, 'UTF-8') . "</p>
                    <p style='margin: 5px 0;'><strong>Budget Range:</strong> " . htmlspecialchars($quoteData['project_budget'] ?? 'To be discussed', ENT_QUOTES, 'UTF-8') . "</p>
                    <p style='margin: 5px 0;'><strong>Timeline:</strong> " . htmlspecialchars($quoteData['project_timeline'] ?? 'To be discussed', ENT_QUOTES, 'UTF-8') . "</p>
                    <p style='margin: 5px 0;'><strong>Submitted:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                </div>
                
                <div style='background: #e8f5e8; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>
                    <h4 style='color: #155724; margin-bottom: 10px;'>What happens next?</h4>
                    <ul style='color: #155724; margin: 0; padding-left: 20px;'>
                        <li>Our experts will analyze your project requirements</li>
                        <li>We'll prepare a customized proposal with detailed pricing</li>
                        <li>You'll receive a comprehensive quote within 24 hours</li>
                        <li>We'll schedule a call to discuss the proposal</li>
                    </ul>
                </div>
                
                <p style='font-size: 16px; color: #333; line-height: 1.6;'>
                    Best regards,<br>
                    <strong>The Ideovent Technologies Team</strong>
                </p>
            </div>
            
            <div style='background: #0d3269; color: white; padding: 20px; text-align: center; font-size: 14px;'>
                <p style='margin: 0;'>Ideovent Technologies - Transforming Ideas into Digital Reality</p>
            </div>
        </div>";
        
        $text = "Dear {$quoteData['name']},\n\n" .
                "Thank you for requesting a quote for your " . ucfirst(str_replace('-', ' ', $quoteData['service_type'])) . " project. We're excited about the opportunity to work with you!\n\n" .
                "Our team is reviewing your requirements and will provide you with a detailed proposal within 24 hours. We'll include project timeline, cost breakdown, and next steps.\n\n" .
                "Your Quote Request Summary:\n" .
                "Service: " . ucfirst(str_replace('-', ' ', $quoteData['service_type'])) . "\n" .
                "Budget Range: " . ($quoteData['project_budget'] ?? 'To be discussed') . "\n" .
                "Timeline: " . ($quoteData['project_timeline'] ?? 'To be discussed') . "\n" .
                "Submitted: " . date('F j, Y \a\t g:i A') . "\n\n" .
                "What happens next?\n" .
                "- Our experts will analyze your project requirements\n" .
                "- We'll prepare a customized proposal with detailed pricing\n" .
                "- You'll receive a comprehensive quote within 24 hours\n" .
                "- We'll schedule a call to discuss the proposal\n\n" .
                "Best regards,\n" .
                "The Ideovent Technologies Team\n\n" .
                "Ideovent Technologies - Transforming Ideas into Digital Reality";
        
        return $this->sendEmail(
            $quoteData['email'],
            $subject,
            $text,
            $html
        );
    }
}

// Backward compatibility: Create alias for existing code
class HostingerMail extends EmailService {
    // This class exists for backward compatibility
    // All functionality is inherited from EmailService
}
?>