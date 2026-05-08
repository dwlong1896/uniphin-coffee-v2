<?php

class FaqModel extends Model
{
    private string $table = 'faqs';

    /** Lấy FAQs active, sắp xếp theo sort_order */
    public function getActive(): array
    {
        $result = $this->db->query(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order ASC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /** Lấy tất cả FAQs (admin) */
    public function getAllAdmin(): array
    {
        $result = $this->db->query(
            "SELECT * FROM {$this->table} ORDER BY sort_order ASC, id ASC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /** Lấy 1 FAQ theo id */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /** Thêm FAQ */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (question, answer, sort_order, is_active) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('ssii', $data['question'], $data['answer'], $data['sort_order'], $data['is_active']);
        return $stmt->execute();
    }

    /** Sửa FAQ */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET question = ?, answer = ?, sort_order = ?, is_active = ? WHERE id = ?"
        );
        $stmt->bind_param('ssiii', $data['question'], $data['answer'], $data['sort_order'], $data['is_active'], $id);
        return $stmt->execute();
    }

    /** Xoá FAQ */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
