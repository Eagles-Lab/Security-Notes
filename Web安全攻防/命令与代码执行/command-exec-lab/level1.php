<?php
/**
 * Level 1: 基础命令执行
 * 难度: ★☆☆☆☆
 * 描述: 最基础的命令执行，无任何过滤
 * 目标: 理解命令执行的危害，执行系统命令
 */

$message = '';
$output = '';

if (isset($_GET['cmd'])) {
    $cmd = $_GET['cmd'];

    // ❌ 危险: 完全无过滤的命令执行
    // Windows下需要添加 2>&1 来捕获stderr输出
    $output = shell_exec($cmd . " 2>&1");
    if ($output === null) {
        $output = '';
    }
    $message = '<div class="success">✅ 命令执行成功</div>';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Level 1 - 基础命令执行</title>
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
        table th, table td { padding: 8px; text-align: left; border: 1px solid #ddd; font-size: 13px; }
        table th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Level 1 - 基础命令执行</h1>
            <div class="difficulty">难度: ★☆☆☆☆ | 类型: 命令执行 (RCE)</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>服务器使用 system() 函数执行用户输入的命令</li>
                    <li>没有任何过滤和验证</li>
                    <li>可以执行任意系统命令</li>
                    <li>这是最危险的漏洞类型之一</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 学习目标</h3>
                <ol>
                    <li>理解什么是命令执行漏洞</li>
                    <li>学习基本的 Linux/Windows 命令</li>
                    <li>了解命令执行的危害</li>
                    <li>体验攻击者可以做什么</li>
                </ol>
            </div>

            <div class="info-box">
                <h3>🎯 常用Windows命令</h3>
                <table>
                    <tr>
                        <th>命令</th>
                        <th>功能</th>
                        <th>示例</th>
                    </tr>
                    <tr>
                        <td>whoami</td>
                        <td>查看当前用户</td>
                        <td><code>whoami /all</code></td>
                    </tr>
                    <tr>
                        <td>dir</td>
                        <td>列出文件</td>
                        <td><code>dir C:\</code></td>
                    </tr>
                    <tr>
                        <td>cd</td>
                        <td>查看当前目录</td>
                        <td><code>cd</code></td>
                    </tr>
                    <tr>
                        <td>type</td>
                        <td>读取文件</td>
                        <td><code>type C:\Windows\system.ini</code></td>
                    </tr>
                    <tr>
                        <td>ipconfig</td>
                        <td>查看网络配置</td>
                        <td><code>ipconfig /all</code></td>
                    </tr>
                    <tr>
                        <td>systeminfo</td>
                        <td>查看系统信息</td>
                        <td><code>systeminfo</code></td>
                    </tr>
                    <tr>
                        <td>net user</td>
                        <td>查看用户信息</td>
                        <td><code>net user</code></td>
                    </tr>
                    <tr>
                        <td>tasklist</td>
                        <td>查看进程列表</td>
                        <td><code>tasklist</code></td>
                    </tr>
                </table>
            </div>

            <?php echo $message; ?>

            <div class="form-box">
                <h3>命令执行测试</h3>
                <form method="GET">
                    <label>输入要执行的命令:</label>
                    <input type="text" name="cmd" placeholder="whoami" value="<?php echo isset($_GET['cmd']) ? htmlspecialchars($_GET['cmd']) : ''; ?>" />
                    <input type="submit" value="执行命令" />
                </form>
            </div>

            <?php if (isset($_GET['cmd'])): ?>
            <div class="result-box">
                <h3 style="color: #00ff00; margin-bottom: 10px;">执行结果:</h3>
                <pre><?php
                // 直接输出，不经过htmlspecialchars避免中文编码问题
                if ($output !== null && $output !== '') {
                    echo htmlspecialchars($output, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                } else {
                    // 如果shell_exec没有输出，使用system直接输出
                    system($_GET['cmd'] . " 2>&1");
                }
                ?></pre>
            </div>
            <?php endif; ?>

            <div class="hint">
                <strong>🔍 漏洞代码:</strong>
                <div class="code-box">$cmd = $_GET['cmd'];
system($cmd);  // ❌ 危险！直接执行用户输入</div>

                <p><strong>尝试以下Windows命令:</strong></p>
                <ul>
                    <li><code>whoami</code> - 查看当前用户</li>
                    <li><code>cd</code> - 查看当前目录</li>
                    <li><code>dir</code> - 列出文件</li>
                    <li><code>type C:\Windows\system.ini</code> - 读取系统文件</li>
                    <li><code>type C:\Windows\win.ini</code> - 读取系统配置</li>
                    <li><code>ipconfig</code> - 查看网络配置</li>
                    <li><code>ipconfig /all</code> - 详细网络信息</li>
                    <li><code>systeminfo</code> - 系统信息</li>
                    <li><code>net user</code> - 用户列表</li>
                    <li><code>tasklist</code> - 查看进程</li>
                    <li><code>netstat -ano</code> - 网络连接</li>
                </ul>

                <p style="margin-top: 15px;"><strong>攻击危害:</strong></p>
                <ul>
                    <li>获取服务器完全控制权</li>
                    <li>读取、修改、删除任意文件</li>
                    <li>窃取数据库配置和敏感信息</li>
                    <li>植入后门程序</li>
                    <li>作为跳板攻击内网</li>
                </ul>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a> |
            <a href="level2.php" class="back-link">下一关 →</a>
        </div>
    </div>
</body>
</html>
