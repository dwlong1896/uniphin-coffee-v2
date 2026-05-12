<?php

class CommentModel extends Model
{


    public function countRootCommentsWithFilter(int $newsId, string $search = '', string $status = ''): int
    {
        $sql = "SELECT COUNT(*) as total FROM COMMENTS WHERE News_ID = ? AND parent_comment_id IS NULL";
        $params = [$newsId];
        $types = "i";

        if (!empty($search)) {
            $sql .= " AND content LIKE ?";
            $params[] = "%$search%";
            $types .= "s";
        }
        if (!empty($status)) {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int) ($result['total'] ?? 0);
    }


    public function getCommentsByNewsWithFilters($newsId, $search = '', $status = '', $offset = 0, $limit = 10)
    {
        // 1. Tìm tất cả ID bình luận (không phân biệt root hay con) mà thỏa mãn điều kiện Search/Status
        $sqlFilter = "SELECT ID FROM COMMENTS WHERE News_ID = ?";
        $params = [(int) $newsId];
        $types = "i";

        if (!empty($status)) {
            $sqlFilter .= " AND status = ?";
            $params[] = $status;
            $types .= "s";
        }
        if (!empty($search)) {
            $sqlFilter .= " AND content LIKE ?";
            $params[] = "%$search%";
            $types .= "s";
        }

        // Giới hạn phân trang dựa trên các bản ghi thỏa mãn
        $sqlFilter .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = (int) $limit;
        $params[] = (int) $offset;
        $types .= "ii";

        $stmt = $this->db->prepare($sqlFilter);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $matchedIds = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'ID');
        $stmt->close();

        if (empty($matchedIds))
            return [];

        $sqlAll = "SELECT c.*, u.first_name, u.last_name, u.image as user_avatar 
               FROM COMMENTS c 
               LEFT JOIN USERS u ON c.User_ID = u.ID 
               WHERE c.News_ID = ?
               ORDER BY c.created_at ASC";

        $stmtAll = $this->db->prepare($sqlAll);
        $stmtAll->bind_param('i', $newsId);
        $stmtAll->execute();
        $allComments = $stmtAll->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmtAll->close();

        return $allComments;
    }
    public function countRootComments(int $newsId, bool $isAdmin = false): int
    {
        $sql = "SELECT COUNT(*) as total FROM COMMENTS 
            WHERE News_ID = ? AND parent_comment_id IS NULL";


        if (!$isAdmin) {
            $sql .= " AND status = 'presented'";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $newsId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int) ($result['total'] ?? 0);
    }

    public function getCommentsByNewsId(int $newsId, bool $isAdmin = false, string $sort = 'newest', int $limit = 10, int $offset = 0): array
    {
        $orderBy = ($sort === 'oldest') ? 'ASC' : 'DESC';

        $sqlRoot = "SELECT ID FROM COMMENTS WHERE News_ID = ? AND parent_comment_id IS NULL";
        if (!$isAdmin)
            $sqlRoot .= " AND status = 'presented'";
        $sqlRoot .= " ORDER BY created_at $orderBy LIMIT ? OFFSET ?";

        $stmtRoot = $this->db->prepare($sqlRoot);
        $stmtRoot->bind_param('iii', $newsId, $limit, $offset);
        $stmtRoot->execute();
        $rootIds = array_column($stmtRoot->get_result()->fetch_all(MYSQLI_ASSOC), 'ID');
        $stmtRoot->close();

        if (empty($rootIds))
            return [];


        $sqlAll = "SELECT c.*, u.first_name, u.last_name, u.image, u.role 
               FROM COMMENTS c 
               JOIN USERS u ON c.User_ID = u.ID 
               WHERE c.News_ID = ?";
        if (!$isAdmin)
            $sqlAll .= " AND c.status = 'presented'";
        $sqlAll .= " ORDER BY c.created_at ASC";

        $stmt = $this->db->prepare($sqlAll);
        $stmt->bind_param('i', $newsId);
        $stmt->execute();
        $allComments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $this->sortCommentsForDisplay($allComments, $rootIds, $orderBy);
    }

    private function sortCommentsForDisplay(array $comments, array $rootIds, string $direction): array
    {
        if ($direction === 'ASC')
            return $comments;


        $indexed = [];
        foreach ($comments as $c) {
            $indexed[$c['ID']] = $c;
        }

        $finalResult = [];
        foreach ($rootIds as $id) {
            if (isset($indexed[$id])) {

                $finalResult[] = $indexed[$id];


                unset($indexed[$id]);
            }
        }

        foreach ($indexed as $remainingCmt) {
            $finalResult[] = $remainingCmt;
        }

        return $finalResult;
    }

    public function getCommentById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM COMMENTS WHERE ID = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result ?: null;
    }
    public function addComment(int $newsId, int $userId, string $content, ?int $parentId = null): bool
    {

        $sql = "INSERT INTO COMMENTS (News_ID, User_ID, content, parent_comment_id, status) 
            VALUES (?, ?, ?, ?, 'presented')";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {

            die("Lỗi SQL: " . $this->db->error);
        }


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
        $currentComment = $this->getCommentById($commentId);
        if (!$currentComment)
            return false;

        $newStatus = ($currentComment['status'] === 'presented') ? 'hidden' : 'presented';

        $this->db->begin_transaction();

        try {
      
            $stmt = $this->db->prepare("UPDATE COMMENTS SET status = ? WHERE ID = ?");
            $stmt->bind_param('si', $newStatus, $commentId);
            $stmt->execute();

            $sqlChildren = "
            UPDATE COMMENTS 
            SET status = ? 
            WHERE ID IN (
                WITH RECURSIVE comment_path AS (
                    SELECT ID FROM COMMENTS WHERE parent_comment_id = ?
                    UNION ALL
                    SELECT c.ID FROM COMMENTS c 
                    INNER JOIN comment_path cp ON c.parent_comment_id = cp.ID
                )
                SELECT ID FROM comment_path
            )";

            $stmtChild = $this->db->prepare($sqlChildren);
            $stmtChild->bind_param('si', $newStatus, $commentId);
            $stmtChild->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Lỗi ẩn bình luận: " . $e->getMessage());
            return false;
        }
    }
}