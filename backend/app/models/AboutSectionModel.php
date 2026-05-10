<?php

class AboutSectionModel extends Model
{
    private string $table = 'about_sections';

    /** Lấy tất cả sections, sắp xếp theo sort_order */
    public function getAll(): array
    {
        $result = $this->db->query("SELECT * FROM {$this->table} ORDER BY sort_order ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /** Lấy 1 section theo key */
    public function getByKey(string $key): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE section_key = ?");
        $stmt->bind_param('s', $key);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /** Cập nhật 1 section */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET title = ?, content = ?, image_url = ? WHERE id = ?"
        );
        $stmt->bind_param('sssi', $data['title'], $data['content'], $data['image_url'], $id);
        return $stmt->execute();
    }
}
