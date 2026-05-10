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
<link rel="stylesheet" href="<?= $assetUrl('css/comments_list.css?v=' . time()) ?>">

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