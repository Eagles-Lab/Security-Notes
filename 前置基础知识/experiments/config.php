<?php
// config.php - 配置文件
return [
    'database' => [
        'default' => [
            'host' => 'localhost',
            'database' => 'user_management',
            'username' => 'root',
            'password' => 'password'
        ]
    ],
    'security' => [
        'password_min_length' => 8,
        'max_login_attempts' => 5,
        'session_timeout' => 3600
    ]
];
?>