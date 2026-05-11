const newsContainer = document.getElementById("news-container");
const paginationContainer = document.getElementById("pagination-container");
const categoryTitle = document.getElementById("category-title");
const sortSelect = document.getElementById("sort-select");

function escapeHTML(str) {
  if (!str) return "";
  return str.replace(/[&<>"']/g, function (m) {
    return {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;",
    }[m];
  });
}

function fetchNews(page = 1) {
  const searchInput = document.getElementById("search-input");
  const search = searchInput ? searchInput.value : "";
  const activeTab = document.querySelector(".ajax-tab.bg-white");
  const category = activeTab?.dataset.id || "";
  const sort = sortSelect ? sortSelect.value : "newest";

  if (activeTab && categoryTitle) {
    categoryTitle.innerText = activeTab.innerText.trim().toUpperCase();
  }

  // SỬ DỤNG BIẾN ĐÃ KHAI BÁO Ở PHP
  const url = `${NEWS_URL}?search=${encodeURIComponent(search)}&category=${category}&sort=${sort}&page=${page}`;
  
  if (newsContainer) newsContainer.style.opacity = "0.5";

  fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
    .then((res) => res.json())
    .then((data) => {
      let html = "";
      if (!data.news || data.news.length === 0) {
        html = '<p class="col-span-full text-center py-20 italic">Không tìm thấy bài viết nào.</p>';
      } else {
        data.news.forEach((item) => {
          const imgName = item.post_image || item.image || "default-news.png";
          const safeTitle = escapeHTML(item.title);
          const safeCategory = escapeHTML(item.category_name);
          
          // XỬ LÝ URL ẢNH VÀ URL CHI TIẾT QUA BIẾN GLOBAL
          const imgPath = UPLOADS_NEWS_URL + imgName;
          const detailPath = NEWS_DETAIL_URL + item.slug;

          html += `
            <article class="group bg-white rounded-card shadow-[0_24px_60px_rgba(19,54,74,0.08)] overflow-hidden flex flex-col transition-all duration-500 hover:-translate-y-2">
                <a href="${detailPath}" class="block overflow-hidden h-56 relative">
                    <img src="${imgPath}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" onerror="this.src='${UPLOADS_NEWS_URL}default-news.png'">
                    <div class="absolute top-4 left-4 bg-white/95 backdrop-blur px-3 py-1 rounded-lg shadow-sm">
                        <span class="text-[10px] font-extrabold text-uniphin-brand uppercase tracking-tighter">${safeCategory}</span>
                    </div>
                </a>
                <div class="p-8 flex-1 flex flex-col">
                    <span class="text-[11px] font-bold text-gray-300 mb-3 uppercase tracking-widest">
                        <i class="far fa-calendar-alt mr-2"></i>${item.created_at}
                    </span>
                    <h3 class="text-xl font-bold text-uniphin-title mb-4 line-clamp-2 leading-snug group-hover:text-uniphin-brand transition-colors">
                        <a href="${detailPath}">${safeTitle}</a>
                    </h3>
                    <div class="mt-auto pt-6 border-t border-gray-50">
                        <a href="${detailPath}" class="text-uniphin-title font-bold text-[11px] tracking-[0.2em] uppercase hover:text-uniphin-brand flex items-center transition-all group/btn">
                            XEM CHI TIẾT <i class="fas fa-arrow-right ml-3 transform group-hover/btn:translate-x-2 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </article>`;
        });
      }
      newsContainer.innerHTML = html;

      let pagHTML = "";
      if (data.totalPages > 1) {
        for (let i = 1; i <= data.totalPages; i++) {
          const isActive = i == data.currentPage;
          const activeClass = isActive
            ? "bg-[#00aeef] text-white border-[#00aeef] shadow-lg shadow-cyan-200 scale-110"
            : "bg-white text-[#0c2233] border-gray-200 hover:border-[#00aeef] hover:text-[#00aeef] hover:shadow-md";

          pagHTML += `<a href="javascript:void(0)" onclick="fetchNews(${i})" class="px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300 shadow-sm border ${activeClass}">${i}</a>`;
        }
      }
      paginationContainer.innerHTML = pagHTML;
      newsContainer.style.opacity = "1";
      window.history.pushState({}, "", url);
      window.scrollTo({ top: newsContainer.offsetTop - 100, behavior: "smooth" });
    })
    .catch((err) => {
      console.error("Lỗi Fetch:", err);
      if (newsContainer) newsContainer.style.opacity = "1";
    });
}

// KHAI BÁO GLOBAL ĐỂ NÚT BẤM ONCLICK TRONG HTML HIỂU ĐƯỢC
window.fetchNews = fetchNews;

// Gắn sự kiện
document.addEventListener("DOMContentLoaded", () => {
    const searchForm = document.getElementById("search-form");
    if (searchForm) {
        searchForm.addEventListener("submit", (e) => {
            e.preventDefault();
            fetchNews(1);
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener("change", () => fetchNews(1));
    }

    document.querySelectorAll(".ajax-tab").forEach((tab) => {
        tab.addEventListener("click", function (e) {
            e.preventDefault();
            document.querySelectorAll(".ajax-tab").forEach((t) => t.classList.remove("bg-white", "text-uniphin-brand", "shadow-sm"));
            this.classList.add("bg-white", "text-uniphin-brand", "shadow-sm");
            fetchNews(1);
        });
    });
});