<?php
/**
 * 高级MySQL操作系统
 * 包含事务处理、并发控制、性能优化、银行转账系统等
 */

// 数据库配置
$config = [
    'host' => 'localhost',
    'username' => 'security_user',
    'password' => 'SecurePass123!',
    'database' => 'security_management',
    'charset' => 'utf8mb4'
];

// 建立PDO连接
try {
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    echo "高级MySQL操作系统连接成功\n\n";
} catch (PDOException $e) {
    die("连接失败: " . $e->getMessage());
}

// 事务管理器
class TransactionManager {
    private $pdo;
    private $transactionLevel = 0;
    private $rollbackOnly = false;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // 嵌套事务支持
    public function beginTransaction() {
        if ($this->transactionLevel === 0) {
            $this->pdo->beginTransaction();
            $this->rollbackOnly = false;
        } else {
            // 创建保存点
            $savepoint = "sp_" . $this->transactionLevel;
            $this->pdo->exec("SAVEPOINT $savepoint");
        }
        
        $this->transactionLevel++;
        return $this->transactionLevel;
    }
    
    public function commit() {
        if ($this->transactionLevel === 0) {
            throw new Exception("没有活动的事务");
        }
        
        $this->transactionLevel--;
        
        if ($this->transactionLevel === 0) {
            if ($this->rollbackOnly) {
                $this->pdo->rollback();
                throw new Exception("事务被标记为只能回滚");
            } else {
                $this->pdo->commit();
            }
        }
    }
    
    public function rollback() {
        if ($this->transactionLevel === 0) {
            throw new Exception("没有活动的事务");
        }
        
        if ($this->transactionLevel === 1) {
            $this->pdo->rollback();
            $this->transactionLevel = 0;
            $this->rollbackOnly = false;
        } else {
            // 回滚到保存点
            $savepoint = "sp_" . ($this->transactionLevel - 1);
            $this->pdo->exec("ROLLBACK TO SAVEPOINT $savepoint");
            $this->transactionLevel--;
        }
    }
    
    public function setRollbackOnly() {
        $this->rollbackOnly = true;
    }
    
    // 事务模板方法
    public function executeInTransaction(callable $callback) {
        $this->beginTransaction();
        
        try {
            $result = $callback();
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}

// 银行转账服务
class BankTransferService {
    private $pdo;
    private $transactionManager;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->transactionManager = new TransactionManager($pdo);
        $this->createAccountTable();
    }
    
    private function createAccountTable() {
        // 创建账户表
        $sql = "
        CREATE TABLE IF NOT EXISTS accounts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            account_number VARCHAR(20) UNIQUE NOT NULL,
            owner_name VARCHAR(100) NOT NULL,
            balance DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            status ENUM('active', 'frozen', 'closed') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            version INT NOT NULL DEFAULT 1,
            INDEX idx_account_number (account_number)
        ) ENGINE=InnoDB
        ";
        
        $this->pdo->exec($sql);
        
        // 创建转账日志表
        $transferLogSql = "
        CREATE TABLE IF NOT EXISTS transfer_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            from_account VARCHAR(20) NOT NULL,
            to_account VARCHAR(20) NOT NULL,
            amount DECIMAL(15,2) NOT NULL,
            description TEXT,
            status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_from_account (from_account),
            INDEX idx_to_account (to_account),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB
        ";
        
        $this->pdo->exec($transferLogSql);
        
        // 插入测试账户
        $accounts = [
            ['account_number' => 'ACC001', 'owner_name' => '张三', 'balance' => 1000.00],
            ['account_number' => 'ACC002', 'owner_name' => '李四', 'balance' => 500.00],
            ['account_number' => 'ACC003', 'owner_name' => '王五', 'balance' => 2000.00]
        ];
        
        foreach ($accounts as $account) {
            $sql = "INSERT IGNORE INTO accounts (account_number, owner_name, balance) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$account['account_number'], $account['owner_name'], $account['balance']]);
        }
    }
    
    // 安全转账操作
    public function transfer($fromAccount, $toAccount, $amount, $description = '') {
        return $this->transactionManager->executeInTransaction(function() use ($fromAccount, $toAccount, $amount, $description) {
            
            // 参数验证
            if ($amount <= 0) {
                throw new InvalidArgumentException("转账金额必须大于0");
            }
            
            if ($fromAccount === $toAccount) {
                throw new InvalidArgumentException("不能向同一账户转账");
            }
            
            // 获取账户信息（使用悲观锁）
            $fromAccountData = $this->getAccountForUpdate($fromAccount);
            $toAccountData = $this->getAccountForUpdate($toAccount);
            
            if (!$fromAccountData || !$toAccountData) {
                throw new Exception("账户不存在");
            }
            
            if ($fromAccountData['status'] !== 'active' || $toAccountData['status'] !== 'active') {
                throw new Exception("账户状态不允许转账");
            }
            
            // 检查余额
            if ($fromAccountData['balance'] < $amount) {
                throw new Exception("余额不足");
            }
            
            // 更新账户余额（使用乐观锁）
            $this->updateAccountBalance($fromAccount, -$amount, $fromAccountData['version']);
            $this->updateAccountBalance($toAccount, $amount, $toAccountData['version']);
            
            // 记录转账日志
            $transferId = $this->logTransfer($fromAccount, $toAccount, $amount, $description);
            
            return [
                'transfer_id' => $transferId,
                'from_account' => $fromAccount,
                'to_account' => $toAccount,
                'amount' => $amount,
                'status' => 'completed'
            ];
        });
    }
    
