<?php
/**
 * 完整的数据库连接管理系统
 * 包含PDO连接、MySQLi连接、安全配置、错误处理等
 */

// 数据库配置
$config = [
    'host' => 'localhost',
    'username' => 'security_user',
    'password' => 'SecurePass123!',
    'database' => 'security_management',
    'charset' => 'utf8mb4',
    'port' => 3306,
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]
];

// 数据库连接管理器
class DatabaseConnectionManager {
    private $config;
    private $pdoConnection = null;
    private $mysqliConnection = null;
    
    public function __construct($config) {
        $this->config = $config;
    }
    
    // PDO连接
    public function getPDOConnection() {
        if ($this->pdoConnection === null) {
            try {
                $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']};charset={$this->config['charset']};port={$this->config['port']}";
                $this->pdoConnection = new PDO($dsn, $this->config['username'], $this->config['password'], $this->config['options']);
                echo "PDO连接建立成功\n";
            } catch (PDOException $e) {
                throw new Exception("PDO连接失败: " . $e->getMessage());
            }
        }
        return $this->pdoConnection;
    }
    
    // MySQLi连接
    public function getMySQLiConnection() {
        if ($this->mysqliConnection === null) {
            try {
                $this->mysqliConnection = new mysqli(
                    $this->config['host'],
                    $this->config['username'],
                    $this->config['password'],
                    $this->config['database'],
                    $this->config['port']
                );
                
                if ($this->mysqliConnection->connect_error) {
                    throw new Exception("MySQLi连接失败: " . $this->mysqliConnection->connect_error);
                }
                
                $this->mysqliConnection->set_charset($this->config['charset']);
                echo "MySQLi连接建立成功\n";
            } catch (Exception $e) {
                throw new Exception("MySQLi连接失败: " . $e->getMessage());
            }
        }
        return $this->mysqliConnection;
    }
    
    // 测试连接
    public function testConnections() {
        echo "=== 数据库连接测试 ===\n";
        
        try {
            // 测试PDO连接
            $pdo = $this->getPDOConnection();
            $stmt = $pdo->query("SELECT VERSION() as version, NOW() as current_time");
            $result = $stmt->fetch();
            echo "PDO测试成功 - MySQL版本: {$result['version']}, 当前时间: {$result['current_time']}\n";
            
            // 测试MySQLi连接
            $mysqli = $this->getMySQLiConnection();
            $result = $mysqli->query("SELECT VERSION() as version, NOW() as current_time");
            $row = $result->fetch_assoc();
            echo "MySQLi测试成功 - MySQL版本: {$row['version']}, 当前时间: {$row['current_time']}\n";
            
        } catch (Exception $e) {
            echo "连接测试失败: " . $e->getMessage() . "\n";
        }
    }
    
    // 关闭连接
    public function closeConnections() {
        if ($this->pdoConnection !== null) {
            $this->pdoConnection = null;
            echo "PDO连接已关闭\n";
        }
        
        if ($this->mysqliConnection !== null) {
            $this->mysqliConnection->close();
            $this->mysqliConnection = null;
            echo "MySQLi连接已关闭\n";
        }
    }
}

