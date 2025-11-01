<?php
/**
 * Level 3: php://filter 伪协议
 * 难度: ★☆☆☆☆
 * 描述: 使用php://filter读取PHP源码
 * 目标: 读取config.php等配置文件的源码
 */

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
    <title>Level 3 - php://filter</title>
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
        table th, table td { padding: 8px; text-align: left; border: 1px solid #ddd; font-size: 13px; }
        table th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Level 3 - php://filter 伪协议</h1>
            <div class="difficulty">难度: ★☆☆☆☆ | 技巧: 读取源码</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>php://filter 是PHP的一个伪协议</li>
                    <li>可以读取文件内容,常用于读取PHP源码</li>
                    <li>直接include PHP文件会执行代码,看不到源码</li>
                    <li>使用filter协议配合base64编码,可以获取源码</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 通关目标</h3>
                <ol>
                    <li>读取 files/config.php 的源码,获取数据库密码</li>
                    <li>读取 level3.php 本身的源码</li>
                    <li>理解base64编码的作用</li>
                </ol>
            </div>

            <div class="info-box">
                <h3>📖 php://filter 常用语法</h3>
                <table>
                    <tr>
                        <th>用法</th>
                        <th>示例</th>
                        <th>说明</th>
                    </tr>
                    <tr>
                        <td>base64编码读取</td>
                        <td>php://filter/read=convert.base64-encode/resource=config.php</td>
                        <td>获取base64编码的文件内容</td>
                    </tr>
                    <tr>
                        <td>直接读取</td>
                        <td>php://filter/resource=test.txt</td>
                        <td>读取文本文件</td>
                    </tr>
                </table>
            </div>

            <?php echo $message; ?>

            <div class="form-box">
                <h3>文件包含测试</h3>
                <form method="GET">
                    <label>要包含的文件路径:</label>
                    <input type="text" name="file" placeholder="php://filter/read=convert.base64-encode/resource=files/config.php" value="<?php echo isset($_GET['file']) ? htmlspecialchars($_GET['file']) : ''; ?>" />
                    <input type="submit" value="包含文件" />
                </form>
            </div>

            <?php if ($content !== ''): ?>
            <div class="result-box">
                <h3>包含结果:</h3>
                <pre><?php echo htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></pre>

                <?php if (strpos($_GET['file'], 'base64') !== false): ?>
                <h3 style="margin-top: 20px;">解码后的内容:</h3>
                <pre><?php echo htmlspecialchars(base64_decode($content), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></pre>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="hint">
                <strong>🔍 为什么要使用base64编码？</strong>
                <p style="margin: 10px 0;">直接include PHP文件,代码会被执行而不是显示:</p>
                <div class="code-box">// 直接包含
?file=files/config.php
// ❌ PHP代码会被执行,看不到源码</div>

                <p style="margin: 10px 0;">使用filter协议+base64编码:</p>
                <div class="code-box">// 使用filter协议
?file=php://filter/read=convert.base64-encode/resource=files/config.php
// ✅ 获取base64编码的源码,解码后可以看到密码等敏感信息</div>

                <p><strong>攻击示例:</strong></p>
                <ul>
                    <li>读取配置文件: <code>?file=php://filter/read=convert.base64-encode/resource=files/config.php</code></li>
                    <li>读取本文件: <code>?file=php://filter/read=convert.base64-encode/resource=level3.php</code></li>
                </ul>

                <p style="margin-top: 10px;"><strong>Linux下解码:</strong></p>
                <div class="code-box">echo "PD9waHAg..." | base64 -d</div>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a>
        </div>
    </div>
</body>
</html>