    private function getAccountForUpdate($accountNumber) {
        $sql = "SELECT id, account_number, owner_name, balance, status, version 
                FROM accounts 
                WHERE account_number = :account_number 
                FOR UPDATE";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':account_number' => $accountNumber]);
        return $stmt->fetch();
    }
    
    private function updateAccountBalance($accountNumber, $amount, $expectedVersion) {
        $sql = "UPDATE accounts 
                SET balance = balance + :amount, 
                    version = version + 1,
                    updated_at = NOW()
                WHERE account_number = :account_number 
                AND version = :expected_version";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':amount' => $amount,
            ':account_number' => $accountNumber,
            ':expected_version' => $expectedVersion
        ]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("账户更新失败，可能存在并发冲突");
        }
        
        return true;
    }
    
    private function logTransfer($fromAccount, $toAccount, $amount, $description) {
        $sql = "INSERT INTO transfer_logs (from_account, to_account, amount, description) 
                VALUES (:from_account, :to_account, :amount, :description)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':from_account' => $fromAccount,
            ':to_account' => $toAccount,
            ':amount' => $amount,
            ':description' => $description
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    // 获取账户余额
    public function getBalance($accountNumber) {
        $sql = "SELECT balance FROM accounts WHERE account_number = :account_number";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':account_number' => $accountNumber]);
        
        $result = $stmt->fetch();
        return $result ? $result['balance'] : null;
    }
    
    // 获取转账历史
    public function getTransferHistory($accountNumber, $limit = 50) {
        $sql = "SELECT * FROM transfer_logs 
                WHERE from_account = :account_number OR to_account = :account_number 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':account_number', $accountNumber);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}

