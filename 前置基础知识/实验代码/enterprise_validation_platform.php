<!DOCTYPE html>
<html>
<head>
    <title>PHP正则表达式实验 - 企业级数据验证中台</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; }
        .section { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .result { background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0; }
        .error { background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin: 10px 0; }
        .warning { background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; margin: 10px 0; }
        .info { background: #d1ecf1; padding: 10px; border: 1px solid #bee5eb; margin: 10px 0; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; font-size: 12px; }
        .tabs { margin-bottom: 20px; }
        .tab { display: inline-block; padding: 10px 20px; background: #f8f9fa; border: 1px solid #ddd; cursor: pointer; margin-right: 5px; }
        .tab.active { background: #007bff; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .stats { display: flex; justify-content: space-between; }
        .stats div { text-align: center; padding: 10px; background: #f8f9fa; border: 1px solid #ddd; flex: 1; margin: 0 5px; }
    </style>
</head>
<body>
    <h1>PHP正则表达式实验 - 企业级数据验证中台</h1>

    <?php
    /**
     * 企业级数据验证中台
     * 集成PHP正则表达式的各种应用场景
     */

    // 配置文件
    class ValidationConfig {
        public static $patterns = [
            // 基础数据类型
            'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'phone_cn' => '/^1[3-9]\d{9}$/',
            'phone_intl' => '/^\+\d{1,3}\d{10,14}$/',
            'idcard_cn' => '/^[1-9]\d{5}(19|20)\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{3}[\dXx]$/',
            'username' => '/^[a-zA-Z0-9_\u4e00-\u9fa5]{2,30}$/u',
            'password_strong' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'url' => '/^https?:\/\/([\w\-]+\.)+[\w\-]+(\/[\w\-._~:\/?#[\]@!$&\'()*+,;%=]*)?$/',
            'ip_v4' => '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',
            'mac_address' => '/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
            
            // 业务特定格式
            'credit_card' => '/^\d{4}[\s-]?\d{4}[\s-]?\d{4}[\s-]?\d{4}$/',
            'social_credit' => '/^[0-9A-HJ-NPQRTUWXY]{2}\d{6}[0-9A-HJ-NPQRTUWXY]{10}$/',
            'license_plate' => '/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-Z0-9]{4}[A-Z0-9挂学警港澳]{1}$/',
            
            // 文档和代码
            'version' => '/^\d+\.\d+\.\d+(?:-[a-zA-Z0-9]+)?$/',
            'hex_color' => '/^#[0-9A-Fa-f]{6}$/',
            'base64' => '/^[A-Za-z0-9+\/]*={0,2}$/',
            'json_string' => '/^\{.*\}$|^\[.*\]$/'
        ];
        
        public static $securityPatterns = [
            'xss' => [
                '/<script[^>]*>.*?<\/script>/is',
                '/javascript:/i',
                '/onload\s*=/i',
                '/onerror\s*=/i',
                '/onclick\s*=/i'
            ],
            'sql_injection' => [
                '/(union\s+select|insert\s+into|delete\s+from|update\s+set|drop\s+table)/i',
                '/(\'\s*(or|and)\s*\')/i',
                '/(\'\s*;\s*)/i'
            ],
            'path_traversal' => [
                '/\.\.\//',
                '/\.\.\\\\/',
                '/%2e%2e%2f/i',
                '/%2e%2e%5c/i'
            ],
            'command_injection' => [
                '/;\s*(rm|del|format|shutdown)/i',
                '/\|\s*(cat|type|dir)/i',
                '/`[^`]*`/',
                '/\$\([^)]*\)/'
            ]
        ];
    }

    // 验证结果类
    class ValidationResult {
        public $isValid;
        public $errorMessage;
        public $securityThreats;
        public $extractedData;
        public $processingTime;
        
        public function __construct($isValid = false) {
            $this->isValid = $isValid;
            $this->errorMessage = null;
            $this->securityThreats = [];
            $this->extractedData = [];
            $this->processingTime = 0;
        }
    }

    // 性能追踪器
    class PerformanceTracker {
        private $records = [];
        
        public function record($type, $time) {
            if (!isset($this->records[$type])) {
                $this->records[$type] = [];
            }
            $this->records[$type][] = $time;
        }
        
        public function getStats() {
            $stats = [];
            
            foreach ($this->records as $type => $times) {
                $stats[$type] = [
                    'count' => count($times),
                    'total_time' => array_sum($times),
                    'avg_time' => array_sum($times) / count($times),
                    'min_time' => min($times),
                    'max_time' => max($times)
                ];
            }
            
            return $stats;
        }
    }

    // 核心验证引擎
    class ValidationEngine {
        private $config;
        private $performanceTracker;
        
        public function __construct() {
            $this->config = new ValidationConfig();
            $this->performanceTracker = new PerformanceTracker();
        }
        
        // 单个数据验证
        public function validate($type, $data, $options = []) {
            $startTime = microtime(true);
            $result = new ValidationResult();
            
            try {
                // 安全检查
                $result->securityThreats = $this->checkSecurity($data);
                if (!empty($result->securityThreats) && !($options['allow_unsafe'] ?? false)) {
                    $result->errorMessage = '检测到安全威胁: ' . implode(', ', $result->securityThreats);
                    return $result;
                }
                
                // 格式验证
                if (!isset($this->config::$patterns[$type])) {
                    throw new InvalidArgumentException("未知的验证类型: $type");
                }
                
                $pattern = $this->config::$patterns[$type];
                $result->isValid = preg_match($pattern, $data) === 1;
                
                if (!$result->isValid) {
                    $result->errorMessage = "数据格式不符合 {$type} 类型要求";
                }
                
                // 提取结构化数据
                if ($result->isValid && ($options['extract_data'] ?? false)) {
                    preg_match($pattern, $data, $matches);
                    $result->extractedData = $matches;
                }
                
            } catch (Exception $e) {
                $result->errorMessage = $e->getMessage();
            } finally {
                $result->processingTime = (microtime(true) - $startTime) * 1000;
                $this->performanceTracker->record($type, $result->processingTime);
            }
            
            return $result;
        }
        
        // 批量验证
        public function batchValidate($type, $dataArray, $options = []) {
            $results = [];
            $successCount = 0;
            
            foreach ($dataArray as $key => $data) {
                $result = $this->validate($type, $data, $options);
                $results[$key] = $result;
                
                if ($result->isValid) {
                    $successCount++;
                }
            }
            
            return [
                'results' => $results,
                'summary' => [
                    'total' => count($dataArray),
                    'success' => $successCount,
                    'failure' => count($dataArray) - $successCount,
                    'success_rate' => $successCount / count($dataArray) * 100
                ]
            ];
        }
        
        // 数据提取
        public function extractAll($type, $text, $options = []) {
            if (!isset($this->config::$patterns[$type])) {
                throw new InvalidArgumentException("未知的提取类型: $type");
            }
            
            $pattern = $this->config::$patterns[$type];
            $flags = $options['with_offset'] ?? false ? PREG_OFFSET_CAPTURE : 0;
            
            preg_match_all($pattern, $text, $matches, $flags);
            
            return [
                'matches' => $matches[0],
                'count' => count($matches[0]),
                'text_length' => strlen($text)
            ];
        }
        
        // 数据过滤
        public function filter($type, $dataArray, $options = []) {
            if (!isset($this->config::$patterns[$type])) {
                throw new InvalidArgumentException("未知的过滤类型: $type");
            }
            
            $pattern = $this->config::$patterns[$type];
            $filtered = preg_filter($pattern, '$0', $dataArray);
            
            return [
                'filtered_data' => $filtered ?? [],
                'original_count' => count($dataArray),
                'filtered_count' => count($filtered ?? []),
                'filter_rate' => count($filtered ?? []) / count($dataArray) * 100
            ];
        }
        
        // 数据分割
        public function split($delimiter, $text, $options = []) {
            $flags = 0;
            if ($options['no_empty'] ?? true) {
                $flags |= PREG_SPLIT_NO_EMPTY;
            }
            if ($options['capture_delimiter'] ?? false) {
                $flags |= PREG_SPLIT_DELIM_CAPTURE;
            }
            if ($options['with_offset'] ?? false) {
                $flags |= PREG_SPLIT_OFFSET_CAPTURE;
            }
            
            $limit = $options['limit'] ?? -1;
            $parts = preg_split($delimiter, $text, $limit, $flags);
            
            return [
                'parts' => $parts,
                'count' => count($parts),
                'original_length' => strlen($text)
            ];
        }
        
        // 安全检查
        private function checkSecurity($data) {
            $threats = [];
            
            foreach ($this->config::$securityPatterns as $threatType => $patterns) {
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $data)) {
                        $threats[] = $threatType;
                        break;
                    }
                }
            }
            
            return array_unique($threats);
        }
        
        // 获取性能统计
        public function getPerformanceStats() {
            return $this->performanceTracker->getStats();
        }
    }

    // 数据清洗器
    class DataSanitizer {
        public static function cleanHTML($input) {
            $cleaned = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $input);
            $cleaned = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/', '', $cleaned);
            $cleaned = preg_replace('/javascript:/i', '', $cleaned);
            return $cleaned;
        }
        
        public static function sanitizeFileName($filename) {
            $cleaned = preg_replace('/[^\w.\-\u4e00-\u9fa5]/u', '_', $filename);
            $cleaned = preg_replace('/\.{2,}/', '.', $cleaned);
            $cleaned = preg_replace('/^\.+|\.+$/', '', $cleaned);
            return substr($cleaned, 0, 255);
        }
        
        public static function normalizeWhitespace($text) {
            $cleaned = preg_replace('/\s+/', ' ', $text);
            return trim($cleaned);
        }
    }

    // 主应用类
    class ValidationPlatform {
        private $engine;
        
        public function __construct() {
            $this->engine = new ValidationEngine();
        }
        
        public function processData($request) {
            $response = [
                'status' => 'success',
                'data' => [],
                'errors' => [],
                'performance' => []
            ];
            
            try {
                switch ($request['action']) {
                    case 'validate':
                        $response['data'] = $this->engine->validate(
                            $request['type'], 
                            $request['data'], 
                            $request['options'] ?? []
                        );
                        break;
                        
                    case 'batch_validate':
                        $response['data'] = $this->engine->batchValidate(
                            $request['type'], 
                            $request['data'], 
                            $request['options'] ?? []
                        );
                        break;
                        
                    case 'extract':
                        $response['data'] = $this->engine->extractAll(
                            $request['type'], 
                            $request['text'], 
                            $request['options'] ?? []
                        );
                        break;
                        
                    case 'filter':
                        $response['data'] = $this->engine->filter(
                            $request['type'], 
                            $request['data'], 
                            $request['options'] ?? []
                        );
                        break;
                        
                    case 'split':
                        $response['data'] = $this->engine->split(
                            $request['delimiter'], 
                            $request['text'], 
                            $request['options'] ?? []
                        );
                        break;
                        
                    default:
                        throw new InvalidArgumentException("不支持的操作: " . $request['action']);
                }
                
            } catch (Exception $e) {
                $response['status'] = 'error';
                $response['errors'][] = $e->getMessage();
            } finally {
                $response['performance'] = $this->engine->getPerformanceStats();
            }
            
            return $response;
        }
    }

    // 运行测试
    $platform = new ValidationPlatform();
    ?>

    <div class="tabs">
        <div class="tab active" onclick="showTab('single')">单个验证</div>
        <div class="tab" onclick="showTab('batch')">批量验证</div>
        <div class="tab" onclick="showTab('extract')">数据提取</div>
        <div class="tab" onclick="showTab('filter')">数据过滤</div>
        <div class="tab" onclick="showTab('split')">数据分割</div>
        <div class="tab" onclick="showTab('sanitize')">数据清洗</div>
        <div class="tab" onclick="showTab('performance')">性能统计</div>
        <div class="tab" onclick="showTab('info')">系统信息</div>
    </div>

    <!-- 单个验证测试 -->
    <div id="single" class="tab-content active">
        <div class="section">
            <h2>1. 单个数据验证测试</h2>
            <?php
            echo "<div class='result'>";
            
            // 测试各种数据类型
            $testCases = [
                ['type' => 'email', 'data' => 'admin@company.com.cn', 'desc' => '邮箱验证'],
                ['type' => 'phone_cn', 'data' => '13812345678', 'desc' => '中国手机号'],
                ['type' => 'username', 'data' => '安全管理员123', 'desc' => '用户名（中文+数字）'],
                ['type' => 'password_strong', 'data' => 'SecurePass123!', 'desc' => '强密码'],
                ['type' => 'url', 'data' => 'https://www.example.com/path?param=value', 'desc' => 'URL地址'],
                ['type' => 'ip_v4', 'data' => '192.168.1.100', 'desc' => 'IPv4地址'],
                ['type' => 'credit_card', 'data' => '4123-4567-8901-2345', 'desc' => '信用卡号'],
                ['type' => 'hex_color', 'data' => '#FF5733', 'desc' => '十六进制颜色']
            ];
            
            foreach ($testCases as $test) {
                $request = [
                    'action' => 'validate',
                    'type' => $test['type'],
                    'data' => $test['data'],
                    'options' => ['extract_data' => true]
                ];
                
                $result = $platform->processData($request);
                $validation = $result['data'];
                
                $status = $validation->isValid ? '✅ 通过' : '❌ 失败';
                $time = number_format($validation->processingTime, 3);
                
                echo "<p><strong>{$test['desc']}</strong>: '{$test['data']}' - $status (耗时: {$time}ms)</p>";
                
                if (!$validation->isValid && $validation->errorMessage) {
                    echo "<p style='color: red; margin-left: 20px;'>错误: {$validation->errorMessage}</p>";
                }
                
                if (!empty($validation->securityThreats)) {
                    echo "<p style='color: orange; margin-left: 20px;'>安全威胁: " . implode(', ', $validation->securityThreats) . "</p>";
                }
            }
            
            echo "</div>";
            ?>
        </div>
    </div>

    <!-- 批量验证测试 -->
    <div id="batch" class="tab-content">
        <div class="section">
            <h2>2. 批量验证测试</h2>
            <?php
            echo "<div class='result'>";
            
            // 测试邮箱批量验证
            $testEmails = [
                'user1@example.com',
                'invalid-email-format',
                'admin@company.org',
                'test@domain.co.uk',
                '<script>alert("xss")</script>@hack.com',
                'normal.user+tag@valid-domain.com',
                'bad@domain',
                'good@example.net'
            ];
            
            $request = [
                'action' => 'batch_validate',
                'type' => 'email',
                'data' => $testEmails
            ];
            
            $result = $platform->processData($request);
            $batchResult = $result['data'];
            
            echo "<h3>邮箱批量验证结果</h3>";
            echo "<div class='stats'>";
            echo "<div><strong>总数</strong><br>{$batchResult['summary']['total']}</div>";
            echo "<div><strong>成功</strong><br>{$batchResult['summary']['success']}</div>";
            echo "<div><strong>失败</strong><br>{$batchResult['summary']['failure']}</div>";
            echo "<div><strong>成功率</strong><br>" . number_format($batchResult['summary']['success_rate'], 1) . "%</div>";
            echo "</div>";
            
            echo "<h4>详细结果:</h4>";
            foreach ($batchResult['results'] as $index => $validation) {
                $email = $testEmails[$index];
                $status = $validation->isValid ? '✅' : '❌';
                $time = number_format($validation->processingTime, 3);
                
                echo "<p>$status '$email' (耗时: {$time}ms)";
                if (!$validation->isValid) {
                    echo " - " . ($validation->errorMessage ?: '格式错误');
                }
                echo "</p>";
            }
            
            echo "</div>";
            ?>
        </div>
    </div>

    <!-- 数据提取测试 -->
    <div id="extract" class="tab-content">
        <div class="section">
            <h2>3. 数据提取测试</h2>
            <?php
            echo "<div class='result'>";
            
            // 从文本中提取邮箱地址
            $contactText = "
            联系我们：
            销售部：sales@company.com，电话：138-0000-1111
            技术支持：support@company.com，电话：139-0000-2222  
            客服热线：service@company.com，电话：400-123-4567
            人事部：hr@company.com.cn
            财务部：finance@corp.example.org
            ";
            
            $request = [
                'action' => 'extract',
                'type' => 'email',
                'text' => $contactText,
                'options' => ['with_offset' => true]
            ];
            
            $result = $platform->processData($request);
            $extractResult = $result['data'];
            
            echo "<h3>从联系信息中提取邮箱地址</h3>";
            echo "<p><strong>原始文本长度:</strong> {$extractResult['text_length']} 字符</p>";
            echo "<p><strong>找到邮箱数量:</strong> {$extractResult['count']} 个</p>";
            
            echo "<h4>提取到的邮箱地址:</h4><ul>";
            foreach ($extractResult['matches'] as $match) {
                if (is_array($match)) {
                    echo "<li>{$match[0]} (位置: {$match[1]})</li>";
                } else {
                    echo "<li>$match</li>";
                }
            }
            echo "</ul>";
            
            // 提取手机号
            $request2 = [
                'action' => 'extract', 
                'type' => 'phone_cn',
                'text' => $contactText
            ];
            
            $result2 = $platform->processData($request2);
            $phoneResult = $result2['data'];
            
            echo "<h4>提取到的手机号:</h4><ul>";
            foreach ($phoneResult['matches'] as $phone) {
                echo "<li>$phone</li>";
            }
            echo "</ul>";
            
            echo "</div>";
            ?>
        </div>
    </div>

    <!-- 数据过滤测试 -->
    <div id="filter" class="tab-content">
        <div class="section">
            <h2>4. 数据过滤测试</h2>
            <?php
            echo "<div class='result'>";
            
            $mixedData = [
                'valid@email.com',
                'not-an-email',
                'another@valid.org', 
                'invalid.email.format',
                'test@domain.co.uk',
                '这不是邮箱',
                'admin@company.com.cn',
                '123456',
                'user@test.net'
            ];
            
            $request = [
                'action' => 'filter',
                'type' => 'email',
                'data' => $mixedData
            ];
            
            $result = $platform->processData($request);
            $filterResult = $result['data'];
            
            echo "<h3>邮箱数据过滤结果</h3>";
            echo "<div class='stats'>";
            echo "<div><strong>原始数量</strong><br>{$filterResult['original_count']}</div>";
            echo "<div><strong>过滤后数量</strong><br>{$filterResult['filtered_count']}</div>";
            echo "<div><strong>过滤率</strong><br>" . number_format($filterResult['filter_rate'], 1) . "%</div>";
            echo "<div><strong>有效率</strong><br>" . number_format(($filterResult['filtered_count']/$filterResult['original_count'])*100, 1) . "%</div>";
            echo "</div>";
            
            echo "<h4>原始数据:</h4>";
            echo "<pre>" . print_r($mixedData, true) . "</pre>";
            
            echo "<h4>过滤后的有效邮箱:</h4>";
            echo "<pre>" . print_r($filterResult['filtered_data'], true) . "</pre>";
            
            echo "</div>";
            ?>
        </div>
    </div>

    <!-- 数据分割测试 -->
    <div id="split" class="tab-content">
        <div class="section">
            <h2>5. 数据分割测试</h2>
            <?php
            echo "<div class='result'>";
            
            // CSV数据分割
            $csvData = "姓名,邮箱,电话,部门,职位";
            
            $request = [
                'action' => 'split',
                'delimiter' => '/[,;]+/',
                'text' => $csvData,
                'options' => ['no_empty' => true]
            ];
            
            $result = $platform->processData($request);
            $splitResult = $result['data'];
            
            echo "<h3>CSV标题行分割</h3>";
            echo "<p><strong>原始数据:</strong> '$csvData'</p>";
            echo "<p><strong>分割结果 ({$splitResult['count']} 个字段):</strong></p><ol>";
            foreach ($splitResult['parts'] as $part) {
                echo "<li>$part</li>";
            }
            echo "</ol>";
            
            // 复杂文本分割
            $complexText = "用户1@email.com;用户2@test.org,用户3@company.net\t用户4@domain.co.uk";
            
            $request2 = [
                'action' => 'split',
                'delimiter' => '/[,;\s]+/',
                'text' => $complexText,
                'options' => ['no_empty' => true, 'with_offset' => true]
            ];
            
            $result2 = $platform->processData($request2);
            $splitResult2 = $result2['data'];
            
            echo "<h3>混合分隔符文本分割</h3>";
            echo "<p><strong>原始数据:</strong> '$complexText'</p>";
            echo "<p><strong>分割结果:</strong></p><ul>";
            foreach ($splitResult2['parts'] as $part) {
                if (is_array($part)) {
                    echo "<li>'{$part[0]}' (位置: {$part[1]})</li>";
                } else {
                    echo "<li>'$part'</li>";
                }
            }
            echo "</ul>";
            
            echo "</div>";
            ?>
        </div>
    </div>

    <!-- 数据清洗测试 -->
    <div id="sanitize" class="tab-content">
        <div class="section">
            <h2>6. 数据清洗测试</h2>
            <?php
            echo "<div class='result'>";
            
            // HTML清洗测试
            $dangerousHTML = '<div onclick="alert(1)">正常内容</div><script>alert("XSS")</script>更多内容';
            $cleanHTML = DataSanitizer::cleanHTML($dangerousHTML);
            
            echo "<h3>HTML安全清洗</h3>";
            echo "<p><strong>原始HTML:</strong></p>";
            echo "<pre>" . htmlspecialchars($dangerousHTML) . "</pre>";
            echo "<p><strong>清洗后HTML:</strong></p>";
            echo "<pre>" . htmlspecialchars($cleanHTML) . "</pre>";
            
            // 文件名清洗测试
            $dangerousFilenames = [
                '../../../etc/passwd',
                'normal-file.txt',
                'file with spaces.jpg',
                '文档名称.pdf',
                'script<script>.js',
                'file|with|pipes.doc',
                'file:with:colons.txt'
            ];
            
            echo "<h3>文件名安全清洗</h3>";
            foreach ($dangerousFilenames as $filename) {
                $cleanFilename = DataSanitizer::sanitizeFileName($filename);
                echo "<p><strong>原始:</strong> '$filename' → <strong>清洗后:</strong> '$cleanFilename'</p>";
            }
            
            // 空白字符规范化测试
            $messyText = "这是    一个   \n\n   包含    多余空白  \t  的文本   ";
            $normalText = DataSanitizer::normalizeWhitespace($messyText);
            
            echo "<h3>空白字符规范化</h3>";
            echo "<p><strong>原始文本:</strong> '<code>" . htmlspecialchars($messyText) . "</code>'</p>";
            echo "<p><strong>规范化后:</strong> '<code>" . htmlspecialchars($normalText) . "</code>'</p>";
            
            echo "</div>";
            ?>
        </div>
    </div>

    <!-- 性能统计 -->
    <div id="performance" class="tab-content">
        <div class="section">
            <h2>7. 性能统计报告</h2>
            <?php
            echo "<div class='result'>";
            
            $stats = $platform->processData(['action' => 'validate', 'type' => 'email', 'data' => 'test@example.com'])['performance'];
            
            if (!empty($stats)) {
                echo "<h3>验证性能统计</h3>";
                
                foreach ($stats as $type => $stat) {
                    echo "<div style='margin-bottom: 15px; padding: 10px; background: #f8f9fa; border: 1px solid #ddd;'>";
                    echo "<h4>验证类型: $type</h4>";
                    echo "<div class='stats'>";
                    echo "<div><strong>执行次数</strong><br>{$stat['count']}</div>";
                    echo "<div><strong>总耗时</strong><br>" . number_format($stat['total_time'], 3) . "ms</div>";
                    echo "<div><strong>平均耗时</strong><br>" . number_format($stat['avg_time'], 3) . "ms</div>";
                    echo "<div><strong>最快</strong><br>" . number_format($stat['min_time'], 3) . "ms</div>";
                    echo "<div><strong>最慢</strong><br>" . number_format($stat['max_time'], 3) . "ms</div>";
                    echo "</div>";
                    echo "</div>";
                }
            }
            
            // 系统性能信息
            echo "<h3>系统性能信息</h3>";
            echo "<p><strong>内存使用:</strong> " . number_format(memory_get_usage() / 1024 / 1024, 2) . " MB</p>";
            echo "<p><strong>峰值内存:</strong> " . number_format(memory_get_peak_usage() / 1024 / 1024, 2) . " MB</p>";
            echo "<p><strong>执行时间:</strong> " . number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3) . " 秒</p>";
            
            echo "</div>";
            ?>
        </div>
    </div>

    <!-- 系统信息 -->
    <div id="info" class="tab-content">
        <div class="section">
            <h2>8. 企业级验证中台系统信息</h2>
            <div class="info">
                <h3>支持的验证类型 (<?= count(ValidationConfig::$patterns) ?> 种):</h3>
                <div style="columns: 2; column-gap: 20px;">
                    <?php foreach (array_keys(ValidationConfig::$patterns) as $type): ?>
                        <p>✅ <?= $type ?></p>
                    <?php endforeach; ?>
                </div>
                
                <h3>安全威胁检测 (<?= count(ValidationConfig::$securityPatterns) ?> 类):</h3>
                <?php foreach (ValidationConfig::$securityPatterns as $threat => $patterns): ?>
                    <p>🛡️ <?= $threat ?> (<?= count($patterns) ?> 个模式)</p>
                <?php endforeach; ?>
                
                <h3>核心功能特性:</h3>
                <ul>
                    <li>✅ 单个数据验证 - validate()</li>
                    <li>✅ 批量数据验证 - batchValidate()</li>
                    <li>✅ 数据提取 - extractAll()</li>
                    <li>✅ 数据过滤 - filter()</li>
                    <li>✅ 数据分割 - split()</li>
                    <li>✅ 安全威胁检测 - checkSecurity()</li>
                    <li>✅ 性能监控 - PerformanceTracker</li>
                    <li>✅ 数据清洗 - DataSanitizer</li>
                </ul>
                
                <h3>企业级特性:</h3>
                <ul>
                    <li>🏗️ 模块化架构设计</li>
                    <li>⚡ 高性能批量处理</li>
                    <li>🛡️ 多层安全防护</li>
                    <li>📊 详细性能统计</li>
                    <li>🔧 灵活配置管理</li>
                    <li>📋 完整错误处理</li>
                    <li>🔍 实时威胁检测</li>
                    <li>💾 内存优化设计</li>
                </ul>
                
                <h3>技术栈信息:</h3>
                <p><strong>PHP版本:</strong> <?= PHP_VERSION ?></p>
                <p><strong>PCRE版本:</strong> <?= PCRE_VERSION ?></p>
                <p><strong>系统时间:</strong> <?= date('Y-m-d H:i:s') ?></p>
                <p><strong>时区:</strong> <?= date_default_timezone_get() ?></p>
                <p><strong>内存限制:</strong> <?= ini_get('memory_limit') ?></p>
                <p><strong>最大执行时间:</strong> <?= ini_get('max_execution_time') ?>秒</p>
                
                <div class="warning">
                    <h3>使用说明:</h3>
                    <p>本系统是PHP正则表达式的综合实验平台，展示了企业级数据验证中台的完整实现。包含了从基础验证到复杂业务场景的全方位解决方案。</p>
                    
                    <h4>主要学习价值:</h4>
                    <ul>
                        <li>🎯 正则表达式在实际业务中的应用</li>
                        <li>🛡️ 安全编程的最佳实践</li>
                        <li>⚡ 高性能数据处理技术</li>
                        <li>🏗️ 企业级架构设计思路</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // 隐藏所有标签内容
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));
            
            // 移除所有标签的active类
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // 显示选中的标签内容
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>