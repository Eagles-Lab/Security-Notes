<!DOCTYPE html>
<html>
<head>
    <title>PHP基本语法练习</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; }
        .code { background: #f4f4f4; padding: 10px; margin: 10px 0; }
        .result { background: #e8f5e8; padding: 10px; margin: 10px 0; }
        .warning { background: #fff3cd; padding: 10px; margin: 10px 0; color: #856404; }
    </style>
</head>
<body>
    <h1>PHP基本语法综合练习</h1>
    
    <div class="section">
        <h2>1. 基本语法格式与数据类型</h2>
        <?php
            // 不同的数据类型
            $string_var = "Hello PHP!";
            $integer_var = 42;
            $float_var = 3.14159;
            $boolean_var = true;
            $null_var = null;
            
            echo "<div class='result'>";
            echo "<p>字符串变量: $string_var (" . gettype($string_var) . ")</p>";
            echo "<p>整数变量: $integer_var (" . gettype($integer_var) . ")</p>";
            echo "<p>浮点数变量: $float_var (" . gettype($float_var) . ")</p>";
            echo "<p>布尔变量: " . ($boolean_var ? 'true' : 'false') . " (" . gettype($boolean_var) . ")</p>";
            echo "<p>NULL变量: " . (is_null($null_var) ? 'NULL' : $null_var) . " (" . gettype($null_var) . ")</p>";
            echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>2. 运算符练习</h2>
        <?php
            $a = 10;
            $b = 3;
            
            echo "<div class='result'>";
            echo "<h3>算术运算符：</h3>";
            echo "<p>$a + $b = " . ($a + $b) . "</p>";
            echo "<p>$a - $b = " . ($a - $b) . "</p>";
            echo "<p>$a * $b = " . ($a * $b) . "</p>";
            echo "<p>$a / $b = " . ($a / $b) . "</p>";
            echo "<p>$a % $b = " . ($a % $b) . "</p>";
            
            echo "<h3>比较运算符（安全示例）：</h3>";
            echo "<p>弱类型比较 $a == '10': " . ($a == '10' ? 'true' : 'false') . "</p>";
            echo "<p>强类型比较 $a === '10': " . ($a === '10' ? 'true' : 'false') . "</p>";
            echo "</div>";
            
            echo "<div class='warning'>";
            echo "<strong>安全提示：</strong>始终使用强类型比较（===）避免类型混淆攻击！";
            echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>3. 控制语句练习</h2>
        <?php
            $userScore = 85;
            
            echo "<div class='result'>";
            echo "<h3>条件语句：</h3>";
            echo "<p>学生分数：$userScore</p>";
            
            // if-elseif-else
            if ($userScore >= 90) {
                $grade = "优秀";
            } elseif ($userScore >= 80) {
                $grade = "良好";
            } elseif ($userScore >= 70) {
                $grade = "中等";
            } elseif ($userScore >= 60) {
                $grade = "及格";
            } else {
                $grade = "不及格";
            }
            echo "<p>等级：$grade</p>";
            
            // switch语句
            $dayOfWeek = date('w');
            $dayName = "";
            
            switch ($dayOfWeek) {
                case 0: $dayName = "星期日"; break;
                case 1: $dayName = "星期一"; break;
                case 2: $dayName = "星期二"; break;
                case 3: $dayName = "星期三"; break;
                case 4: $dayName = "星期四"; break;
                case 5: $dayName = "星期五"; break;
                case 6: $dayName = "星期六"; break;
                default: $dayName = "未知"; break;
            }
            echo "<p>今天是：$dayName</p>";
            
            echo "<h3>循环语句：</h3>";
            
            // for循环
            echo "<p>for循环示例（1-5）：";
            for ($i = 1; $i <= 5; $i++) {
                echo "$i ";
            }
            echo "</p>";
            
            // while循环
            echo "<p>while循环示例（倒计时）：";
            $countdown = 5;
            while ($countdown > 0) {
                echo "$countdown ";
                $countdown--;
            }
            echo "</p>";
            echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>4. PHP数组练习</h2>
        <?php
            echo "<div class='result'>";
            
            // 索引数组
            $fruits = ["苹果", "香蕉", "橙子", "草莓"];
            echo "<h3>索引数组：</h3>";
            echo "<p>水果列表：</p><ul>";
            foreach ($fruits as $index => $fruit) {
                echo "<li>索引 $index: $fruit</li>";
            }
            echo "</ul>";
            
            // 关联数组
            $student = [
                "name" => "张三",
                "age" => 20,
                "major" => "网络安全",
                "score" => 88.5
            ];
            
            echo "<h3>关联数组：</h3>";
            echo "<p>学生信息：</p><ul>";
            foreach ($student as $key => $value) {
                echo "<li>$key: $value</li>";
            }
            echo "</ul>";
            
            // 多维数组
            $classes = [
                "网络安全班" => [
                    "学生数" => 30,
                    "教师" => "李老师",
                    "课程" => ["PHP基础", "Web安全", "渗透测试"]
                ],
                "软件开发班" => [
                    "学生数" => 25,
                    "教师" => "王老师",
                    "课程" => ["Java", "Python", "数据库"]
                ]
            ];
            
            echo "<h3>多维数组：</h3>";
            foreach ($classes as $className => $classInfo) {
                echo "<h4>$className</h4>";
                echo "<ul>";
                foreach ($classInfo as $key => $value) {
                    if (is_array($value)) {
                        echo "<li>$key: " . implode(", ", $value) . "</li>";
                    } else {
                        echo "<li>$key: $value</li>";
                    }
                }
                echo "</ul>";
            }
            
            // 数组函数演示
            echo "<h3>数组函数演示：</h3>";
            echo "<p>水果数组元素个数：" . count($fruits) . "</p>";
            
            // 安全的数组搜索
            $searchFruit = "香蕉";
            if (in_array($searchFruit, $fruits, true)) {  // 使用严格比较
                echo "<p>✅ 找到了：$searchFruit</p>";
            } else {
                echo "<p>❌ 没有找到：$searchFruit</p>";
            }
            
            echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>5. 综合练习：安全的用户数据处理</h2>
        <?php
            // 模拟用户输入数据
            $rawUserData = [
                "username" => "  admin  ",
                "email" => "admin@example.com",
                "age" => "25",
                "interests" => ["编程", "安全", "游戏"],
                "malicious" => "<script>alert('XSS')</script>"
            ];
            
            echo "<div class='result'>";
            echo "<h3>原始数据：</h3>";
            echo "<pre>" . print_r($rawUserData, true) . "</pre>";
            
            // 安全处理
            $cleanUserData = [];
            $allowedFields = ["username", "email", "age", "interests"];
            
            foreach ($allowedFields as $field) {
                if (isset($rawUserData[$field])) {
                    if (is_array($rawUserData[$field])) {
                        $cleanUserData[$field] = array_map('htmlspecialchars', $rawUserData[$field]);
                    } else {
                        $cleanUserData[$field] = htmlspecialchars(trim($rawUserData[$field]), ENT_QUOTES, 'UTF-8');
                    }
                }
            }
            
            echo "<h3>安全处理后的数据：</h3>";
            echo "<pre>" . print_r($cleanUserData, true) . "</pre>";
            echo "</div>";
            
            echo "<div class='warning'>";
            echo "<strong>安全要点：</strong><br>";
            echo "1. 使用白名单过滤字段<br>";
            echo "2. 使用htmlspecialchars()防止XSS<br>";
            echo "3. 使用trim()去除多余空格<br>";
            echo "4. 验证数据类型和格式";
            echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>6. PHP环境安全检查</h2>
        <?php
            echo "<div class='result'>";
            echo "<h3>PHP版本信息：</h3>";
            echo "<p>PHP版本：" . PHP_VERSION . "</p>";
            echo "<p>服务器系统：" . PHP_OS . "</p>";
            
            echo "<h3>重要安全配置：</h3>";
            $securityChecks = [
                'display_errors' => '应该为 Off（生产环境）',
                'expose_php' => '应该为 Off',
                'allow_url_include' => '应该为 Off',
                'register_globals' => '应该为 Off'
            ];
            
            foreach ($securityChecks as $setting => $recommendation) {
                $value = ini_get($setting) ? 'On' : 'Off';
                $status = (($setting === 'display_errors' || $setting === 'expose_php' || 
                          $setting === 'allow_url_include' || $setting === 'register_globals') 
                          && $value === 'Off') ? '✅' : '⚠️';
                echo "<p>$status $setting: $value - $recommendation</p>";
            }
            echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>实验总结</h2>
        <div class="warning">
            <h3>本次实验涵盖的要点：</h3>
            <ul>
                <li>✅ PHP基本语法格式和标记</li>
                <li>✅ 各种数据类型的使用和识别</li>
                <li>✅ 算术、比较、逻辑运算符</li>
                <li>✅ if-else、switch、for、while等控制语句</li>
                <li>✅ 索引数组、关联数组、多维数组</li>
                <li>✅ 数组常用函数和安全操作</li>
                <li>✅ 输入数据的安全处理</li>
                <li>✅ PHP环境安全配置检查</li>
            </ul>
            
            <h3>安全编程要点：</h3>
            <ul>
                <li>始终使用强类型比较（===）</li>
                <li>对用户输入进行严格验证和过滤</li>
                <li>使用白名单而不是黑名单</li>
                <li>注意PHP环境的安全配置</li>
            </ul>
        </div>
    </div>
</body>
</html>