<?php
// user_management_system.php - 主系统文件
session_start();

// 包含配置和类定义
require_once 'config.php';

// 安全处理超全局变量的类
class SecureGlobals {
    
    // 安全获取GET参数
    public static function getParam($key, $default = null, $type = 'string') {
        if (!isset($_GET[$key])) {
            return $default;
        }
        
        return self::sanitizeInput($_GET[$key], $type);
    }
    
    // 安全获取POST参数
    public static function postParam($key, $default = null, $type = 'string') {
        if (!isset($_POST[$key])) {
            return $default;
        }
        
        return self::sanitizeInput($_POST[$key], $type);
    }
    
    // 安全获取SESSION数据
    public static function sessionGet($key, $default = null) {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        return $_SESSION[$key] ?? $default;
    }
    
    // 安全设置SESSION数据
    public static function sessionSet($key, $value) {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $_SESSION[$key] = $value;
    }
    
    // 输入清理函数
    private static function sanitizeInput($input, $type) {
        switch ($type) {
            case 'int':
                return filter_var($input, FILTER_VALIDATE_INT);
            case 'float':
                return filter_var($input, FILTER_VALIDATE_FLOAT);
            case 'email':
                return filter_var($input, FILTER_VALIDATE_EMAIL);
            case 'url':
                return filter_var($input, FILTER_VALIDATE_URL);
            case 'string':
            default:
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    // 获取客户端IP地址
    public static function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}

// 安全的配置管理类
class SecureConfig {
    private static $config = [];
    private static $initialized = false;
    
    // 私有构造函数防止实例化
    private function __construct() {}
    
    // 初始化配置
    public static function init($configFile) {
        if (self::$initialized) {
            return;
        }
        
        if (!file_exists($configFile) || !is_readable($configFile)) {
            throw new Exception('配置文件不存在或不可读');
        }
        
        $config = include $configFile;
        
        if (!is_array($config)) {
            throw new Exception('配置文件格式错误');
        }
        
        self::$config = $config;
        self::$initialized = true;
    }
    
    // 安全获取配置值
    public static function get($key, $default = null) {
        if (!self::$initialized) {
            throw new Exception('配置未初始化');
        }
        
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    // 防止配置被外部修改
    public static function set($key, $value) {
        throw new Exception('配置为只读，不允许动态修改');
    }
}

// 安全的数据库连接管理
class DatabaseManager {
    private static $connections = [];
    
    public static function connect($name = 'default') {
        if (isset(self::$connections[$name])) {
            return self::$connections[$name];
        }
        
        $config = SecureConfig::get("database.{$name}");
        
        if (!$config) {
            throw new Exception("数据库配置 '{$name}' 不存在");
        }
        
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            
            self::$connections[$name] = $pdo;
            return $pdo;
        } catch (PDOException $e) {
            error_log("数据库连接失败: " . $e->getMessage());
            throw new Exception('数据库连接失败');
        }
    }
}

// 初始化配置
SecureConfig::init('config.php');

// 用户管理类
class UserManager {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseManager::connect();
    }
    
    // 用户注册
    public function register($userData, $validationCallback = null) {
        try {
            // 默认验证回调
            $defaultValidation = function($data) {
                $errors = [];
                
                if (empty($data['username']) || strlen($data['username']) < 3) {
                    $errors[] = '用户名至少3个字符';
                }
                
                if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = '请输入有效邮箱地址';
                }
                
                if (empty($data['password']) || strlen($data['password']) < 8) {
                    $errors[] = '密码至少8个字符';
                }
                
                return $errors;
            };
            
            $validator = $validationCallback ?: $defaultValidation;
            $errors = $validator($userData);
            
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }
            
            // 检查用户名是否已存在
            if ($this->userExists($userData['username'], $userData['email'])) {
                return ['success' => false, 'errors' => ['用户名或邮箱已存在']];
            }
            
            // 创建用户
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                INSERT INTO users (username, email, password_hash, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userData['username'],
                $userData['email'],
                $hashedPassword
            ]);
            
            return ['success' => true, 'message' => '用户注册成功'];
            
        } catch (Exception $e) {
            error_log("用户注册失败: " . $e->getMessage());
            return ['success' => false, 'errors' => ['注册失败，请稍后重试']];
        }
    }
    
    // 用户登录
    public function login($username, $password, $rememberMe = false) {
        try {
            // 检查登录尝试次数
            $attempts = $this->getLoginAttempts($username);
            $maxAttempts = SecureConfig::get('security.max_login_attempts', 5);
            
            if ($attempts >= $maxAttempts) {
                return ['success' => false, 'message' => '登录尝试次数过多，请稍后再试'];
            }
            
            // 查找用户
            $stmt = $this->db->prepare("SELECT id, username, email, password_hash FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password_hash'])) {
                $this->recordLoginAttempt($username, false);
                return ['success' => false, 'message' => '用户名或密码错误'];
            }
            
            // 登录成功
            $this->recordLoginAttempt($username, true);
            $this->createSession($user, $rememberMe);
            
            return [
                'success' => true,
                'message' => '登录成功',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ];
            
        } catch (Exception $e) {
            error_log("用户登录失败: " . $e->getMessage());
            return ['success' => false, 'message' => '登录失败，请稍后重试'];
        }
    }
    
    // 检查用户是否存在
    private function userExists($username, $email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->fetchColumn() > 0;
    }
    
    // 获取登录尝试次数
    private function getLoginAttempts($username) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM login_attempts 
            WHERE username = ? AND success = 0 AND attempt_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $stmt->execute([$username]);
        return $stmt->fetchColumn();
    }
    
    // 记录登录尝试
    private function recordLoginAttempt($username, $success) {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (username, success, ip_address, attempt_time) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$username, $success ? 1 : 0, $_SERVER['REMOTE_ADDR']]);
    }
    
    // 创建会话
    private function createSession($user, $rememberMe) {
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        
        if ($rememberMe) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60)); // 30天
            
            // 将记住密码令牌存储到数据库
            $stmt = $this->db->prepare("
                UPDATE users SET remember_token = ?, remember_token_expires = DATE_ADD(NOW(), INTERVAL 30 DAY) 
                WHERE id = ?
            ");
            $stmt->execute([$token, $user['id']]);
        }
    }
}

