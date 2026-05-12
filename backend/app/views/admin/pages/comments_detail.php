<?php
/**
 * 1. HELPER & ĐỆ QUY ADMIN (STYLE SRTDASH)
 */
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};
function renderAdminCommentsRecursive($comments, $parentId = null, $level = 0, $toUrl, $filterStatus = '', $searchKeyword = '')
{
    $children = array_filter($comments, function ($c) use ($parentId) {
        if ($parentId === null)
            return $c['parent_comment_id'] === null;
        return (int) $c['parent_comment_id'] === (int) $parentId;
    });

    foreach ($children as $cmt) {

        if (!empty($filterStatus) && $cmt['status'] !== $filterStatus) {
            continue;
        }
        $isMatch = empty($searchKeyword) || mb_strpos(mb_strtolower($cmt['content']), mb_strtolower($searchKeyword)) !== false;

        if (!$isMatch) {

            renderAdminCommentsRecursive($comments, $cmt['ID'], $level + 1, $toUrl, $filterStatus, $searchKeyword);
            continue;
        }
        $isHidden = ($cmt['status'] === 'hidden');
        $childCount = count(array_filter($comments, fn($c) => (int) $c['parent_comment_id'] === (int) $cmt['ID'] && (empty($searchKeyword) || mb_strpos(mb_strtolower($c['content']), mb_strtolower($searchKeyword)) !== false)));
        $paddingStep = ($level > 0) ? 30 : 0;
        $opacityClass = $isHidden ? 'comment-hidden' : '';
        ?>
        <div class="comment-item-wrap <?= $opacityClass ?>" id="comment-wrap-<?= $cmt['ID'] ?>"
            style="margin-left: <?= $paddingStep ?>px; border-left: <?= ($level > 0) ? '2px solid #ebf0ff' : 'none' ?>; padding-left: <?= ($level > 0) ? '15px' : '0' ?>;">

            <div class="d-flex align-items-start py-3 mb-2">
                <div class="avatar-wrapper mr-3">
                    <img src="<?= $toUrl('uploads/' . (!empty($cmt['user_avatar']) ? $cmt['user_avatar'] : 'default-avatar.png')) ?>"
                        class="avatar-img shadow-sm" onerror="this.src='<?= $toUrl('uploads/default-avatar.png') ?>'">
                </div>

                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <?= htmlspecialchars($cmt['first_name'] . ' ' . $cmt['last_name']) ?>
                            <?php if ($isHidden): ?>
                                <span class="badge badge-secondary ml-2" style="font-size: 8px;">ĐANG ẨN</span>
                            <?php endif; ?>
                        </h6>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle no-caret" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fa fa-ellipsis-v" style="color: #a4a4ba;"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <a class="dropdown-item small font-weight-bold py-2" href="javascript:void(0)"
                                    onclick="showReplyForm(<?= $cmt['ID'] ?>, '<?= $cmt['first_name'] ?>')">
                                    <i class="fa fa-reply mr-2"></i> Phản hồi
                                </a>
                                <a class="dropdown-item small font-weight-bold py-2" href="javascript:void(0)"
                                    onclick="toggleCommentStatus(<?= $cmt['ID'] ?>)">
                                    <i class="fa <?= $isHidden ? 'fa-eye' : 'fa-eye-slash' ?> mr-2"></i>
                                    <?= $isHidden ? 'Hiện bình luận' : 'Ẩn bình luận' ?>
                                </a>
                                <?php if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $cmt['User_ID']): ?>
                                    <a class="dropdown-item small font-weight-bold py-2" href="javascript:void(0)"
                                        onclick="showEditForm(<?= $cmt['ID'] ?>)" style="color: #666;">
                                        <i class="fa fa-edit mr-2"></i> Sửa bình luận
                                    </a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item small font-weight-bold py-2 text-danger" href="javascript:void(0)"
                                    onclick="deleteComment(<?= $cmt['ID'] ?>)">
                                    <i class="fa fa-trash mr-2"></i> Xóa vĩnh viễn
                                </a>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted italic"><?= date('H:i - d/m/Y', strtotime($cmt['created_at'])) ?></small>
                    <p class="mt-2 mb-2 text-dark" id="content-text-<?= $cmt['ID'] ?>">
                        <?= nl2br(htmlspecialchars($cmt['content'])) ?>
                    </p>

                    <div class="action-links">
                        <?php if ($childCount > 0): ?>
                            <a href="javascript:void(0)" onclick="toggleThread(<?= $cmt['ID'] ?>)" id="btn-thread-<?= $cmt['ID'] ?>"
                                class="text-info font-weight-bold small">
                                <i class="fa fa-chevron-up mr-1"></i> Thu gọn <?= $childCount ?> phản hồi
                            </a>
                        <?php endif; ?>
                    </div>

                    <div id="reply-form-<?= $cmt['ID'] ?>" class="d-none mt-3">
                        <div class="input-group shadow-sm">
                            <input type="text" id="reply-text-<?= $cmt['ID'] ?>" class="form-control form-control-sm"
                                placeholder="Nhập nội dung trả lời...">
                            <div class="input-group-append">
                                <button class="btn btn-primary btn-sm" onclick="submitReply(<?= $cmt['ID'] ?>)">Gửi</button>
                            </div>
                        </div>
                    </div>

                    <div id="thread-<?= $cmt['ID'] ?>" class="mt-2">
                        <?php renderAdminCommentsRecursive($comments, $cmt['ID'], $level + 1, $toUrl, $filterStatus); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
<link rel="stylesheet" href="<?= $assetUrl('css/comments_details.css?v=' . time()) ?>">

<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row align-items-center">
                        <input type="hidden" name="news_id" value="<?= $newsId ?>">

                        <div class="col-md-7 mb-3 mb-md-0">
                            <label class="font-weight-bold text-muted small text-uppercase mb-2">Tìm kiếm thảo
                                luận</label>
                            <div class="search-box-srtdash-new">
                                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                    placeholder="Nhập từ khóa và Enter...">
                                <i class="fa fa-search" onclick="this.closest('form').submit()"></i>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label class="font-weight-bold text-muted small text-uppercase mb-2">Trạng thái</label>
                            <select name="status" class="custom-select-srtdash shadow-none"
                                onchange="this.form.submit()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="presented" <?= ($status == 'presented') ? 'selected' : '' ?>>Đang hiển thị
                                </option>
                                <option value="hidden" <?= ($status == 'hidden') ? 'selected' : '' ?>>Đang bị ẩn</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h4 class="header-title mb-0">Bình luận bài viết: <span
                                class="text-primary"><?= htmlspecialchars($article['title']) ?></span></h4>
                        <a href="<?= $toUrl('admin/comments') ?>"
                            class="btn btn-light btn-sm font-weight-bold shadow-sm">
                            <i class="fa fa-arrow-left mr-1"></i> Quay lại
                        </a>
                    </div>

                    <div class="card mb-5 border-0 bg-light shadow-sm" style="border-radius: 15px;">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-3 text-uppercase small text-secondary">
                                <i class="fa fa-pencil mr-2 text-primary"></i>Viết bình luận mới
                            </h6>

                            <div class="d-flex flex-column">

                                <textarea id="admin-main-comment"
                                    class="form-control border-light shadow-none bg-white rounded-lg p-3 mb-3" rows="3"
                                    style="resize: none; border-radius: 12px;"
                                    placeholder="Nhập nội dung thảo luận của bạn tại đây..."></textarea>

                                <button class="btn btn-primary align-self-end px-5 font-weight-bold shadow-sm"
                                    onclick="submitAdminMainComment()"
                                    style="height: 45px; border-radius: 10px; min-width: 130px;">
                                    <i class="fa fa-paper-plane mr-2"></i>ĐĂNG BÌNH LUẬN
                                </button>

                            </div>
                        </div>
                    </div>
                    <div class="comment-list-section">
                        <?php if (empty($comments)): ?>
                            <p class="text-center py-5 text-muted italic">Chưa có bình luận nào khớp với bộ lọc.</p>
                        <?php else: ?>
                            <?php

                            renderAdminCommentsRecursive($comments, null, 0, $toUrl, $status, $search);
                            ?>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($totalPages) && $totalPages > 1): ?>
                        <div class="mt-5 d-flex justify-content-center">
                            <nav>
                                <ul class="pagination pagination-md">
                                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?news_id=<?= $newsId ?>&page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>">Trước</a>
                                    </li>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                            <a class="page-link"
                                                href="?news_id=<?= $newsId ?>&page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?news_id=<?= $newsId ?>&page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>">Sau</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="edit-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content card">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold">Chỉnh sửa bình luận của tôi</h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-comment-id">
                <textarea id="edit-comment-content" maxlength="1000" class="form-control border" rows="5"
                    style="border-radius: 12px;"></textarea>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light btn-sm font-weight-bold" data-dismiss="modal">HỦY</button>
                <button type="button" class="btn btn-primary btn-sm px-4 font-weight-bold"
                    onclick="saveEditComment()">LƯU THAY ĐỔI</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const API_URL = '<?= $toUrl('admin/comments/') ?>';
    const NEWS_ID = <?= (int) $newsId ?>;
</script>

<script src="<?= $assetUrl('js/comments_details.js?v=' . time()) ?>"></script>