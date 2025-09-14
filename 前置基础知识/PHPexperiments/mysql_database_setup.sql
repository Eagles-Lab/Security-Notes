-- MySQL数据库完整建库脚本
-- 包含用户权限管理、安全设置、表结构创建等

-- ==============================================
-- 1. 数据库和用户创建
-- ==============================================

-- 创建数据库
CREATE DATABASE IF NOT EXISTS security_management 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- 使用数据库
USE security_management;

-- 创建专用数据库用户
CREATE USER IF NOT EXISTS 'security_user'@'localhost' 
IDENTIFIED BY 'SecurePass123!' 
REQUIRE NONE 
PASSWORD EXPIRE INTERVAL 90 DAY 
ACCOUNT UNLOCK;

-- 授予权限
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP 
ON security_management.* 
TO 'security_user'@'localhost';

-- 刷新权限
FLUSH PRIVILEGES;

-- ==============================================
-- 2. 用户管理相关表
-- ==============================================

-- 用户表
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '用户ID',
    username VARCHAR(50) UNIQUE NOT NULL COMMENT '用户名',
    email VARCHAR(100) UNIQUE NOT NULL COMMENT '邮箱地址',
    password_hash VARCHAR(255) NOT NULL COMMENT '密码哈希',
    salt VARCHAR(32) NOT NULL COMMENT '密码盐值',
    role ENUM('admin', 'user', 'guest') DEFAULT 'user' COMMENT '用户角色',
    status ENUM('active', 'inactive', 'banned', 'deleted') DEFAULT 'active' COMMENT '用户状态',
    failed_login_attempts INT DEFAULT 0 COMMENT '失败登录尝试次数',
    locked_until DATETIME NULL COMMENT '锁定到期时间',
    last_login_at DATETIME NULL COMMENT '最后登录时间',
    last_login_ip VARCHAR(45) NULL COMMENT '最后登录IP',
    profile_picture VARCHAR(255) NULL COMMENT '头像路径',
    phone VARCHAR(20) NULL COMMENT '手机号码',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    deleted_at DATETIME NULL COMMENT '删除时间',
    
    -- 索引
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_role (role),
    INDEX idx_created_at (created_at),
    INDEX idx_last_login_at (last_login_at)
) ENGINE=InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci 
COMMENT='用户基础信息表';

-- 用户角色表
CREATE TABLE IF NOT EXISTS user_roles (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '角色ID',
    role_name VARCHAR(50) UNIQUE NOT NULL COMMENT '角色名称',
    role_description TEXT NULL COMMENT '角色描述',
    permissions JSON NOT NULL COMMENT '权限列表',
    is_system_role BOOLEAN DEFAULT FALSE COMMENT '是否系统角色',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    
    INDEX idx_role_name (role_name)
) ENGINE=InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci 
COMMENT='用户角色定义表';

