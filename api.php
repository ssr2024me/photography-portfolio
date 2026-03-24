<?php
// =============================================
// api.php — InfinityFree wali file mein ye 
// upload_base64 action add karo
// Ye poori api.php hai — InfinityFree pe replace karo
// =============================================
require_once 'config.php';

$action = $_GET['action'] ?? 'get_list';

// ── 1. GET PHOTO LIST
if ($action === 'get_list') {
    header('Content-Type: application/json');
    $db = getDB();
    $db->query("ALTER TABLE photos ADD COLUMN IF NOT EXISTS description TEXT DEFAULT ''");
    $db->query("ALTER TABLE photos ADD COLUMN IF NOT EXISTS watermark_text VARCHAR(100) DEFAULT 'Photography-life4me'");
    $db->query("ALTER TABLE photos ADD COLUMN IF NOT EXISTS watermark_font VARCHAR(50) DEFAULT 'bold'");
    $db->query("ALTER TABLE photos ADD COLUMN IF NOT EXISTS has_watermark TINYINT(1) DEFAULT 1");
    $res = $db->query("SELECT id, title, category, mime_type, description, is_featured, sort_order, has_watermark, watermark_text, watermark_font, created_at FROM photos ORDER BY sort_order ASC, created_at DESC");
    $photos = [];
    if ($res) while ($row = $res->fetch_assoc()) $photos[] = $row;
    echo json_encode(['success' => true, 'photos' => $photos]);
    $db->close();
}

// ── 2. SERVE IMAGE
elseif ($action === 'get_image') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) { http_response_code(400); exit; }
    $db = getDB();
    $stmt = $db->prepare("SELECT image_data, mime_type FROM photos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($imageData, $mimeType);
    if ($stmt->fetch() && $imageData) {
        header('Content-Type: ' . $mimeType);
        header('Cache-Control: public, max-age=86400');
        $decoded = base64_decode($imageData, true);
        echo ($decoded !== false && strlen($decoded) > 100) ? $decoded : $imageData;
    } else {
        http_response_code(404);
    }
    $stmt->close(); $db->close();
}

// ── 3. UPLOAD BASE64 (for sync tool)
elseif ($action === 'upload_base64') {
    header('Content-Type: application/json');
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!$data) { echo json_encode(['error' => 'Invalid JSON']); exit; }
    if (($data['admin_key'] ?? '') !== ADMIN_KEY) { echo json_encode(['error' => 'Unauthorized']); exit; }

    $title       = htmlspecialchars($data['title']          ?? 'Untitled');
    $category    = htmlspecialchars($data['category']       ?? 'General');
    $description = htmlspecialchars($data['description']    ?? '');
    $featured    = intval($data['is_featured']              ?? 0);
    $hasWM       = intval($data['has_watermark']            ?? 0);
    $wmText      = htmlspecialchars($data['watermark_text'] ?? 'Photography-life4me');
    $wmFont      = htmlspecialchars($data['watermark_font'] ?? 'bold');
    $mimeType    = $data['mime_type'] ?? 'image/jpeg';
    $imageData   = $data['image_data'] ?? '';

    if (!$imageData) { echo json_encode(['error' => 'No image data']); exit; }

    $db = getDB();
    $db->query("ALTER TABLE photos ADD COLUMN IF NOT EXISTS description TEXT DEFAULT ''");
    $db->query("ALTER TABLE photos ADD COLUMN IF NOT EXISTS watermark_text VARCHAR(100) DEFAULT 'Photography-life4me'");
    $db->query("ALTER TABLE photos ADD COLUMN IF NOT EXISTS watermark_font VARCHAR(50) DEFAULT 'bold'");
    $db->query("ALTER TABLE photos ADD COLUMN IF NOT EXISTS has_watermark TINYINT(1) DEFAULT 1");
    $db->query("ALTER TABLE photos MODIFY COLUMN image_data LONGTEXT");

    if ($featured) $db->query("UPDATE photos SET is_featured=0");

    $stmt = $db->prepare("INSERT INTO photos (title, category, mime_type, image_data, description, is_featured, has_watermark, watermark_text, watermark_font) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) { echo json_encode(['error' => 'Prepare: ' . $db->error]); exit; }
    $stmt->bind_param('ssssssiis', $title, $category, $mimeType, $imageData, $description, $featured, $hasWM, $wmText, $wmFont);
    if (!$stmt->execute()) { echo json_encode(['error' => 'Execute: ' . $stmt->error]); exit; }
    $id = $db->insert_id;
    $stmt->close(); $db->close();
    echo json_encode(['success' => true, 'id' => $id]);
}

