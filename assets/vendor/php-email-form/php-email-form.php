<?php

class PHP_Email_Form {
  public $to;
  public $from_name;
  public $from_email;
  public $subject;
  public $smtp;
  public $ajax = false;
  private $messages = array();

  public function add_message($content, $label, $priority = 0) {
    $this->messages[] = array(
      'content' => $content,
      'label' => $label,
      'priority' => $priority
    );
  }

  public function send() {
    // Validate required fields
    if (empty($this->to)) {
      return 'Recipient email is required.';
    }
    if (empty($this->from_email)) {
      return 'Sender email is required.';
    }
    if (empty($this->from_name)) {
      return 'Sender name is required.';
    }

    // Build email content
    $email_content = "New contact form submission:\n\n";
    
    foreach ($this->messages as $message) {
      $email_content .= $message['label'] . ": " . $message['content'] . "\n";
    }

    // Email headers
    $headers = array();
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/plain; charset=UTF-8';
    $headers[] = 'From: ' . $this->from_name . ' <' . $this->from_email . '>';
    $headers[] = 'Reply-To: ' . $this->from_email;
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    // Subject
    $subject = !empty($this->subject) ? $this->subject : 'New Contact Form Submission';

    // Send email
    if ($this->smtp && is_array($this->smtp)) {
      // Use SMTP if configured
      return $this->send_smtp($email_content, $subject, $headers);
    } else {
      // Use PHP mail function
      if (mail($this->to, $subject, $email_content, implode("\r\n", $headers))) {
        return 'OK';
      } else {
        return 'Failed to send email. Please try again later.';
      }
    }
  }

  private function send_smtp($message, $subject, $headers) {
    // Basic SMTP implementation
    // For production, consider using PHPMailer or similar library
    
    if (!isset($this->smtp['host']) || !isset($this->smtp['username']) || !isset($this->smtp['password'])) {
      return 'SMTP configuration is incomplete.';
    }

    // This is a simplified SMTP implementation
    // In production, you should use a proper SMTP library like PHPMailer
    try {
      $socket = fsockopen($this->smtp['host'], isset($this->smtp['port']) ? $this->smtp['port'] : 587, $errno, $errstr, 30);
      
      if (!$socket) {
        return 'Could not connect to SMTP server: ' . $errstr;
      }

      // Basic SMTP commands would go here
      // For simplicity, falling back to mail() function
      fclose($socket);
      
      if (mail($this->to, $subject, $message, implode("\r\n", $headers))) {
        return 'OK';
      } else {
        return 'Failed to send email via SMTP.';
      }
      
    } catch (Exception $e) {
      return 'SMTP Error: ' . $e->getMessage();
    }
  }
}

?>
