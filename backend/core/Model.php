<?php
class Model {
    // Bien ket noi DB duoc model con ke thua va su dung.
    protected mysqli $db;

    public function __construct() {
        // Lay ket noi dung chung tu Database singleton.
        $this->db = Database::getInstance()->getConnection();
    }
}