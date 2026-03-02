<?php
// Wrapper so /logout.php funktioniert (original: public_logout.php)
$target = __DIR__ . '/public_logout.php';
if (file_exists($target)) {
    require $target;
    return;
}
http_response_code(404);
echo 'Logout file not found.';
