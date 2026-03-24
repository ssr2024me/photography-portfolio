<?php
// contact.php — Contact Form Handler
// Saves message to DB + sends email notification
require_once 'config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request']); exit;
}

$name    = htmlspecialchars(trim($_POST['name']    ?? ''));
$email   = htmlspecialchars(trim($_POST['email']   ?? ''));
$subject = htmlspecialchars(trim($_POST['type']    ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));

if (!$name || !$email || !$message) {
    echo json_encode(['error' => 'Naam, email aur message zaroori hai!']); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Valid email daalen!']); exit;
}

// Create table if not exists
$db = getDB();
$db->query("CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) DEFAULT '',
    message TEXT NOT NULL,
    reply TEXT DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    is_replied TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Save to DB
$stmt = $db->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $name, $email, $subject, $message);
$stmt->execute();
$stmt->close();
$db->close();

// Send email notification to you
$to      = 'shubhsagar2gkp@gmail.com';
$subj    = "New Message from $name — Photography-life4me";
$body    = "Namaskar Shubham!\n\nAapki website se naya message aaya hai:\n\nNaam    : $name\nEmail   : $email\nSubject : $subject\nMessage : $message\n\nAdmin panel pe jaake reply karein:\nhttp://localhost/photography/admin.php\n";
$headers = "From: noreply@photography-life4me.com\r\nReply-To: $email";
mail($to, $subj, $body, $headers);

echo json_encode(['success' => true, 'msg' => 'Message bhej diya! Hum jald reply karenge.']);
?>
