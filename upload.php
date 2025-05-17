<?php
/**
 * File upload handler
 */

$settingsFile = __DIR__ . '/config/settings.php';
if (!file_exists($settingsFile)) {
    sendResponse(false, 'システムが設定されていません。setup.htmlで初期設定を行ってください。');
    exit;
}

require_once $settingsFile;

ini_set('upload_max_filesize', $maxUploadSize);
ini_set('post_max_size', $postMaxSize);
ini_set('max_execution_time', $maxExecutionTime);
ini_set('max_input_time', $maxInputTime);
ini_set('memory_limit', $memoryLimit);

if (!file_exists($uploadDirectory)) {
    if (!mkdir($uploadDirectory, 0777, true)) {
        sendResponse(false, 'アップロードディレクトリを作成できませんでした。');
        exit;
    }
}

if (!is_writable($uploadDirectory)) {
    if (!chmod($uploadDirectory, 0777)) {
        sendResponse(false, 'アップロードディレクトリに書き込み権限がありません。');
        exit;
    }
}

if (!isset($_FILES['file'])) {
    sendResponse(false, 'ファイルがアップロードされていません。');
    exit;
}

$file = $_FILES['file'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    $errorMessage = getUploadErrorMessage($file['error']);
    sendResponse(false, $errorMessage);
    exit;
}

if ($file['size'] > $maxUploadSize) {
    sendResponse(false, 'ファイルサイズが大きすぎます。最大' . formatBytes($maxUploadSize) . 'までです。');
    exit;
}

$fileInfo = pathinfo($file['name']);
$extension = isset($fileInfo['extension']) ? strtolower($fileInfo['extension']) : '';

if (isset($allowedFileExtensions) && !empty($allowedFileExtensions) && !in_array($extension, $allowedFileExtensions)) {
    sendResponse(false, '許可されていないファイル形式です。許可されている形式: ' . implode(', ', $allowedFileExtensions));
    exit;
}

$extension = isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : '';
$filename = $fileInfo['filename'] . '_' . time() . '_' . mt_rand(1000, 9999) . $extension;
$targetPath = $uploadDirectory . '/' . $filename;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    sendResponse(true, 'ファイルが正常にアップロードされました。', $filename);
} else {
    sendResponse(false, 'ファイルの保存中にエラーが発生しました。');
}

/**
 * Send JSON response
 *
 * @param bool $success Whether the operation was successful
 * @param string $message Message to display to the user
 * @param string $filename Optional filename that was saved
 */
function sendResponse($success, $message, $filename = '') {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($filename) {
        $response['filename'] = $filename;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

/**
 * Get human-readable error message for upload errors
 *
 * @param int $errorCode PHP upload error code
 * @return string Human-readable error message
 */
function getUploadErrorMessage($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return 'アップロードされたファイルがPHPのupload_max_filesizeディレクティブを超えています。';
        case UPLOAD_ERR_FORM_SIZE:
            return 'アップロードされたファイルがHTMLフォームで指定されたMAX_FILE_SIZEを超えています。';
        case UPLOAD_ERR_PARTIAL:
            return 'ファイルの一部のみがアップロードされました。';
        case UPLOAD_ERR_NO_FILE:
            return 'ファイルがアップロードされませんでした。';
        case UPLOAD_ERR_NO_TMP_DIR:
            return '一時フォルダがありません。';
        case UPLOAD_ERR_CANT_WRITE:
            return 'ディスクへの書き込みに失敗しました。';
        case UPLOAD_ERR_EXTENSION:
            return 'PHPの拡張機能によってアップロードが停止されました。';
        default:
            return '不明なエラーが発生しました。';
    }
}

/**
 * Format bytes to human-readable format
 *
 * @param int $bytes Number of bytes
 * @param int $precision Decimal precision
 * @return string Formatted size with unit
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}