// ── 4. UPLOAD WITH FILE (normal admin upload)
elseif ($action === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (($_POST['admin_key'] ?? '') !== ADMIN_KEY) { echo json_encode(['error' => 'Unauthorized']); exit; }
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => 'File upload failed']); exit;
    }
    $file     = $_FILES['photo'];
    $mimeType = mime_content_type($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_TYPES)) { echo json_encode(['error' => 'Invalid type']); exit; }
    if ($file['size'] > MAX_FILE_SIZE) { echo json_encode(['error' => 'Too large']); exit; }

    $title       = htmlspecialchars($_POST['title']       ?? 'Untitled');
    $category    = htmlspecialchars($_POST['category']    ?? 'General');
    $description = htmlspecialchars($_POST['description'] ?? '');
    $featured    = intval($_POST['is_featured']           ?? 0);
    $hasWM       = intval($_POST['has_watermark']         ?? 0);
    $wmText      = htmlspecialchars($_POST['watermark_text'] ?? 'Photography-life4me');
    $wmFont      = htmlspecialchars($_POST['watermark_font'] ?? 'bold');
    $imageData   = base64_encode(file_get_contents($file['tmp_name']));

    $db = getDB();
    $db->query("ALTER TABLE photos MODIFY COLUMN image_data LONGTEXT");
    if ($featured) $db->query("UPDATE photos SET is_featured=0");
    $stmt = $db->prepare("INSERT INTO photos (title, category, mime_type, image_data, description, is_featured, has_watermark, watermark_text, watermark_font) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) { echo json_encode(['error' => 'Prepare: ' . $db->error]); exit; }
    $stmt->bind_param('ssssssiis', $title, $category, $mimeType, $imageData, $description, $featured, $hasWM, $wmText, $wmFont);
    if (!$stmt->execute()) { echo json_encode(['error' => 'Execute: ' . $stmt->error]); exit; }
    $id = $db->insert_id;
    $stmt->close(); $db->close();
    echo json_encode(['success' => true, 'id' => $id]);
}

// ── 5. UPDATE
elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (($_POST['admin_key'] ?? '') !== ADMIN_KEY) { echo json_encode(['error' => 'Unauthorized']); exit; }
    $id = intval($_POST['id'] ?? 0);
    $title = htmlspecialchars($_POST['title'] ?? 'Untitled');
    $category = htmlspecialchars($_POST['category'] ?? 'General');
    $description = htmlspecialchars($_POST['description'] ?? '');
    $featured = intval($_POST['is_featured'] ?? 0);
    $db = getDB();
    if ($featured) $db->query("UPDATE photos SET is_featured=0");
    $stmt = $db->prepare("UPDATE photos SET title=?, category=?, description=?, is_featured=? WHERE id=?");
    $stmt->bind_param('sssii', $title, $category, $description, $featured, $id);
    $stmt->execute(); $stmt->close(); $db->close();
    echo json_encode(['success' => true]);
}

// ── 6. DELETE
elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (($_POST['admin_key'] ?? '') !== ADMIN_KEY) { echo json_encode(['error' => 'Unauthorized']); exit; }
    $id = intval($_POST['id'] ?? 0);
    $db = getDB(); $db->query("DELETE FROM photos WHERE id=$id");
    echo json_encode(['success' => true]); $db->close();
}

