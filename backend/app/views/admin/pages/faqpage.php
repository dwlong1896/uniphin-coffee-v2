<div class="row">
    <div class="col-12 mt-5">

        <!-- Flash messages -->
        <?php if (!empty($flashSuccess)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-1">Quản lý nội dung trang Hỏi đáp (FAQ)</h4>
                <p class="text-muted mb-4">Thêm / sửa / xóa các câu hỏi thường gặp.</p>

                <!-- Form thêm câu hỏi -->
                <div class="border p-3 mb-4 rounded bg-light">
                    <h5>Thêm câu hỏi mới</h5>
                    <form action="<?php echo $toUrl('admin/faq/save'); ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label">Câu hỏi</label>
                            <input type="text" name="question" class="form-control" placeholder="Nhập câu hỏi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trả lời</label>
                            <textarea name="answer" class="form-control" rows="3" placeholder="Nhập câu trả lời" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thứ tự (sắp xếp)</label>
                            <input type="number" name="display_order" class="form-control" min="0" max="999" value="999" placeholder="999">
                            <small class="text-muted">Số nhỏ hiển thị trước</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti-plus"></i> Thêm câu hỏi
                        </button>
                    </form>
                </div>

                <!-- Danh sách câu hỏi hiện có -->
                <?php if (!empty($faqs)): ?>
                    <?php foreach ($faqs as $faq): ?>
                        <div class="card mb-3 border">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <strong><?php echo htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo $toUrl('admin/faq/edit/' . (int)$faq['id']); ?>"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="ti-pencil"></i> Sửa
                                    </a>
                                    <a href="<?php echo $toUrl('admin/faq/delete/' . (int)$faq['id']); ?>"
                                        class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Chắc chắn xóa câu hỏi này?')">
                                        <i class="ti-trash"></i> Xóa
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p><?php echo htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <small class="text-muted">Thứ tự: <?php echo (int)$faq['display_order']; ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <p>Chưa có câu hỏi. Hãy thêm câu hỏi đầu tiên!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>