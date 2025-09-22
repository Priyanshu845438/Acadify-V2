<?php
require_once '../config/database.php';
require_once '../config/email.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get form data - handle both name formats
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $name = $_POST['name'] ?? trim($firstName . ' ' . $lastName);
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Handle project type from contact form
    $projectType = $_POST['projectType'] ?? '';
    if ($projectType && $projectType !== 'Not specified') {
        $message = "Project Type: " . $projectType . "\n\n" . $message;
    }
    
    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
        exit;
    }
    
    // Save to database
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("
        INSERT INTO contact_entries (name, email, phone, message) 
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute([$name, $email, $phone, $message]);
    
    // Send email notifications
    try {
        $mailer = new EmailService();
        
        // Prepare contact data for email
        $contactData = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message,
            'subject' => $projectType ?: 'General Inquiry'
        ];
        
        // Send notification to admin
        $mailer->sendContactNotificationToAdmin($contactData);
        
        // Send confirmation to user
        $mailer->sendContactConfirmationToUser($contactData);
        
    } catch (Throwable $emailError) {
        // Log email error but don't fail the form submission
        error_log("Email error in contact_handler.php: " . $emailError->getMessage());
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Thank you for your message! We have received your inquiry and will get back to you within 24 hours. You should also receive a confirmation email shortly.'
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in contact_handler.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was an error processing your request. Please try again later.'
    ]);
} catch (Throwable $e) {
    error_log("General error in contact_handler.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was an error processing your request. Please try again later.'
    ]);
}
?>