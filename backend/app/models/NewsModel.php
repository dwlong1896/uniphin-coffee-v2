<?php

class NewsModel extends Model
{
    // Lấy danh sách tin tức
    public function getNews(array $filters, int $limit, int $offset, bool $isAdmin = true): array
    {
        $sql = "SELECT n.*, n.ID as ID, n.image as post_image, n.Admin_ID, c.Name as category_name, 
                   u.first_name as admin_fname, u.last_name as admin_lname,
                   COUNT(com.ID) as comment_count
            FROM NEWS n 
            LEFT JOIN NEWS_CATEGORIES c ON n.N_Cate_ID = c.ID 
            LEFT JOIN ADMIN a ON n.Admin_ID = a.ID
            LEFT JOIN USERS u ON a.ID = u.ID
            LEFT JOIN COMMENTS com ON n.ID = com.News_ID
            WHERE 1=1";

        $params = [];
        $types = '';
        if (!$isAdmin) {
            $sql .= " AND n.status = 'published'";
        }

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
                case 'oldest':
                    $orderBy = 'n.created_at ASC';
                    break;
                case 'title_asc':
                    $orderBy = 'n.title ASC';
                    break;
            }
        }


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

    public function countNews(array $filters, bool $isAdmin = false): int
    {
        $sql = "SELECT COUNT(*) as total FROM NEWS WHERE status = 'published'";
        $params = [];
        $types = '';
        if (!$isAdmin) {
            $sql .= " AND status = 'published'";
        }
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
        return (int) $result['total'];
    }


    public function findBySlug(string $slug): ?array
    {
        // Chỉ định rõ n.ID as ID để không bị ghi đè bởi ID của User
        $sql = "SELECT n.*, n.ID as ID, n.image as post_image, c.Name as category_name, 
                   u.first_name as admin_fname, u.last_name as admin_lname 
            FROM NEWS n 
            LEFT JOIN NEWS_CATEGORIES c ON n.N_Cate_ID = c.ID 
            LEFT JOIN ADMIN a ON n.Admin_ID = a.ID
            LEFT JOIN USERS u ON a.ID = u.ID
            WHERE n.slug = ? LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $result ?: null;
    }

    // Thêm hàm lấy bài liên quan cho sidebar
    public function getRelatedNews(int $categoryId, int $currentId, int $limit = 4): array
    {
        // Lấy tin cùng danh mục
        $sql = "SELECT n.*, n.image as post_image, c.Name as category_name 
            FROM NEWS n
            LEFT JOIN NEWS_CATEGORIES c ON n.N_Cate_ID = c.ID
            WHERE n.N_Cate_ID = ? AND n.ID != ? AND n.status = 'published' 
            ORDER BY n.created_at DESC LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $categoryId, $currentId, $limit);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // FIX: Nếu không có tin liên quan cùng loại, lấy các tin mới nhất khác
        if (empty($result)) {
            $sqlFallback = "SELECT n.*, n.image as post_image, c.Name as category_name 
                        FROM NEWS n
                        LEFT JOIN NEWS_CATEGORIES c ON n.N_Cate_ID = c.ID
                        WHERE n.ID != ? AND n.status = 'published' 
                        ORDER BY n.created_at DESC LIMIT ?";
            $stmtF = $this->db->prepare($sqlFallback);
            $stmtF->bind_param('ii', $currentId, $limit);
            $stmtF->execute();
            $result = $stmtF->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmtF->close();
        }

        return $result;
    }

    public function getPostById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM NEWS WHERE ID = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result ?: null;
    }
    public function slugify($str)
    {
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);


        $str = preg_replace('/[^a-z0-9\s]/', '', $str);


        $str = preg_replace('/[\s]+/', '-', $str);


        $str = preg_replace('/-+/', '-', $str);
        $str = trim($str, '-');

        return $str;
    }
    public function createUniqueSlug($title, $id = null)
    {
        $slug = $this->slugify($title);
        $originalSlug = $slug;
        $count = 1;

        while (true) {

            $sql = "SELECT ID FROM NEWS WHERE slug = ?";
            if ($id) {
                $sql .= " AND ID != ?";
            }

            $stmt = $this->db->prepare($sql);
            if ($id) {
                $stmt->bind_param("si", $slug, $id);
            } else {
                $stmt->bind_param("s", $slug);
            }

            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows === 0) {

                break;
            }
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function getPostByIdWithJoin(int $id): ?array
    {

        $sql = "SELECT 
                n.*, 
                c.Name as category_name, 
                u.first_name as admin_fname, 
                u.last_name as admin_lname 
            FROM NEWS n 
            LEFT JOIN NEWS_CATEGORIES c ON n.N_Cate_ID = c.ID 
            LEFT JOIN USERS u ON n.Admin_ID = u.ID 
            WHERE n.ID = ? 
            LIMIT 1";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        $stmt->close();


        return $result ?: null;
    }
    // Tạo bài viết
    public function createPost(array $data): bool
    {

        $stmt = $this->db->prepare('
        INSERT INTO NEWS (title, content, N_Cate_ID, slug, image, keywords, meta_description, Admin_ID,  status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, "published")
    ');


        $stmt->bind_param(
            'ssssssii',
            $data['title'],
            $data['content'],
            $data['category_id'],
            $data['slug'],
            $data['image'],
            $data['keywords'],
            $data['meta_description'],
            $data['admin_id']

        );

        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }


    public function updatePost(int $id, array $data): bool
    {
        $sql = "UPDATE NEWS 
            SET title = ?, 
                content = ?, 
                N_Cate_ID = ?, 
                slug = ?, 
                image = ?, 
                Admin_ID = ?, 
                meta_description = ?, 
                keywords = ?,
                status = ?  
            WHERE ID = ?";

        $stmt = $this->db->prepare($sql);


        $stmt->bind_param(
            'ssississsi',
            $data['title'],
            $data['content'],
            $data['category_id'],
            $data['slug'],
            $data['image'],
            $data['admin_id'],
            $data['meta_description'],
            $data['keywords'],
            $data['status'],
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