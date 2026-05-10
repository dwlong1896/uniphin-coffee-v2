<?php

class AdminController extends Controller
{
    private UserModel $userModel;
    private NewsModel $newsModel;
    private NewsCategoryModel $newsCategoryModel;
    private CommentModel $commentModel;


    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->newsModel = new NewsModel();
        $this->newsCategoryModel = new NewsCategoryModel();
        $this->commentModel = new CommentModel();
    }

    public function dashboard(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/index', ['title' => 'Dashboard'], 'admin/layouts/main');
    }

    public function users(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/users', ['title' => 'Quản lý người dùng'], 'admin/layouts/main');
    }

    public function products(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/products', ['title' => 'Quản lý sản phẩm'], 'admin/layouts/main');
    }

    public function orders(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/orders', ['title' => 'Quản lý đơn hàng'], 'admin/layouts/main');
    }
    public function createCategory(): void
    {
        if (ob_get_length())
            ob_clean();
        header('Content-Type: application/json');

        $name = isset($_POST['name']) ? trim($_POST['name']) : '';

        // VALIDATION ĐỒNG BỘ
        if (empty($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên danh mục không được để trống.']);
            exit;
        }
        if (mb_strlen($name) < 2 || mb_strlen($name) > 50) {
            echo json_encode(['status' => 'error', 'message' => 'Tên danh mục phải từ 2 đến 50 ký tự.']);
            exit;
        }
        if (!preg_match('/^[\p{L}0-9\s\-\_]+$/u', $name)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên danh mục không được chứa ký tự đặc biệt.']);
            exit;
        }

        // Kiểm tra trùng tên
        if ($this->newsCategoryModel->existsByName($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên danh mục này đã tồn tại trong hệ thống.']);
            exit;
        }

        $result = $this->newsCategoryModel->create($name);
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Thêm mới thành công.' : 'Lỗi thực thi truy vấn.'
        ]);
        exit;
    }
    public function handleUpdateCategory(): void
    {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $id = (int) ($_POST['id'] ?? 0);
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        // --- VALIDATION GIỐNG HÀM CREATE ---
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID danh mục không hợp lệ.']);
            exit;
        }
        if (empty($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên danh mục không được để trống.']);
            exit;
        }
        if (mb_strlen($name) < 2 || mb_strlen($name) > 50) {
            echo json_encode(['status' => 'error', 'message' => 'Tên danh mục từ 2 đến 50 ký tự.']);
            exit;
        }
        if (!preg_match('/^[\p{L}0-9\s\-\_]+$/u', $name)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên danh mục chứa ký tự lạ.']);
            exit;
        }

        // Chặn trùng nhưng loại trừ chính cái ID đang sửa
        if ($this->newsCategoryModel->existsByName($name, $id)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên này bị trùng với một danh mục khác rồi!']);
            exit;
        }

        $result = $this->newsCategoryModel->update($id, $name);
        echo json_encode(['status' => $result ? 'success' : 'error']);
        exit;
    }
    public function deleteCategory(): void
    {
        // Dọn dẹp bộ đệm đầu ra để loại bỏ các ký tự lạ hoặc Warning phát sinh trước đó
        if (ob_get_length())
            ob_clean();

        header('Content-Type: application/json');

        try {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID danh mục không hợp lệ.']);
                exit;
            }
            $category = $this->newsCategoryModel->getById($id);
            if (!$category) {
                echo json_encode(['status' => 'error', 'message' => 'Danh mục không tồn tại hoặc đã bị xóa trước đó.']);
                exit;
            }
            // Kiểm tra ràng buộc
            $postCount = $this->newsCategoryModel->countPostsByCategoryId($id);

            if ($postCount > 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => "Danh mục hiện có $postCount bài viết. Bạn không thể xóa."
                ]);
                exit;
            }

            $result = $this->newsCategoryModel->delete($id);

            if ($result) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi truy vấn cơ sở dữ liệu.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit; // Kết thúc ngay lập tức để không chạy thêm code bên dưới
    }
    public function categories(): void
    {
        AuthMiddleware::requireAdmin();

        $search = isset($_GET['search']) ? trim(htmlspecialchars($_GET['search'])) : '';

        $allowedSort = ['name_asc', 'name_desc', 'newest', 'oldest'];
        $sort = $_GET['sort'] ?? 'name_asc';
        if (!in_array($sort, $allowedSort)) {
            $sort = 'name_asc'; // Trả về mặc định nếu user nhập bậy
        }
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1)
            $page = 1;

        $limit = 10; // Mỗi trang 10 dòng
        $offset = ($page - 1) * $limit;


        $categories = $this->newsCategoryModel->getAllPaginated($search, $sort, $limit, $offset);
        $totalCategories = $this->newsCategoryModel->countAll($search);
        $totalPages = ceil($totalCategories / $limit);
        if ($page > $totalPages && $totalPages > 0) {
            $this->redirect($this->baseUrl("admin/categories?page=$totalPages&search=$search&sort=$sort"));
            exit;
        }

        $this->view('admin/pages/categories', [
            'title' => 'Quản lý danh mục',
            'categories' => $categories,
            'totalPages' => ceil($totalCategories / $limit), // Biến này quan trọng nè!
            'currentPage' => $page                            // Biến này cũng quan trọng nè!
        ], 'admin/layouts/main');
    }
    public function getCategoryJson(): void
    {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? null;
        if ($id) {

            $category = $this->newsCategoryModel->getById((int) $id);
            if ($category) {
                echo json_encode($category);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy danh mục!']);
            }
        }
        exit;
    }
    public function posts(): void
    {
        AuthMiddleware::requireAdmin();

        // Validate Filters
        $filters = [
            'search' => isset($_GET['search']) ? trim(htmlspecialchars($_GET['search'])) : '',
            'category' => (int) ($_GET['category'] ?? 0), // Ép kiểu số nguyên
            'sort' => $_GET['sort'] ?? 'newest'
        ];
        if (!in_array($filters['sort'], ['newest', 'oldest', 'title_asc'])) {
            $filters['sort'] = 'newest';
        }

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $newsList = $this->newsModel->getNews($filters, $limit, $offset);
        $totalNews = $this->newsModel->countNews($filters);


        $categories = $this->newsCategoryModel->getAll();

        $this->view('admin/pages/posts', [
            'title' => 'Quản lý tin tức',
            'news' => $newsList,
            'categories' => $categories, // Truyền sang View nè
            'totalPages' => ceil($totalNews / $limit),
            'currentPage' => $page,
            'totalNews' => $totalNews
        ], 'admin/layouts/main');
    }

    public function deletePost(): void
    {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? null;
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ.']);
            exit;
        }
        if ($id) {

            $post = $this->newsModel->getPostById((int) $id);
            if (!$post) {
                echo json_encode(['status' => 'error', 'message' => 'Bài viết không tồn tại.']);
                exit;
            }

            if ($post) {

                $result = $this->newsModel->deletePost((int) $id);

                if ($result) {

                    $imageName = $post['image'];
                    if ($imageName && $imageName !== 'default-news.png') {

                        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/news/';
                        $filePath = $uploadDir . $imageName;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                    echo json_encode(['status' => 'success', 'message' => 'Đã xóa bài viết và ảnh thành công']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Lỗi xóa Database']);
                }
            }
        }
        exit;
    }
    public function handleCreatePost(): void
    {
        AuthMiddleware::requireAdmin();

        if (ob_get_length())
            ob_clean();
        header('Content-Type: application/json');

        try {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $slug = trim($_POST['slug'] ?? '');

            // SEO bổ sung
            $meta_keywords = trim($_POST['keywords'] ?? '');
            $meta_description = trim($_POST['meta_description'] ?? '');

            // --- SERVER-SIDE VALIDATION ---
            if (mb_strlen($title) < 10 || mb_strlen($title) > 255) {
                echo json_encode(['status' => 'error', 'message' => 'Tiêu đề quá ngắn (tối thiểu 10 ký tự).']);
                exit;
            }
            if (empty($content)) {
                echo json_encode(['status' => 'error', 'message' => 'Nội dung bài viết không được để trống.']);
                exit;
            }
            if ($categoryId <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Vui lòng chọn danh mục hợp lệ.']);
                exit;
            }
            if (mb_strlen($meta_description) > 160) { // Chuẩn SEO meta description
                echo json_encode(['status' => 'error', 'message' => 'Mô tả SEO không nên vượt quá 160 ký tự.']);
                exit;
            }
            if (mb_strlen($meta_keywords) > 255) {
                echo json_encode(['status' => 'error', 'message' => 'Từ khóa SEO quá dài (tối đa 255 ký tự).']);
                exit;
            }

            // --- XỬ LÝ UPLOAD HÌNH ẢNH ---
            $file = $_FILES['thumbnail'] ?? null;
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $fileSize = $file['size'] / 1024 / 1024; // MB
                if ($fileSize > 2) {
                    echo json_encode(['status' => 'error', 'message' => 'Ảnh quá nặng (tối đa 2MB).']);
                    exit;
                }

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    echo json_encode(['status' => 'error', 'message' => 'Định dạng ảnh không hỗ trợ (chỉ nhận JPG, PNG, WEBP).']);
                    exit;
                }
            }
            $imageName = 'default-news.png';

            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($ext, $allowed)) {
                    // Tạo tên file ngẫu nhiên để không trùng
                    $newFileName = 'news_' . time() . '_' . rand(100, 999) . '.' . $ext;

                    $uploadDir = dirname(__DIR__, 2) . '/public/uploads/news/';

                    // Tự động tạo thư mục nếu chưa có
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    // Thực hiện di chuyển file từ bộ nhớ tạm vào thư mục web
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
                        $imageName = $newFileName;
                    }
                }
            }
            $slug = $this->newsModel->createUniqueSlug($title);
            // 2. CHUẨN BỊ DỮ LIỆU ĐỂ LƯU DB
            $data = [
                'title' => $title,
                'content' => $content,
                'category_id' => $categoryId,
                'slug' => $slug,
                'image' => $imageName,
                'keywords' => $meta_keywords,
                'meta_description' => $meta_description,
                'admin_id' => $_SESSION['user_id']
            ];

            $result = $this->newsModel->createPost($data);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Đã đăng bài viết thành công!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi lưu dữ liệu vào Database.']);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Sự cố hệ thống: ' . $e->getMessage()]);
        }

        exit;
    }
    public function getPostDetailFull(): void
    {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $id = (int) ($_GET['id'] ?? 0);

        $post = $this->newsModel->getPostByIdWithJoin($id);

        if ($post) {
            echo json_encode($post);
        } else {
            echo json_encode(['status' => 'error']);
        }
        exit;
    }
    public function getPostJson(): void
    {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? null;
        if ($id) {
            $post = $this->newsModel->getPostById((int) $id);
            echo json_encode($post);
        }
        exit;
    }
    public function handleUpdatePost(): void
    {
        AuthMiddleware::requireAdmin();
        if (ob_get_length())
            ob_clean();
        header('Content-Type: application/json');

        $id = (int) $_POST['id'];
        $oldPost = $this->newsModel->getPostById($id);
        $imageName = $oldPost['image'] ?? 'default-news.png';
        if (!$oldPost) {
            echo json_encode(['status' => 'error', 'message' => 'Bài viết không tồn tại để cập nhật.']);
            exit;
        }
        $title = trim($_POST['title'] ?? '');
        $status = trim($_POST['status'] ?? 'published');
        $content = trim($_POST['content'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $meta_keywords = trim($_POST['keywords'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        // --- SERVER-SIDE VALIDATION (COPY TỪ CREATE) ---
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID bài viết không hợp lệ.']);
            exit;
        }
        if (mb_strlen($title) < 10 || mb_strlen($title) > 255) {
            echo json_encode(['status' => 'error', 'message' => 'Tiêu đề từ 10-255 ký tự.']);
            exit;
        }
        if (empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Nội dung không được để trống.']);
            exit;
        }
        if ($categoryId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Danh mục không hợp lệ.']);
            exit;
        }
        if (!in_array($status, ['published', 'archived'])) {
            $status = 'published';
        }
        if (mb_strlen($meta_description) > 160) {
            echo json_encode(['status' => 'error', 'message' => 'Mô tả SEO tối đa 160 ký tự.']);
            exit;
        }
        if (mb_strlen($meta_keywords) > 255) {
            echo json_encode(['status' => 'error', 'message' => 'Từ khóa SEO quá dài (tối đa 255 ký tự).']);
            exit;
        }

        $file = $_FILES['thumbnail'] ?? null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $newImageName = 'news_' . time() . '.' . $ext;

                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/news/';

                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0755, true);

                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newImageName)) {

                    if ($imageName && $imageName !== 'default-news.png') {
                        $oldPath = $uploadDir . $imageName;
                        if (file_exists($oldPath))
                            unlink($oldPath);
                    }
                    $imageName = $newImageName;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Lỗi di chuyển file vào thư mục.']);
                    exit;
                }
            }
        }

        $slug = $this->newsModel->createUniqueSlug($title, $id);

        if (empty($slug)) {
            $slug = 'post-' . time();
        }

        $data = [
            'title' => $title,
            'content' => trim($_POST['content'] ?? ''),
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'slug' => $slug,
            'image' => $imageName,
            'admin_id' => $_SESSION['user_id'],
            'meta_description' => trim($_POST['meta_description'] ?? ''),
            'keywords' => trim($_POST['keywords'] ?? ''),
            'status' => $status
        ];

        $result = $this->newsModel->updatePost($id, $data);
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Cập nhật thành công!' : 'Lỗi cập nhật Database'
        ]);
        exit;
    }


    public function toggleCommentStatus(): void
    {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $commentId = $_POST['id'] ?? null;
        if ($commentId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID bình luận không hợp lệ.']);
            exit;
        }
        $comment = $this->commentModel->getCommentById($commentId);
        if (!$comment) {
            echo json_encode(['status' => 'error', 'message' => 'Bình luận không tồn tại.']);
            exit;
        }

        $result = $this->commentModel->toggleCommentStatus($commentId);
        echo json_encode(['status' => $result ? 'success' : 'error']);
        exit;
    }

    public function getCommentJson(): void
    {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? null;
        if ($id) {

            $comment = $this->commentModel->getCommentById((int) $id);
            echo json_encode($comment);
        }
        exit;
    }

    public function comments(): void
    {
        AuthMiddleware::requireAdmin();
        $newsId = isset($_GET['news_id']) ? (int) $_GET['news_id'] : 0;

        if ($newsId > 0) {
            $article = $this->newsModel->getPostById($newsId);
            if (!$article) {
                $this->redirect($this->baseUrl('admin/comments'));
                exit;
            }

            // Bổ sung logic lọc & phân trang cho trang chi tiết
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $search = trim(htmlspecialchars($_GET['search'] ?? ''));
            $status = in_array($_GET['status'] ?? '', ['visible', 'hidden']) ? $_GET['status'] : '';

            // Lấy cmt theo filter
            $comments = $this->commentModel->getCommentsByNewsWithFilters($newsId, $search, $status, $offset, $limit);

            // Đếm tổng root comments để phân trang
            $totalRoots = $this->commentModel->countRootCommentsWithFilter($newsId, $search, $status);

            $this->view('admin/pages/comments_detail', [
                'title' => 'Bình luận: ' . $article['title'],
                'comments' => $comments,
                'article' => $article,
                'newsId' => $newsId,
                'totalPages' => ceil($totalRoots / $limit),
                'currentPage' => $page,
                'search' => $search,
                'status' => $status
            ], 'admin/layouts/main');
        } else {

            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;
            $search = $_GET['search'] ?? '';

            $posts = $this->newsModel->getNews(['search' => $search], $limit, $offset);
            $totalPosts = $this->newsModel->countNews(['search' => $search]);

            $this->view('admin/pages/comments_list', [
                'title' => 'Chọn bài viết để quản lý bình luận',
                'posts' => $posts,
                'totalPages' => ceil($totalPosts / $limit),
                'currentPage' => $page,
                'search' => $search
            ], 'admin/layouts/main');
        }
    }
    public function deleteComment(): void
    {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ.']);
            exit;
        }
        $comment = $this->commentModel->getCommentById($id);
        if (!$comment) {
            echo json_encode(['status' => 'error', 'message' => 'Bình luận không tồn tại.']);
            exit;
        }

        $result = $this->commentModel->deleteComment($id, 0, true);
        echo json_encode(['status' => $result ? 'success' : 'error']);
        exit;
    }
    public function handlePostComment(): void
    {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $newsId = (int) $_POST['news_id'];
        $content = trim($_POST['content'] ?? '');
        $parentId = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;

        if (empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Nội dung không được để trống!']);
            exit;
        }
        if ($newsId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Bài viết không hợp lệ.']);
            exit;
        }
        if (empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Nội dung không được để trống.']);
            exit;
        }
        if (mb_strlen($content) > 1000) {
            echo json_encode(['status' => 'error', 'message' => 'Bình luận quá dài (tối đa 1000 ký tự).']);
            exit;
        }

        $result = $this->commentModel->addComment($newsId, $_SESSION['user_id'], $content, $parentId);

        echo json_encode(['status' => $result ? 'success' : 'error']);
        exit;
    }
    public function handleUpdateComment(): void
    {
        AuthMiddleware::requireAdmin();
        header('Content-Type: application/json');

        $commentId = (int) $_POST['id'];
        $content = trim($_POST['content'] ?? '');

        // VALIDATION ĐỒNG BỘ VỚI POST
        if ($commentId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ.']);
            exit;
        }
        if (empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Nội dung không được để trống.']);
            exit;
        }
        if (mb_strlen($content) > 1000) {
            echo json_encode(['status' => 'error', 'message' => 'Bình luận quá dài.']);
            exit;
        }
        $result = $this->commentModel->updateComment($commentId, $_SESSION['user_id'], $content);

        echo json_encode(['status' => $result ? 'success' : 'error']);
        exit;
    }
    public function contacts(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/contacts', ['title' => 'Quản lý liên hệ'], 'admin/layouts/main');
    }

    public function qa(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/qa', ['title' => 'Quản lý hỏi đáp'], 'admin/layouts/main');
    }

    public function profile(): void
    {
        AuthMiddleware::requireAdmin();

        $user = $this->userModel->findById($_SESSION['user_id']);
        $this->view('admin/pages/profile', [
            'title' => 'My Profile',
            'user' => $user,
        ], 'admin/layouts/main');
    }

    public function updateProfile(): void
    {
        AuthMiddleware::requireAdmin();

        $userId = $_SESSION['user_id'];

        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'gender' => trim($_POST['gender'] ?? ''),
            'birth_date' => trim($_POST['birth_date'] ?? ''),
        ];

        $file = $_FILES['avatar'] ?? null;
        $imageName = null;

        $currentUser = $this->userModel->findById($userId);
        $oldImage = $currentUser['image'] ?? null;

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed, true)) {
                $this->setFlash('error', 'File khong hop le');
                $this->redirect($this->baseUrl('admin/profile'));
            }

            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uniphin2/backend/public/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                $imageName = $fileName;

                if ($oldImage) {
                    $oldFile = $uploadDir . $oldImage;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
            }
        }

        $this->userModel->updateProfile($userId, $data, $imageName);

        $_SESSION['name'] = $data['first_name'] . ' ' . $data['last_name'];

        $this->setFlash('success', 'Cap nhat ho so thanh cong!');
        $this->redirect($this->baseUrl('admin/profile'));
    }

    public function homepage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/homepage', ['title' => 'Home Page'], 'admin/layouts/main');
    }

    public function contactpage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/contactpage', ['title' => 'Contact Page'], 'admin/layouts/main');
    }

    public function aboutpage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/aboutpage', ['title' => 'About Page'], 'admin/layouts/main');
    }

    public function faqspage(): void
    {
        AuthMiddleware::requireAdmin();
        $this->view('admin/pages/faqpage', ['title' => 'FAQ Page'], 'admin/layouts/main');
    }
}