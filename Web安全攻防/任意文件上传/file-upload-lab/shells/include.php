<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>文件包含演示</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .info {
            background: #e7f3ff;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #2196F3;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #45a049;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            overflow-x: auto;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>文件包含漏洞演示</h1>

        <div class="warning">
            <strong>⚠️ 警告：</strong> 这是一个存在文件包含漏洞的页面，仅用于演示图片马的利用。请勿在生产环境使用！
        </div>

        <div class="info">
            <strong>使用说明：</strong><br>
            1. 先在Level 5上传图片马（例如shell.gif）<br>
            2. 在下方输入：../uploads/shell.gif<br>
            3. 点击包含文件，查看效果<br>
            4. 图片中的PHP代码会被执行
        </div>

        <form method="GET">
            <label><strong>要包含的文件路径：</strong></label>
            <input type="text" name="file" placeholder="例如: ../uploads/shell.gif" value="<?php echo isset($_GET['file']) ? htmlspecialchars($_GET['file']) : ''; ?>" />
            <input type="submit" value="包含文件" />
        </form>

        <?php
        if (isset($_GET['file'])) {
            $file = $_GET['file'];
            echo "<h3>包含文件: " . htmlspecialchars($file) . "</h3>";

            // ⚠️ 危险的文件包含，存在漏洞
            if (file_exists($file)) {
                echo "<pre>";
                include($file);
                echo "</pre>";
            } else {
                echo "<p style='color: red;'>文件不存在！</p>";
            }
        }
        ?>

        <div class="info" style="margin-top: 30px;">
            <strong>漏洞代码：</strong>
            <pre style="background: #fff; color: #333; border: 1px solid #ddd;">$file = $_GET['file'];
if (file_exists($file)) {
    include($file);  // ⚠️ 危险！直接包含用户指定的文件
}</pre>
        </div>
    </div>
</body>
</html>
