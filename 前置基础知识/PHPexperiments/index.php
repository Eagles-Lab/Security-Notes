<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP安全编程实验代码</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background-color: #f5f5f5; 
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .experiment { 
            margin: 20px 0; 
            padding: 15px; 
            border-left: 4px solid #007cba; 
            background-color: #f9f9f9; 
        }
        .experiment h3 {
            margin-top: 0;
            color: #007cba;
        }
        .file-link {
            display: inline-block;
            padding: 8px 16px;
            background: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .file-link:hover {
            background: #005a8b;
        }
        .description {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PHP安全编程实验代码</h1>
        <p>这里包含了PHP安全工程师课程的所有实验代码文件。点击下面的链接查看和运行代码。</p>
        
        <div class="warning">
            <strong>⚠️ 重要提醒：</strong>
            <ul>
                <li>这些代码仅用于学习目的，不要在生产环境中直接使用</li>
                <li>涉及数据库的代码需要先配置数据库连接</li>
                <li>某些演示包含故意的安全示例，请注意区分</li>
            </ul>
        </div>

        <div class="experiment">
            <h3>1. PHP基础语法实验</h3>
            <a href="php_basics.php" class="file-link">php_basics.php</a>
            <div class="description">
                演示PHP基本语法、数据类型、运算符、控制语句和数组操作。包含安全编程最佳实践。
            </div>
        </div>

        <div class="experiment">
            <h3>2. PHP函数实验</h3>
            <a href="user_management_system.php" class="file-link">user_management_system.php</a>
            <a href="config.php" class="file-link">config.php</a>
            <div class="description">
                完整的用户管理系统，演示函数定义、参数传递、作用域等概念。包含用户注册、登录、权限管理功能。
            </div>
        </div>

        <div class="experiment">
            <h3>3. PHP类与对象实验</h3>
            <a href="01-basic-user-class.php" class="file-link">01-basic-user-class.php</a>
            <a href="security_management_system.php" class="file-link">security_management_system.php</a>
            <div class="description">
                从基础类定义到完整的面向对象安全管理系统。演示封装、继承、多态等OOP概念。
            </div>
        </div>

        <div class="experiment">
            <h3>4. PHP正则表达式实验</h3>
            <a href="enterprise_validation_platform.php" class="file-link">enterprise_validation_platform.php</a>
            <div class="description">
                企业级数据验证平台，演示正则表达式在数据验证、安全检查中的应用。
            </div>
        </div>

        <div class="experiment">
            <h3>5. PHP调用MySQL实验</h3>
            <a href="database_connection_system.php" class="file-link">database_connection_system.php</a>
            <a href="advanced_mysql_operations.php" class="file-link">advanced_mysql_operations.php</a>
            <a href="mysql_database_setup.sql" class="file-link">mysql_database_setup.sql</a>
            <div class="description">
                完整的数据库操作系统。包含连接管理、CRUD操作、事务处理、性能优化等高级特性。
            </div>
        </div>

        <div class="experiment">
            <h3>数据库配置文件</h3>
            <a href="database_setup.sql" class="file-link">database_setup.sql</a>
            <div class="description">
                基础数据库表结构脚本。运行PHP数据库相关实验前需要先执行此脚本。
            </div>
        </div>

        <div class="warning">
            <strong>📋 使用步骤：</strong>
            <ol>
                <li>确保PHPStudy已启动Apache和MySQL服务</li>
                <li>对于数据库相关实验，先在phpMyAdmin中执行SQL脚本</li>
                <li>修改代码中的数据库配置信息（用户名、密码等）</li>
                <li>点击对应链接访问实验页面</li>
                <li>观察代码执行结果和安全特性</li>
            </ol>
        </div>

        <hr style="margin: 30px 0;">
        <p style="text-align: center; color: #666; font-size: 14px;">
            PHP安全工程师课程实验平台 | 
            <a href="http://localhost" style="color: #007cba;">返回首页</a>
        </p>
    </div>
</body>
</html>