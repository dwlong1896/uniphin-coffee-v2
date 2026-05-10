<?php
/**
 * 1. HELPER URL & SEO 
 */
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

$currentCategoryName = 'TIN TỨC MỚI NHẤT';
if (!empty($filters['category']) && !empty($categories)) {
    foreach ($categories as $cate) {
        if ((int) $cate['ID'] === (int) $filters['category']) {
            $currentCategoryName = $cate['Name'];
            break;
        }
    }
}

$pageTitle = "UniPhin Coffee | " . htmlspecialchars($currentCategoryName);
$metaDesc = "Khám phá những câu chuyện cà phê, đánh giá và tin tức khuyến mãi mới nhất từ UniPhin Coffee.";
// Fix meta image: ưu tiên post_image cho đồng bộ
$metaImgName = !empty($news) ? ($news[0]['post_image'] ?? $news[0]['image']) : 'default-news.png';
$metaImage = $toUrl('uploads/news/' . $metaImgName);
?>

<head>
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $metaDesc ?>">
    <meta property="og:title" content="<?= $pageTitle ?>">
    <meta property="og:image" content="<?= $metaImage ?>">
</head>

<section class="min-h-screen py-10 px-4 sm:px-6 lg:px-8"
    style="background: radial-gradient(circle at top, rgba(117, 221, 255, 0.22), transparent 32%), linear-gradient(180deg, #f9fcff 0%, #eef7fb 100%);">

    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col items-center mb-10">
            <p class="text-uniphin-brand font-extrabold text-xs tracking-[0.24em] uppercase mb-3 text-center">UniPhin
                Coffee News</p>
            <h1 id="category-title"
                class="text-uniphin-title text-4xl font-bold tracking-tight uppercase mb-8 text-center">
                <?= htmlspecialchars($currentCategoryName) ?>
            </h1>

            <form id="search-form" class="relative w-full max-w-md">
                <input type="text" name="search" id="search-input" maxlength="100" placeholder="Tìm bài viết..."
                    value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                    class="w-full bg-white/70 backdrop-blur-sm border border-gray-100 text-uniphin-title text-sm rounded-2xl focus:ring-2 focus:ring-uniphin-brand/20 focus:border-uniphin-brand block pl-5 pr-12 py-3 outline-none shadow-sm transition-all">
                <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-4">
                    <i class="fa fa-search text-gray-400"></i>
                </button>
            </form>
        </div>

        <div class="flex justify-center mb-6">
            <div
                class="inline-flex p-1 bg-gray-200/50 backdrop-blur-sm rounded-2xl shadow-inner overflow-x-auto no-scrollbar">
                <a href="<?= $toUrl('tin-tuc') ?>" data-id=""
                    class="ajax-tab px-8 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all <?= empty($filters['category']) ? 'bg-white text-uniphin-brand shadow-sm' : 'text-gray-500 hover:text-uniphin-brand' ?>">
                    Tất cả
                </a>
                <?php if (!empty($categories)):
                    foreach ($categories as $cate): ?>
                        <a href="<?= $toUrl('tin-tuc?category=' . $cate['ID']) ?>" data-id="<?= $cate['ID'] ?>"
                            class="ajax-tab px-8 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all <?= ($filters['category'] ?? '') == $cate['ID'] ? 'bg-white text-uniphin-brand shadow-sm' : 'text-gray-500 hover:text-uniphin-brand' ?>">
                            <?= htmlspecialchars($cate['Name']) ?>
                        </a>
                    <?php endforeach; endif; ?>
            </div>
        </div>

        <div class="flex justify-end mb-12">
            <div class="flex items-center gap-2 bg-white/50 px-3 py-1.5 rounded-xl border border-gray-100 shadow-sm">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Sắp xếp:</span>
                <select id="sort-select"
                    class="bg-transparent text-uniphin-text text-xs font-bold focus:outline-none cursor-pointer">
                    <option value="newest" <?= ($filters['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="oldest" <?= ($filters['sort'] ?? '') == 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                </select>
            </div>
        </div>

        <div id="news-container"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-opacity duration-300">
            <?php foreach ($news as $item): ?>
                <article
                    class="group bg-white rounded-card shadow-[0_24px_60px_rgba(19,54,74,0.08)] overflow-hidden flex flex-col transition-all duration-500 hover:-translate-y-2">
                    <a href="<?= $toUrl('tin-tuc/' . $item['slug']) ?>" class="block overflow-hidden h-56 relative">
                        <img src="<?= $toUrl('uploads/news/' . ($item['post_image'] ?? 'default-news.png')) ?>"
                            class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                            onerror="this.src='<?= $toUrl('uploads/news/default-news.png') ?>'">
                        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur px-3 py-1 rounded-lg shadow-sm">
                            <span
                                class="text-[10px] font-extrabold text-uniphin-brand uppercase tracking-tighter"><?= htmlspecialchars($item['category_name']) ?></span>
                        </div>
                    </a>
                    <div class="p-8 flex-1 flex flex-col">
                        <span class="text-[11px] font-bold text-gray-300 mb-3 uppercase tracking-widest"><i
                                class="far fa-calendar-alt mr-2"></i><?= date('H:i - d/m/Y', strtotime($item['created_at'])) ?></span>
                        <h3
                            class="text-xl font-bold text-uniphin-title mb-4 line-clamp-2 leading-snug group-hover:text-uniphin-brand transition-colors">
                            <a href="<?= $toUrl('tin-tuc/' . $item['slug']) ?>"><?= htmlspecialchars($item['title']) ?></a>
                        </h3>
                        <div class="mt-auto pt-6 border-t border-gray-50">
                            <a href="<?= $toUrl('tin-tuc/' . $item['slug']) ?>"
                                class="text-uniphin-title font-bold text-[11px] tracking-[0.2em] uppercase hover:text-uniphin-brand flex items-center transition-all group/btn">
                                XEM CHI TIẾT <i
                                    class="fas fa-arrow-right ml-3 transform group-hover/btn:translate-x-2 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div id="pagination-container" class="flex justify-center mt-20 gap-4">
            <?php if ($totalPages > 1): ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="javascript:void(0)" onclick="fetchNews(<?= $i ?>)"
                        class="px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300 shadow-sm border 
               <?= ($i == $currentPage)
                   ? 'bg-[#00aeef] text-white border-[#00aeef] shadow-lg shadow-cyan-200 scale-110'
                   : 'bg-white text-[#0c2233] border-gray-200 hover:border-[#00aeef] hover:text-[#00aeef] hover:shadow-md' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<script>
    const newsContainer = document.getElementById('news-container');
    const paginationContainer = document.getElementById('pagination-container');
    const categoryTitle = document.getElementById('category-title');
    const sortSelect = document.getElementById('sort-select');

    // --- HÀM BẢO MẬT CHỐNG XSS (THÊM MỚI) ---
    function escapeHTML(str) {
        if (!str) return "";
        return str.replace(/[&<>"']/g, function (m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[m];
        });
    }

    function fetchNews(page = 1) {
        const search = document.getElementById('search-input').value;
        const activeTab = document.querySelector('.ajax-tab.bg-white');
        const category = activeTab?.dataset.id || '';
        const sort = sortSelect.value;

        if (activeTab) {
            categoryTitle.innerText = activeTab.innerText.trim().toUpperCase();
        }

        const url = `<?= $toUrl('tin-tuc') ?>?search=${encodeURIComponent(search)}&category=${category}&sort=${sort}&page=${page}`;
        newsContainer.style.opacity = '0.5';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                let html = '';
                if (!data.news || data.news.length === 0) {
                    html = '<p class="col-span-full text-center py-20 italic">Không tìm thấy bài viết nào.</p>';
                } else {
                    data.news.forEach(item => {
                        const imgName = item.post_image || item.image || 'default-news.png';

                        // Dùng escapeHTML cho Title và Category Name để an toàn tuyệt đối
                        const safeTitle = escapeHTML(item.title);
                        const safeCategory = escapeHTML(item.category_name);
                        const d = new Date(item.created_at);
                        const safeDate = `${d.getHours().toString().padStart(2, '0')}:${d.getMinutes().toString().padStart(2, '0')} - ${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}/${d.getFullYear()}`;

                        html += `
                        <article class="group bg-white rounded-card shadow-[0_24px_60px_rgba(19,54,74,0.08)] overflow-hidden flex flex-col transition-all duration-500 hover:-translate-y-2">
                            <a href="<?= $toUrl('tin-tuc/') ?>${item.slug}" class="block overflow-hidden h-56 relative">
                                <img src="<?= $toUrl('uploads/news/') ?>${imgName}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" onerror="this.src='<?= $toUrl('uploads/news/default-news.png') ?>'">
                                <div class="absolute top-4 left-4 bg-white/95 backdrop-blur px-3 py-1 rounded-lg shadow-sm">
                                    <span class="text-[10px] font-extrabold text-uniphin-brand uppercase tracking-tighter">${safeCategory}</span>
                                </div>
                            </a>
                            <div class="p-8 flex-1 flex flex-col">
                                <span class="text-[11px] font-bold text-gray-300 mb-3 uppercase tracking-widest">
                                    <i class="far fa-calendar-alt mr-2"></i>${item.created_at}
                                </span>
                                <h3 class="text-xl font-bold text-uniphin-title mb-4 line-clamp-2 leading-snug group-hover:text-uniphin-brand transition-colors">
                                    <a href="<?= $toUrl('tin-tuc/') ?>${item.slug}">${safeTitle}</a>
                                </h3>
                                <div class="mt-auto pt-6 border-t border-gray-50">
                                    <a href="<?= $toUrl('tin-tuc/') ?>${item.slug}" class="text-uniphin-title font-bold text-[11px] tracking-[0.2em] uppercase hover:text-uniphin-brand flex items-center transition-all group/btn">
                                        XEM CHI TIẾT <i class="fas fa-arrow-right ml-3 transform group-hover/btn:translate-x-2 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </article>`;
                    });
                }
                newsContainer.innerHTML = html;

                // Render Phân trang (Giữ nguyên logic cũ của bà)
                let pagHTML = '';
                if (data.totalPages > 1) {
                    for (let i = 1; i <= data.totalPages; i++) {
                        const isActive = (i == data.currentPage);
                        const activeClass = isActive
                            ? 'bg-[#00aeef] text-white border-[#00aeef] shadow-lg shadow-cyan-200 scale-110'
                            : 'bg-white text-[#0c2233] border-gray-200 hover:border-[#00aeef] hover:text-[#00aeef] hover:shadow-md';

                        pagHTML += `
                        <a href="javascript:void(0)" onclick="fetchNews(${i})" 
                           class="px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300 shadow-sm border ${activeClass}">
                            ${i}
                        </a>`;
                    }
                }
                paginationContainer.innerHTML = pagHTML;

                newsContainer.style.opacity = '1';
                window.history.pushState({}, '', url);
                window.scrollTo({ top: newsContainer.offsetTop - 100, behavior: 'smooth' });
            })
            .catch(err => {
                console.error("Lỗi Fetch:", err);
                newsContainer.style.opacity = '1';
            });
    }

    // Gắn sự kiện (Giữ nguyên)
    document.getElementById('search-form').addEventListener('submit', (e) => { e.preventDefault(); fetchNews(1); });
    sortSelect.addEventListener('change', () => fetchNews(1));
    document.querySelectorAll('.ajax-tab').forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('.ajax-tab').forEach(t => t.classList.remove('bg-white', 'text-uniphin-brand', 'shadow-sm'));
            this.classList.add('bg-white', 'text-uniphin-brand', 'shadow-sm');
            fetchNews(1);
        });
    });
</script>