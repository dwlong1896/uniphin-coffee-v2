<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = $toUrl ?? static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

// Giữ nguyên các biến bà truyền từ Controller
$categories = $categories ?? [];
$totalPages = $totalPages ?? 1;
$currentPage = $currentPage ?? 1;
$totalCategories = $totalCategories ?? 0; // Nhớ check Controller truyền biến này nhé
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'name_asc';
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<style>
    /* Ép style phân trang chuẩn mẫu */
    .pagination .page-item .page-link {
        border-radius: 0 !important;
        color: #444;
        padding: 8px 16px;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: #fff !important;
    }

    .pagination .page-item.disabled .page-link {
        background-color: #f4f7f6;
        color: #ccc;
    }

    /* Icon hành động trần */
    .action-icon {
        font-size: 18px;
        transition: 0.3s;
    }

    .action-icon:hover {
        opacity: 0.7;
    }
</style>

<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="GET" action="<?= $toUrl('admin/categories'); ?>" class="row align-items-end">
                        <div class="col-md-5">
                            <label class="col-form-label text-uppercase font-weight-bold text-muted small">Tìm kiếm danh
                                mục</label>

                            <div class="position-relative">
                                <input type="text" name="search"
                                    class="form-control rounded-pill border-light shadow-none pr-5"
                                    style="height: 45px; padding-left: 20px; background-color: #f8f9ff;"
                                    value="<?= htmlspecialchars($search); ?>"
                                    placeholder="Nhập tên danh mục và nhấn Enter...">

                                <i class="fa fa-search position-absolute text-muted"
                                    style="right: 20px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 16px;"
                                    onclick="this.closest('form').submit();"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="col-form-label text-uppercase font-weight-bold small">Thứ tự hiển thị</label>
                            <select name="sort" class="custom-select w-100" style="height: 40px;"
                                onchange="this.form.submit()">
                                <option value="name_asc" <?= ($sort == 'name_asc') ? 'selected' : ''; ?>>Tên: A đến Z
                                </option>
                                <option value="name_desc" <?= ($sort == 'name_desc') ? 'selected' : ''; ?>>Tên: Z đến A
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-success btn-block font-weight-bold"
                                onclick="showAddModal()" style="height: 40px;">
                                <i class="fa fa-plus"></i> Thêm mới
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="header-title mb-4">
                        Quản lý danh mục tin tức
                        <span class="badge badge-pill badge-info text-white ml-2"
                            style="font-size: 14px; vertical-align: middle;">
                            <?= $totalCategories ?> danh mục
                        </span>
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover text-center align-middle">
                            <thead class="bg-light text-uppercase text-muted small">
                                <tr>
                                    <th style="width: 100px;">ID</th>
                                    <th class="text-left">Tên danh mục</th>
                                    <th style="width: 200px;">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($categories)):
                                    foreach ($categories as $cate): ?>
                                        <tr>
                                            <td class="text-muted">#<?= $cate['ID'] ?></td>
                                            <td class="text-left font-weight-bold text-dark">
                                                <?= htmlspecialchars($cate['Name']) ?>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center" style="gap: 20px;">
                                                    <a href="javascript:void(0)" onclick="editCategory(<?= $cate['ID'] ?>)"
                                                        class="text-secondary action-icon" title="Sửa">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="javascript:void(0)"
                                                        onclick="deleteCategory(<?= $cate['ID'] ?>, '<?= htmlspecialchars($cate['Name'], ENT_QUOTES) ?>')"
                                                        class="text-danger action-icon" title="Xóa">
                                                        <i class="ti-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="3" class="p-5 text-muted">Không tìm thấy dữ liệu.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <div class="d-flex justify-content-end mt-4">
                            <nav>
                                <ul class="pagination mb-0">
                                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>">Previous</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                            <a class="page-link"
                                                href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link"
                                            href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>">Next</a>
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

<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="modal-header d-flex align-items-center justify-content-between bg-light">
                <h5 class="modal-title font-weight-bold text-dark" id="modalTitle">Danh mục</h5>
                <button type="button" class="close" data-dismiss="modal"
                    style="font-size: 2rem; font-weight: 300; line-height: 1; border: none; background: none; margin: 0;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="categoryForm">
                <div class="modal-body p-4">
                    <input type="hidden" id="category_id" name="id">
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">Tên danh mục <span
                                class="text-danger">*</span></label>
                        <<input type="text" class="form-control shadow-none" id="name_input" name="name" required
                            minlength="2" maxlength="50" pattern="^[\p{L}0-9\s\-_]+$"
                            placeholder="Ví dụ: Tin công nghệ...">
                            <small class="text-muted">Từ 2-50 ký tự, không chứa ký tự đặc biệt lạ.</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary px-4">Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // TUI GIỮ NGUYÊN TOÀN BỘ LOGIC SCRIPT CŨ CỦA HIỀN NHÉ
    const API_BASE_URL = '<?= $toUrl('admin/categories/'); ?>';

    window.showAddModal = function () {
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
        $('#modalTitle').text('Thêm danh mục mới');
        $('#categoryModal').modal('show');
    };

    window.editCategory = function (id) {
        $.get(API_BASE_URL + 'get-json', { id: id }, function (data) {
            if (data && data.ID) {
                $('#category_id').val(data.ID);
                $('#name_input').val(data.Name);
                $('#modalTitle').text('Chỉnh sửa danh mục');
                $('#categoryModal').modal('show');
            }
        }, 'json');
    };

    window.deleteCategory = function (id, name) {
        swal({
            title: "Xác nhận xóa?",
            text: "Hành động này sẽ gỡ bỏ [" + name + "] khỏi hệ thống.",
            icon: "warning",
            buttons: ["Hủy bỏ", "Xác nhận xóa"],
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.post(API_BASE_URL + 'delete', { id: id }, function (res) {
                    if (res.status === 'success') {
                        swal("Thành công!", "Dữ liệu đã được cập nhật.", "success").then(() => location.reload());
                    } else {
                        swal("Thông báo", res.message, "info");
                    }
                }, 'json');
            }
        });
    };

    $(document).ready(function () {
        $('#categoryForm').on('submit', function (e) {
            e.preventDefault();
            const name = $('#name_input').val().trim();

            // 1. Check trống & độ dài
            if (name.length < 2 || name.length > 50) {
                swal("Lỗi!", "Tên danh mục phải từ 2 đến 50 ký tự nhen Hiền!", "error");
                return false;
            }

            
            const regex = /^[\p{L}0-9\s\-_]+$/u;
          
            const safeRegex = /^[a-zA-Z0-9À-ỹ\s\-_]+$/;
            if (!safeRegex.test(name)) {
                swal("Lỗi!", "Tên danh mục không được chứa ký tự lạ nhen!", "error");
                return false;
            }
            const id = $('#category_id').val();
            const url = id ? API_BASE_URL + 'update' : API_BASE_URL + 'create';
            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        $('#categoryModal').modal('hide');
                        swal("Thành công!", "Thông tin đã được lưu lại.", "success").then(() => location.reload());
                    } else {
                        swal("Không thể thực hiện", res.message, "error");
                    }
                }
            });
        });
    });
</script>