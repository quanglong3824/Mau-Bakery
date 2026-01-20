<?php
// admin/includes/media_helper.php

/**
 * Send JSON Response
 */
function jsonResponse($success, $message, $data = [])
{
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

/**
 * Generate SEO-friendly slug (Vietnamese Safe)
 */
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

    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');

    return empty($text) ? 'n-a' : $text;
}

/**
 * Setup Upload Directory
 */
function getUploadDirectory()
{
    // Relative path from this file (inside includes/) to uploads/
    // Assuming structure: admin/includes/media_helper.php
    // Uploads is at: root/uploads/

    // Note: The caller is usually admin/api/media_action.php, so __DIR__ logic depends on use context.
    // Ideally, pass the path or define a constant. 
    // Let's use a safe absolute path assumption relative to project root.

    $path = __DIR__ . '/../../uploads/';

    if (!file_exists($path)) {
        if (!mkdir($path, 0777, true)) {
            return false;
        }
    }

    if (!is_writable($path)) {
        chmod($path, 0777);
    }

    return is_writable($path) ? $path : false;
}
?>