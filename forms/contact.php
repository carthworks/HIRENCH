<?php
  /**
  * HIRENCH HR Solutions Contact Form Handler
  * Processes contact form submissions and sends emails
  */

  // Set content type for JSON response
  header('Content-Type: application/json');

  // Replace with your real receiving email address
  $receiving_email_address = 'hr@hirench.com';

  // Check if form was submitted via POST
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
  }

  // Validate required fields
  $required_fields = ['name', 'email', 'phone', 'subject', 'message'];
  $errors = [];

  foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
      $errors[] = ucfirst($field) . ' is required';
    }
  }

  // Validate email format
  if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
  }

  // If there are validation errors, return them
  if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['error' => implode(', ', $errors)]);
    exit;
  }

  // Sanitize input data
  $name = htmlspecialchars(trim($_POST['name']));
  $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
  $phone = htmlspecialchars(trim($_POST['phone']));
  $subject = htmlspecialchars(trim($_POST['subject']));
  $message = htmlspecialchars(trim($_POST['message']));

  // Try to load the PHP Email Form library
  if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
    include($php_email_form);

    try {
      $contact = new PHP_Email_Form;
      $contact->ajax = true;

      $contact->to = $receiving_email_address;
      $contact->from_name = $name;
      $contact->from_email = $email;
      $contact->subject = 'HIRENCH Contact Form: ' . $subject;

      // Uncomment and configure SMTP if needed
      /*
      $contact->smtp = array(
        'host' => 'your-smtp-host.com',
        'username' => 'your-smtp-username',
        'password' => 'your-smtp-password',
        'port' => '587'
      );
      */

      $contact->add_message($name, 'Name');
      $contact->add_message($email, 'Email');
      $contact->add_message($phone, 'Phone');
      $contact->add_message($subject, 'Service Requested');
      $contact->add_message($message, 'Message', 10);

      $result = $contact->send();

      if ($result === 'OK') {
        echo json_encode(['success' => 'Your message has been sent successfully!']);
      } else {
        http_response_code(500);
        echo json_encode(['error' => $result]);
      }

    } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
    }

  } else {
    // Fallback to basic PHP mail if library is not available
    $email_subject = 'HIRENCH Contact Form: ' . $subject;
    $email_body = "New contact form submission:\n\n";
    $email_body .= "Name: " . $name . "\n";
    $email_body .= "Email: " . $email . "\n";
    $email_body .= "Phone: " . $phone . "\n";
    $email_body .= "Service Requested: " . $subject . "\n";
    $email_body .= "Message: " . $message . "\n";

    $headers = "From: " . $name . " <" . $email . ">\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($receiving_email_address, $email_subject, $email_body, $headers)) {
      echo json_encode(['success' => 'Your message has been sent successfully!']);
    } else {
      http_response_code(500);
      echo json_encode(['error' => 'Failed to send email. Please try again later.']);
    }
  }
?>
