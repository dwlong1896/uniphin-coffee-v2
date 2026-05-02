<?php

class NewsModel extends Model
{
    // Lấy danh sách tin tức (có tìm kiếm và phân trang)
    public function getNewsList(int $limit, int $offset, string $search = ''): array
    {
        $searchTerm = "%$search%";
        $stmt = $this->db->prepare('
            SELECT * FROM NEWS 
            WHERE status = "published" AND title LIKE ? 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ');
        
        $stmt->bind_param('sii', $searchTerm, $limit, $offset);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $news = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $news;
    }

    // Đếm tổng số bài viết để làm phân trang
    public function countNews(string $search = ''): int
    {
        $searchTerm = "%$search%";
        $stmt = $this->db->prepare('SELECT COUNT(*) as total FROM NEWS WHERE status = "published" AND title LIKE ?');
        $stmt->bind_param('s', $searchTerm);
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int)$result['total'];
    }

    // Lấy chi tiết 1 bài viết qua Slug
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM NEWS WHERE slug = ? LIMIT 1');
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $result ?: null;
    }

    // Lấy bình luận của bài viết (Hiển thị tên người dùng và ảnh đại diện)
    public function getCommentsByNewsId(int $newsId): array
    {
        $stmt = $this->db->prepare('
            SELECT c.*, u.first_name, u.last_name, u.image 
            FROM COMMENTS c 
            JOIN USER u ON c.User_ID = u.ID 
            WHERE c.News_ID = ? AND c.status = "presented" 
            ORDER BY c.created_at DESC
        ');
        $stmt->bind_param('i', $newsId);
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $result;
    }
}