// 数据库安全管理类
class DatabaseSecurityManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->initializeDatabase();
    }
    
    // 初始化数据库表结构
    private function initializeDatabase() {
        try {
            // 创建用户表
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    email VARCHAR(100) UNIQUE NOT NULL,
                    password_hash VARCHAR(255) NOT NULL,
                    salt VARCHAR(32) NOT NULL,
                    role ENUM('admin', 'user', 'guest') DEFAULT 'user',
                    status ENUM('active', 'inactive', 'banned', 'deleted') DEFAULT 'active',
                    failed_login_attempts INT DEFAULT 0,
                    locked_until DATETIME NULL,
                    last_login_at DATETIME NULL,
                    last_login_ip VARCHAR(45) NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    deleted_at DATETIME NULL,
                    INDEX idx_username (username),
                    INDEX idx_email (email),
                    INDEX idx_status (status)
                ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
            ");
            
            // 创建用户会话表
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS user_sessions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    session_token VARCHAR(128) UNIQUE NOT NULL,
                    ip_address VARCHAR(45) NOT NULL,
                    user_agent TEXT,
                    is_active BOOLEAN DEFAULT TRUE,
                    expires_at DATETIME NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    INDEX idx_session_token (session_token),
                    INDEX idx_user_id (user_id),
                    INDEX idx_expires_at (expires_at)
                ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
            ");
            
            // 创建安全日志表
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS security_logs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NULL,
                    action VARCHAR(50) NOT NULL,
                    resource VARCHAR(100),
                    ip_address VARCHAR(45) NOT NULL,
                    user_agent TEXT,
                    success BOOLEAN NOT NULL,
                    additional_data JSON,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                    INDEX idx_user_id (user_id),
                    INDEX idx_action (action),
                    INDEX idx_created_at (created_at),
                    INDEX idx_success (success)
                ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
            ");
            
            echo "数据库表结构初始化完成\n";
            
        } catch (PDOException $e) {
            throw new Exception("数据库初始化失败: " . $e->getMessage());
        }
    }
    
    // 创建安全的用户
    public function createUser($username, $email, $password, $role = 'user') {
        try {
            $this->pdo->beginTransaction();
            
            // 验证输入
            if (!$this->validateUsername($username)) {
                throw new Exception("用户名格式不正确");
            }
            
            if (!$this->validateEmail($email)) {
                throw new Exception("邮箱格式不正确");
            }
            
            if (!$this->validatePasswordStrength($password)) {
                throw new Exception("密码强度不足");
            }
            
            // 检查用户是否已存在
            if ($this->userExists($username, $email)) {
                throw new Exception("用户名或邮箱已存在");
            }
            
            // 生成安全的密码哈希
            $salt = bin2hex(random_bytes(16));
            $passwordHash = password_hash($password . $salt, PASSWORD_ARGON2ID);
            
            // 插入用户
            $sql = "INSERT INTO users (username, email, password_hash, salt, role, status) VALUES (?, ?, ?, ?, ?, 'active')";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$username, $email, $passwordHash, $salt, $role]);
            
            if (!$result) {
                throw new Exception("用户创建失败");
            }
            
            $userId = $this->pdo->lastInsertId();
            
            // 记录安全日志
            $this->logSecurityEvent(null, 'user_created', "用户创建: $username", true, [
                'user_id' => $userId,
                'username' => $username,
                'email' => $email,
                'role' => $role
            ]);
            
            $this->pdo->commit();
            
            echo "用户 '$username' 创建成功，ID: $userId\n";
            return $userId;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            $this->logSecurityEvent(null, 'user_creation_failed', "用户创建失败: $username", false, [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    // 用户认证
    public function authenticateUser($username, $password, $ipAddress = '127.0.0.1', $userAgent = '') {
        try {
            $this->pdo->beginTransaction();
            
            // 获取用户信息
            $sql = "SELECT id, username, password_hash, salt, status, failed_login_attempts, locked_until FROM users WHERE username = ? FOR UPDATE";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $this->logSecurityEvent(null, 'login_failed', "登录失败: 用户不存在", false, [
                    'username' => $username,
                    'ip_address' => $ipAddress
                ]);
                throw new Exception("用户名或密码错误");
            }
            
            // 检查账户状态
            if ($user['status'] !== 'active') {
                $this->logSecurityEvent($user['id'], 'login_failed', "登录失败: 账户状态异常", false, [
                    'status' => $user['status']
                ]);
                throw new Exception("账户已被禁用");
            }
            
            // 检查是否被锁定
            if ($user['locked_until'] && $user['locked_until'] > date('Y-m-d H:i:s')) {
                $this->logSecurityEvent($user['id'], 'login_failed', "登录失败: 账户被锁定", false, [
                    'locked_until' => $user['locked_until']
                ]);
                throw new Exception("账户已被锁定，请稍后再试");
            }
            
            // 验证密码
            if (!password_verify($password . $user['salt'], $user['password_hash'])) {
                // 增加失败尝试次数
                $failedAttempts = $user['failed_login_attempts'] + 1;
                $lockTime = null;
                
                if ($failedAttempts >= 5) {
                    $lockTime = date('Y-m-d H:i:s', time() + 900); // 锁定15分钟
                }
                
                $updateSql = "UPDATE users SET failed_login_attempts = ?, locked_until = ? WHERE id = ?";
                $updateStmt = $this->pdo->prepare($updateSql);
                $updateStmt->execute([$failedAttempts, $lockTime, $user['id']]);
                
                $this->logSecurityEvent($user['id'], 'login_failed', "登录失败: 密码错误", false, [
                    'failed_attempts' => $failedAttempts,
                    'locked_until' => $lockTime
                ]);
                
                throw new Exception("用户名或密码错误");
            }
            
            // 登录成功，重置失败计数
            $updateSql = "UPDATE users SET failed_login_attempts = 0, locked_until = NULL, last_login_at = NOW(), last_login_ip = ? WHERE id = ?";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->execute([$ipAddress, $user['id']]);
            
            // 创建会话
            $sessionToken = bin2hex(random_bytes(64));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1小时有效期
            
            $sessionSql = "INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?)";
            $sessionStmt = $this->pdo->prepare($sessionSql);
            $sessionStmt->execute([$user['id'], $sessionToken, $ipAddress, $userAgent, $expiresAt]);
            
            $this->logSecurityEvent($user['id'], 'login_success', "登录成功", true, [
                'ip_address' => $ipAddress,
                'session_token' => substr($sessionToken, 0, 16) . '...'
            ]);
            
            $this->pdo->commit();
            
            echo "用户 '{$user['username']}' 登录成功\n";
            
            return [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'session_token' => $sessionToken,
                'expires_at' => $expiresAt
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    // 验证会话
    public function validateSession($sessionToken) {
        $sql = "SELECT us.user_id, us.expires_at, u.username, u.status 
                FROM user_sessions us 
                JOIN users u ON us.user_id = u.id 
                WHERE us.session_token = ? AND us.is_active = TRUE AND us.expires_at > NOW()";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$sessionToken]);
        $session = $stmt->fetch();
        
        if (!$session || $session['status'] !== 'active') {
            return false;
        }
        
        return $session;
    }
    
    // 记录安全事件
    private function logSecurityEvent($userId, $action, $resource, $success, $additionalData = []) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $sql = "INSERT INTO security_logs (user_id, action, resource, ip_address, user_agent, success, additional_data) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $userId,
            $action,
            $resource,
            $ipAddress,
            $userAgent,
            $success,
            json_encode($additionalData)
        ]);
    }
    
    // 辅助验证方法
    private function validateUsername($username) {
        return preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username);
    }
    
    private function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    private function validatePasswordStrength($password) {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }
    
    private function userExists($username, $email) {
        $sql = "SELECT 1 FROM users WHERE username = ? OR email = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username, $email]);
        return $stmt->fetchColumn() !== false;
    }
    
    // 获取安全统计信息
    public function getSecurityStats() {
        $stats = [];
        
        // 总用户数
        $stmt = $this->pdo->query("SELECT COUNT(*) as total_users FROM users WHERE status != 'deleted'");
        $stats['total_users'] = $stmt->fetchColumn();
        
        // 活跃用户数
        $stmt = $this->pdo->query("SELECT COUNT(*) as active_users FROM users WHERE status = 'active'");
        $stats['active_users'] = $stmt->fetchColumn();
        
        // 最近24小时登录次数
        $stmt = $this->pdo->query("SELECT COUNT(*) as recent_logins FROM security_logs WHERE action = 'login_success' AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stats['recent_logins'] = $stmt->fetchColumn();
        
        // 最近24小时失败登录次数
        $stmt = $this->pdo->query("SELECT COUNT(*) as failed_logins FROM security_logs WHERE action = 'login_failed' AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stats['failed_logins'] = $stmt->fetchColumn();
        
        // 当前活跃会话数
        $stmt = $this->pdo->query("SELECT COUNT(*) as active_sessions FROM user_sessions WHERE is_active = TRUE AND expires_at > NOW()");
        $stats['active_sessions'] = $stmt->fetchColumn();
        
        return $stats;
    }
}

// 演示和测试
echo "=== PHP调用MySQL完整演示系统 ===\n\n";

try {
    // 1. 建立数据库连接
    $connectionManager = new DatabaseConnectionManager($config);
    $connectionManager->testConnections();
    
    $pdo = $connectionManager->getPDOConnection();
    
    // 2. 初始化安全管理系统
    echo "\n=== 数据库安全管理系统测试 ===\n";
    $securityManager = new DatabaseSecurityManager($pdo);
    
    // 3. 创建测试用户
    echo "\n1. 用户创建测试:\n";
    try {
        $adminId = $securityManager->createUser('security_admin', 'admin@security.com', 'SecurePass123!@#', 'admin');
        $userId = $securityManager->createUser('test_user', 'user@security.com', 'UserPass456!@#', 'user');
    } catch (Exception $e) {
        echo "用户可能已存在: " . $e->getMessage() . "\n";
    }
    
    // 4. 用户认证测试
    echo "\n2. 用户认证测试:\n";
    try {
        $authResult = $securityManager->authenticateUser('security_admin', 'SecurePass123!@#', '192.168.1.100', 'TestBrowser/1.0');
        echo "认证成功，会话令牌: " . substr($authResult['session_token'], 0, 16) . "...\n";
        
        // 验证会话
        $sessionValid = $securityManager->validateSession($authResult['session_token']);
        if ($sessionValid) {
            echo "会话验证成功，用户: {$sessionValid['username']}\n";
        }
        
    } catch (Exception $e) {
        echo "认证测试失败: " . $e->getMessage() . "\n";
    }
    
    // 5. 错误认证测试
    echo "\n3. 错误认证测试:\n";
    try {
        $securityManager->authenticateUser('security_admin', 'WrongPassword', '192.168.1.100');
    } catch (Exception $e) {
        echo "预期的认证失败: " . $e->getMessage() . "\n";
    }
    
    // 6. 安全统计信息
    echo "\n4. 系统安全统计:\n";
    $stats = $securityManager->getSecurityStats();
    foreach ($stats as $key => $value) {
        echo "- " . ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
    }
    
    // 7. 查看最近的安全日志
    echo "\n5. 最近的安全日志:\n";
    $logStmt = $pdo->query("SELECT action, resource, ip_address, success, created_at FROM security_logs ORDER BY created_at DESC LIMIT 10");
    $logs = $logStmt->fetchAll();
    
    foreach ($logs as $log) {
        $status = $log['success'] ? '成功' : '失败';
        echo "- {$log['created_at']} | {$log['action']} | {$log['resource']} | $status | {$log['ip_address']}\n";
    }
    
    echo "\n=== 系统演示完成 ===\n";
    
} catch (Exception $e) {
    echo "系统错误: " . $e->getMessage() . "\n";
} finally {
    // 清理连接
    if (isset($connectionManager)) {
        $connectionManager->closeConnections();
    }
}
?>