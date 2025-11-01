<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文件包含漏洞靶场</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            border-left: 4px solid #f5576c;
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
            border-color: #f5576c;
        }
        .level-card h3 {
            color: #f5576c;
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
            background: #f5576c;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 15px;
            transition: background 0.3s;
        }
        .level-card a:hover {
            background: #e04555;
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
            <h1>🎯 文件包含漏洞靶场</h1>
            <p>File Inclusion Vulnerability Lab - 5个基础关卡</p>
        </div>

        <div class="content">
            <div class="intro">
                <h2>📖 靶场说明</h2>
                <ul>
                    <li>本靶场包含5个基础关卡，适合新生学习文件包含漏洞</li>
                    <li>涵盖本地文件包含、日志包含、伪协议等常见技巧</li>
                    <li>目标：读取敏感文件或执行任意代码</li>
                    <li>提示：配合使用Burp Suite等工具</li>
                    <li>警告：仅用于学习目的，请勿用于非法用途</li>
                </ul>
            </div>

            <h2 style="margin-bottom: 20px; color: #333;">🔐 关卡列表</h2>
            <div class="levels">
                <!-- Level 1 -->
                <div class="level-card">
                    <h3>Level 1 - 基础文件包含</h3>
                    <span class="difficulty easy">简单</span>
                    <p>最基础的本地文件包含，无任何过滤。学习目录遍历和读取敏感文件。</p>
                    <div>
                        <span class="tag">LFI</span>
                        <span class="tag">目录遍历</span>
                    </div>
                    <a href="level1.php">进入关卡 →</a>
                </div>

                <!-- Level 2 -->
                <div class="level-card">
                    <h3>Level 2 - 日志包含</h3>
                    <span class="difficulty medium">中等</span>
                    <p>通��包含Web服务器日志文件来getshell。学习日志包含技巧。</p>
                    <div>
                        <span class="tag">日志包含</span>
                        <span class="tag">Getshell</span>
                    </div>
                    <a href="level2.php">进入关卡 →</a>
                </div>

                <!-- Level 3 -->
                <div class="level-card">
                    <h3>Level 3 - php://filter</h3>
                    <span class="difficulty easy">简单</span>
                    <p>使用php://filter伪协议读取PHP源码。学习读取配置文件。</p>
                    <div>
                        <span class="tag">伪协议</span>
                        <span class="tag">源码读取</span>
                    </div>
                    <a href="level3.php">进入关卡 →</a>
                </div>

                <!-- Level 4 -->
                <div class="level-card">
                    <h3>Level 4 - php://input</h3>
                    <span class="difficulty medium">中等</span>
                    <p>使用php://input伪协议执行POST数据中的代码。学习代码执行。</p>
                    <div>
                        <span class="tag">php://input</span>
                        <span class="tag">代码执行</span>
                    </div>
                    <a href="level4.php">进入关卡 →</a>
                </div>

                <!-- Level 5 -->
                <div class="level-card">
                    <h3>Level 5 - data://协议</h3>
                    <span class="difficulty medium">中等</span>
                    <p>使用data://协议直接传递数据执行。学习URL编码和base64编码。</p>
                    <div>
                        <span class="tag">data://</span>
                        <span class="tag">base64</span>
                    </div>
                    <a href="level5.php">进入关卡 →</a>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>⚠️ 仅供安全学习使用 | 请勿用于非法用途</p>
            <p style="margin-top: 10px;">
                测试文件: <a href="files/" target="_blank" style="color: #f5576c;">files/</a> |
                日志目录: <a href="logs/" target="_blank" style="color: #f5576c;">logs/</a>
            </p>
        </div>
    </div>
</body>
</html>
