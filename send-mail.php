<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success'=>false,'message'=>'Method not allowed']);
    exit;
}
function cl($v){ return htmlspecialchars(strip_tags(trim($v??'')), ENT_QUOTES, 'UTF-8'); }
$fn = cl($_POST['fname'] ?? '');
$ln = cl($_POST['lname'] ?? '');
$em = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$ph = cl($_POST['phone'] ?? '');
$sv = cl($_POST['service'] ?? '');
$mg = cl($_POST['message'] ?? '');
$hp = $_POST['website'] ?? '';
// Honeypot check
if (!empty($hp)) { echo json_encode(['success'=>true,'message'=>'Message sent!']); exit; }
// Validate
if (!$fn || !$ln) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Please enter your name.']); exit; }
if (!$em || !filter_var($em, FILTER_VALIDATE_EMAIL)) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Please enter a valid email address.']); exit; }
$to      = 'info@synergyafhproviderswa.com';
$subject = 'New Care Inquiry from ' . $fn . ' ' . $ln;
$body    = "New consultation request from the Synergy AFH website.\n\n";
$body   .= "Name    : $fn $ln\n";
$body   .= "Email   : $em\n";
$body   .= "Phone   : " . ($ph ?: 'Not provided') . "\n";
$body   .= "Service : " . ($sv ?: 'General inquiry') . "\n\n";
$body   .= "Message:\n$mg\n\n";
$body   .= "Sent: " . date('Y-m-d H:i:s T');
$headers  = "From: Synergy AFH Website <noreply@synergyafhproviderswa.com>\r\n";
$headers .= "Reply-To: $fn $ln <$em>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$sent = mail($to, $subject, $body, $headers);
echo json_encode([
    'success' => (bool)$sent,
    'message' => $sent
        ? 'Message sent! We will contact you within 24 hours.'
        : 'Mail server error. Please call +1 (425) 503-2432 directly.'
]);
