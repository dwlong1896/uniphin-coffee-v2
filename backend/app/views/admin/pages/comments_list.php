<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = $toUrl ?? static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

$posts = $posts ?? [];
$totalPages = $totalPages ?? 1;
$currentPage = $currentPage ?? 1;
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'title_asc';
?>

<style>
    /* 1. Ô SEARCH BO TRÒN, ICON BÊN PHẢI */
    .search-container {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 25px;
        padding: 0 15px 0 20px;
        transition: all 0.3s;
        height: 45px;
    }
    .search-container:focus-within {
        border-color: #007bff;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.1);
    }
    .search-container input {
        border: none !important;
        box-shadow: none !important;
        background: transparent;
        font-size: 14px;
        width: 100%;
        color: #495057;
        outline: none;
    }
    .search-container i {
        color: #6c757d;
        margin-left: 10px;
        cursor: pointer;
    }

    /* 2. DROPDOWN MŨI TÊN TAM GIÁC CHUẨN */
    .custom-dropdown-select {
        height: 45px;
        border: 1px solid #dee2e6;
        border-radius: 10px !important;
        padding: 0 35px 0 15px !important;
        font-size: 14px;
        background-color: #f8f9fa;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 10 6'%3E%3Cpath fill='%23333' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 10px;
        cursor: pointer;
    }

    /* 3. PHÂN TRANG VUÔNG VỨC DÍNH LIỀN */
    .pagination .page-item .page-link {
        padding: 10px 18px;
        margin-left: -1px;
        color: #333;
        border: 1px solid #dee2e6;
        border-radius: 0 !important;
        font-size: 14px;
    }
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    .post-link { color: #0c2233; font-weight: 600; transition: 0.2s; }
    .post-link:hover { color: #007bff; text-decoration: none; }
</style>

<div class="main-content-inner text-dark">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="GET" action="<?= $toUrl('admin/comments') ?>" class="row align-items-center">
                        <div class="col-md-7">
                            <label class="font-weight-bold text-muted small text-uppercase mb-2 d-block">Tìm kiếm bài viết</label>
                            <div class="search-container">
                                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nhập tiêu đề và nhấn Enter...">
                                <i class="fa fa-search" onclick="this.closest('form').submit();"></i>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="font-weight-bold text-muted small text-uppercase mb-2 d-block">Thứ tự hiển thị</label>
                            <select name="sort" class="form-control custom-dropdown-select shadow-none" onchange="this.form.submit()">
                                <option value="title_asc" <?= ($sort == 'title_asc') ? 'selected' : ''; ?>>A đến Z</option>
                                <option value="title_desc" <?= ($sort == 'title_desc') ? 'selected' : ''; ?>>Z đến A</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="header-title mb-4">Quản lý thảo luận</h4>
                    <div class="table-responsive">
                        <table class="table table-hover text-center align-middle">
                            <thead class="bg-light text-uppercase text-muted small font-weight-bold">
                                <tr>
                                    <th style="width: 80px;">Ảnh</th>
                                    <th class="text-left">Tiêu đề bài viết</th>
                                    <th>Số bình luận</th>
                                    <th>Quản lý</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($posts)): foreach ($posts as $p): ?>
                                    <tr>
                                        <td>
                                            <img src="<?= $toUrl('uploads/news/' . (!empty($p['post_image']) ? $p['post_image'] : 'default-news.png')) ?>" 
                                                 width="60" class="rounded shadow-sm" style="height: 40px; object-fit: cover;"
                                                 onerror="this.src='<?= $toUrl('uploads/news/default-news.png') ?>'">
                                        </td>
                                        <td class="text-left">
                                            <a href="<?= $toUrl('admin/comments?news_id=' . $p['ID']) ?>" class="post-link">
                                                <?= htmlspecialchars($p['title']) ?>
                                            </a>
                                        </td>
                                        <td class="font-weight-bold"><?= $p['comment_count'] ?? 0 ?></td>
                                        <td>
                                            <a href="<?= $toUrl('admin/comments?news_id=' . $p['ID']) ?>" class="text-secondary">
                                                <i class="fa fa-chevron-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="4" class="p-5 text-muted">Không tìm thấy dữ liệu bài viết.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1): ?>
                    <div class="mt-4 d-flex justify-content-center">
                        <ul class="pagination">
                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>">Next</a>
                            </li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>