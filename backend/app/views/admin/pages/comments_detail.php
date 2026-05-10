<?php
/**
 * 1. HELPER & ĐỆ QUY ADMIN (STYLE SRTDASH)
 */
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

function renderAdminCommentsRecursive($comments, $parentId = null, $level = 0, $toUrl)
{
    // Lọc con theo cha - Ép kiểu int để chính xác tuyệt đối các cấp
    $children = array_filter($comments, function ($c) use ($parentId) {
        if ($parentId === null)
            return $c['parent_comment_id'] === null;
        return (int) $c['parent_comment_id'] === (int) $parentId;
    });

    foreach ($children as $cmt) {
        $isHidden = ($cmt['status'] === 'hidden');
        $childCount = count(array_filter($comments, fn($c) => (int) $c['parent_comment_id'] === (int) $cmt['ID']));

        // Thụt lề: mỗi cấp vào thêm 30px
        $paddingStep = ($level > 0) ? 30 : 0;
        // Hiệu ứng mờ nếu bị ẩn
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
                        </h6>


                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle no-caret" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false"
                                style="background: transparent; border: none;">
                                <i class="fa fa-ellipsis-v" style="color: #a4a4ba;"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                                style="border-radius: 12px; padding: 8px; min-width: 160px;">
                                <a class="dropdown-item small font-weight-bold py-2" href="javascript:void(0)"
                                    onclick="showReplyForm(<?= $cmt['ID'] ?>, '<?= $cmt['first_name'] ?>')"
                                    style="color: #666;">
                                    <i class="fa fa-reply mr-2" style="color: #a4a4ba;"></i> Phản hồi
                                </a>

                                <a class="dropdown-item small font-weight-bold py-2" href="javascript:void(0)"
                                    onclick="toggleCommentStatus(<?= $cmt['ID'] ?>)" style="color: #666;">
                                    <i class="fa <?= $isHidden ? 'fa-eye' : 'fa-eye-slash' ?> mr-2" style="color: #a4a4ba;"></i>
                                    <?= $isHidden ? 'Hiện bình luận' : 'Ẩn bình luận' ?>
                                </a>

                                <?php if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $cmt['User_ID']): ?>
                                    <a class="dropdown-item small font-weight-bold py-2" href="javascript:void(0)"
                                        onclick="showEditForm(<?= $cmt['ID'] ?>)" style="color: #666;">
                                        <i class="fa fa-edit mr-2" style="color: #a4a4ba;"></i> Sửa bình luận
                                    </a>
                                <?php endif; ?>

                                <div class="dropdown-divider" style="border-top: 1px solid #f0f1f7;"></div>

                                <a class="dropdown-item small font-weight-bold py-2 text-hover-danger" href="javascript:void(0)"
                                    onclick="deleteComment(<?= $cmt['ID'] ?>)" style="color: #666;">
                                    <i class="fa fa-trash mr-2" style="color: #a4a4ba;"></i> Xóa vĩnh viễn
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
                                <i class="fa <?= ($level >= 1) ? 'fa-chevron-down' : 'fa-chevron-up' ?> mr-1"></i>
                                <?= ($level >= 1) ? 'Xem' : 'Thu gọn' ?>             <?= $childCount ?> phản hồi
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

                    <div id="thread-<?= $cmt['ID'] ?>" class="<?= ($level >= 1) ? 'd-none' : '' ?> mt-2">
                        <?php renderAdminCommentsRecursive($comments, $cmt['ID'], $level + 1, $toUrl); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>

<style>
    .avatar-img {
        width: 42px;
        height: 42px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #f0f1f7;
    }

    .comment-item-wrap {
        transition: 0.3s;
        border-radius: 8px;
        padding-right: 10px;
    }

    .comment-item-wrap:hover {
        background-color: #f8f9ff;
    }

    .comment-hidden {
        opacity: 0.5;
        background-color: #fafafa;
    }

    .comment-hidden img {
        filter: grayscale(100%);
    }

    .no-caret::after {
        display: none;
    }

    .dropdown-menu {
        border-radius: 12px;
        padding: 10px;
        min-width: 170px;
        z-index: 1060;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    }

    .filter-round {
        height: 45px;
        border-radius: 25px !important;
        border: 1px solid #e1e6f1;
    }

    .card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.03);
    }

    .btn-primary {
        background-color: #5856d6;
        border-color: #5856d6;
    }

    .search-box-srtdash-new {
        position: relative;
        width: 100%;
    }

    /* Ô nhập liệu OVAL hoàn toàn */
    .search-box-srtdash-new input {
        width: 100%;
        height: 45px;
        padding-left: 25px;
        padding-right: 45px;
        /* Chừa chỗ cho kính lúp nằm ké */
        border-radius: 25px !important;
        /* Tạo hình Oval */
        border: 1px solid #e1e6f1;
        background-color: #f8f9ff;
        font-size: 14px;
        transition: 0.3s;
        outline: none;
    }

    /* Khi rê chuột/click vào ô nhập */
    .search-box-srtdash-new input:focus {
        background-color: #fff;
        border-color: #5856d6;
        box-shadow: 0 0 10px rgba(88, 86, 214, 0.05);
    }

    /* Kính lúp "nhập xác" vào bên trong */
    .search-box-srtdash-new i {
        position: absolute;
        right: 18px;
        /* Đẩy vào trong ô nhập */
        top: 50%;
        transform: translateY(-50%);
        /* Căn giữa dọc */
        color: #a4a4ba;
        cursor: pointer;
        font-size: 15px;
        z-index: 5;
    }

    .search-box-srtdash-new i:hover {
        color: #5856d6;
    }

    .custom-select-srtdash {
        height: 45px;
        width: 100%;
        border-radius: 10px !important;
        /* Bo góc vuông vuông giống mẫu bà gửi */
        background-color: #f8f9ff !important;
        border: 1px solid #e1e6f1 !important;
        padding-left: 20px !important;
        padding-right: 35px !important;
        font-size: 14px;
        color: #333;
        cursor: pointer;

        /* Triệt hạ giao diện mặc định để tự vẽ mũi tên */
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;

        /* Vẽ lại mũi tên tam giác nhỏ gọn bên phải */
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 10 6'%3E%3Cpath fill='%23333' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 15px center !important;
        background-size: 10px !important;

        transition: all 0.2s ease-in-out;
    }

    .custom-select-srtdash:hover {
        background-color: #f1f3f9 !important;
    }

    .custom-select-srtdash:focus {
        background-color: #fff !important;
        border-color: #5856d6 !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(88, 86, 214, 0.1);
    }

    /* Màu tím srtdash */
