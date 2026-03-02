<?php
// Lightweight wrapper so /register.php works while original file is named public_register.php
$target = __DIR__ . '/public_register.php';
if (file_exists($target)) {
    require $target;
    return;
}
// fallback: if an HTML register exists, redirect
if (file_exists(__DIR__ . '/public_register.html')) {
    header('Location: /public_register.html');
    exit;
}
http_response_code(404);
echo 'Register file not found.';
