<?php
// Lightweight autoloader for this project when Composer is not used.
spl_autoload_register(function ($class) {
    $map = [
        'App\\Security\\PasswordHasher' => __DIR__ . '/../src_Security_PasswordHasher.php',
        'App\\Entity\\User' => __DIR__ . '/../src_Entity_User.php',
    ];

    if (isset($map[$class]) && file_exists($map[$class])) {
        require_once $map[$class];
    }
});

return null;
