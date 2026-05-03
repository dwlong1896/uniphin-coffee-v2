<?php

class NewsModel extends Model
{
    // Lấy danh sách tin tức
    public function getNews(array $filters, int $limit, int $offset): array
    {
        $sql = "SELECT n.*, c.Name as category_name, COUNT(com.ID) as comment_count
                FROM NEWS n 
                LEFT JOIN NEWS_CATEGORIES c ON n.N_Cate_ID = c.ID 
                LEFT JOIN COMMENTS com ON n.ID = com.News_ID
                WHERE n.status = 'published'";
        
        $params = [];
        $types = '';

        if (!empty($filters['search'])) {
            $sql .= " AND n.title LIKE ?";
            $params[] = '%' . $filters['search'] . '%';
            $types .= 's';
        }

        if (!empty($filters['category'])) {
            $sql .= " AND n.N_Cate_ID = ?";
            $params[] = $filters['category'];
            $types .= 'i';
        }

        $orderBy = 'n.created_at DESC';
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'oldest': $orderBy = 'n.created_at ASC'; break;
                case 'title_asc': $orderBy = 'n.title ASC'; break;
            }
        }

        // QUAN TRỌNG: Phải Group By theo ID bài viết trước khi Order By và Limit
        $sql .= " GROUP BY n.ID ORDER BY $orderBy LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    // 2. Đếm số lượng để phân trang (Phải khớp với hàm getNews phía trên)
    public function countNews(array $filters): int
    {
        $sql = "SELECT COUNT(*) as total FROM NEWS WHERE status = 'published'";
        $params = [];
        $types = '';

        if (!empty($filters['search'])) {
            $sql .= " AND title LIKE ?";
            $params[] = '%' . $filters['search'] . '%';
            $types .= 's';
        }

        if (!empty($filters['category'])) {
            $sql .= " AND N_Cate_ID = ?";
            $params[] = $filters['category'];
            $types .= 'i';
        }

        $stmt = $this->db->prepare($sql);
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)$result['total'];
    }

    // Lấy chi tiết bài viết
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM NEWS WHERE slug = ? LIMIT 1
        ');

        $stmt->bind_param('s', $slug);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $result ?: null;
    }
    public function getAllCommentsForAdmin(int $newsId = null): array {
        $sql = "SELECT c.*, u.first_name, u.last_name, n.title as news_title, n.slug as news_slug
                FROM COMMENTS c 
                JOIN users u ON c.User_ID = u.ID 
                JOIN NEWS n ON c.News_ID = n.ID";
        
        $params = [];
        $types = "";

        if ($newsId) {
            $sql .= " WHERE c.News_ID = ?";
            $params[] = $newsId;
            $types = "i";
        }

        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if ($newsId) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    // Lấy thông tin bài viết theo ID (để hiện tiêu đề trang)
    public function getPostById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM NEWS WHERE ID = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result ?: null;
    }

    // Lấy comments
    public function getCommentsByNewsId(int $newsId, bool $isAdmin = false, string $sort = 'newest'): array
{
    // Xác định thứ tự sắp xếp
    $orderBy = ($sort === 'oldest') ? 'c.created_at ASC' : 'c.created_at DESC';

    $sql = "SELECT c.*, u.first_name, u.last_name, u.image 
            FROM COMMENTS c 
            JOIN users u ON c.User_ID = u.ID 
            WHERE c.News_ID = ?";
    
    if (!$isAdmin) {
        $sql .= " AND c.status = 'presented'";
    }
    
    $sql .= " ORDER BY $orderBy";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('i', $newsId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $result;
}
    // Thêm comment
    public function getCommentById(int $id): ?array {
    $stmt = $this->db->prepare('SELECT * FROM COMMENTS WHERE ID = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result ?: null;
}
    public function addComment(int $newsId, int $userId, string $content, ?int $parentId = null): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO COMMENTS (News_ID, User_ID, content, parent_comment_id, status) 
            VALUES (?, ?, ?, ?, "presented")
        ');

        $stmt->bind_param('iisi', $newsId, $userId, $content, $parentId);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
    public function deleteComment(int $commentId, int $userId, bool $isAdmin): bool
{
    if ($isAdmin) {
        $stmt = $this->db->prepare('DELETE FROM COMMENTS WHERE ID = ?');
        $stmt->bind_param('i', $commentId);
    } else {
        // user chỉ xóa comment của mình
        $stmt = $this->db->prepare('DELETE FROM COMMENTS WHERE ID = ? AND User_ID = ?');
        $stmt->bind_param('ii', $commentId, $userId);
    }

    $result = $stmt->execute();
    $stmt->close();

    return $result;
}
    public function updateComment(int $commentId, int $userId, string $content): bool
{
    // Bất kể là ai, muốn UPDATE thì ID bình luận phải khớp với User_ID của người đó
    $stmt = $this->db->prepare('
        UPDATE COMMENTS 
        SET content = ? 
        WHERE ID = ? AND User_ID = ?
    ');
    
    $stmt->bind_param('sii', $content, $commentId, $userId);
    $result = $stmt->execute();
    $stmt->close();

    return $result;
}
    public function toggleCommentStatus(int $commentId): bool
{
    $stmt = $this->db->prepare('
        UPDATE COMMENTS 
        SET status = CASE 
            WHEN status = "presented" THEN "hidden"
            ELSE "presented"
        END
        WHERE ID = ?
    ');

    $stmt->bind_param('i', $commentId);

    $result = $stmt->execute();
    $stmt->close();

    return $result;
}
    public function slugify($str) {
    $str = trim(mb_strtolower($str));
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/([\s]+)/', '-', $str);
    return $str;
}

    // Tạo bài viết
    public function createPost(array $data): bool {
        $stmt = $this->db->prepare('
            INSERT INTO NEWS (title, content, slug, image, meta_keywords, meta_description, Admin_ID, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, "published")
        ');
        // 6 chuỗi (s) và 1 số nguyên (i) cho Admin_ID
        $stmt->bind_param('ssssssi', 
            $data['title'], $data['content'], $data['slug'], 
            $data['image'], $data['meta_keywords'], $data['meta_description'], 
            $data['admin_id']
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // 3. Fix hàm updatePost (Khớp với Controller của Hiền)
    public function updatePost(int $id, array $data): bool {
        $stmt = $this->db->prepare('
            UPDATE NEWS 
            SET title = ?, content = ?, slug = ?, image = ?, meta_keywords = ?, meta_description = ?, updated_at = NOW()
            WHERE ID = ?
        ');
        $stmt->bind_param('ssssssi', 
            $data['title'], $data['content'], $data['slug'], 
            $data['image'], $data['meta_keywords'], $data['meta_description'], 
            $id
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function deletePost(int $id): bool
{
    $stmt = $this->db->prepare('DELETE FROM NEWS WHERE ID = ?');
    $stmt->bind_param('i', $id);

    $result = $stmt->execute();
    $stmt->close();

    return $result;
}


}