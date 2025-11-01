<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>命令和代码执行漏洞实验室</title>
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
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header h1 { font-size: 32px; margin-bottom: 10px; }
        .header p { opacity: 0.9; font-size: 16px; }
        .content { padding: 40px; }
        .intro {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 5px solid #667eea;
        }
        .intro h2 { color: #333; margin-bottom: 15px; font-size: 24px; }
        .intro p { color: #666; line-height: 1.8; margin-bottom: 10px; }
        .levels {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .level-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .level-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            border-color: #667eea;
        }
        .level-card h3 {
            color: #667eea;
            font-size: 20px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .level-card .difficulty {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
            font-weight: normal;
        }
        .difficulty.easy { background: #d4edda; color: #155724; }
        .difficulty.medium { background: #fff3cd; color: #856404; }
        .difficulty.hard { background: #f8d7da; color: #721c24; }
        .level-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .level-card ul {
            list-style: none;
            padding: 0;
            margin-bottom: 15px;
        }
        .level-card ul li {
            color: #888;
            font-size: 14px;
            padding: 5px 0;
            padding-left: 20px;
            position: relative;
        }
        .level-card ul li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning strong { color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 命令和代码执行漏洞实验室</h1>
            <p>Command & Code Execution Vulnerability Lab</p>
        </div>

        <div class="content">
            <div class="intro">
                <h2>📚 实验说明</h2>
                <p><strong>欢迎来到命令和代码执行漏洞实验室！</strong></p>
                <p>本实验环境包含7个关卡，涵盖命令注入、代码执行、绕过技巧等内容。</p>
                <p><strong>学习目标：</strong></p>
                <ul style="margin-left: 20px; margin-top: 10px; line-height: 1.8;">
                    <li>理解命令执行和代码执行的区别</li>
                    <li>掌握各种命令注入符号的使用</li>
                    <li>学习绕过过滤的多种技巧</li>
                    <li>了解如何进行有效的防御</li>
                </ul>
            </div>

            <div class="warning">
                <strong>⚠️ 警告：</strong> 本实验环境仅用于教学目的，请勿在生产环境中使用这些代码！所有漏洞都是故意设计的。
            </div>

            <div class="levels">
                <div class="level-card" onclick="location.href='level1.php'">
                    <h3>Level 1 <span class="difficulty easy">简单</span></h3>
                    <p>基础命令执行 - 无任何过滤</p>
                    <ul>
                        <li>学习基本的system()函数</li>
                        <li>了解命令执行的危害</li>
                        <li>尝试执行各种系统命令</li>
                    </ul>
                    <a href="level1.php" class="btn">开始挑战</a>
                </div>

                <div class="level-card" onclick="location.href='level2.php'">
                    <h3>Level 2 <span class="difficulty easy">简单</span></h3>
                    <p>命令连接符注入</p>
                    <ul>
                        <li>使用分号、管道符等</li>
                        <li>理解 && 和 || 的区别</li>
                        <li>学习命令链接技巧</li>
                    </ul>
                    <a href="level2.php" class="btn">开始挑战</a>
                </div>

                <div class="level-card" onclick="location.href='level3.php'">
                    <h3>Level 3 <span class="difficulty medium">中等</span></h3>
                    <p>黑名单过滤绕过</p>
                    <ul>
                        <li>绕过危险字符过滤</li>
                        <li>使用注释符绕过</li>
                        <li>学习编码技巧</li>
                    </ul>
                    <a href="level3.php" class="btn">开始挑战</a>
                </div>

                <div class="level-card" onclick="location.href='level4.php'">
                    <h3>Level 4 <span class="difficulty medium">中等</span></h3>
                    <p>空格过滤绕过</p>
                    <ul>
                        <li>使用 $IFS 绕过</li>
                        <li>使用 %09 (Tab) 绕过</li>
                        <li>学习其他空格替代方案</li>
                    </ul>
                    <a href="level4.php" class="btn">开始挑战</a>
                </div>

                <div class="level-card" onclick="location.href='level5.php'">
                    <h3>Level 5 <span class="difficulty medium">中等</span></h3>
                    <p>关键字过滤绕过</p>
                    <ul>
                        <li>使用通配符绕过</li>
                        <li>使用命令替代</li>
                        <li>学习拼接技巧</li>
                    </ul>
                    <a href="level5.php" class="btn">开始挑战</a>
                </div>

                <div class="level-card" onclick="location.href='level6.php'">
                    <h3>Level 6 <span class="difficulty hard">困难</span></h3>
                    <p>综合过滤绕过</p>
                    <ul>
                        <li>多重过滤绕过</li>
                        <li>综合运用各种技巧</li>
                        <li>挑战你的绕过能力</li>
                    </ul>
                    <a href="level6.php" class="btn">开始挑战</a>
                </div>

                <div class="level-card" onclick="location.href='level7.php'">
                    <h3>Level 7 <span class="difficulty easy">简单</span></h3>
                    <p>代码执行函数演示</p>
                    <ul>
                        <li>体验 eval() 的危险</li>
                        <li>了解 assert() 的风险</li>
                        <li>学习代码执行防御</li>
                    </ul>
                    <a href="level7.php" class="btn">开始挑战</a>
                </div>
            </div>

            <div class="intro" style="margin-top: 40px;">
                <h2>🛡️ 防御建议</h2>
                <p>通过这些实验，你将学会：</p>
                <ul style="margin-left: 20px; margin-top: 10px; line-height: 1.8;">
                    <li>永远不要直接执行用户输入的命令</li>
                    <li>使用白名单而不是黑名单</li>
                    <li>使用 escapeshellarg() 和 escapeshellcmd() 保护参数</li>
                    <li>尽可能避免使用 system()、eval() 等危险函数</li>
                    <li>在 php.ini 中禁用危险函数</li>
                    <li>使用最小权限原则运行 Web 服务</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
