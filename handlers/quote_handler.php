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
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $company = $_POST['company'] ?? '';
    $service_type = $_POST['service_type'] ?? '';
    $project_budget = $_POST['project_budget'] ?? '';
    $project_timeline = $_POST['project_timeline'] ?? '';
    $project_description = $_POST['project_description'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($service_type)) {
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
        INSERT INTO quote_requests (name, email, phone, company, service_type, project_budget, project_timeline, project_description) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([$name, $email, $phone, $company, $service_type, $project_budget, $project_timeline, $project_description]);
    
    // Send email notifications
    try {
        $mailer = new EmailService();
        
        // Prepare quote data for email
        $quoteData = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'company' => $company,
            'service_type' => $service_type,
            'project_budget' => $project_budget,
            'project_timeline' => $project_timeline,
            'project_description' => $project_description
        ];
        
        // Send notification to admin (high priority)
        $mailer->sendQuoteNotificationToAdmin($quoteData);
        
        // Send confirmation to user
        $mailer->sendQuoteConfirmationToUser($quoteData);
        
    } catch (Throwable $emailError) {
        // Log email error but don't fail the form submission
        error_log("Email error in quote_handler.php: " . $emailError->getMessage());
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Thank you for your quote request! Our team will review your requirements and get back to you within 24 hours with a detailed proposal. You should also receive a confirmation email shortly.'
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in quote_handler.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was an error processing your request. Please try again later.'
    ]);
} catch (Throwable $e) {
    error_log("General error in quote_handler.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was an error processing your request. Please try again later.'
    ]);
}
?>