</style>

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
                            <p class="text-center py-5 text-muted italic">Chưa có bình luận nào cho bài viết này.</p>
                        <?php else: ?>
                            <?php renderAdminCommentsRecursive($comments, null, 0, $toUrl); ?>
                        <?php endif; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
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

    /** 1. ADMIN ĐĂNG BÌNH LUẬN GỐC */
    function submitAdminMainComment() {
        const content = $('#admin-main-comment').val().trim();
        if (!content) return swal("Nhắc nhở", "Nội dung không được để trống!", "warning");
        if (content.length > 1000) {
            return swal("Lỗi!", "Nội dung bình luận quá dài (tối đa 1000 ký tự)!", "error");
        }
        $.post(API_URL + 'post-admin', { news_id: NEWS_ID, content: content, parent_id: null }, (res) => {
            if (res.status === 'success') location.reload();
            else alert(res.message || "Lỗi rồi!");
        }, 'json');
    }

    /** 2. PHẢN HỒI (REPLY) */
    function showReplyForm(id, name) {
        // Đóng các form reply khác cho đỡ rối
        $('.input-group').closest('.mt-3').addClass('d-none');

        $(`#reply-form-${id}`).removeClass('d-none');

        // CHỖ THAY ĐỔI: Xóa sạch nội dung cũ và chỉ placeholder tên thôi
        $(`#reply-text-${id}`).val('').focus();
        $(`#reply-text-${id}`).attr('placeholder', 'Đang trả lời ' + name + '...');
    }

    function submitReply(parentId) {
        // 1. Phải lấy giá trị và gán vào biến 'content' ĐẦU TIÊN
        const inputElement = document.getElementById('reply-text-' + parentId);
        if (!inputElement) return; // Phòng hờ không tìm thấy ô nhập

        const content = inputElement.value.trim();

        // 2. Giờ mới có biến 'content' để check rỗng
        if (!content) {
            return swal("Nhắc nhở", "Bà chưa nhập nội dung phản hồi kìa!", "warning");
        }

        // 3. Giờ mới check độ dài (Không còn lỗi undefined nữa)
        if (content.length > 1000) {
            return swal("Lỗi!", "Nội dung phản hồi dài quá (tối đa 1000 ký tự nhen)!", "error");
        }

        // 4. Mọi thứ OK thì gửi API
        $.post(API_URL + 'post-admin', {
            news_id: NEWS_ID,
            content: content,
            parent_id: parentId
        }, (res) => {
            if (res.status === 'success') {
                location.reload();
            } else {
                swal("Lỗi!", res.message || "Không thể gửi phản hồi.", "error");
            }
        }, 'json');
    }

    /** 3. THAO TÁC CƠ BẢN */
    window.toggleThread = function (id) {
        $(`#thread-${id}`).toggleClass('d-none');
        const isHidden = $(`#thread-${id}`).hasClass('d-none');
        $(`#btn-thread-${id}`).html(`<i class="fa fa-chevron-${isHidden ? 'down' : 'up'} mr-1"></i> ${isHidden ? 'Xem' : 'Thu gọn'} phản hồi`);
    };

    function toggleCommentStatus(id) {
        $.post(API_URL + 'toggle', { id: id }, (res) => { if (res.status === 'success') location.reload(); }, 'json');
    }

    function deleteComment(id) {
        swal({ title: "Xác nhận xóa?", text: "Bình luận và các phản hồi con sẽ biến mất hoàn toàn!", icon: "warning", buttons: ["Hủy", "Xóa ngay"], dangerMode: true })
            .then((willDelete) => { if (willDelete) $.post(API_URL + 'delete', { id: id }, (res) => { if (res.status === 'success') location.reload(); }, 'json'); });
    }

    /** 4. CHỈNH SỬA CỦA ADMIN */
    window.showEditForm = function (id) {
        const text = $(`#content-text-${id}`).text().trim();
        $('#edit-comment-id').val(id);
        $('#edit-comment-content').val(text);
        $('#edit-modal').modal('show');
    };

    window.saveEditComment = function () {
        const id = $('#edit-comment-id').val();
        const content = $('#edit-comment-content').val().trim();
        $.post(API_URL + 'update', { id: id, content: content }, (res) => {
            if (res.status === 'success') location.reload();
        }, 'json');
    };
</script>
