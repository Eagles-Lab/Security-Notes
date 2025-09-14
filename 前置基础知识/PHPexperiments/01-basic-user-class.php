<?php
    // 基本类定义
    class User {
        // 属性（成员变量）
        public $username;
        public $email;
        private $password;
        
        // 方法（成员函数）
        public function setUsername($username) {
            $this->username = $username;
        }
        
        public function getUsername() {
            return $this->username;
        }
        
        // 私有方法，外部无法访问
        private function hashPassword($password) {
            return password_hash($password, PASSWORD_DEFAULT);
        }
    }
    
    // 创建对象
    $user = new User();
    $user->setUsername("alice");
    echo $user->getUsername(); // 输出: alice
?>