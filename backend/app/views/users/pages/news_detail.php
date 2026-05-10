<?php
/**
 * 1. HELPER & ĐỆ QUY CÓ NHÃN ĐẶC BIỆT VÀ THU GỌN LUỒNG
 */
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

function renderCommentsRecursive($comments, $parentId = null, $level = 0, $toUrl, $newsId, $authorId)
{

    $children = array_filter($comments, fn($c) => (int) $c['parent_comment_id'] === (int) $parentId);
    foreach ($children as $cmt) {

        $paddingStep = ($level > 0) ? min($level * 20, 100) : 0;
        $userName = htmlspecialchars($cmt['first_name'] . ' ' . $cmt['last_name']);
        $childCount = count(array_filter($comments, fn($c) => $c['parent_comment_id'] == $cmt['ID']));

        $isAuthor = ((int) $cmt['User_ID'] === (int) $authorId);
        $isAdminRole = (isset($cmt['role']) && $cmt['role'] === 'admin');
        ?>
        <div class="comment-item mt-6" id="comment-wrap-<?= $cmt['ID'] ?>" style="margin-left: <?= $paddingStep ?>px;">
            <div class="flex gap-3 group">
                <img src="<?= $toUrl('uploads/' . ($cmt['image'] ?? 'default-avatar.png')) ?>"
                    class="<?= ($level == 0) ? 'w-12 h-12' : 'w-9 h-9' ?> rounded-2xl object-cover shadow-sm border-2 border-white"
                    onerror="this.src='<?= $toUrl('assets/images/default-avatar.png') ?>'">

                <div class="flex-1">
                    <div
                        class="bg-gray-50/80 p-4 rounded-[1.2rem] mb-2 hover:bg-white transition-all border border-transparent hover:border-[#00aeef]/30 shadow-sm">
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-black text-[#0c2233]"><?= $userName ?></h4>
                                <?php if ($isAuthor): ?>
                                    <span class="bg-[#00aeef] text-white text-[7px] font-black px-1.5 py-0.5 rounded uppercase">Tác
                                        giả</span>
                                <?php elseif ($isAdminRole): ?>
                                    <span
                                        class="bg-[#0c2233] text-white text-[7px] font-black px-1.5 py-0.5 rounded uppercase">Admin</span>
                                <?php endif; ?>
                            </div>
                            <span
                                class="text-[8px] font-bold text-gray-400 italic"><?= date('H:i - d/m/Y', strtotime($cmt['created_at'])) ?></span>
                        </div>
                        <p id="content-text-<?= $cmt['ID'] ?>" class="text-xs text-[#456072] leading-relaxed">
                            <?= nl2br(htmlspecialchars($cmt['content'])) ?>
                        </p>
                    </div>

                    <div class="flex items-center gap-4 text-[9px] font-black uppercase text-gray-400 pl-2">
                        <button onclick="replyWithTag(<?= $cmt['ID'] ?>, '<?= $userName ?>')" class="hover:text-[#00aeef]"><i
                                class="fas fa-reply mr-1"></i> Phản hồi</button>

                        <?php if ($childCount > 0): ?>
                            <button onclick="toggleThread(<?= $cmt['ID'] ?>)" id="btn-thread-<?= $cmt['ID'] ?>"
                                class="text-[#00aeef]">
                                <i class="fas fa-chevron-down mr-1"></i> Hiện <?= $childCount ?> phản hồi
                            </button>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $cmt['User_ID'] || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'))): ?>
                            <button onclick="showEditForm(<?= $cmt['ID'] ?>)" class="hover:text-blue-600">Sửa</button>
                            <button onclick="deleteComment(<?= $cmt['ID'] ?>)" class="hover:text-red-500">Xóa</button>
                        <?php endif; ?>
                    </div>

                    <div id="reply-form-<?= $cmt['ID'] ?>" class="hidden mt-4">
                        <form onsubmit="event.preventDefault(); handleAjax(this);" action="<?= $toUrl('post-comment') ?>"
                            method="POST">
                            <input type="hidden" name="news_id" value="<?= $newsId ?>">
                            <input type="hidden" name="parent_id" value="<?= $cmt['ID'] ?>">
                            <textarea name="content" rows="2" required maxlength="1000"
                                class="w-full bg-white border border-gray-100 rounded-xl p-4 text-xs outline-none focus:ring-2 focus:ring-[#00aeef]"
                                placeholder="Trả lời <?= $userName ?>..."></textarea>
                            <div class="flex justify-end mt-2">
                                <button type="submit"
                                    class="bg-[#0c2233] text-white px-5 py-2 rounded-lg text-[10px] font-black uppercase">Gửi</button>
                            </div>
                        </form>
                    </div>

                    <div id="thread-<?= $cmt['ID'] ?>" class="hidden border-l-2 border-gray-100 ml-2">
                        <?php renderCommentsRecursive($comments, $cmt['ID'], $level + 1, $toUrl, $newsId, $authorId); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>

<title><?= htmlspecialchars($newsItem['title']) ?> | UniPhin Coffee</title>
<meta name="description" content="<?= htmlspecialchars($newsItem['meta_description'] ?? '') ?>">
<meta name="keywords" content="<?= htmlspecialchars($newsItem['keywords'] ?? '') ?>">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<section class="min-h-screen py-10 px-4 sm:px-6 lg:px-8 bg-[#f9fcff]">
    <div class="max-w-7xl mx-auto">
        <nav class="flex mb-10 text-[11px] font-black uppercase tracking-[0.2em] text-[#0c2233]">
            <a href="<?= $toUrl('/') ?>" class="hover:text-[#00aeef]">Trang chủ</a>
            <span class="mx-3 text-[#00aeef]">/</span>
            <a href="<?= $toUrl('tin-tuc') ?>" class="hover:text-[#00aeef]">Tin tức</a>
            <span class="mx-3 text-[#00aeef]">/</span>
            <span class="text-[#00aeef] italic font-bold"><?= htmlspecialchars($newsItem['title']) ?></span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-8">
                <article class="bg-white rounded-[3.5rem] shadow-sm overflow-hidden mb-12 border border-gray-100">
                    <div class="w-full h-[450px] md:h-[600px] overflow-hidden relative">
                        <img src="<?= $toUrl('uploads/news/' . ($newsItem['image'] ?? 'default-news.png')) ?>"
                            class="w-full h-full object-cover">
                        <div class="absolute bottom-10 left-10">
                            <span
                                class="bg-[#00aeef] text-white px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest shadow-2xl shadow-cyan-500/50">
                                <?= htmlspecialchars($newsItem['category_name']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-10 md:p-20">
                        <div
                            class="flex flex-wrap items-center gap-x-10 gap-y-5 mb-12 text-[11px] font-black uppercase tracking-widest text-[#0c2233] border-b-2 border-gray-50 pb-10">
                            <span class="flex items-center"><i
                                    class="fa-solid fa-user-pen mr-3 text-[#00aeef] text-base"></i> Tác giả: <span
                                    class="ml-1 text-gray-600 font-bold"><?= htmlspecialchars(($newsItem['admin_fname'] ?? '') . ' ' . ($newsItem['admin_lname'] ?? 'Admin')) ?></span></span>
                            <span class="flex items-center"><i
                                    class="fa-solid fa-calendar-day mr-3 text-[#00aeef] text-base"></i> Đăng: <span
                                    class="ml-1 text-gray-600 font-bold"><?= date('H:i - d/m/Y', strtotime($newsItem['created_at'])) ?></span></span>

                            <span
                                class="flex items-center bg-cyan-50 text-[#00aeef] px-4 py-2 rounded-xl animate-pulse border border-cyan-200 shadow-sm">
                                <i class="fas fa-sync-alt mr-2"></i> Chỉnh sửa gần nhất:
                                <span
                                    class="ml-1"><?= !empty($newsItem['updated_at']) ? date('H:i - d/m/Y', strtotime($newsItem['updated_at'])) : date('H:i - d/m/Y', strtotime($newsItem['created_at'])) ?></span>
                            </span>
                        </div>

                        <h1 class="text-4xl md:text-6xl font-black text-[#0c2233] mb-12 leading-[1.1]">
                            <?= htmlspecialchars($newsItem['title']) ?>
                        </h1>
                        <div class="text-[#456072] leading-[2.1] text-lg news-content space-y-8">
                            <?= $newsItem['content'] ?>
                        </div>
                    </div>
                </article>

                <div class="bg-white rounded-[3rem] shadow-sm p-10 md:p-16 w-full">
                    <div
                        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6 border-b-2 border-gray-50 pb-8">
                        <div>
                            <h3 class="text-2xl font-black text-[#0c2233] uppercase">Bình luận (<?= count($comments) ?>)
                            </h3>

                        </div>

                        <form method="GET" class="flex items-center bg-gray-50 rounded-2xl p-1 border border-gray-100">
                            <input type="hidden" name="comment_page" value="1">
                            <button type="submit" name="comment_sort" value="newest"
                                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?= ($currentSort === 'newest') ? 'bg-white shadow-sm text-[#00aeef]' : 'text-gray-400' ?>">Mới
                                nhất</button>
                            <button type="submit" name="comment_sort" value="oldest"
                                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?= ($currentSort === 'oldest') ? 'bg-white shadow-sm text-[#00aeef]' : 'text-gray-400' ?>">Cũ
                                nhất</button>
                        </form>
                    </div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="mb-20">
                            <form id="main-comment-form" class="js-ajax-form" action="<?= $toUrl('post-comment') ?>"
                                method="POST">
                                <input type="hidden" name="news_id" value="<?= $newsItem['ID'] ?>">
                                <textarea name="content" rows="4" required maxlength="1000"
                                    placeholder="Viết bình luận của bạn (tối đa 1000 ký tự)."
                                    class="w-full bg-gray-50 border border-gray-100 rounded-[2rem] p-8 text-sm outline-none focus:ring-4 focus:ring-cyan-50 shadow-inner resize-none transition-all"></textarea>
                                <div class="flex justify-end mt-6">
                                    <button type="submit"
                                        class="bg-[#00aeef] text-white px-12 py-5 rounded-[1.5rem] text-xs font-black uppercase tracking-widest hover:shadow-2xl active:scale-95 transition-all shadow-lg shadow-cyan-500/30">Đăng
                                        bình luận</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div id="comment-section-root">
                        <?php renderCommentsRecursive($comments, null, 0, $toUrl, $newsItem['ID'], $newsItem['Admin_ID']); ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <div class="flex justify-center items-center gap-4 mt-12 border-t border-gray-50 pt-10">
                            <?php if ($currentPage > 1): ?>
                                <a href="?comment_page=<?= $currentPage - 1 ?>&comment_sort=<?= $currentSort ?>"
                                    class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-[#0c2233] hover:bg-[#00aeef] hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </a>
                            <?php endif; ?>

                            <div class="flex gap-2">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <a href="?comment_page=<?= $i ?>&comment_sort=<?= $currentSort ?>"
                                        class="px-4 py-2 rounded-xl text-[11px] font-black transition-all <?= ($i == $currentPage) ? 'bg-[#00aeef] text-white shadow-lg shadow-cyan-500/30' : 'bg-gray-50 text-gray-400 hover:bg-white border border-transparent hover:border-gray-100' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </div>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?comment_page=<?= $currentPage + 1 ?>&comment_sort=<?= $currentSort ?>"
                                    class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-[#0c2233] hover:bg-[#00aeef] hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="lg:col-span-4">
                <div class="bg-white rounded-[2.5rem] shadow-sm p-10 sticky top-10 border border-gray-100">
                    <h3 class="text-lg font-black text-[#0c2233] mb-10 relative inline-block uppercase">Có thể bạn thích
                        <span class="absolute -bottom-3 left-0 w-12 h-1.5 bg-[#00aeef] rounded-full"></span>
                    </h3>
                    <div class="space-y-10">
                        <?php foreach ($relatedNews as $related): ?>
                            <a href="<?= $toUrl('tin-tuc/' . $related['slug']) ?>" class="group flex items-start gap-5">
                                <div class="w-20 h-20 flex-shrink-0 rounded-[1.2rem] overflow-hidden shadow-md">
                                    <?php
                                    // Kiểm tra cả 2 tên biến post_image và image cho chắc
                                    $relatedImg = $related['post_image'] ?? ($related['image'] ?? 'default-news.png');
                                    ?>
                                    <img src="<?= $toUrl('uploads/news/' . $relatedImg) ?>"
                                        class="w-full h-full object-cover group-hover:scale-125 transition-all duration-700"
                                        onerror="this.src='<?= $toUrl('uploads/news/default-news.png') ?>'">
                                </div>
                                <div class="flex-1">
                                    <h4
                                        class="text-sm font-black text-[#0c2233] group-hover:text-[#00aeef] line-clamp-2 leading-tight transition-colors">
                                        <?= htmlspecialchars($related['title']) ?>
                                    </h4>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<div id="edit-modal"
    class="hidden fixed inset-0 bg-[#0c2233]/80 backdrop-blur-md z-[100] flex items-center justify-center p-6">
    <div class="bg-white rounded-[3rem] p-10 w-full max-w-lg shadow-2xl animate-in zoom-in duration-300">
        <h3 class="text-xl font-black text-[#0c2233] mb-8 uppercase text-center tracking-tight">Chỉnh sửa bình luận</h3>
        <form id="edit-comment-form" class="js-ajax-form" action="<?= $toUrl('comment-action') ?>" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="comment_id" id="edit-comment-id">
            <textarea name="content" id="edit-comment-content" rows="6" required required maxlength="1000"
                class="w-full bg-gray-50 border border-gray-100 rounded-[2rem] p-6 text-sm outline-none focus:ring-2 focus:ring-[#00aeef] mb-8 shadow-inner"></textarea>
            <div class="flex justify-center gap-6">
                <button type="button" onclick="closeEditModal()"
                    class="text-xs font-black uppercase text-gray-400">Hủy</button>
                <button type="submit"
                    class="bg-[#00aeef] text-white px-10 py-4 rounded-2xl shadow-xl font-black text-[11px] uppercase">Lưu</button>
            </div>
        </form>
    </div>
</div>
<script>
    // 1. CÁC HÀM XỬ LÝ NÚT BẤM (PHẢI NẰM NGOÀI READY)
    window.toggleThread = function (id) {
        const thread = document.getElementById('thread-' + id);
        const btn = document.getElementById('btn-thread-' + id);
        if (!thread || !btn) return;

        if (thread.classList.contains('hidden')) {
            thread.classList.remove('hidden');
            btn.innerHTML = `<i class="fas fa-chevron-up mr-1"></i> Thu gọn phản hồi`;
        } else {
            thread.classList.add('hidden');
            btn.innerHTML = `<i class="fas fa-chevron-down mr-1"></i> Xem phản hồi`;
            document.getElementById('comment-wrap-' + id).scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    window.replyWithTag = function (id, userName) {
        const f = document.getElementById('reply-form-' + id);
        if (!f) return;
        f.classList.remove('hidden');
        const textarea = f.querySelector('textarea');
        textarea.value = '';
        textarea.placeholder = 'Đang trả lời ' + userName + '...';
        textarea.focus();
    };

    window.showEditForm = function (id) {
        const textElement = document.getElementById('content-text-' + id);
        if (textElement) {
            document.getElementById('edit-comment-id').value = id;
            document.getElementById('edit-comment-content').value = textElement.innerText.trim();
            document.getElementById('edit-modal').classList.remove('hidden');
        }
    };

    window.closeEditModal = function () {
        document.getElementById('edit-modal').classList.add('hidden');
    };

    window.deleteComment = async function (id) {
        const result = await Swal.fire({
            title: 'Xác nhận xóa?',
            text: "Xóa là mất tiêu luôn đó nhen Hiền!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Xóa luôn!'
        });

        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('comment_id', id);

            const r = await fetch('<?= $toUrl("comment-action") ?>', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const res = await r.json();
            if (res.status === 'success') location.reload();
        }
    };

    // 2. HÀM XỬ LÝ AJAX (TUI ĐÃ FIX LỖI ĐÓNG NGOẶC CỦA BÀ)
    async function handleAjax(form) {
        const textarea = form.querySelector('textarea');
        const content = textarea ? textarea.value.trim() : "";

        // Kiểm tra nội dung rỗng
        if (content === "") {
            Swal.fire({ icon: 'warning', title: 'Nội dung trống!', text: 'Bà chưa nhập gì hết trơn kìa!', confirmButtonColor: '#00aeef' });
            return;
        }

        // Kiểm tra độ dài (Đồng bộ với Server)
        if (content.length > 1000) {
            Swal.fire({ icon: 'warning', title: 'Quá dài!', text: 'Bình luận tối đa 1000 ký tự thôi nhen Hiền!', confirmButtonColor: '#00aeef' });
            return;
        }

        const url = form.getAttribute('action');
        const formData = new FormData(form);

        try {
            const res = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const text = await res.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error("Server trả về không đúng định dạng JSON:", text);
                throw new Error("Phản hồi không phải JSON");
            }

            if (data.status === 'success') {
                location.reload();
            } else {
                Swal.fire({ icon: 'error', title: 'Lỗi!', text: data.message });
            }
        } catch (err) {
            console.error(err);
            Swal.fire({ icon: 'error', title: 'Lỗi!', text: 'Gửi bình luận thất bại!' });
        }
    }

    // 3. GẮN SỰ KIỆN KHI TRANG SẴN SÀNG
    $(document).ready(function () {
        $(document).on('submit', '.js-ajax-form', function (e) {
            e.preventDefault();
            handleAjax(this);
        });
    });
</script>

<style>
    .news-content p {
        margin-bottom: 2rem;
        color: #456072;
        line-height: 2.1;
    }

    .news-content h2 {
        font-weight: 900;
        color: #0c2233;
        margin: 4rem 0 2rem;
        text-transform: uppercase;
    }

    .news-content img {
        border-radius: 2.5rem;
        margin: 3rem auto;
        display: block;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.06);
    }
</style>