<?php
/**
 * Level 1: 无任何验证
 * 难度: ★☆☆☆☆
 * 描述: 服务器没有任何文件上传验证,可以直接上传任意文件
 */

$upload_dir = 'uploads/';

// 创建上传目录
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$message = '';
$upload_file = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploadfile'])) {
    $filename = $_FILES['uploadfile']['name'];
    $tmp_name = $_FILES['uploadfile']['tmp_name'];

    // ❌ 危险:直接移动文件,没有任何验证
    if (move_uploaded_file($tmp_name, $upload_dir . $filename)) {
        $message = '<div class="success">✅ 文件上传成功!</div>';
        $upload_file = $upload_dir . $filename;
    } else {
        $message = '<div class="error">❌ 文件上传失败!</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Level 1 - 无验证</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
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
        .info-box ul { margin-left: 20px; line-height: 1.8; color: #666; }
        .upload-form {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
        }
        .file-input {
            margin: 20px 0;
        }
        input[type="file"] {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            background: white;
        }
        input[type="submit"] {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        input[type="submit"]:hover {
            background: #5568d3;
        }
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
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Level 1 - 无任何验证</h1>
            <div class="difficulty">难度: ★☆☆☆☆ | 绕过方法: 直接上传</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>服务器没有任何文件上传验证</li>
                    <li>可以直接上传任意类型的文件</li>
                    <li>目标:上传PHP Webshell并访问执行</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 通关提示</h3>
                <ul>
                    <li>创建一个PHP文件,例如: <code>shell.php</code></li>
                    <li>内容可以是: <code>&lt;?php phpinfo(); ?&gt;</code></li>
                    <li>直接上传该文件</li>
                    <li>访问 uploads/shell.php 查看结果</li>
                </ul>
            </div>

            <?php echo $message; ?>

            <?php if ($upload_file): ?>
            <div class="success">
                文件路径: <a href="<?php echo htmlspecialchars($upload_file); ?>" target="_blank" style="color: #155724; font-weight: bold;">
                    <?php echo htmlspecialchars($upload_file); ?>
                </a>
            </div>
            <?php endif; ?>

            <div class="upload-form">
                <h3>上传文件</h3>
                <form method="post" enctype="multipart/form-data">
                    <div class="file-input">
                        <input type="file" name="uploadfile" required />
                    </div>
                    <input type="submit" value="上传文件" />
                </form>
            </div>

            <div class="hint">
                <strong>🔍 漏洞代码分析:</strong>
                <div class="code-box">// 直接移动文件,没有任何验证
if (move_uploaded_file($tmp_name, $upload_dir . $filename)) {
    echo "文件上传成功!";
}</div>
                <p>存在的问题:</p>
                <ul>
                    <li>❌ 无文件类型验证</li>
                    <li>❌ 无文件大小限制</li>
                    <li>❌ 直接使用用户提供的文件名</li>
                    <li>❌ 上传目录权限过大</li>
                </ul>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a>
        </div>
    </div>
</body>
</html>
