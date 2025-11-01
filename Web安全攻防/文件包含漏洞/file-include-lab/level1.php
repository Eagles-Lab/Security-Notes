<?php
/**
 * Level 1: 基础本地文件包含
 * 难度: ★☆☆☆☆
 * 描述: 最基础的文件包含，无任何过滤
 * 目标: 读取敏感文件
 */

$message = '';
$content = '';

if (isset($_GET['file'])) {
    $file = $_GET['file'];

    // ❌ 危险:完全无过滤的文件包含
    if (file_exists($file)) {
        ob_start();
        include($file);
        $content = ob_get_clean();
        $message = '<div class="success">✅ 文件包含成功</div>';
    } else {
        $message = '<div class="error">❌ 文件不存在: ' . htmlspecialchars($file) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Level 1 - 基础文件包含</title>
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
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table th, table td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        table th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Level 1 - 基础本地文件包含</h1>
            <div class="difficulty">难度: ★☆☆☆☆ | 漏洞类型: LFI (Local File Inclusion)</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>服务器使用include()函数包含用户指定的文件</li>
                    <li>没有任何过滤和验证</li>
                    <li>可以使用目录遍历符（../）读取任意文件</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 通关目标</h3>
                <ol>
                    <li>读取本关卡的PHP源码（level1.php）</li>
                    <li>读取配置文件（files/config.php）</li>
                    <li>尝试读取系统敏感文件</li>
                </ol>
            </div>

            <div class="info-box">
                <h3>🎯 常见敏感文件路径</h3>
                <table>
                    <tr>
                        <th>系统</th>
                        <th>文件路径</th>
                        <th>说明</th>
                    </tr>
                    <tr>
                        <td>Linux</td>
                        <td>/etc/passwd</td>
                        <td>用户信息</td>
                    </tr>
                    <tr>
                        <td>Linux</td>
                        <td>/etc/hosts</td>
                        <td>主机配置</td>
                    </tr>
                    <tr>
                        <td>Windows</td>
                        <td>C:/Windows/system.ini</td>
                        <td>系统配置</td>
                    </tr>
                    <tr>
                        <td>Web应用</td>
                        <td>files/config.php</td>
                        <td>数据库配置</td>
                    </tr>
                </table>
            </div>

            <?php echo $message; ?>

            <div class="form-box">
                <h3>文件包含测试</h3>
                <form method="GET">
                    <label>要包含的文件路径:</label>
                    <input type="text" name="file" placeholder="例如: files/test.txt 或 level1.php" value="<?php echo isset($_GET['file']) ? htmlspecialchars($_GET['file']) : ''; ?>" />
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
                <strong>🔍 漏洞代码:</strong>
                <div class="code-box">$file = $_GET['file'];
if (file_exists($file)) {
    include($file);  // ❌ 危险！无任何过滤
}</div>
                <p><strong>攻击提示:</strong></p>
                <ul>
                    <li>直接包含: <code>?file=files/config.php</code></li>
                    <li>目录遍历: <code>?file=../../../etc/passwd</code></li>
                    <li>读取源码: <code>?file=level1.php</code></li>
                </ul>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a>
        </div>
    </div>
</body>
</html>