-- 用户权限关联表
CREATE TABLE IF NOT EXISTS user_role_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '关联ID',
    user_id INT NOT NULL COMMENT '用户ID',
    role_id INT NOT NULL COMMENT '角色ID',
    assigned_by INT NULL COMMENT '分配人ID',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '分配时间',
    expires_at DATETIME NULL COMMENT '权限过期时间',
    
    UNIQUE KEY uk_user_role (user_id, role_id),
    FOREIGN KEY fk_user_role_user (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY fk_user_role_role (role_id) REFERENCES user_roles(id) ON DELETE CASCADE,
    FOREIGN KEY fk_user_role_assigner (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_user_id (user_id),
    INDEX idx_role_id (role_id),
    INDEX idx_assigned_at (assigned_at)
) ENGINE=InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci 
COMMENT='用户角色分配表';

-- ==============================================
-- 3. 会话管理表
-- ==============================================

-- 用户会话表
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '会话ID',
    user_id INT NOT NULL COMMENT '用户ID',
    session_token VARCHAR(128) UNIQUE NOT NULL COMMENT '会话令牌',
    refresh_token VARCHAR(128) NULL COMMENT '刷新令牌',
    ip_address VARCHAR(45) NOT NULL COMMENT 'IP地址',
    user_agent TEXT NULL COMMENT '用户代理',
    device_info JSON NULL COMMENT '设备信息',
    location_info JSON NULL COMMENT '地理位置信息',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否活跃',
    last_activity_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后活动时间',
    expires_at DATETIME NOT NULL COMMENT '过期时间',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    
    FOREIGN KEY fk_session_user (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_session_token (session_token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at),
    INDEX idx_is_active (is_active),
    INDEX idx_last_activity (last_activity_at)
) ENGINE=InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci 
COMMENT='用户会话管理表';

-- ==============================================
-- 4. 安全日志表
-- ==============================================

-- 安全操作日志表
CREATE TABLE IF NOT EXISTS security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '日志ID',
    user_id INT NULL COMMENT '用户ID',
    username VARCHAR(50) NULL COMMENT '用户名快照',
    action VARCHAR(50) NOT NULL COMMENT '操作类型',
    resource VARCHAR(100) NULL COMMENT '操作资源',
    resource_id VARCHAR(50) NULL COMMENT '资源ID',
    ip_address VARCHAR(45) NOT NULL COMMENT 'IP地址',
    user_agent TEXT NULL COMMENT '用户代理',
    success BOOLEAN NOT NULL COMMENT '操作是否成功',
    failure_reason VARCHAR(255) NULL COMMENT '失败原因',
    additional_data JSON NULL COMMENT '附加数据',
    risk_level ENUM('low', 'medium', 'high', 'critical') DEFAULT 'low' COMMENT '风险等级',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    
    FOREIGN KEY fk_log_user (user_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    INDEX idx_success (success),
    INDEX idx_risk_level (risk_level),
    INDEX idx_ip_address (ip_address)
) ENGINE=InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci 
COMMENT='安全操作日志表';

-- ==============================================
-- 5. 银行转账系统表（演示事务处理）
-- ==============================================

-- 账户表
CREATE TABLE IF NOT EXISTS accounts (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '账户ID',
    account_number VARCHAR(20) UNIQUE NOT NULL COMMENT '账户号码',
    owner_name VARCHAR(100) NOT NULL COMMENT '账户所有者',
    owner_id_card VARCHAR(18) NULL COMMENT '身份证号',
    account_type ENUM('savings', 'checking', 'credit') DEFAULT 'savings' COMMENT '账户类型',
    balance DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT '账户余额',
    frozen_amount DECIMAL(15,2) DEFAULT 0.00 COMMENT '冻结金额',
    daily_limit DECIMAL(15,2) DEFAULT 50000.00 COMMENT '日转账限额',
    status ENUM('active', 'frozen', 'closed') DEFAULT 'active' COMMENT '账户状态',
    currency_code CHAR(3) DEFAULT 'CNY' COMMENT '货币代码',
    branch_code VARCHAR(20) NULL COMMENT '开户行代码',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    version INT NOT NULL DEFAULT 1 COMMENT '版本号（乐观锁）',
    
    INDEX idx_account_number (account_number),
    INDEX idx_owner_name (owner_name),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_balance (balance)
) ENGINE=InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci 
COMMENT='银行账户表';

-- 转账记录表
CREATE TABLE IF NOT EXISTS transfer_logs (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '转账ID',
    transaction_no VARCHAR(32) UNIQUE NOT NULL COMMENT '交易流水号',
    from_account VARCHAR(20) NOT NULL COMMENT '转出账户',
    to_account VARCHAR(20) NOT NULL COMMENT '转入账户',
    amount DECIMAL(15,2) NOT NULL COMMENT '转账金额',
    fee DECIMAL(10,2) DEFAULT 0.00 COMMENT '手续费',
    currency_code CHAR(3) DEFAULT 'CNY' COMMENT '货币代码',
    exchange_rate DECIMAL(10,4) DEFAULT 1.0000 COMMENT '汇率',
    description TEXT NULL COMMENT '转账说明',
    reference_no VARCHAR(50) NULL COMMENT '参考号码',
    transaction_type ENUM('transfer', 'deposit', 'withdrawal', 'fee') DEFAULT 'transfer' COMMENT '交易类型',
    status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled') DEFAULT 'pending' COMMENT '转账状态',
    initiated_by VARCHAR(50) NULL COMMENT '发起人',
    approved_by VARCHAR(50) NULL COMMENT '审批人',
    processed_at DATETIME NULL COMMENT '处理时间',
    completed_at DATETIME NULL COMMENT '完成时间',
    failure_reason TEXT NULL COMMENT '失败原因',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    
    FOREIGN KEY fk_transfer_from (from_account) REFERENCES accounts(account_number) ON DELETE RESTRICT,
    FOREIGN KEY fk_transfer_to (to_account) REFERENCES accounts(account_number) ON DELETE RESTRICT,
    
    INDEX idx_transaction_no (transaction_no),
    INDEX idx_from_account (from_account),
    INDEX idx_to_account (to_account),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_completed_at (completed_at),
    INDEX idx_amount (amount)
) ENGINE=InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci 
COMMENT='转账交易日志表';

-- ==============================================
-- 6. 系统配置表
-- ==============================================

-- 系统配置表
CREATE TABLE IF NOT EXISTS system_configurations (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '配置ID',
    config_key VARCHAR(100) UNIQUE NOT NULL COMMENT '配置键',
    config_value TEXT NOT NULL COMMENT '配置值',
    config_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string' COMMENT '配置类型',
    description TEXT NULL COMMENT '配置描述',
    is_public BOOLEAN DEFAULT FALSE COMMENT '是否公开配置',
    is_encrypted BOOLEAN DEFAULT FALSE COMMENT '是否加密存储',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    
    INDEX idx_config_key (config_key),
    INDEX idx_is_public (is_public)
) ENGINE=InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci 
COMMENT='系统配置表';

-- ==============================================
-- 7. 数据字典表
-- ==============================================

-- 数据字典表
CREATE TABLE IF NOT EXISTS data_dictionary (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '字典ID',
    dict_type VARCHAR(50) NOT NULL COMMENT '字典类型',
    dict_key VARCHAR(100) NOT NULL COMMENT '字典键',
    dict_value VARCHAR(255) NOT NULL COMMENT '字典值',
    dict_label VARCHAR(255) NOT NULL COMMENT '字典标签',
    sort_order INT DEFAULT 0 COMMENT '排序',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否激活',
    parent_id INT NULL COMMENT '父级ID',
    description TEXT NULL COMMENT '描述',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    
    UNIQUE KEY uk_dict_type_key (dict_type, dict_key),
    INDEX idx_dict_type (dict_type),
    INDEX idx_is_active (is_active),
    INDEX idx_parent_id (parent_id)
) ENGINE=InnoDB 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci 
COMMENT='数据字典表';

-- ==============================================
-- 8. 初始化数据
-- ==============================================

-- 插入系统角色
INSERT IGNORE INTO user_roles (role_name, role_description, permissions, is_system_role) VALUES
('admin', '系统管理员', '["*"]', TRUE),
('user_manager', '用户管理员', '["user.create", "user.read", "user.update", "user.delete"]', TRUE),
('auditor', '审计员', '["log.read", "report.read"]', TRUE),
('guest', '访客', '["public.read"]', TRUE);

-- 插入测试管理员用户（密码：Admin123!@#）
INSERT IGNORE INTO users (username, email, password_hash, salt, role, status) VALUES
('admin', 'admin@security.com', '$argon2id$v=19$m=65536,t=4,p=3$YWRtaW5zZWN1cml0eXNhbHQ$K7GfTxvVZGjXNQnG5J8K9L2MnOp3QrStUvWxYz1A2B4', 'adminsecuritysalt', 'admin', 'active');

-- 插入测试账户
INSERT IGNORE INTO accounts (account_number, owner_name, balance, status) VALUES
('ACC001', '张三', 1000.00, 'active'),
('ACC002', '李四', 500.00, 'active'),
('ACC003', '王五', 2000.00, 'active');

-- 插入系统配置
INSERT IGNORE INTO system_configurations (config_key, config_value, config_type, description, is_public) VALUES
('system.name', '安全管理系统', 'string', '系统名称', TRUE),
('system.version', '1.0.0', 'string', '系统版本', TRUE),
('security.password_policy.min_length', '8', 'number', '密码最小长度', FALSE),
('security.password_policy.require_special_chars', 'true', 'boolean', '是否要求特殊字符', FALSE),
('security.session_timeout', '3600', 'number', '会话超时时间（秒）', FALSE),
('security.max_login_attempts', '5', 'number', '最大登录尝试次数', FALSE),
('security.account_lockout_duration', '900', 'number', '账户锁定时长（秒）', FALSE);

-- 插入数据字典
INSERT IGNORE INTO data_dictionary (dict_type, dict_key, dict_value, dict_label, sort_order, is_active) VALUES
('user_status', 'active', 'active', '活跃', 1, TRUE),
('user_status', 'inactive', 'inactive', '非活跃', 2, TRUE),
('user_status', 'banned', 'banned', '已封禁', 3, TRUE),
('user_status', 'deleted', 'deleted', '已删除', 4, TRUE),
('user_role', 'admin', 'admin', '管理员', 1, TRUE),
('user_role', 'user', 'user', '普通用户', 2, TRUE),
('user_role', 'guest', 'guest', '访客', 3, TRUE),
('account_status', 'active', 'active', '正常', 1, TRUE),
('account_status', 'frozen', 'frozen', '冻结', 2, TRUE),
('account_status', 'closed', 'closed', '关闭', 3, TRUE);

-- ==============================================
-- 9. 视图创建
-- ==============================================

-- 用户详细信息视图
CREATE OR REPLACE VIEW v_user_details AS
SELECT 
    u.id,
    u.username,
    u.email,
    u.role,
    u.status,
    u.last_login_at,
    u.last_login_ip,
    u.failed_login_attempts,
    u.created_at,
    u.updated_at,
    GROUP_CONCAT(ur.role_name) as assigned_roles,
    COUNT(us.id) as active_sessions,
    MAX(us.last_activity_at) as last_activity
FROM users u
LEFT JOIN user_role_assignments ura ON u.id = ura.user_id
LEFT JOIN user_roles ur ON ura.role_id = ur.id
LEFT JOIN user_sessions us ON u.id = us.user_id AND us.is_active = TRUE AND us.expires_at > NOW()
WHERE u.status != 'deleted'
GROUP BY u.id, u.username, u.email, u.role, u.status, u.last_login_at, u.last_login_ip, u.failed_login_attempts, u.created_at, u.updated_at;

-- 账户余额汇总视图
CREATE OR REPLACE VIEW v_account_summary AS
SELECT 
    account_number,
    owner_name,
    account_type,
    balance,
    frozen_amount,
    (balance - frozen_amount) as available_balance,
    status,
    currency_code,
    DATE(created_at) as open_date,
    DATEDIFF(CURRENT_DATE, DATE(created_at)) as account_age_days
FROM accounts
WHERE status != 'closed';

-- 转账统计视图
CREATE OR REPLACE VIEW v_transfer_statistics AS
SELECT 
    DATE(created_at) as transfer_date,
    COUNT(*) as total_transfers,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_transfers,
    COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_transfers,
    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_amount,
    AVG(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as avg_amount,
    MAX(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as max_amount
FROM transfer_logs
GROUP BY DATE(created_at)
ORDER BY transfer_date DESC;

-- ==============================================
-- 10. 存储过程和函数
-- ==============================================

DELIMITER //

-- 创建账户余额更新存储过程
CREATE PROCEDURE IF NOT EXISTS sp_update_account_balance(
    IN p_account_number VARCHAR(20),
    IN p_amount DECIMAL(15,2),
    IN p_transaction_type ENUM('debit', 'credit'),
    OUT p_result INT,
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_current_balance DECIMAL(15,2) DEFAULT 0;
    DECLARE v_new_balance DECIMAL(15,2) DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result = -1;
        SET p_message = 'Transaction failed due to database error';
    END;
    
    START TRANSACTION;
    
    -- 获取当前余额（加锁）
    SELECT balance INTO v_current_balance 
    FROM accounts 
    WHERE account_number = p_account_number 
    AND status = 'active'
    FOR UPDATE;
    
    -- 检查账户是否存在
    IF v_current_balance IS NULL THEN
        SET p_result = -2;
        SET p_message = 'Account not found or inactive';
        ROLLBACK;
    ELSE
        -- 计算新余额
        IF p_transaction_type = 'debit' THEN
            SET v_new_balance = v_current_balance - p_amount;
        ELSE
            SET v_new_balance = v_current_balance + p_amount;
        END IF;
        
        -- 检查余额是否足够
        IF v_new_balance < 0 THEN
            SET p_result = -3;
            SET p_message = 'Insufficient balance';
            ROLLBACK;
        ELSE
            -- 更新余额
            UPDATE accounts 
            SET balance = v_new_balance,
                updated_at = NOW(),
                version = version + 1
            WHERE account_number = p_account_number;
            
            SET p_result = 0;
            SET p_message = 'Balance updated successfully';
            COMMIT;
        END IF;
    END IF;
END//

-- 创建获取用户权限函数
CREATE FUNCTION IF NOT EXISTS fn_get_user_permissions(p_user_id INT)
RETURNS JSON
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE v_permissions JSON DEFAULT JSON_ARRAY();
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_role_permissions JSON;
    
    DECLARE permission_cursor CURSOR FOR
        SELECT ur.permissions
        FROM user_role_assignments ura
        JOIN user_roles ur ON ura.role_id = ur.id
        WHERE ura.user_id = p_user_id
        AND (ura.expires_at IS NULL OR ura.expires_at > NOW());
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN permission_cursor;
    permission_loop: LOOP
        FETCH permission_cursor INTO v_role_permissions;
        IF done THEN
            LEAVE permission_loop;
        END IF;
        
        -- 合并权限（这里简化处理，实际应用中需要更复杂的权限合并逻辑）
        SET v_permissions = JSON_MERGE(v_permissions, v_role_permissions);
    END LOOP;
    
    CLOSE permission_cursor;
    RETURN v_permissions;
END//

DELIMITER ;

-- ==============================================
-- 11. 触发器
-- ==============================================

-- 用户更新时的审计日志触发器
DELIMITER //
CREATE TRIGGER IF NOT EXISTS tr_users_audit_update
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    INSERT INTO security_logs (
        user_id, 
        username, 
        action, 
        resource, 
        ip_address, 
        success, 
        additional_data
    ) VALUES (
        NEW.id,
        NEW.username,
        'user_updated',
        CONCAT('User ID: ', NEW.id),
        COALESCE(@current_ip, '127.0.0.1'),
        TRUE,
        JSON_OBJECT(
            'old_status', OLD.status,
            'new_status', NEW.status,
            'old_role', OLD.role,
            'new_role', NEW.role,
            'updated_fields', JSON_ARRAY(
                CASE WHEN OLD.username != NEW.username THEN 'username' END,
                CASE WHEN OLD.email != NEW.email THEN 'email' END,
                CASE WHEN OLD.status != NEW.status THEN 'status' END,
                CASE WHEN OLD.role != NEW.role THEN 'role' END
            )
        )
    );
END//

-- 账户余额变更触发器
CREATE TRIGGER IF NOT EXISTS tr_accounts_balance_audit
AFTER UPDATE ON accounts
FOR EACH ROW
BEGIN
    IF OLD.balance != NEW.balance THEN
        INSERT INTO security_logs (
            user_id,
            action,
            resource,
            ip_address,
            success,
            additional_data
        ) VALUES (
            NULL,
            'account_balance_changed',
            CONCAT('Account: ', NEW.account_number),
            COALESCE(@current_ip, '127.0.0.1'),
            TRUE,
            JSON_OBJECT(
                'account_number', NEW.account_number,
                'old_balance', OLD.balance,
                'new_balance', NEW.balance,
                'change_amount', (NEW.balance - OLD.balance),
                'old_version', OLD.version,
                'new_version', NEW.version
            )
        );
    END IF;
END//

DELIMITER ;

-- ==============================================
-- 12. 索引优化
-- ==============================================

-- 为大表创建复合索引以优化查询性能
CREATE INDEX IF NOT EXISTS idx_security_logs_composite ON security_logs (user_id, action, created_at);
CREATE INDEX IF NOT EXISTS idx_user_sessions_composite ON user_sessions (user_id, is_active, expires_at);
CREATE INDEX IF NOT EXISTS idx_transfer_logs_composite ON transfer_logs (from_account, status, created_at);

-- ==============================================
-- 13. 数据库维护
-- ==============================================

-- 设置事件调度器（如果未开启）
-- SET GLOBAL event_scheduler = ON;

-- 创建定期清理过期会话的事件
DELIMITER //
CREATE EVENT IF NOT EXISTS ev_cleanup_expired_sessions
ON SCHEDULE EVERY 1 HOUR
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    -- 清理过期的会话
    DELETE FROM user_sessions 
    WHERE expires_at < NOW() 
    OR (is_active = FALSE AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY));
    
    -- 记录清理操作
    INSERT INTO security_logs (action, resource, ip_address, success, additional_data) 
    VALUES (
        'system_cleanup', 
        'Expired sessions cleanup', 
        '127.0.0.1', 
        TRUE,
        JSON_OBJECT('cleanup_time', NOW(), 'cleaned_sessions', ROW_COUNT())
    );
END//

-- 创建定期归档旧日志的事件
CREATE EVENT IF NOT EXISTS ev_archive_old_logs
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP + INTERVAL 1 HOUR
DO
BEGIN
    DECLARE v_archive_date DATE DEFAULT DATE_SUB(CURRENT_DATE, INTERVAL 90 DAY);
    
    -- 这里可以将旧日志移动到归档表
    -- 为简化，这里只是删除90天前的普通操作日志（保留高风险日志）
    DELETE FROM security_logs 
    WHERE created_at < v_archive_date 
    AND risk_level = 'low' 
    AND action NOT IN ('login_failed', 'account_balance_changed', 'user_updated');
END//

DELIMITER ;

-- ==============================================
-- 14. 权限和安全设置
-- ==============================================

-- 确保安全配置
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
SET GLOBAL log_bin_trust_function_creators = 1;

-- 显示创建结果
SELECT '数据库初始化完成' as status;
SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = 'security_management';
SELECT COUNT(*) as user_count FROM users;
SELECT COUNT(*) as account_count FROM accounts;

-- 显示数据库版本和字符集信息
SELECT 
    VERSION() as mysql_version,
    @@character_set_database as db_charset,
    @@collation_database as db_collation,
    @@sql_mode as sql_mode,
    @@event_scheduler as event_scheduler_status;