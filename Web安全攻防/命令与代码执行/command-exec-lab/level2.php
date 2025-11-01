<?php
/**
 * Level 2: 命令连接符注入
 * 难度: ★☆☆☆☆
 * 描述: 在ping命令中注入其他命令
 * 目标: 使用各种命令连接符执行额外的命令
 */

$message = '';
$output = '';

if (isset($_GET['ip'])) {
    $ip = $_GET['ip'];

    // ❌ 危险: 未过滤的IP参数，可以注入命令
    $cmd = "ping -c 4 " . $ip;

    ob_start();
    system($cmd . " 2>&1");
    $output = ob_get_clean();
    $message = '<div class="success">✅ Ping命令执行完成</div>';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Level 2 - 命令连接符注入</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-left: 4px solid #667eea;
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
            font-family: 'Courier New', monospace;
        }
        input[type="submit"] {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover { background: #5568d3; }
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
            background: #2d2d2d;
            color: #00ff00;
            border: 1px solid #444;
            padding: 15px;
            border-radius: 5px;
            max-height: 400px;
            overflow: auto;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .result-box pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table th, table td { padding: 10px; text-align: left; border: 1px solid #ddd; font-size: 13px; }
        table th { background: #f8f9fa; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Level 2 - 命令连接符注入</h1>
            <div class="difficulty">难度: ★☆☆☆☆ | 技巧: 管道符、逻辑运算符</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>服务器提供了一个Ping工具</li>
                    <li>用户输入的IP地址直接拼接到ping命令中</li>
                    <li>可以使用命令连接符注入额外的命令</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 命令连接符详解</h3>
                <table>
                    <tr>
                        <th style="width: 80px;">符号</th>
                        <th style="width: 150px;">名称</th>
                        <th>功能</th>
                        <th style="width: 250px;">示例</th>
                    </tr>
                    <tr>
                        <td><code>;</code></td>
                        <td>分号</td>
                        <td>顺序执行，前一个失败不影响后续</td>
                        <td><code>127.0.0.1;whoami</code></td>
                    </tr>
                    <tr>
                        <td><code>|</code></td>
                        <td>管道符</td>
                        <td>将前一个命令的输出作为后一个命令的输入</td>
                        <td><code>127.0.0.1|whoami</code></td>
                    </tr>
                    <tr>
                        <td><code>||</code></td>
                        <td>逻辑或</td>
                        <td>前一个命令失败才执行后一个</td>
                        <td><code>invalid||whoami</code></td>
                    </tr>
                    <tr>
                        <td><code>&&</code></td>
                        <td>逻辑与</td>
                        <td>前一个命令成功才执行后一个</td>
                        <td><code>127.0.0.1&&whoami</code></td>
                    </tr>
                    <tr>
                        <td><code>%0a</code></td>
                        <td>换行符</td>
                        <td>Unix系统中分隔命令</td>
                        <td><code>127.0.0.1%0awhoami</code></td>
                    </tr>
                </table>
            </div>

            <div class="info-box">
                <h3>🎯 通关目标</h3>
                <ol>
                    <li>尝试使用不同的连接符执行 <code>whoami</code> 命令</li>
                    <li>理解各种连接符的区别</li>
                    <li>执行 <code>pwd</code> 查看当前目录</li>
                    <li>执行 <code>ls</code> 或 <code>dir</code> 查看文件列表</li>
                </ol>
            </div>

            <?php echo $message; ?>

            <div class="form-box">
                <h3>Ping工具</h3>
                <form method="GET">
                    <label>输入要Ping的IP地址:</label>
                    <input type="text" name="ip" placeholder="127.0.0.1" value="<?php echo isset($_GET['ip']) ? htmlspecialchars($_GET['ip']) : ''; ?>" />
                    <input type="submit" value="执行Ping" />
                </form>
            </div>

            <?php if ($output !== ''): ?>
            <div class="result-box">
                <h3 style="color: #00ff00; margin-bottom: 10px;">执行结果:</h3>
                <pre><?php echo htmlspecialchars($output, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></pre>
            </div>
            <?php endif; ?>

            <div class="hint">
                <strong>🔍 漏洞代码:</strong>
                <div class="code-box">$ip = $_GET['ip'];
$cmd = "ping -c 4 " . $ip;
system($cmd);  // ❌ 未验证IP，可注入命令</div>

                <p><strong>攻击示例:</strong></p>
                <div class="code-box"># 使用分号
?ip=127.0.0.1;whoami

# 使用管道符
?ip=127.0.0.1|whoami

# 使用逻辑与
?ip=127.0.0.1&&whoami

# 使用逻辑或（前面故意写错）
?ip=invalid_ip||whoami

# 使用换行符
?ip=127.0.0.1%0awhoami</div>

                <p><strong>更多尝试:</strong></p>
                <ul>
                    <li><code>127.0.0.1;pwd</code> - 查看当前目录</li>
                    <li><code>127.0.0.1;ls -la</code> - 查看详细文件列表</li>
                    <li><code>127.0.0.1;cat /etc/passwd</code> - 读取系统文件</li>
                    <li><code>127.0.0.1&&id</code> - 查看用户ID信息</li>
                    <li><code>xxx|ls</code> - 利用管道符（前面命令会失败）</li>
                </ul>

                <p style="margin-top: 15px;"><strong>连接符的区别:</strong></p>
                <ul>
                    <li><strong>分号 (;):</strong> 无论前面成功与否都执行后面的命令</li>
                    <li><strong>管道 (|):</strong> 将前面的输出传给后面（前面失败也会执行后面）</li>
                    <li><strong>逻辑与 (&&):</strong> 前面成功才执行后面（更安全的链接）</li>
                    <li><strong>逻辑或 (||):</strong> 前面失败才执行后面（可以用无效输入触发）</li>
                </ul>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a> |
            <a href="level1.php" class="back-link">← 上一关</a> |
            <a href="level3.php" class="back-link">下一关 →</a>
        </div>
    </div>
</body>
</html>
