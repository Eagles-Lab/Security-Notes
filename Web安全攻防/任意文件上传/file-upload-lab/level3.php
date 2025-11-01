<?php
/**
 * Level 3: MIME类型验证
 * 难度: ★☆☆☆☆
 * 描述: 检查$_FILES['type'],但可被伪造
 * 绕过: Burp修改Content-Type
 */

$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

$message = '';
$upload_file = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploadfile'])) {
    $allowed_mime = array('image/jpeg', 'image/png', 'image/gif');
    $file_type = $_FILES['uploadfile']['type'];  // ⚠️ 可被伪造!

    if (!in_array($file_type, $allowed_mime, true)) {
        $message = '<div class="error">❌ 只允许上传图片文件! 检测到类型: ' . htmlspecialchars($file_type) . '</div>';
    } else {
        $filename = $_FILES['uploadfile']['name'];
        $tmp_name = $_FILES['uploadfile']['tmp_name'];

        if (move_uploaded_file($tmp_name, $upload_dir . $filename)) {
            $message = '<div class="success">✅ 文件上传成功!</div>';
            $upload_file = $upload_dir . $filename;
        } else {
            $message = '<div class="error">❌ 文件上传失败!</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Level 3 - MIME验证</title>
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
        .info-box ul, .info-box ol { margin-left: 20px; line-height: 1.8; color: #666; }
        .upload-form {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
        }
        .file-input { margin: 20px 0; }
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
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Level 3 - MIME类型验证</h1>
            <div class="difficulty">难度: ★☆☆☆☆ | 绕过方法: 伪造Content-Type</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>检查$_FILES['uploadfile']['type']字段</li>
                    <li>只允许image/jpeg、image/png、image/gif</li>
                    <li>但这个值来自HTTP请求头,可以伪造</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 绕过方法</h3>
                <ol>
                    <li>准备一个PHP文件,例如shell.php</li>
                    <li>使用Burp Suite拦截上传请求</li>
                    <li>找到Content-Type字段</li>
                    <li>将Content-Type改为image/jpeg</li>
                    <li>保持filename为shell.php</li>
                    <li>Forward放行请求</li>
                </ol>
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
                <strong>🔍 漏洞代码:</strong>
                <div class="code-box">$allowed_mime = array('image/jpeg', 'image/png', 'image/gif');
$file_type = $_FILES['uploadfile']['type'];  // ⚠️ 可被伪造!

if (!in_array($file_type, $allowed_mime, true)) {
    die("只允许上传图片!");
}</div>
                <p><strong>HTTP请求示例:</strong></p>
                <div class="code-box">------WebKitFormBoundary
Content-Disposition: form-data; name="uploadfile"; filename="shell.php"
Content-Type: image/jpeg   ← 伪造为图片类型

&lt;?php phpinfo(); ?&gt;
------WebKitFormBoundary--</div>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a>
        </div>
    </div>
</body>
</html>