// 高性能数据读取器
class HighPerformanceReader {
    private $pdo;
    private $queryCache = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        // 优化PDO性能
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
    }
    
    // 使用游标进行大数据集处理
    public function processBulkUsers($callback, $batchSize = 1000) {
        try {
            $sql = "SELECT id, username, email, status FROM users ORDER BY id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $count = 0;
            while ($user = $stmt->fetch()) {
                $callback($user);
                $count++;
                
                if ($count % $batchSize === 0) {
                    echo "已处理 $count 条记录\n";
                    // 释放内存
                    gc_collect_cycles();
                }
            }
            
            echo "总计处理 $count 条记录\n";
            return $count;
            
        } catch (PDOException $e) {
            echo "批量处理错误: " . $e->getMessage() . "\n";
            return 0;
        }
    }
    
    // 预编译查询缓存
    public function getCachedStatement($key, $sql) {
        if (!isset($this->queryCache[$key])) {
            $this->queryCache[$key] = $this->pdo->prepare($sql);
        }
        return $this->queryCache[$key];
    }
    
    // 快速用户验证
    public function quickUserValidation($username) {
        $stmt = $this->getCachedStatement(
            'user_validation',
            "SELECT id, status FROM users WHERE username = ? LIMIT 1"
        );
        
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    // 批量获取用户状态
    public function batchGetUserStatus($usernames) {
        if (empty($usernames)) return [];
        
        $placeholders = str_repeat('?,', count($usernames) - 1) . '?';
        $sql = "SELECT username, status FROM users WHERE username IN ($placeholders)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($usernames);
        
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}

// 连接池管理
class ConnectionPool {
    private $connections = [];
    private $config;
    private $maxConnections;
    private $activeConnections = 0;
    
    public function __construct($config, $maxConnections = 10) {
        $this->config = $config;
        $this->maxConnections = $maxConnections;
    }
    
    public function getConnection() {
        // 尝试从池中获取现有连接
        if (!empty($this->connections)) {
            $connection = array_pop($this->connections);
            if ($this->isConnectionAlive($connection)) {
                return $connection;
            }
        }
        
        // 创建新连接
        if ($this->activeConnections < $this->maxConnections) {
            $connection = $this->createConnection();
            $this->activeConnections++;
            return $connection;
        }
        
        throw new Exception("连接池已满，无法创建新连接");
    }
    
    public function releaseConnection($connection) {
        if ($connection && $this->isConnectionAlive($connection)) {
            $this->connections[] = $connection;
        } else {
            $this->activeConnections--;
        }
    }
    
    private function createConnection() {
        $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false
        ];
        
        return new PDO($dsn, $this->config['username'], $this->config['password'], $options);
    }
    
    private function isConnectionAlive($connection) {
        try {
            $connection->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function closeAll() {
        $this->connections = [];
        $this->activeConnections = 0;
    }
}

// 安全数据更新器
class SecureDataUpdater {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // 更新用户信息
    public function updateUser($userId, $updateData, $operatorId = null) {
        try {
            $this->pdo->beginTransaction();
            
            // 验证用户存在性
            if (!$this->userExists($userId)) {
                throw new Exception("用户不存在");
            }
            
            // 构建更新字段
            $updateFields = [];
            $parameters = [':user_id' => $userId];
            
            $allowedFields = ['username', 'email', 'role', 'status'];
            foreach ($allowedFields as $field) {
                if (isset($updateData[$field])) {
                    $updateFields[] = "$field = :$field";
                    $parameters[":$field"] = $updateData[$field];
                }
            }
            
            if (empty($updateFields)) {
                throw new Exception("没有可更新的字段");
            }
            
            // 添加更新时间
            $updateFields[] = "updated_at = NOW()";
            
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :user_id";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($parameters);
            
            if (!$result || $stmt->rowCount() === 0) {
                throw new Exception("更新失败或没有数据被修改");
            }
            
            // 记录操作日志
            $this->logOperation($operatorId, 'user_update', "用户ID: $userId", $updateData);
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    // 批量更新用户状态
    public function batchUpdateUserStatus($userIds, $newStatus, $operatorId = null) {
        if (empty($userIds) || !is_array($userIds)) {
            throw new InvalidArgumentException("用户ID数组不能为空");
        }
        
        try {
            $this->pdo->beginTransaction();
            
            // 验证状态值
            $validStatuses = ['active', 'inactive', 'banned'];
            if (!in_array($newStatus, $validStatuses)) {
                throw new InvalidArgumentException("无效的用户状态");
            }
            
            // 构建IN查询的占位符
            $userIds = array_map('intval', $userIds); // 确保是整数
            $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
            
            $sql = "UPDATE users SET status = ?, updated_at = NOW() 
                    WHERE id IN ($placeholders) AND status != ?";
            
            $stmt = $this->pdo->prepare($sql);
            $params = array_merge([$newStatus], $userIds, [$newStatus]);
            $result = $stmt->execute($params);
            
            $affectedRows = $stmt->rowCount();
            
            if ($affectedRows > 0) {
                $this->logOperation($operatorId, 'batch_user_status_update', 
                                  "批量更新用户状态为 $newStatus", [
                                      'user_ids' => $userIds,
                                      'new_status' => $newStatus,
                                      'affected_rows' => $affectedRows
                                  ]);
            }
            
            $this->pdo->commit();
            return $affectedRows;
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    // 辅助方法
    private function userExists($userId) {
        $sql = "SELECT 1 FROM users WHERE id = :user_id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchColumn() !== false;
    }
    
    private function logOperation($operatorId, $action, $description, $data = []) {
        // 确保security_logs表存在
        $createTableSql = "
        CREATE TABLE IF NOT EXISTS security_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            action VARCHAR(50) NOT NULL,
            resource VARCHAR(100),
            ip_address VARCHAR(45) NOT NULL,
            success BOOLEAN NOT NULL DEFAULT TRUE,
            additional_data JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB";
        
        $this->pdo->exec($createTableSql);
        
        $sql = "INSERT INTO security_logs (user_id, action, resource, ip_address, success, additional_data) 
                VALUES (:user_id, :action, :resource, :ip_address, :success, :additional_data)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $operatorId,
            ':action' => $action,
            ':resource' => $description,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            ':success' => 1,
            ':additional_data' => json_encode($data)
        ]);
    }
}

// 并发测试工具
class ConcurrencyTester {
    private $bankService;
    
    public function __construct(BankTransferService $bankService) {
        $this->bankService = $bankService;
    }
    
    // 模拟并发转账（测试锁机制）
    public function simulateConcurrentTransfers() {
        echo "=== 并发转账测试 ===\n";
        
        // 显示初始余额
        echo "初始余额:\n";
        echo "ACC001: " . $this->bankService->getBalance('ACC001') . "\n";
        echo "ACC002: " . $this->bankService->getBalance('ACC002') . "\n";
        echo "ACC003: " . $this->bankService->getBalance('ACC003') . "\n\n";
        
        // 模拟多个并发转账
        $transfers = [
            ['from' => 'ACC001', 'to' => 'ACC002', 'amount' => 100, 'desc' => '转账1'],
            ['from' => 'ACC001', 'to' => 'ACC003', 'amount' => 200, 'desc' => '转账2'],
            ['from' => 'ACC002', 'to' => 'ACC003', 'amount' => 150, 'desc' => '转账3']
        ];
        
        $results = [];
        foreach ($transfers as $index => $transfer) {
            try {
                $result = $this->bankService->transfer(
                    $transfer['from'], 
                    $transfer['to'], 
                    $transfer['amount'], 
                    $transfer['desc']
                );
                $results[] = ['index' => $index, 'status' => 'success', 'result' => $result];
                echo "转账 " . ($index + 1) . " 成功\n";
                
            } catch (Exception $e) {
                $results[] = ['index' => $index, 'status' => 'failed', 'error' => $e->getMessage()];
                echo "转账 " . ($index + 1) . " 失败: " . $e->getMessage() . "\n";
            }
        }
        
        // 显示最终余额
        echo "\n最终余额:\n";
        echo "ACC001: " . $this->bankService->getBalance('ACC001') . "\n";
        echo "ACC002: " . $this->bankService->getBalance('ACC002') . "\n";
        echo "ACC003: " . $this->bankService->getBalance('ACC003') . "\n";
        
        return $results;
    }
}

// 演示和测试
echo "=== 高级MySQL操作系统演示 ===\n\n";

try {
    // 1. 银行转账系统测试
    echo "1. 银行转账系统测试:\n";
    $bankService = new BankTransferService($pdo);
    
    // 正常转账测试
    $result = $bankService->transfer('ACC001', 'ACC002', 50.00, '测试转账');
    echo "转账成功，转账ID: {$result['transfer_id']}\n";
    
    // 异常情况测试
    try {
        $bankService->transfer('ACC002', 'ACC001', 10000.00, '余额不足测试');
    } catch (Exception $e) {
        echo "预期的异常: " . $e->getMessage() . "\n";
    }
    
    // 2. 高性能数据读取测试
    echo "\n2. 高性能数据读取测试:\n";
    $performanceReader = new HighPerformanceReader($pdo);
    
    // 批量处理用户数据
    $processCount = $performanceReader->processBulkUsers(function($user) {
        // 模拟数据处理
        if ($user['status'] === 'inactive') {
            // 处理非活跃用户
        }
    }, 2);
    
    // 3. 连接池测试
    echo "\n3. 连接池测试:\n";
    $pool = new ConnectionPool($config, 5);
    
    try {
        $conn1 = $pool->getConnection();
        $conn2 = $pool->getConnection();
        echo "成功从连接池获取2个连接\n";
        
        // 使用连接
        $stmt = $conn1->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "查询结果: {$result['count']} 个用户\n";
        
        // 释放连接
        $pool->releaseConnection($conn1);
        $pool->releaseConnection($conn2);
        echo "连接已释放回池中\n";
        
    } catch (Exception $e) {
        echo "连接池错误: " . $e->getMessage() . "\n";
    }
    
    // 4. 数据更新测试
    echo "\n4. 数据更新测试:\n";
    $updater = new SecureDataUpdater($pdo);
    
    try {
        // 更新用户信息
        $updateData = [
            'email' => 'new_admin@security.com',
            'status' => 'active'
        ];
        
        $result = $updater->updateUser(1, $updateData, 1);
        if ($result) {
            echo "用户信息更新成功\n";
        }
        
        // 批量更新用户状态
        $userIds = [2, 3]; // 假设这些用户存在
        $affectedRows = $updater->batchUpdateUserStatus($userIds, 'inactive', 1);
        echo "批量更新完成，影响 $affectedRows 行记录\n";
        
    } catch (Exception $e) {
        echo "更新操作: " . $e->getMessage() . "\n";
    }
    
    // 5. 并发转账测试
    echo "\n5. 并发转账测试:\n";
    $concurrencyTester = new ConcurrencyTester($bankService);
    $concurrencyResults = $concurrencyTester->simulateConcurrentTransfers();
    
    // 6. 查看转账历史
    echo "\n6. ACC001的转账历史:\n";
    $history = $bankService->getTransferHistory('ACC001', 5);
    foreach ($history as $transfer) {
        $direction = $transfer['from_account'] === 'ACC001' ? '转出到' : '从' . $transfer['from_account'] . '转入';
        $otherAccount = $transfer['from_account'] === 'ACC001' ? $transfer['to_account'] : $transfer['from_account'];
        echo "- {$transfer['created_at']}: $direction $otherAccount, 金额: {$transfer['amount']}\n";
    }
    
    echo "\n=== 高级MySQL操作演示完成 ===\n";
    
} catch (Exception $e) {
    echo "系统错误: " . $e->getMessage() . "\n";
} finally {
    // 关闭连接池
    if (isset($pool)) {
        $pool->closeAll();
    }
}
?>