// ── 7. SET HERO
elseif ($action === 'set_hero' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (($_POST['admin_key'] ?? '') !== ADMIN_KEY) { echo json_encode(['error' => 'Unauthorized']); exit; }
    $id = intval($_POST['id'] ?? 0);
    $db = getDB();
    $db->query("UPDATE photos SET is_featured=0");
    $db->query("UPDATE photos SET is_featured=1 WHERE id=$id");
    echo json_encode(['success' => true]); $db->close();
}

// ── 8. MESSAGES
elseif ($action === 'get_messages') {
    header('Content-Type: application/json');
    $db = getDB();
    $db->query("CREATE TABLE IF NOT EXISTS messages (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), email VARCHAR(255), subject VARCHAR(255) DEFAULT '', message TEXT, reply TEXT DEFAULT NULL, is_read TINYINT(1) DEFAULT 0, is_replied TINYINT(1) DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
    $res = $db->query("SELECT * FROM messages ORDER BY created_at DESC");
    $msgs = [];
    if ($res) while ($row = $res->fetch_assoc()) $msgs[] = $row;
    echo json_encode(['success' => true, 'messages' => $msgs]); $db->close();
}

// ── 9. GET/SAVE SETTINGS
elseif ($action === 'get_settings') {
    header('Content-Type: application/json');
    $db = getDB();
    $res = $db->query("SELECT `key`, `value` FROM settings");
    $s = [];
    if ($res) while ($r = $res->fetch_assoc()) $s[$r['key']] = $r['value'];
    echo json_encode(['success' => true, 'settings' => $s]); $db->close();
}
elseif ($action === 'save_setting' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (($_POST['admin_key'] ?? '') !== ADMIN_KEY) { echo json_encode(['error' => 'Unauthorized']); exit; }
    $key = $_POST['key'] ?? ''; $val = $_POST['value'] ?? '';
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO settings (`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=?");
    $stmt->bind_param('sss', $key, $val, $val);
    $stmt->execute(); $stmt->close(); $db->close();
    echo json_encode(['success' => true]);
}


// ── MARK READ
elseif ($action === 'mark_read' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $id = intval($_POST['id'] ?? 0);
    $db = getDB();
    $db->query("UPDATE messages SET is_read=1 WHERE id=$id");
    echo json_encode(['success' => true]); $db->close();
}

// ── REPLY MESSAGE
elseif ($action === 'reply_message' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (($_POST['admin_key'] ?? '') !== ADMIN_KEY) { echo json_encode(['error' => 'Unauthorized']); exit; }
    $id    = intval($_POST['id']    ?? 0);
    $reply = htmlspecialchars($_POST['reply'] ?? '');
    $email = $_POST['email'] ?? '';
    $db = getDB();
    $stmt = $db->prepare("UPDATE messages SET reply=?, is_read=1, is_replied=1 WHERE id=?");
    $stmt->bind_param('si', $reply, $id);
    $stmt->execute(); $stmt->close();
    // Send email
    $to = $email;
    $subj = 'Reply from Photography-life4me';
    $headers = "From: noreply@photography-life4me.com";
    mail($to, $subj, $reply, $headers);
    echo json_encode(['success' => true]); $db->close();
}

// ── DELETE MESSAGE
elseif ($action === 'delete_message' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (($_POST['admin_key'] ?? '') !== ADMIN_KEY) { echo json_encode(['error' => 'Unauthorized']); exit; }
    $id = intval($_POST['id'] ?? 0);
    $db = getDB(); $db->query("DELETE FROM messages WHERE id=$id");
    echo json_encode(['success' => true]); $db->close();
}

// ── SET FEATURED BATCH
elseif ($action === 'set_featured_batch' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (($_POST['admin_key'] ?? '') !== ADMIN_KEY) { echo json_encode(['error' => 'Unauthorized']); exit; }
    $ids = json_decode($_POST['ids'] ?? '[]', true);
    $db = getDB();
    $db->query('UPDATE photos SET is_featured=0');
    $count = 0;
    foreach ($ids as $id) {
        $id = intval($id);
        if ($id > 0) { $db->query("UPDATE photos SET is_featured=1 WHERE id=$id"); $count++; }
    }
    echo json_encode(['success' => true, 'count' => $count]); $db->close();
}

else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid action']);
}
?>