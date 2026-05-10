window.submitAdminMainComment = function() {
    const content = $('#admin-main-comment').val().trim();
    if (!content) return swal("Nhắc nhở", "Nội dung không được để trống!", "warning");
    $.post(API_URL + 'post-admin', { news_id: NEWS_ID, content: content, parent_id: null }, (res) => {
        if (res.status === 'success') location.reload();
        else swal("Lỗi!", res.message, "error");
    }, 'json');
};

window.showReplyForm = function(id, name) {
    $('.input-group').closest('.mt-3').addClass('d-none');
    $(`#reply-form-${id}`).removeClass('d-none');
    $(`#reply-text-${id}`).val('').focus().attr('placeholder', 'Đang trả lời ' + name + '...');
};

window.submitReply = function(parentId) {
    const inputElement = document.getElementById('reply-text-' + parentId);
    if (!inputElement) return;
    const content = inputElement.value.trim();
    if (!content) return swal("Nhắc nhở", "Bạn chưa nhập nội dung phản hồi!", "warning");

    $.post(API_URL + 'post-admin', { news_id: NEWS_ID, content: content, parent_id: parentId }, (res) => {
        if (res.status === 'success') location.reload();
        else swal("Lỗi!", res.message, "error");
    }, 'json');
};

window.toggleThread = function(id) {
    $(`#thread-${id}`).toggleClass('d-none');
    const isHidden = $(`#thread-${id}`).hasClass('d-none');
    $(`#btn-thread-${id}`).html(`<i class="fa fa-chevron-${isHidden ? 'down' : 'up'} mr-1"></i> ${isHidden ? 'Xem' : 'Thu gọn'} phản hồi`);
};

window.toggleCommentStatus = function(id) {
    $.post(API_URL + 'toggle', { id: id }, (res) => { if (res.status === 'success') location.reload(); }, 'json');
};

window.deleteComment = function(id) {
    swal({ title: "Xác nhận xóa?", text: "Bình luận sẽ biến mất hoàn toàn!", icon: "warning", buttons: ["Hủy", "Xóa ngay"], dangerMode: true })
        .then((willDelete) => { if (willDelete) $.post(API_URL + 'delete', { id: id }, (res) => { if (res.status === 'success') location.reload(); }, 'json'); });
};

window.showEditForm = function(id) {
    const text = $(`#content-text-${id}`).text().trim();
    $('#edit-comment-id').val(id);
    $('#edit-comment-content').val(text);
    $('#edit-modal').modal('show');
};

window.saveEditComment = function() {
    const id = $('#edit-comment-id').val();
    const content = $('#edit-comment-content').val().trim();
    $.post(API_URL + 'update', { id: id, content: content }, (res) => {
        if (res.status === 'success') location.reload();
    }, 'json');
};