<?php
/**
 * Level 7: 代码执行函数
 * 难度: ★★★★☆
 * 描述: 演示PHP代码执行函数的使用和危害
 * 目标: 理解代码执行与命令执行的区别
 */

$message = '';
$output = '';
$func_type = isset($_GET['type']) ? $_GET['type'] : '';
$code = isset($_GET['code']) ? $_GET['code'] : '';

if ($func_type && $code) {
    ob_start();

    try {
        switch($func_type) {
            case 'eval':
                // ❌ 最危险的函数
                eval($code);
                break;

            case 'assert':
                // PHP < 7.2 可执行字符串
                if (version_compare(PHP_VERSION, '7.2.0', '<')) {
                    assert($code);
                    $message = '<div class="success">✅ assert()执行成功（PHP < 7.2）</div>';
                } else {
                    $message = '<div class="error">❌ PHP >= 7.2 不支持assert()执行字符串</div>';
                }
                break;

            case 'call_user_func':
                // 需要函数名和参数，用逗号分隔
                $parts = explode(',', $code, 2);
                if (count($parts) == 2) {
                    call_user_func(trim($parts[0]), trim($parts[1]));
                } else if (count($parts) == 1) {
                    call_user_func(trim($parts[0]));
                }
                break;

            case 'array_map':
                // 需要函数名和参数，用逗号分隔
                $parts = explode(',', $code, 2);
                if (count($parts) == 2) {
                    array_map(trim($parts[0]), array(trim($parts[1])));
                }
                break;

            default:
                $message = '<div class="error">❌ 未知的函数类型</div>';
        }

        if (!$message) {
            $message = '<div class="success">✅ 代码执行成功</div>';
        }
    } catch (Exception $e) {
        $message = '<div class="error">❌ 执行错误: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }

    $output = ob_get_clean();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Level 7 - 代码执行函数</title>
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
        select, input[type="text"] {
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
            <h1>Level 7 - 代码执行函数</h1>
            <div class="difficulty">难度: ★★★★☆ | 类型: 代码执行 vs 命令执行</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>演示PHP代码执行函数的使用</li>
                    <li>理解代码执行与命令执行的区别</li>
                    <li>掌握不同函数的利用方式</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 代码执行函数对比</h3>
                <table>
                    <tr>
                        <th style="width: 150px;">函数</th>
                        <th style="width: 120px;">PHP版本</th>
                        <th style="width: 100px;">需要分号</th>
                        <th>示例</th>
                    </tr>
                    <tr>
                        <td>eval()</td>
                        <td>所有版本</td>
                        <td>是</td>
                        <td><code>phpinfo();</code></td>
                    </tr>
                    <tr>
                        <td>assert()</td>
                        <td>&lt; 7.2</td>
                        <td>否</td>
                        <td><code>phpinfo()</code></td>
                    </tr>
                    <tr>
                        <td>call_user_func()</td>
                        <td>所有版本</td>
                        <td>-</td>
                        <td><code>system,whoami</code></td>
                    </tr>
                    <tr>
                        <td>array_map()</td>
                        <td>所有版本</td>
                        <td>-</td>
                        <td><code>system,dir</code></td>
                    </tr>
                </table>
            </div>

            <div class="info-box">
                <h3>🎯 Windows环境Payload示例</h3>
                <table>
                    <tr>
                        <th style="width: 150px;">函数</th>
                        <th>Payload示例</th>
                    </tr>
                    <tr>
                        <td>eval()</td>
                        <td>
                            <code>system('whoami');</code><br>
                            <code>system('dir');</code><br>
                            <code>echo file_get_contents('C:\\Windows\\system.ini');</code>
                        </td>
                    </tr>
                    <tr>
                        <td>assert()</td>
                        <td>
                            <code>system('whoami')</code><br>
                            <code>system('ipconfig')</code>
                        </td>
                    </tr>
                    <tr>
                        <td>call_user_func()</td>
                        <td>
                            <code>system,whoami</code><br>
                            <code>system,hostname</code><br>
                            <code>passthru,net user</code>
                        </td>
                    </tr>
                    <tr>
                        <td>array_map()</td>
                        <td>
                            <code>system,dir</code><br>
                            <code>system,ipconfig</code>
                        </td>
                    </tr>
                </table>
            </div>

            <?php echo $message; ?>

            <div class="form-box">
                <h3>代码执行测试</h3>
                <form method="GET">
                    <label>选择执行函数:</label>
                    <select name="type">
                        <option value="">-- 选择函数 --</option>
                        <option value="eval" <?php echo $func_type == 'eval' ? 'selected' : ''; ?>>eval()</option>
                        <option value="assert" <?php echo $func_type == 'assert' ? 'selected' : ''; ?>>assert() (PHP < 7.2)</option>
                        <option value="call_user_func" <?php echo $func_type == 'call_user_func' ? 'selected' : ''; ?>>call_user_func()</option>
                        <option value="array_map" <?php echo $func_type == 'array_map' ? 'selected' : ''; ?>>array_map()</option>
                    </select>

                    <label>输入代码/参数:</label>
                    <input type="text" name="code" placeholder="根据函数类型输入相应代码" value="<?php echo htmlspecialchars($code); ?>" />
                    <input type="submit" value="执行代码" />
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
                <div class="code-box">switch($func_type) {
    case 'eval':
        eval($code);  // ❌ 极度危险！
        break;
    case 'call_user_func':
        call_user_func($func, $arg);  // ❌ 可调用任意函数
        break;
}</div>

                <p><strong>📝 使用说明:</strong></p>
                <ul>
                    <li><strong>eval():</strong> 需要完整PHP语句，以分号结尾
                        <ul>
                            <li><code>system('whoami');</code></li>
                            <li><code>system('dir');</code></li>
                            <li><code>echo "test";</code></li>
                        </ul>
                    </li>
                    <li><strong>assert():</strong> 不需要分号（仅PHP < 7.2有效）
                        <ul>
                            <li><code>system('whoami')</code></li>
                            <li><code>phpinfo()</code></li>
                        </ul>
                    </li>
                    <li><strong>call_user_func():</strong> 函数名和参数用逗号分隔
                        <ul>
                            <li><code>system,whoami</code></li>
                            <li><code>system,hostname</code></li>
                            <li><code>phpinfo</code> (无参数)</li>
                        </ul>
                    </li>
                    <li><strong>array_map():</strong> 函数名和参数用逗号分隔
                        <ul>
                            <li><code>system,dir</code></li>
                            <li><code>passthru,ipconfig</code></li>
                        </ul>
                    </li>
                </ul>

                <p style="margin-top: 15px;"><strong>代码执行 vs 命令执行:</strong></p>
                <ul>
                    <li><strong>代码执行:</strong> 执行PHP代码（eval, assert等）</li>
                    <li><strong>命令执行:</strong> 执行系统命令（system, exec等）</li>
                    <li>代码执行可以调用命令执行函数</li>
                    <li>代码执行的权限更高，危害更大</li>
                </ul>

                <p style="margin-top: 15px;"><strong>当前PHP版本信息:</strong></p>
                <div class="code-box">PHP版本: <?php echo PHP_VERSION; ?>
操作系统: <?php echo PHP_OS; ?>
<?php if (version_compare(PHP_VERSION, '7.2.0', '<')): ?>
assert()字符串执行: 支持 ✅
<?php else: ?>
assert()字符串执行: 不支持 ❌
<?php endif; ?></div>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a> |
            <a href="level6.php" class="back-link">← 上一关</a>
        </div>
    </div>
</body>
</html>
