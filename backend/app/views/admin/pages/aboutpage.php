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
                <h4 class="header-title mb-1">Quản lý nội dung trang Giới thiệu</h4>
                <p class="text-muted mb-4">Chỉnh sửa tiêu đề và nội dung từng phần hiển thị trên trang.</p>

                <?php if (!empty($sections)): ?>
                    <?php foreach ($sections as $section): ?>
                        <div class="card mb-3 border">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <strong><?php echo htmlspecialchars($section['title'] ?: $section['section_key'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <span class="badge bg-info text-dark"><?php echo htmlspecialchars($section['section_key'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="card-body">
                                <form action="<?php echo $toUrl('admin/about/save'); ?>" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo (int)$section['id']; ?>">
                                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($section['image_url'], ENT_QUOTES, 'UTF-8'); ?>">

                                    <div class="mb-3">
                                        <label class="form-label">Tiêu đề</label>
                                        <input type="text" name="title" class="form-control"
                                            value="<?php echo htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nội dung</label>
                                        <textarea name="content" class="form-control" rows="5"><?php echo htmlspecialchars($section['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Ảnh hiện tại</label>
                                        <?php if (!empty($section['image_url'])): ?>
                                            <div class="mb-2">
                                                <img src="<?php echo htmlspecialchars($section['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="Ảnh <?php echo htmlspecialchars($section['section_key'], ENT_QUOTES, 'UTF-8'); ?>" style="max-height: 120px; width: auto; display: block;" />
                                            </div>
                                        <?php else: ?>
                                            <div class="text-muted mb-2">Chưa có ảnh. Upload ảnh mới nếu cần.</div>
                                        <?php endif; ?>

                                        <input type="file" name="image" class="form-control" accept="image/*">
                                        <small class="text-muted">Chọn file ảnh mới để thay thế ảnh hiện tại.</small>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="ti-save"></i> Cập nhật
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <p>Chưa có dữ liệu. Vui lòng import file <code>setup_about_faq.sql</code> vào database.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>