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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="header-title mb-0">Danh sách câu hỏi thường gặp</h4>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#faqModal" onclick="resetFaqForm()">
                        <i class="ti-plus"></i> Thêm mới
                    </button>
                </div>

                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="text-uppercase bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Câu hỏi</th>
                                    <th>Thứ tự</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($faqs)): ?>
                                    <?php foreach ($faqs as $i => $faq): ?>
                                        <tr>
                                            <td><?php echo $i + 1; ?></td>
                                            <td style="max-width:350px;"><?php echo htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo (int)$faq['sort_order']; ?></td>
                                            <td>
                                                <?php if ($faq['is_active']): ?>
                                                    <span class="badge bg-success">Hiển thị</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Ẩn</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info text-white"
                                                    onclick='editFaq(<?php echo json_encode($faq, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                                    <i class="ti-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="confirmDeleteFaq(<?php echo (int)$faq['id']; ?>)">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">Chưa có câu hỏi nào.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm / Sửa -->
<div class="modal fade" id="faqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo $toUrl('admin/faq/save'); ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="faqModalTitle">Thêm câu hỏi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="faqId">
                    <div class="mb-3">
                        <label class="form-label">Câu hỏi <span class="text-danger">*</span></label>
                        <input type="text" name="question" id="faqQuestion" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Câu trả lời <span class="text-danger">*</span></label>
                        <textarea name="answer" id="faqAnswer" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thứ tự</label>
                            <input type="number" name="sort_order" id="faqSortOrder" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="is_active" id="faqIsActive" class="form-select">
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xóa -->
<div class="modal fade" id="deleteFaqModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="<?php echo $toUrl('admin/faq/delete'); ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="deleteFaqId">
                    <p>Bạn chắc chắn muốn xóa câu hỏi này?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetFaqForm() {
    document.getElementById('faqModalTitle').textContent = 'Thêm câu hỏi';
    document.getElementById('faqId').value = '';
    document.getElementById('faqQuestion').value = '';
    document.getElementById('faqAnswer').value = '';
    document.getElementById('faqSortOrder').value = '0';
    document.getElementById('faqIsActive').value = '1';
}
function editFaq(faq) {
    document.getElementById('faqModalTitle').textContent = 'Chỉnh sửa câu hỏi';
    document.getElementById('faqId').value = faq.id;
    document.getElementById('faqQuestion').value = faq.question;
    document.getElementById('faqAnswer').value = faq.answer;
    document.getElementById('faqSortOrder').value = faq.sort_order;
    document.getElementById('faqIsActive').value = faq.is_active;
    new bootstrap.Modal(document.getElementById('faqModal')).show();
}
function confirmDeleteFaq(id) {
    document.getElementById('deleteFaqId').value = id;
    new bootstrap.Modal(document.getElementById('deleteFaqModal')).show();
}
</script>