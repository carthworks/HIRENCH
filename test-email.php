<?php
/**
 * Simple test script to verify email functionality
 * This script tests if PHP mail() function is working
 */

// Test email configuration
$test_email = 'hr@hirench.com';
$subject = 'HIRENCH Contact Form Test';
$message = 'This is a test message to verify that the contact form is working correctly.';
$headers = 'From: test@hirench.com' . "\r\n" .
           'Reply-To: test@hirench.com' . "\r\n" .
           'Content-Type: text/plain; charset=UTF-8' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

echo "<h2>HIRENCH Contact Form Test</h2>";
echo "<p>Testing email functionality...</p>";

// Check if mail function exists
if (!function_exists('mail')) {
    echo "<div style='color: red;'>❌ PHP mail() function is not available on this server.</div>";
    echo "<p>You will need to configure SMTP settings in the contact.php file.</p>";
} else {
    echo "<div style='color: green;'>✅ PHP mail() function is available.</div>";
    
    // Test sending email
    if (mail($test_email, $subject, $message, $headers)) {
        echo "<div style='color: green;'>✅ Test email sent successfully to $test_email</div>";
    } else {
        echo "<div style='color: orange;'>⚠️ Email sending failed. This might be due to server configuration.</div>";
        echo "<p>Consider using SMTP configuration in contact.php for reliable email delivery.</p>";
    }
}

echo "<hr>";
echo "<h3>Contact Form Status Check:</h3>";

// Check if required files exist
$files_to_check = [
    'forms/contact.php' => 'Contact form handler',
    'assets/vendor/php-email-form/php-email-form.php' => 'PHP Email Form library',
    'assets/vendor/php-email-form/validate.js' => 'Form validation JavaScript'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "<div style='color: green;'>✅ $description: $file</div>";
    } else {
        echo "<div style='color: red;'>❌ Missing: $description ($file)</div>";
    }
}

echo "<hr>";
echo "<h3>Form Field Validation Test:</h3>";

// Simulate form submission
$test_data = [
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'phone' => '+1234567890',
    'subject' => 'Recruitment Services',
    'message' => 'This is a test message for the contact form.'
];

echo "<p>Testing with sample data:</p>";
foreach ($test_data as $field => $value) {
    echo "<strong>$field:</strong> $value<br>";
}

// Basic validation
$errors = [];
if (empty($test_data['name'])) $errors[] = 'Name is required';
if (empty($test_data['email']) || !filter_var($test_data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
if (empty($test_data['phone'])) $errors[] = 'Phone is required';
if (empty($test_data['subject'])) $errors[] = 'Subject is required';
if (empty($test_data['message'])) $errors[] = 'Message is required';

if (empty($errors)) {
    echo "<div style='color: green;'>✅ All form fields pass validation</div>";
} else {
    echo "<div style='color: red;'>❌ Validation errors: " . implode(', ', $errors) . "</div>";
}

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Test the contact form on the main website: <a href='index.html#contact'>index.html#contact</a></li>";
echo "<li>If emails are not being sent, configure SMTP settings in forms/contact.php</li>";
echo "<li>Check server error logs if form submission fails</li>";
echo "</ul>";
?>
