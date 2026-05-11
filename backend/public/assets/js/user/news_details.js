// 1. CÁC HÀM XỬ LÝ NÚT BẤM (Dùng window. để HTML nhìn thấy hàm)
window.toggleThread = function (id) {
  const thread = document.getElementById("thread-" + id);
  const btn = document.getElementById("btn-thread-" + id);
  if (!thread || !btn) return;

  if (thread.classList.contains("hidden")) {
    thread.classList.remove("hidden");
    btn.innerHTML = `<i class="fas fa-chevron-up mr-1"></i> Thu gọn phản hồi`;
  } else {
    thread.classList.add("hidden");
    btn.innerHTML = `<i class="fas fa-chevron-down mr-1"></i> Xem phản hồi`;
    const wrap = document.getElementById("comment-wrap-" + id);
    if (wrap) wrap.scrollIntoView({ behavior: "smooth", block: "start" });
  }
};

window.replyWithTag = function (id, userName) {
  const f = document.getElementById("reply-form-" + id);
  if (!f) return;
  f.classList.remove("hidden");
  const textarea = f.querySelector("textarea");
  if (textarea) {
    textarea.value = "";
    textarea.placeholder = "Đang trả lời " + userName + "...";
    textarea.focus();
  }
};

window.showEditForm = function (id) {
  const textElement = document.getElementById("content-text-" + id);
  if (textElement) {
    document.getElementById("edit-comment-id").value = id;
    document.getElementById("edit-comment-content").value = textElement.innerText.trim();
    document.getElementById("edit-modal").classList.remove("hidden");
  }
};

window.closeEditModal = function () {
  document.getElementById("edit-modal").classList.add("hidden");
};

window.deleteComment = async function (id) {
  const result = await Swal.fire({
    title: "Xác nhận xóa?",
    text: "Xóa sẽ mất nội dung!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    confirmButtonText: "Xóa!",
    cancelButtonText: "Hủy"
  });

  if (result.isConfirmed) {
    const fd = new FormData();
    fd.append("action", "delete");
    fd.append("comment_id", id);

    try {
      // SỬ DỤNG BIẾN COMMENT_ACTION_URL ĐÃ KHAI BÁO Ở PHP
      const r = await fetch(COMMENT_ACTION_URL, {
        method: "POST",
        body: fd,
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });
      const res = await r.json();
      if (res.status === "success") {
          location.reload();
      } else {
          Swal.fire("Lỗi!", res.message, "error");
      }
    } catch (err) {
      console.error(err);
      Swal.fire("Lỗi!", "Không thể kết nối máy chủ!", "error");
    }
  }
};

// 2. HÀM XỬ LÝ AJAX SUBMIT (ĐĂNG / SỬA)
async function handleAjax(form) {
  const textarea = form.querySelector("textarea");
  const content = textarea ? textarea.value.trim() : "";

  if (content === "") {
    Swal.fire({
      icon: "warning",
      title: "Nội dung trống!",
      text: "Bà chưa nhập gì hết trơn kìa!",
      confirmButtonColor: "#00aeef",
    });
    return;
  }

  if (content.length > 1000) {
    Swal.fire({
      icon: "warning",
      title: "Quá dài!",
      text: "Bình luận tối đa 1000 ký tự thôi nhen Hiền!",
      confirmButtonColor: "#00aeef",
    });
    return;
  }

  const url = form.getAttribute("action");
  const formData = new FormData(form);

  try {
    const res = await fetch(url, {
      method: "POST",
      body: formData,
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });

    const text = await res.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch (e) {
      console.error("Server trả về lỗi:", text);
      throw new Error("Phản hồi không phải JSON");
    }

    if (data.status === "success") {
      location.reload();
    } else {
      Swal.fire({ icon: "error", title: "Lỗi!", text: data.message });
    }
  } catch (err) {
    console.error(err);
    Swal.fire({ icon: "error", title: "Lỗi!", text: "Gửi bình luận thất bại!" });
  }
}

// 3. GẮN SỰ KIỆN KHI TRANG SẴN SÀNG
$(document).ready(function () {
  $(document).on("submit", ".js-ajax-form", function (e) {
    e.preventDefault();
    handleAjax(this);
  });
});