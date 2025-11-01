<?php
/**
 * 数据库配置文件 (模拟)
 * 这个文件包含敏感的数据库连接信息
 * 攻击者可以通过文件包含漏洞读取这些信息
 */

// 数据库配置
define('DB_HOST', 'localhost');
define('DB_USER', 'admin');
define('DB_PASS', 'SecretPassword123!');
define('DB_NAME', 'security_test');

// 应用配置
define('APP_KEY', 'your-secret-app-key-here');
define('ADMIN_EMAIL', 'admin@example.com');

// API密钥
define('API_SECRET', '11111111111111111111E');

echo "配置文件加载成功！";
?>
