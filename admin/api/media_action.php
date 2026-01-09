<?php
session_start();
require_once '../includes/auth_check.php'; // Ensure admin access

$upload_dir = __DIR__ . '/../../uploads/';
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        die(json_encode(['success' => false, 'message' => 'Cannot create upload directory: ' . $upload_dir]));
    }
}
// Ensure it's writable
if (!is_writable($upload_dir)) {
    // Try to chmod if not writable
    chmod($upload_dir, 0777);
    if (!is_writable($upload_dir)) {
        die(json_encode(['success' => false, 'message' => 'Upload directory is not writable. Please check permissions for: ' . $upload_dir]));
    }
}

// Response helper
function jsonResponse($success, $message, $data = [])
{
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

// Slugify helper (Vietnamese Safe)
function slugify($text)
{
    if (!$text)
        return 'n-a';

    // Map Vietnamese accents to ASCII
    $vietnameseMap = [
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'd' => 'đ',
        'D' => 'Đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
    ];

    foreach ($vietnameseMap as $nonAccent => $accented) {
        $text = preg_replace("/($accented)/u", $nonAccent, $text);
    }

    // Lowercase
    $text = strtolower($text);

    // Replace strict non-alphanumeric chars
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);

    // Replace spaces and multiple and dashes
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');

    return empty($text) ? 'n-a' : $text;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// 1. LIST FILES
if ($action === 'list') {
    $files = [];
    $raw_files = scandir($upload_dir);
    foreach ($raw_files as $file) {
        if ($file !== '.' && $file !== '..' && $file !== '.DS_Store') {
            $path = $upload_dir . $file;
            if (is_file($path)) {
                $files[] = [
                    'name' => $file,
                    'url' => 'uploads/' . $file,
                    'size' => filesize($path),
                    'date' => filemtime($path),
                    'type' => pathinfo($path, PATHINFO_EXTENSION)
                ];
            }
        }
    }
    // Sort by date desc
    usort($files, function ($a, $b) {
        return $b['date'] - $a['date'];
    });
    jsonResponse(true, 'List fetched', $files);
}

// 2. UPLOAD FILES
if ($action === 'upload') {
    if (!isset($_FILES['files'])) {
        jsonResponse(false, 'No files sent');
    }

    $uploaded = [];
    $errors = [];

    // Normalizing file array structure if multiple
    $file_ary = [];
    $file_count = count($_FILES['files']['name']);
    $file_keys = array_keys($_FILES['files']);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $_FILES['files'][$key][$i];
        }
    }

    foreach ($file_ary as $file) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

            if (in_array($ext, $allowed)) {
                $name_no_ext = pathinfo($file['name'], PATHINFO_FILENAME);
                $slug_name = slugify($name_no_ext);
                $final_name = $slug_name . '.' . $ext;

                // Avoid overwrite by appending timestamp if exists
                if (file_exists($upload_dir . $final_name)) {
                    $final_name = $slug_name . '-' . time() . '.' . $ext;
                }

                if (move_uploaded_file($file['tmp_name'], $upload_dir . $final_name)) {
                    $uploaded[] = $final_name;
                } else {
                    $errors[] = "Failed to move " . $file['name'];
                }
            } else {
                $errors[] = "Invalid type: " . $file['name'];
            }
        } else {
            $errors[] = "Error uploading " . $file['name'];
        }
    }

    jsonResponse(true, 'Upload processed', ['uploaded' => $uploaded, 'errors' => $errors]);
}

// 3. RENAME FILE
if ($action === 'rename') {
    $old_name = $_POST['old_name'] ?? '';
    $new_name_raw = $_POST['new_name'] ?? '';

    if (!$old_name || !$new_name_raw)
        jsonResponse(false, 'Missing parameters');
    if (!file_exists($upload_dir . $old_name))
        jsonResponse(false, 'File not found');

    $ext = strtolower(pathinfo($old_name, PATHINFO_EXTENSION));

    // Smartly handle if user typed the extension manually
    // e.g. user input "image.jpg" for a jpg file. We want "image", not "imagejpg"
    $dot_ext = '.' . $ext;
    if (substr(strtolower($new_name_raw), -strlen($dot_ext)) === $dot_ext) {
        $new_name_raw = substr($new_name_raw, 0, -strlen($dot_ext));
    }

    $slug_new = slugify($new_name_raw);
    $final_name = $slug_new . '.' . $ext;

    if ($old_name === $final_name)
        jsonResponse(true, 'No change needed');
    if (file_exists($upload_dir . $final_name))
        jsonResponse(false, 'Tên file đã tồn tại!');

    if (rename($upload_dir . $old_name, $upload_dir . $final_name)) {
        jsonResponse(true, 'Đổi tên thành công', ['new_name' => $final_name, 'url' => 'uploads/' . $final_name]);
    } else {
        jsonResponse(false, 'Lỗi hệ thống khi đổi tên');
    }
}

// 4. DELETE FILE
if ($action === 'delete') {
    $filename = $_POST['filename'] ?? '';
    if (!$filename)
        jsonResponse(false, 'Missing filename');

    $path = $upload_dir . $filename;
    if (file_exists($path)) {
        unlink($path);
        jsonResponse(true, 'Đã xóa file');
    } else {
        jsonResponse(false, 'File không tồn tại');
    }
}

jsonResponse(false, 'Invalid action');
