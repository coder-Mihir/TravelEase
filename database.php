<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'travelease_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Helper: sanitize input
function clean($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

// Helper: redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Helper: check user login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper: check admin login
function isAdmin() {
    return isset($_SESSION['admin_id']);
}

// Helper: ensure every package has a place-based image URL (non-random).
function packageImageUrl($destination, $image = '') {
    $image = trim((string)$image);
    $destinationText = strtolower((string)$destination);

    // Known random placeholder hosts we don't want to show.
    $isRandomPlaceholder = stripos($image, 'picsum.photos') !== false;

    // Keep valid remote URLs as-is.
    if ($image !== '' && preg_match('/^https?:\/\//i', $image) && !$isRandomPlaceholder) {
        return $image;
    }

    // Keep valid local files if they exist and are non-empty.
    if ($image !== '') {
        $normalized = ltrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $image), DIRECTORY_SEPARATOR);
        $absolute = __DIR__ . DIRECTORY_SEPARATOR . $normalized;
        if (is_file($absolute) && filesize($absolute) > 0) {
            return $image;
        }
    }
}
?>