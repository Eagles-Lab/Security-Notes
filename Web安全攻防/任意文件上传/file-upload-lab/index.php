<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文件上传漏洞靶场</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
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
            text-align: center;
        }
        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px;
        }
        .intro {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        .intro h2 {
            color: #333;
            margin-bottom: 15px;
        }
        .intro ul {
            margin-left: 20px;
            line-height: 1.8;
            color: #666;
        }
        .levels {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .level-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .level-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        .level-card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 20px;
        }
        .level-card .difficulty {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .difficulty.easy {
            background: #d4edda;
            color: #155724;
        }
        .difficulty.medium {
            background: #fff3cd;
            color: #856404;
        }
        .level-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .level-card .tag {
            display: inline-block;
            background: #e9ecef;
            color: #495057;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 12px;
            margin-right: 5px;
            margin-top: 5px;
        }
        .level-card a {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 15px;
            transition: background 0.3s;
        }
        .level-card a:hover {
            background: #5568d3;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 文件上传漏洞靶场</h1>
            <p>File Upload Vulnerability Lab - 5个基础关卡</p>
        </div>

        <div class="content">
            <div class="intro">
                <h2>📖 靶场说明</h2>
                <ul>
                    <li>本靶场包含5个基础关卡，适合新生学习文件上传漏洞</li>
                    <li>建议按顺序完成，每个关卡对应一种常见的绕过方法</li>
                    <li>目标：上传PHP Webshell并成功执行代码</li>
                    <li>提示：配合使用Burp Suite等工具进行抓包分析</li>
                    <li>警告：仅用于学习目的，请勿用于非法用途</li>
                </ul>
            </div>

            <h2 style="margin-bottom: 20px; color: #333;">🔐 关卡列表</h2>
            <div class="levels">
                <!-- Level 1 -->
                <div class="level-card">
                    <h3>Level 1 - 无验证</h3>
                    <span class="difficulty easy">简单</span>
                    <p>服务器没有任何文件上传验证，可以直接上传任意文件。最基础的入���关卡。</p>
                    <div>
                        <span class="tag">无过滤</span>
                        <span class="tag">基础</span>
                    </div>
                    <a href="level1.php">进入关卡 →</a>
                </div>

                <!-- Level 2 -->
                <div class="level-card">
                    <h3>Level 2 - 前端验证</h3>
                    <span class="difficulty easy">简单</span>
                    <p>仅使用JavaScript在客户端验证文件类型，学习前端验证绕过技巧。</p>
                    <div>
                        <span class="tag">JavaScript验证</span>
                        <span class="tag">客户端过滤</span>
                    </div>
                    <a href="level2.php">进入关卡 →</a>
                </div>

                <!-- Level 3 -->
                <div class="level-card">
                    <h3>Level 3 - MIME验证</h3>
                    <span class="difficulty easy">简单</span>
                    <p>检查Content-Type，但这个值可以被伪造。学习MIME类型绕过。</p>
                    <div>
                        <span class="tag">MIME类型</span>
                        <span class="tag">Content-Type</span>
                    </div>
                    <a href="level3.php">进入关卡 →</a>
                </div>

                <!-- Level 4 -->
                <div class="level-card">
                    <h3>Level 4 - 黑名单</h3>
                    <span class="difficulty medium">中等</span>
                    <p>使用黑名单禁止.php等扩展名，但黑名单不完整。学习后缀名绕过。</p>
                    <div>
                        <span class="tag">黑名单</span>
                        <span class="tag">扩展名</span>
                    </div>
                    <a href="level4.php">进入关卡 →</a>
                </div>

                <!-- Level 5 -->
                <div class="level-card">
                    <h3>Level 5 - 文件内容检测</h3>
                    <span class="difficulty medium">中等</span>
                    <p>检测文件头，需要制作图片马。学习文件内容伪装技巧。</p>
                    <div>
                        <span class="tag">文件头</span>
                        <span class="tag">图片马</span>
                    </div>
                    <a href="level5.php">进入关卡 →</a>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>⚠️ 仅供安全学习使用 | 请勿用于非法用途</p>
            <p style="margin-top: 10px;">
                上传目录: <a href="uploads/" target="_blank" style="color: #667eea;">uploads/</a> |
                示例文件: <a href="shells/" target="_blank" style="color: #667eea;">shells/</a>
            </p>
        </div>
    </div>
</body>
</html>
