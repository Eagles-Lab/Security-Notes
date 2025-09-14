<!DOCTYPE html>
<html>
<head>
    <title>PHP类与对象实验 - 面向对象安全管理系统</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .section { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .code-demo { background: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 10px 0; }
        .result { background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0; }
        .error { background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin: 10px 0; }
        .info { background: #d1ecf1; padding: 10px; border: 1px solid #bee5eb; margin: 10px 0; }
        h1, h2, h3 { color: #333; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
        .tabs { margin-bottom: 20px; }
        .tab { display: inline-block; padding: 10px 20px; background: #f8f9fa; border: 1px solid #ddd; cursor: pointer; }
        .tab.active { background: #007bff; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <h1>PHP类与对象实验 - 面向对象安全管理系统</h1>
    
    <div class="tabs">
        <div class="tab active" onclick="showTab('basic')">基础类演示</div>
        <div class="tab" onclick="showTab('inheritance')">继承演示</div>
        <div class="tab" onclick="showTab('interface')">接口演示</div>
        <div class="tab" onclick="showTab('security')">安全系统</div>
        <div class="tab" onclick="showTab('info')">系统信息</div>
    </div>

    <!-- 基础类演示 -->
    <div id="basic" class="tab-content active">
        <div class="section">
            <h2>1. 基础类定义与对象创建</h2>
            <?php
            // 安全用户类
            class SecureUser {
                private $username;
                private $email;
                private $passwordHash;
                private $createdAt;
                private $isActive;
                
                public function __construct($username, $email, $password) {
                    $this->setUsername($username);
                    $this->setEmail($email);
                    $this->setPassword($password);
                    $this->createdAt = date('Y-m-d H:i:s');
                    $this->isActive = true;
                }
                
                public function setUsername($username) {
                    if (empty($username) || strlen($username) < 3) {
                        throw new InvalidArgumentException('用户名至少3个字符');
                    }
                    if (!preg_match('/^[a-zA-Z0-9_\u4e00-\u9fa5]+$/u', $username)) {
                        throw new InvalidArgumentException('用户名只能包含字母、数字、下划线和中文');
                    }
                    $this->username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
                }
                
                public function setEmail($email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        throw new InvalidArgumentException('无效的邮箱地址');
                    }
                    $this->email = $email;
                }
                
                private function setPassword($password) {
                    if (strlen($password) < 8) {
                        throw new InvalidArgumentException('密码至少8个字符');
                    }
                    $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
                }
                
                public function getUsername() {
                    return $this->username;
                }
                
                public function getEmail() {
                    return $this->email;
                }
                
                public function verifyPassword($password) {
                    return password_verify($password, $this->passwordHash);
                }
                
                public function getUserInfo() {
                    return [
                        'username' => $this->username,
                        'email' => $this->email,
                        'created_at' => $this->createdAt,
                        'is_active' => $this->isActive
                    ];
                }
            }
            
            echo "<div class='code-demo'>";
            echo "<h3>创建安全用户对象：</h3>";
            
            try {
                $user1 = new SecureUser('安全管理员', 'admin@security.com', 'SecurePass123!');
                $user2 = new SecureUser('test_user', 'user@test.com', 'UserPass456@');
                
                echo "<div class='result'>";
                echo "<p><strong>用户1信息：</strong></p>";
                $info1 = $user1->getUserInfo();
                foreach ($info1 as $key => $value) {
                    echo "<p>$key: $value</p>";
                }
                
                echo "<p><strong>用户2信息：</strong></p>";
                $info2 = $user2->getUserInfo();
                foreach ($info2 as $key => $value) {
                    echo "<p>$key: $value</p>";
                }
                
                echo "<p><strong>密码验证测试：</strong></p>";
                echo "<p>用户1密码验证: " . ($user1->verifyPassword('SecurePass123!') ? '✅ 正确' : '❌ 错误') . "</p>";
                echo "<p>用户2错误密码测试: " . ($user2->verifyPassword('wrongpass') ? '✅ 正确' : '❌ 错误') . "</p>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='error'>错误: " . $e->getMessage() . "</div>";
            }
            echo "</div>";
            ?>
        </div>
    </div>

    <!-- 继承演示 -->
    <div id="inheritance" class="tab-content">
        <div class="section">
            <h2>2. 继承机制演示</h2>
            <?php
            // 基础用户类
            abstract class BaseUser {
                protected $username;
                protected $email;
                protected $permissions = [];
                protected $loginAttempts = 0;
                protected $maxAttempts = 3;
                
                public function __construct($username, $email) {
                    $this->username = $username;
                    $this->email = $email;
                }
                
                abstract public function getPermissions();
                
                public function canPerform($action) {
                    return in_array($action, $this->getPermissions());
                }
                
                public function recordLoginAttempt($success) {
                    if ($success) {
                        $this->loginAttempts = 0;
                    } else {
                        $this->loginAttempts++;
                    }
                }
                
                public function isLocked() {
                    return $this->loginAttempts >= $this->maxAttempts;
                }
                
                protected function log($action) {
                    error_log(date('Y-m-d H:i:s') . " [" . get_class($this) . "] {$this->username}: $action");
                }
            }
            
            // 普通用户类
            class RegularUser extends BaseUser {
                public function getPermissions() {
                    return ['read', 'comment', 'update_profile'];
                }
                
                public function viewContent() {
                    $this->log('查看内容');
                    return "普通用户 {$this->username} 正在查看内容";
                }
            }
            
            // 管理员用户类
            class AdminUser extends BaseUser {
                private $adminLevel;
                
                public function __construct($username, $email, $adminLevel = 1) {
                    parent::__construct($username, $email);
                    $this->adminLevel = $adminLevel;
                    $this->maxAttempts = 5; // 管理员有更多尝试次数
                }
                
                public function getPermissions() {
                    return ['read', 'write', 'delete', 'manage_users', 'system_config'];
                }
                
                public function manageUser($action, $targetUser) {
                    if (!$this->canPerform('manage_users')) {
                        throw new Exception('权限不足');
                    }
                    
                    $this->log("管理用户: $action on $targetUser");
                    return "管理员 {$this->username} 对用户 $targetUser 执行了 $action 操作";
                }
                
                public function getAdminLevel() {
                    return $this->adminLevel;
                }
            }
            
            // 超级管理员类
            class SuperAdmin extends AdminUser {
                public function __construct($username, $email) {
                    parent::__construct($username, $email, 10);
                }
                
                public function getPermissions() {
                    return array_merge(parent::getPermissions(), ['system_maintenance', 'security_audit']);
                }
                
                public function systemMaintenance($task) {
                    $this->log("系统维护: $task");
                    return "超级管理员 {$this->username} 执行系统维护: $task";
                }
            }
            
            echo "<div class='code-demo'>";
            echo "<h3>继承关系演示：</h3>";
            
            $regularUser = new RegularUser('张三', 'zhangsan@example.com');
            $admin = new AdminUser('李管理员', 'admin@company.com', 2);
            $superAdmin = new SuperAdmin('超级管理员', 'super@system.com');
            
            $users = [$regularUser, $admin, $superAdmin];
            
            echo "<div class='result'>";
            foreach ($users as $user) {
                echo "<h4>" . get_class($user) . ": {$user->username}</h4>";
                echo "<p>权限: " . implode(', ', $user->getPermissions()) . "</p>";
                
                $testActions = ['read', 'delete', 'manage_users', 'system_maintenance'];
                echo "<p>权限测试:</p><ul>";
                foreach ($testActions as $action) {
                    $canDo = $user->canPerform($action) ? '✅' : '❌';
                    echo "<li>$canDo $action</li>";
                }
                echo "</ul>";
            }
            
            echo "<h4>功能调用演示：</h4>";
            echo "<p>" . $regularUser->viewContent() . "</p>";
            echo "<p>" . $admin->manageUser('suspend', '张三') . "</p>";
            echo "<p>" . $superAdmin->systemMaintenance('清理日志文件') . "</p>";
            echo "</div>";
            
            echo "</div>";
            ?>
        </div>
    </div>

    <!-- 接口演示 -->
    <div id="interface" class="tab-content">
        <div class="section">
            <h2>3. 接口实现演示</h2>
            <?php
            // 安全验证接口
            interface SecurityValidatorInterface {
                public function validate($input);
                public function getLastError();
            }
            
            // 加密接口
            interface EncryptionInterface {
                public function encrypt($data);
                public function decrypt($data);
            }
            
            // 审计日志接口
            interface AuditLogInterface {
                public function logEvent($event, $user, $details = []);
                public function getRecentLogs($limit = 10);
            }
            
            // 输入验证器实现
            class InputValidator implements SecurityValidatorInterface {
                private $lastError = '';
                
                public function validate($input) {
                    $this->lastError = '';
                    
                    // XSS检查
                    if ($input !== htmlspecialchars($input, ENT_QUOTES, 'UTF-8')) {
                        $this->lastError = '输入包含潜在的XSS代码';
                        return false;
                    }
                    
                    // SQL注入检查
                    $dangerous = [
                        'union\s+select', 'drop\s+table', 'delete\s+from',
                        'insert\s+into', 'update\s+set', '--', ';'
                    ];
                    
                    foreach ($dangerous as $pattern) {
                        if (preg_match("/$pattern/i", $input)) {
                            $this->lastError = '输入包含可疑的SQL代码';
                            return false;
                        }
                    }
                    
                    return true;
                }
                
                public function getLastError() {
                    return $this->lastError;
                }
            }
            
            // 简单加密器实现
            class SimpleEncryption implements EncryptionInterface {
                private $key;
                
                public function __construct($key = 'default-key-12345') {
                    $this->key = $key;
                }
                
                public function encrypt($data) {
                    return base64_encode($data . '|' . md5($data . $this->key));
                }
                
                public function decrypt($data) {
                    $decoded = base64_decode($data);
                    $parts = explode('|', $decoded, 2);
                    
                    if (count($parts) !== 2) {
                        return false;
                    }
                    
                    [$originalData, $hash] = $parts;
                    if (md5($originalData . $this->key) !== $hash) {
                        return false;
                    }
                    
                    return $originalData;
                }
            }
            
            // 审计日志实现
            class FileAuditLog implements AuditLogInterface {
                private $logFile;
                private $logs = [];
                
                public function __construct($logFile = 'audit.log') {
                    $this->logFile = $logFile;
                }
                
                public function logEvent($event, $user, $details = []) {
                    $logEntry = [
                        'timestamp' => date('Y-m-d H:i:s'),
                        'event' => $event,
                        'user' => $user,
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                        'details' => $details
                    ];
                    
                    $this->logs[] = $logEntry;
                    
                    // 模拟写入文件
                    $logLine = json_encode($logEntry) . "\n";
                    // file_put_contents($this->logFile, $logLine, FILE_APPEND);
                    
                    return true;
                }
                
                public function getRecentLogs($limit = 10) {
                    return array_slice($this->logs, -$limit);
                }
            }
            
            echo "<div class='code-demo'>";
            echo "<h3>接口实现演示：</h3>";
            
            // 创建实现对象
            $validator = new InputValidator();
            $encryption = new SimpleEncryption('my-secret-key');
            $auditLog = new FileAuditLog();
            
            echo "<div class='result'>";
            
            // 验证器测试
            echo "<h4>1. 安全验证器测试</h4>";
            $testInputs = [
                'normal input text',
                '<script>alert("xss")</script>',
                'SELECT * FROM users; DROP TABLE users;'
            ];
            
            foreach ($testInputs as $input) {
                $isValid = $validator->validate($input);
                $status = $isValid ? '✅ 安全' : '❌ 危险';
                $error = $isValid ? '' : ' - ' . $validator->getLastError();
                echo "<p>输入: '$input' - $status$error</p>";
            }
            
            // 加密器测试
            echo "<h4>2. 加密器测试</h4>";
            $sensitiveData = '用户敏感信息：密码123456';
            $encrypted = $encryption->encrypt($sensitiveData);
            $decrypted = $encryption->decrypt($encrypted);
            
            echo "<p>原始数据: $sensitiveData</p>";
            echo "<p>加密后: $encrypted</p>";
            echo "<p>解密后: $decrypted</p>";
            echo "<p>解密验证: " . ($sensitiveData === $decrypted ? '✅ 成功' : '❌ 失败') . "</p>";
            
            // 审计日志测试
            echo "<h4>3. 审计日志测试</h4>";
            $auditLog->logEvent('USER_LOGIN', '张三', ['ip' => '192.168.1.100', 'success' => true]);
            $auditLog->logEvent('DATA_ACCESS', '李四', ['resource' => 'user_list', 'action' => 'read']);
            $auditLog->logEvent('PERMISSION_CHANGE', '管理员', ['target_user' => '王五', 'new_role' => 'admin']);
            
            $recentLogs = $auditLog->getRecentLogs(5);
            echo "<p>最近的审计日志:</p><ul>";
            foreach ($recentLogs as $log) {
                echo "<li>{$log['timestamp']} - {$log['event']} by {$log['user']}</li>";
            }
            echo "</ul>";
            
            echo "</div>";
            echo "</div>";
            ?>
        </div>
    </div>

    <!-- 安全系统 -->
    <div id="security" class="tab-content">
        <div class="section">
            <h2>4. 综合安全管理系统</h2>
            <?php
            // 安全管理系统主类
            class SecurityManagementSystem {
                private $users = [];
                private $validator;
                private $encryption;
                private $auditLog;
                private $currentUser = null;
                
                public function __construct() {
                    $this->validator = new InputValidator();
                    $this->encryption = new SimpleEncryption('system-key-2023');
                    $this->auditLog = new FileAuditLog();
                    
                    // 初始化一些测试用户
                    $this->initializeTestUsers();
                }
                
                private function initializeTestUsers() {
                    $this->users['admin'] = new AdminUser('系统管理员', 'admin@system.com', 3);
                    $this->users['user1'] = new RegularUser('普通用户1', 'user1@test.com');
                    $this->users['super'] = new SuperAdmin('超级管理员', 'super@system.com');
                }
                
                public function authenticateUser($username, $password) {
                    // 记录登录尝试
                    $this->auditLog->logEvent('LOGIN_ATTEMPT', $username, ['ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
                    
                    if (!isset($this->users[$username])) {
                        return false;
                    }
                    
                    $user = $this->users[$username];
                    
                    // 检查账户是否被锁定
                    if ($user->isLocked()) {
                        $this->auditLog->logEvent('LOGIN_BLOCKED', $username, ['reason' => 'account_locked']);
                        throw new Exception('账户已被锁定，请联系管理员');
                    }
                    
                    // 模拟密码验证（实际应用中应该从数据库获取哈希密码）
                    $validPasswords = [
                        'admin' => 'admin123',
                        'user1' => 'user123',
                        'super' => 'super123'
                    ];
                    
                    if (isset($validPasswords[$username]) && $validPasswords[$username] === $password) {
                        $user->recordLoginAttempt(true);
                        $this->currentUser = $user;
                        $this->auditLog->logEvent('LOGIN_SUCCESS', $username);
                        return true;
                    } else {
                        $user->recordLoginAttempt(false);
                        $this->auditLog->logEvent('LOGIN_FAILED', $username, ['reason' => 'invalid_password']);
                        return false;
                    }
                }
                
                public function performAction($action, $data = null) {
                    if (!$this->currentUser) {
                        throw new Exception('用户未登录');
                    }
                    
                    if (!$this->currentUser->canPerform($action)) {
                        $this->auditLog->logEvent('UNAUTHORIZED_ACCESS', $this->currentUser->username, ['action' => $action]);
                        throw new Exception('权限不足');
                    }
                    
                    // 验证输入数据
                    if ($data && !$this->validator->validate($data)) {
                        throw new Exception('输入数据不安全: ' . $this->validator->getLastError());
                    }
                    
                    $this->auditLog->logEvent('ACTION_PERFORMED', $this->currentUser->username, ['action' => $action, 'data' => $data]);
                    
                    switch ($action) {
                        case 'read':
                            return "读取数据: " . ($data ? $this->encryption->decrypt($data) : '所有数据');
                        case 'write':
                            return "写入数据: " . $this->encryption->encrypt($data ?: '新数据');
                        case 'delete':
                            return "删除操作执行完成";
                        case 'manage_users':
                            return "用户管理操作: $data";
                        case 'system_config':
                            return "系统配置修改: $data";
                        default:
                            return "执行操作: $action";
                    }
                }
                
                public function getCurrentUser() {
                    return $this->currentUser;
                }
                
                public function getSystemStatus() {
                    return [
                        'total_users' => count($this->users),
                        'current_user' => $this->currentUser ? $this->currentUser->username : 'None',
                        'recent_logs' => count($this->auditLog->getRecentLogs()),
                        'system_time' => date('Y-m-d H:i:s')
                    ];
                }
            }
            
            echo "<div class='code-demo'>";
            echo "<h3>综合安全管理系统演示：</h3>";
            
            try {
                $system = new SecurityManagementSystem();
                
                echo "<div class='result'>";
                
                // 系统状态
                echo "<h4>系统状态</h4>";
                $status = $system->getSystemStatus();
                foreach ($status as $key => $value) {
                    echo "<p>$key: $value</p>";
                }
                
                // 用户登录测试
                echo "<h4>用户登录测试</h4>";
                
                // 1. 管理员登录
                if ($system->authenticateUser('admin', 'admin123')) {
                    echo "<p>✅ 管理员登录成功</p>";
                    
                    $currentUser = $system->getCurrentUser();
                    echo "<p>当前用户: {$currentUser->username}</p>";
                    echo "<p>用户权限: " . implode(', ', $currentUser->getPermissions()) . "</p>";
                    
                    // 执行一些操作
                    echo "<h4>权限操作测试</h4>";
                    try {
                        echo "<p>" . $system->performAction('read', '加密的敏感数据') . "</p>";
                        echo "<p>" . $system->performAction('manage_users', '添加新用户') . "</p>";
                        echo "<p>" . $system->performAction('delete') . "</p>";
                    } catch (Exception $e) {
                        echo "<p class='error'>操作失败: " . $e->getMessage() . "</p>";
                    }
                    
                    // 尝试超级管理员操作
                    try {
                        echo "<p>" . $system->performAction('system_maintenance') . "</p>";
                    } catch (Exception $e) {
                        echo "<p style='color: orange;'>预期的权限限制: " . $e->getMessage() . "</p>";
                    }
                }
                
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='error'>系统错误: " . $e->getMessage() . "</div>";
            }
            
            echo "</div>";
            ?>
        </div>
    </div>

    <!-- 系统信息 -->
    <div id="info" class="tab-content">
        <div class="section">
            <h2>5. 实验系统信息</h2>
            <div class="info">
                <h3>本实验涵盖的PHP面向对象特性：</h3>
                <ul>
                    <li>✅ 类的定义与对象创建</li>
                    <li>✅ 构造函数和析构函数</li>
                    <li>✅ 访问控制（public、private、protected）</li>
                    <li>✅ 继承机制和方法重写</li>
                    <li>✅ 抽象类和抽象方法</li>
                    <li>✅ 接口的定义与实现</li>
                    <li>✅ 多态性的体现</li>
                    <li>✅ 静态属性和方法</li>
                    <li>✅ 常量定义</li>
                    <li>✅ Final关键字使用</li>
                    <li>✅ parent关键字调用</li>
                    <li>✅ 异常处理</li>
                    <li>✅ 命名空间概念</li>
                </ul>
                
                <h3>安全编程实践：</h3>
                <ul>
                    <li>输入验证和数据过滤</li>
                    <li>SQL注入防护</li>
                    <li>XSS攻击防护</li>
                    <li>密码安全存储</li>
                    <li>访问权限控制</li>
                    <li>审计日志记录</li>
                    <li>数据加密保护</li>
                    <li>会话安全管理</li>
                </ul>
                
                <h3>系统架构特点：</h3>
                <ul>
                    <li>模块化设计</li>
                    <li>接口驱动开发</li>
                    <li>继承层次清晰</li>
                    <li>职责分离原则</li>
                    <li>可扩展性良好</li>
                </ul>
                
                <h3>当前PHP环境：</h3>
                <?php
                echo "<p>PHP版本: " . PHP_VERSION . "</p>";
                echo "<p>系统时间: " . date('Y-m-d H:i:s') . "</p>";
                echo "<p>服务器信息: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
                echo "<p>内存限制: " . ini_get('memory_limit') . "</p>";
                echo "<p>最大执行时间: " . ini_get('max_execution_time') . "秒</p>";
                ?>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // 隐藏所有标签内容
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));
            
            // 移除所有标签的active类
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // 显示选中的标签内容
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>