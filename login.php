<?php
// Lightweight wrapper so /login.php works while original file is named public_login.php
$target = __DIR__ . '/public_login.php';
if (file_exists($target)) {
    require $target;
    return;
}
// fallback: if an HTML login exists, redirect
if (file_exists(__DIR__ . '/public_login.html')) {
    header('Location: /public_login.html');
    exit;
}
http_response_code(404);
echo 'Login file not found.';
