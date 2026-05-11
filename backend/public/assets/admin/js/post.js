// 1. XỬ LÝ XEM CHI TIẾT
window.viewPostDetails = function (id) {
    $.get(API_URL + 'get-post-detail-full', { id: id }, function (data) {
        if (data) {
            let imgName = data.post_image || data.image || 'default-news.png';
            let imgUrl = UPLOADS_URL + imgName;

            let html = `
            <div class="table-responsive">
                <div class="text-center mb-4">
                    <img src="${imgUrl}" class="rounded shadow-sm border" 
                         style="max-width: 100%; height: 200px; object-fit: cover;"
                         onerror="this.src='${DEFAULT_IMG}'">
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

// 2. MỞ MODAL THÊM MỚI
window.openCreateModal = function () {
    $('#postForm')[0].reset();
    $('#post_id').val('');
    $('#modalTitle').text('Viết bài mới');
    $('#preview-img').empty();
    $('#postModal').modal('show');
};

// 3. MỞ MODAL CHỈNH SỬA
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
                $('#preview-img').html(`<img src="${UPLOADS_URL}${data.image}" width="120" class="img-thumbnail">`);
            } else {
                $('#preview-img').empty();
            }
            $('#modalTitle').text('Chỉnh sửa bài viết');
            $('#postModal').modal('show');
        }
    }, 'json');
};

// 4. THAY ĐỔI TRẠNG THÁI (ẨN/HIỆN)
window.togglePostStatus = function (id) {
    const badge = $(`#status-badge-${id}`);
    $.post(API_URL + 'toggle-status', { id: id }, (res) => {
        if (res.status === 'success') {
            location.reload();
        } else {
            swal("Lỗi!", res.message, "error");
        }
    }, 'json').fail(() => {
        swal("Lỗi!", "Không thể kết nối máy chủ", "error");
        location.reload();
    });
};

// 5. XÓA BÀI VIẾT
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

// 6. XỬ LÝ SUBMIT FORM (CREATE & UPDATE)
$(document).ready(function () {
    $('#postForm').on('submit', function (e) {
        e.preventDefault();
        const title = $('#title').val().trim();
        const content = $('#post_content').val().trim();
        const fileInput = document.getElementById('thumbnail');

        // Validation phía client
        if (title.length < 10) {
            swal("Lỗi!", "Tiêu đề phải có ít nhất 10 ký tự nhen Hiền!", "error");
            return false;
        }

        if (content === "") {
            swal("Lỗi!", "Bà chưa viết nội dung kìa!", "error");
            return false;
        }

        if (fileInput && fileInput.files.length > 0) {
            const fileSize = fileInput.files[0].size / 1024 / 1024; // MB
            if (fileSize > 2) {
                swal("Lỗi!", "Ảnh nặng vượt dung lượng cho phép (hơn 2MB)!", "error");
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