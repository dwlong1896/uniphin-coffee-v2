const getCategoryModal = () => bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryModal'));

window.showAddModal = function() {
    $('#categoryForm')[0].reset();
    $('#category_id').val('');
    $('#modalTitle').text('Thêm danh mục mới');
    getCategoryModal().show();
};

window.editCategory = function(id) {
    // API_BASE_URL lấy từ biến Global khai báo ở file PHP
    $.get(API_BASE_URL + 'get-json', { id: id }, function(data) {
        if (data && data.ID) {
            $('#category_id').val(data.ID);
            $('#name_input').val(data.Name);
            $('#modalTitle').text('Chỉnh sửa danh mục');
            getCategoryModal().show();
        }
    }, 'json');
};

window.deleteCategory = function(id, name) {
    swal({
        title: "Xác nhận xóa?",
        text: "Hành động này sẽ gỡ bỏ [" + name + "] khỏi hệ thống.",
        icon: "warning",
        buttons: ["Hủy bỏ", "Xác nhận xóa"],
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.post(API_BASE_URL + 'delete', { id: id }, function(res) {
                if (res.status === 'success') {
                    swal("Thành công!", "Dữ liệu đã được cập nhật.", "success").then(() => location.reload());
                } else {
                    swal("Thông báo", res.message, "info");
                }
            }, 'json');
        }
    });
};

$(document).ready(function() {
    $('#categoryForm').on('submit', function(e) {
        e.preventDefault();
        const name = $('#name_input').val().trim();

        // Check validation
        if (name.length < 2 || name.length > 50) {
            swal("Lỗi!", "Tên danh mục phải từ 2 đến 50 ký tự nhen Hiền!", "error");
            return false;
        }

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
            success: function(res) {
                if (res.status === 'success') {
                    getCategoryModal().hide();
                    swal("Thành công!", "Thông tin đã được lưu lại.", "success").then(() => location.reload());
                } else {
                    swal("Không thể thực hiện", res.message, "error");
                }
            },
            error: function() {
                swal("Lỗi!", "Không thể kết nối máy chủ.", "error");
            }
        });
    });
});