<?php

class CategoryModel extends Model
{
    protected $table = 'NEWS_CATEGORIES';

    /**
     * Lấy tất cả danh mục
     */
    public function getAll()
    {
        // Sửa 'name' -> 'Name' trong ORDER BY
        $sql = "SELECT * FROM {$this->table} ORDER BY Name ASC";
        $result = $this->db->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT ID FROM {$this->table} WHERE Name = ?";
        if ($excludeId) {
            $sql .= " AND ID != ?";
        }

        $stmt = $this->db->prepare($sql);
        if ($excludeId) {
            $stmt->bind_param("si", $name, $excludeId);
        } else {
            $stmt->bind_param("s", $name);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0; 
    }
    
    public function create($name)
    {
        // Nhớ check xem tên table có đúng NEWS_CATEGORIES không nhen
        $sql = "INSERT INTO {$this->table} (Name) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }


    public function update($id, $name)
    {
        $sql = "UPDATE {$this->table} SET Name = ? WHERE ID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $name, $id);
        return $stmt->execute();
    }
    public function countPostsByCategoryId(int $id): int
    {
       
        $sql = "SELECT COUNT(*) as total FROM NEWS WHERE N_Cate_ID = ?";

        $stmt = $this->db->prepare($sql);

        if ($stmt === false) {
           
            error_log("SQL Prepare Error: " . $this->db->error);
            return 0;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return (int) ($result['total'] ?? 0);
    }


    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE ID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }


    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE ID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function getAllPaginated($search = '', $sort = 'name_asc', $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($search)) {
            $sql .= " WHERE Name LIKE ?";
        }

        if ($sort == 'name_desc') {
            $sql .= " ORDER BY Name DESC";
        } else {
            $sql .= " ORDER BY Name ASC"; 
        }

        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bind_param("sii", $searchTerm, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function countAll($search = '')
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($search)) {
            $sql .= " WHERE Name LIKE ?";
        }

        $stmt = $this->db->prepare($sql);
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bind_param("s", $searchTerm);
        }

        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res['total'];
    }
}