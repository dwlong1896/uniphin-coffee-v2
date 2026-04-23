<?php
class Database {
    // Luu instance duy nhat cua Database (singleton).
    private static ?Database $instance = null;

    // Doi tuong ket noi MySQLi dung chung cho toan bo ung dung.
    private mysqli $connection;

    private function __construct() {
        // Tao ket noi DB tu cac hang so trong file config/database.php.
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->connection->connect_error) {
            die("Kết nối thất bại: " . $this->connection->connect_error);
        }

        // Set charset de doc/ghi tieng Viet dung (utf8mb4).
        $this->connection->set_charset(DB_CHARSET);
    }

    // Ngan clone de dam bao singleton khong bi tao them ban sao.
    private function __clone() {}

    public static function getInstance(): Database {
        // Chi khoi tao 1 lan, cac lan sau tai su dung cung instance.
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli {
        // Tra ve ket noi MySQLi de model su dung query.
        return $this->connection;
    }
}