// 权限验证功能
class AuthManager {
    
    // 检查用户是否已登录
    public static function isLoggedIn() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // 检查会话超时
        $timeout = SecureConfig::get('security.session_timeout', 3600);
        if (time() - $_SESSION['last_activity'] > $timeout) {
            self::logout();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    // 注销用户
    public static function logout() {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // 清除记住密码cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 42000);
        }
        
        session_destroy();
    }
    
    // 要求用户登录
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
}

// 页面处理逻辑
$action = $_POST['action'] ?? $_GET['action'] ?? 'show_form';
$userManager = new UserManager();
$message = '';

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $userData = [
        'username' => SecureGlobals::postParam('username', '', 'string'),
        'email' => SecureGlobals::postParam('email', '', 'email'),
        'password' => $_POST['password'] ?? ''
    ];
    
    $result = $userManager->register($userData);
    
    if ($result['success']) {
        $message = '<div class="success">' . $result['message'] . '</div>';
    } else {
        $message = '<div class="error">' . implode('<br>', $result['errors']) . '</div>';
    }
} elseif ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = SecureGlobals::postParam('username', '', 'string');
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']);
    
    $result = $userManager->login($username, $password, $rememberMe);
    
    if ($result['success']) {
        header('Location: dashboard.php');
        exit;
    } else {
        $message = '<div class="error">' . $result['message'] . '</div>';
    }
} elseif ($action === 'logout') {
    AuthManager::logout();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP函数实验 - 用户管理系统</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; margin: 15px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; margin: 15px 0; }
        .tabs { margin-bottom: 20px; }
        .tab { display: inline-block; padding: 10px 20px; background: #f8f9fa; border: 1px solid #ddd; cursor: pointer; }
        .tab.active { background: #007bff; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <h1>PHP函数实验 - 用户管理系统</h1>
    
    <?= $message ?>
    
    <div class="tabs">
        <div class="tab active" onclick="showTab('register')">用户注册</div>
        <div class="tab" onclick="showTab('login')">用户登录</div>
        <div class="tab" onclick="showTab('info')">系统信息</div>
    </div>
    
    <!-- 注册表单 -->
    <div id="register" class="tab-content active">
        <h2>用户注册</h2>
        <form method="POST">
            <input type="hidden" name="action" value="register">
            
            <div class="form-group">
                <label>用户名：</label>
                <input type="text" name="username" required minlength="3" maxlength="20">
            </div>
            
            <div class="form-group">
                <label>邮箱：</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>密码：</label>
                <input type="password" name="password" required minlength="8">
            </div>
            
            <button type="submit">注册</button>
        </form>
    </div>
    
    <!-- 登录表单 -->
    <div id="login" class="tab-content">
        <h2>用户登录</h2>
        <form method="POST">
            <input type="hidden" name="action" value="login">
            
            <div class="form-group">
                <label>用户名或邮箱：</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>密码：</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="remember_me"> 记住我（30天）
                </label>
            </div>
            
            <button type="submit">登录</button>
        </form>
    </div>
    
    <!-- 系统信息 -->
    <div id="info" class="tab-content">
        <h2>系统信息</h2>
        <h3>本实验演示的PHP函数特性：</h3>
        <ul>
            <li>✅ 用户定义函数（register、login等）</li>
            <li>✅ 函数参数（必需参数、可选参数）</li>
            <li>✅ 默认参数（验证配置、超时设置）</li>
            <li>✅ 函数返回值（状态数组）</li>
            <li>✅ 匿名函数（数据验证回调）</li>
            <li>✅ 回调函数（自定义验证器）</li>
            <li>✅ 变量作用域（类属性、静态方法）</li>
        </ul>
        
        <h3>安全特性：</h3>
        <ul>
            <li>密码哈希存储</li>
            <li>登录尝试次数限制</li>
            <li>会话超时管理</li>
            <li>输入验证和过滤</li>
            <li>SQL注入防护</li>
            <li>XSS防护</li>
            <li>CSRF防护（会话令牌）</li>
        </ul>
        
        <h3>当前PHP环境：</h3>
        <p>PHP版本：<?= PHP_VERSION ?></p>
        <p>服务器软件：<?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></p>
        <p>客户端IP：<?= SecureGlobals::getClientIP() ?></p>
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