<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = $toUrl ?? static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};
$news = $news ?? [];
$categories = $categories ?? [];
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?= $toUrl('admin/posts') ?>" class="row align-items-end">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label class="col-form-label text-uppercase font-weight-bold text-muted small"> Tìm
                                kiếm</label>
                            <div class="position-relative">
                                <input type="text" name="search"
                                    class="form-control rounded-pill border-light shadow-none pr-5"
                                    style="height: 40px; padding-left: 20px; background-color: #f8f9ff;"
                                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Tiêu đề...">

                                <i class="fa fa-search position-absolute text-muted"
                                    style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;"
                                    onclick="this.closest('form').submit();"></i>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3 mb-md-0">
                            <label class="col-form-label text-uppercase font-weight-bold">Danh mục</label>
                            <select name="category" class="custom-select w-100" style="height: 40px;">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($categories as $cate): ?>
                                    <option value="<?= $cate['ID'] ?>" <?= (($_GET['category'] ?? '') == $cate['ID']) ? 'selected' : '' ?>><?= htmlspecialchars($cate['Name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3 mb-md-0">
                            <label class="col-form-label text-uppercase font-weight-bold">Sắp xếp</label>
                            <select name="sort" class="custom-select w-100" style="height: 40px;">
                                <option value="newest" <?= (($_GET['sort'] ?? '') == 'newest') ? 'selected' : '' ?>>Mới
                                    nhất</option>
                                <option value="oldest" <?= (($_GET['sort'] ?? '') == 'oldest') ? 'selected' : '' ?>>Cũ nhất
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex" style="gap: 8px;">
                                <button type="submit" class="btn btn-primary flex-grow-1 font-weight-bold"
                                    style="height: 40px;">
                                    <i class="fa fa-filter"></i> Lọc
                                </button>
                                <button type="button" class="btn btn-success flex-grow-1 font-weight-bold"
                                    style="height: 40px; white-space: nowrap;" onclick="openCreateModal()">
                                    <i class="fa fa-plus"></i> Viết bài
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Danh Sách Tin Tức

                    </h4>

                    <div class="table-responsive">
                        <table class="table table-hover text-center">
                            <thead class="text-uppercase bg-light">
                                <tr>
                                    <th>Ảnh</th>
                                    <th class="text-left">Tiêu đề bài viết</th>
                                    <th>Danh mục</th>
                                    <th>Trạng thái</th>
                                    <th>Bình luận</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($news as $item): ?>
                                    <tr>
                                        <td><img src="<?= $toUrl('uploads/news/' . ($item['post_image'] ?? 'default-news.png')) ?>"
                                                width="60" class="rounded border shadow-sm"></td>
                                        <td class="text-left">
                                            <div class="font-weight-bold text-dark"><?= htmlspecialchars($item['title']) ?>
                                            </div>
                                            <small class="text-muted"><i class="fa fa-calendar"></i>
                                                <?= date('d/m/Y', strtotime($item['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <span class="text-muted" style="font-size: 14px;">
                                                <i class="fa fa-folder-open-o mr-1"></i>
                                                <?= htmlspecialchars($item['category_name'] ?? 'Chưa phân loại') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($item['status'] === 'published'): ?>
                                                <span class="text-dark" style="font-size: 14px;">
                                                    <i class="fa fa-circle text-success mr-1" style="font-size: 8px;"></i> Hiển
                                                    thị
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted" style="font-size: 14px;">
                                                    <i class="fa fa-circle text-secondary mr-1" style="font-size: 8px;"></i> Lưu
                                                    trữ
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= $toUrl('admin/comments?news_id=' . $item['ID']) ?>"
                                                class="text-primary">
                                                <i class="fa fa-commenting-o"></i> <?= $item['comment_count'] ?? 0 ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center" style="gap: 15px;">
                                                <a href="javascript:void(0)" onclick="viewPostDetails(<?= $item['ID'] ?>)"
                                                    class="text-secondary"><i class="fa fa-eye"></i></a>
                                                <a href="javascript:void(0)" onclick="editPost(<?= $item['ID'] ?>)"
                                                    class="text-secondary"><i class="fa fa-edit"></i></a>
                                                <a href="javascript:void(0)"
                                                    onclick="deletePost(<?= $item['ID'] ?>, '<?= addslashes($item['title']) ?>')"
                                                    class="text-danger"><i class="ti-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (isset($totalPages) && $totalPages > 1): ?>
                        <div class="pagination-area d-flex justify-content-end mt-4">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-md">
                                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&category=<?= $_GET['category'] ?? '' ?>&sort=<?= $_GET['sort'] ?? '' ?>">Previous</a>
                                    </li>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                            <a class="page-link"
                                                href="?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&category=<?= $_GET['category'] ?? '' ?>&sort=<?= $_GET['sort'] ?? '' ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&category=<?= $_GET['category'] ?? '' ?>&sort=<?= $_GET['sort'] ?? '' ?>">Next</a>
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
<div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header d-flex align-items-center justify-content-between">
                <h5 class="modal-title" id="modalTitle">Bài viết</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="font-size: 2rem; font-weight: 300; line-height: 1; color: #000; opacity: .5; cursor: pointer; border: none; background: none; margin: 0;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="postForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="post_id">

                    <div class="form-group">
                        <label class="col-form-label text-uppercase font-weight-bold">Tiêu đề bài viết</label>
                        <input type="text" name="title" id="title" class="form-control"
                            placeholder="Nhập tiêu đề (ít nhất 10 ký tự)" required minlength="10" maxlength="255">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label text-uppercase font-weight-bold">Danh mục</label>
                                <select name="category_id" id="category_id" class="custom-select">
                                    <?php foreach ($categories as $cate): ?>
                                        <option value="<?= $cate['ID'] ?>"><?= htmlspecialchars($cate['Name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label text-uppercase font-weight-bold">Trạng thái bài
                                    viết</label>
                                <select name="status" id="status" class="custom-select">
                                    <option value="published">Công khai (Hiển thị)</option>
                                    <option value="archived">Lưu trữ (Ẩn)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label text-uppercase font-weight-bold">Ảnh minh họa</label>
                                <input type="file" name="thumbnail" id="thumbnail"
                                    class="form-control-file border p-1 w-100" style="border-radius: 5px;"
                                    accept="image/png, image/jpeg, image/jpg, image/webp">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label text-uppercase font-weight-bold">Keywords (SEO)</label>
                                <input type="text" name="keywords" id="keywords" class="form-control"
                                    placeholder="Từ khóa cách nhau bởi dấu phẩy..." maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label text-uppercase font-weight-bold ">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" class="form-control" rows="1"
                                    placeholder="Mô tả SEO..." maxlength="160"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label text-uppercase font-weight-bold">Nội dung bài viết</label>
                        <textarea name="content" id="post_content" class="form-control" rows="8"
                            placeholder="Viết nội dung chi tiết vào đây..." required></textarea>
                    </div>

                    <div id="preview-img" class="text-center mt-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu dữ liệu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewPostModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header d-flex align-items-center justify-content-between border-bottom-0 p-4">
                <h5 class="modal-title font-weight-bold text-dark" id="modalTitle">Thông tin chi tiết bài viết</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="font-size: 2rem; font-weight: 300; line-height: 1; color: #000; opacity: .5; cursor: pointer; border: none; background: none; margin: 0; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="postDetailBody">
            </div>
            <div class="modal-footer border-top-0 p-4">
                <button type="button" class="btn btn-light pr-4 pl-4 font-weight-bold" data-dismiss="modal"
                    style="border-radius: 10px;">Đóng</button>
            </div>
        </div>
    </div>
</div>
<script>
    // KHAI BÁO HÀM NGOÀI ĐỂ HTML NHÌN THẤY
    const API_URL = '<?= $toUrl('admin/posts/') ?>';
    window.viewPostDetails = function (id) {
        $.get(API_URL + 'get-post-detail-full', { id: id }, function (data) {
            if (data) {
                // Lấy tên ảnh từ JSON trả về
                let imgName = data.post_image || data.image || 'default-news.png';
                let imgUrl = `<?= $toUrl('uploads/news/') ?>${imgName}`;

                let html = `
            <div class="table-responsive">
                <div class="text-center mb-4">
                    <img src="${imgUrl}" class="rounded shadow-sm border" 
                         style="max-width: 100%; height: 200px; object-fit: cover;"
                         onerror="this.src='<?= $toUrl('uploads/news/default-news.png') ?>'">
                </div>
                <table class="table table-bordered table-striped" style="color: #333; font-size: 14px;">
        <tbody>
            <tr><th width="30%" class="bg-light font-weight-normal text-muted">ID</th><td>${data.ID}</td></tr>
            <tr><th class="bg-light font-weight-normal text-muted">Tiêu đề</th><td>${data.title}</td></tr>
            <tr><th class="bg-light font-weight-normal text-muted">Slug (URL)</th><td>${data.slug || 'N/A'}</td></tr>
            <tr><th class="bg-light font-weight-normal text-muted">Danh mục</th><td>${data.category_name || 'Chưa phân loại'}</td></tr>
            <tr><th class="bg-light font-weight-normal text-muted">Người đăng</th><td>${data.admin_fname} ${data.admin_lname}</td></tr>
            <tr><th class="bg-light font-weight-normal text-muted">Ngày tạo</th><td>${data.created_at}</td></tr>
            <tr><th class="bg-light font-weight-normal text-muted">Trạng thái</th><td>${data.status}</td></tr>
            <tr><th class="bg-light font-weight-normal text-muted">Mô tả SEO</th><td>${data.meta_description || 'N/A'}</td></tr>
            <tr><th class="bg-light font-weight-normal text-muted">Nội dung</th>
                <td>
                    <div style="max-height: 200px; overflow-y: auto; line-height: 1.6;">
                        ${data.content}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
            </div>`;
                $('#postDetailBody').html(html);
                $('#viewPostModal').modal('show');
            }
        }, 'json');
    };
    window.openCreateModal = function () {
        $('#postForm')[0].reset();
        $('#post_id').val('');
        $('#modalTitle').text('Viết bài mới');
        $('#preview-img').empty();
        $('#postModal').modal('show');
    };

    window.editPost = function (id) {
        $.get(API_URL + 'get-json', { id: id }, function (data) {
            if (data && data.ID) {
                $('#post_id').val(data.ID);
                $('#title').val(data.title);
                $('#category_id').val(data.N_Cate_ID);
                $('#post_content').val(data.content);

                $('#keywords').val(data.keywords);
                $('#meta_description').val(data.meta_description);
                $('#status').val(data.status);

                if (data.image) {

                    $('#preview-img').html(`<img src="<?= $toUrl('uploads/news/') ?>${data.image}" width="120" class="img-thumbnail">`);
                }
                $('#modalTitle').text('Chỉnh sửa bài viết');
                $('#postModal').modal('show');
            }
        }, 'json');
    };
    window.togglePostStatus = function (id) {
        const badge = $(`#status-badge-${id}`);

        $.ajax({
            url: API_URL + 'toggle-status', // Đảm bảo AdminController có hàm toggleStatus
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    // Đổi giao diện badge ngay lập tức cho mượt
                    if (badge.hasClass('badge-success')) {
                        badge.text('Lưu trữ').removeClass('badge-success').addClass('badge-secondary');
                    } else {
                        badge.text('Hiển thị').removeClass('badge-secondary').addClass('badge-success');
                    }
                    // Dùng toastr hoặc thông báo nhỏ cho sang, không nên dùng swal chỗ này vì nó ngắt quãng trải nghiệm
                    console.log("Đã cập nhật trạng thái");
                } else {
                    swal("Lỗi!", res.message, "error");
                    location.reload(); // Lỗi thì load lại để nút gạt về đúng vị trí cũ
                }
            },
            error: function () {
                swal("Lỗi!", "Không thể kết nối máy chủ", "error");
                location.reload();
            }
        });
    };
    window.deletePost = function (id, title) {
        swal({
            title: "Xác nhận xóa?",
            text: "Bạn có chắc chắn muốn xóa bài viết: [" + title + "]?",
            icon: "warning",
            buttons: ["Hủy", "Xác nhận"],
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.post(API_URL + 'delete', { id: id }, function (res) {
                    if (res.status === 'success') {
                        swal("Thành công!", "Bài viết đã được xóa.", "success").then(() => location.reload());
                    } else {
                        swal("Lỗi!", res.message, "error");
                    }
                }, 'json');
            }
        });
    };

    $(document).ready(function () {

        $('#postForm').on('submit', function (e) {
            e.preventDefault();
            const title = $('#title').val().trim();
            const content = $('#post_content').val().trim();
            const fileInput = document.getElementById('thumbnail');
            // 2. Kiểm tra
            if (title.length < 10) {
                swal("Lỗi!", "Tiêu đề phải có ít nhất 10 ký tự nhen Hiền!", "error");
                e.preventDefault(); // Chặn không cho gửi AJAX
                return false;
            }

            if (content === "") {
                swal("Lỗi!", "Bà chưa viết nội dung kìa!", "error");
                e.preventDefault();
                return false;
            }
            if (fileInput && fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size / 1024 / 1024; // Đổi sang MB
                if (fileSize > 2) {
                    swal("Lỗi!", "Ảnh nặng quá (hơn 2MB rồi), bà chọn cái khác nhẹ hơn đi!", "error");
                    return false;
                }
            }

            const id = $('#post_id').val();
            const targetUrl = id ? API_URL + 'update' : API_URL + 'create';
            const formData = new FormData(this);

            $.ajax({
                url: targetUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',

                success: function (res) {
                    if (typeof res === 'string') {
                        try { res = JSON.parse(res); } catch (e) { }
                    }
                    if (res.status === 'success') {
                        $('#postModal').modal('hide');
                        swal("Thành công!", "Thông tin đã được lưu.", "success").then(() => location.reload());
                    } else {
                        swal("Lỗi!", res.message, "error");
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    swal("Lỗi hệ thống!", "Vui lòng kiểm tra lại phản hồi máy chủ.", "error");
                }
            });
        });
    });
</script>