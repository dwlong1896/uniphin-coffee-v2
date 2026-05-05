$(document).ready(function () {
  // ==========================================
  // KHỞI TẠO AOS (ANIMATE ON SCROLL)
  // ==========================================
  $(window).on("load", function () {
    AOS.init({
      duration: 800,
      once: true,
      offset: 80,
    });

    // Tuyệt chiêu: Đợi thêm 0.2 giây để layout ổn định hoàn toàn
    // Sau đó ép AOS quét lại tọa độ và giả lập một thao tác cuộn chuột để đánh thức các phần tử trên màn hình
    setTimeout(function () {
      AOS.refresh();
      window.dispatchEvent(new Event("scroll"));
    }, 200);
  });

  // ═══════════════════════════════════════════
  // BANNER SLIDER
  // ═══════════════════════════════════════════
  $(".uniphin-banner-slider").slick({
    dots: true, // Hiện chấm tròn phân trang
    arrows: true, // Hiện mũi tên 2 bên
    infinite: true, // Lặp vòng không dừng
    autoplay: true, // Tự động chuyển slide
    autoplaySpeed: 3000, // Dừng 3 giây mỗi slide
    speed: 800, // Hiệu ứng chuyển mất 0.8 giây
    cssEase: "linear",
  });

  // ═══════════════════════════════════════════
  // BEST SELLER SLIDER
  // ═══════════════════════════════════════════
  const $bestsellerSlider = $(".bestseller-slider");
  const $infoWrapper = $(".bestseller-info-wrapper");
  const $activeName = $(".bestseller-active-name");
  const $activePrice = $(".bestseller-active-price");

  $bestsellerSlider.slick({
    centerMode: true,
    centerPadding: "60px",
    slidesToShow: 3,
    responsive: [
      {
        breakpoint: 768,
        settings: {
          arrows: false,
          centerMode: true,
          centerPadding: "40px",
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 480,
        settings: {
          arrows: false,
          centerMode: true,
          centerPadding: "40px",
          slidesToShow: 1,
        },
      },
    ],
  });

  // Làm mờ info trước khi slide chuyển — tránh nháy text cũ
  $bestsellerSlider.on("beforeChange", function () {
    $infoWrapper.css("opacity", 0);
  });

  // Sau khi slide chuyển xong: lấy data-* từ slide mới và cập nhật UI
  $bestsellerSlider.on("afterChange", function (event, slick, currentSlide) {
    const $current = $(slick.$slides[currentSlide]);
    $activeName.text($current.data("name"));
    $activePrice.text($current.data("price"));
    $infoWrapper.css("opacity", 1);
  });

  $(".flashsale-slider").slick({
    slidesToShow: 5,
    slidesToScroll: 1,
    arrows: true,
    infinite: false,
    responsive: [
      { breakpoint: 1200, settings: { slidesToShow: 3 } },
      { breakpoint: 768, settings: { slidesToShow: 2 } },
    ],
  });

  // --- 4. CODE CHO ĐỒNG HỒ ĐẾM NGƯỢC ---
  function startCountdown(durationInSeconds) {
    let timer = durationInSeconds;
    setInterval(function () {
      let days = Math.floor(timer / (24 * 3600));
      let hours = Math.floor((timer % (24 * 3600)) / 3600);
      let mins = Math.floor((timer % 3600) / 60);
      let secs = Math.floor(timer % 60);

      $("#days").text(days < 10 ? "0" + days : days);
      $("#hours").text(hours < 10 ? "0" + hours : hours);
      $("#mins").text(mins < 10 ? "0" + mins : mins);
      $("#secs").text(secs < 10 ? "0" + secs : secs);

      if (--timer < 0) timer = 0;
    }, 1000);
  }

  // Gọi hàm chạy (Ví dụ đặt là 1 ngày rưỡi: 125000 giây)
  startCountdown(125000);
});

document.addEventListener("DOMContentLoaded", function () {
  const sections = document.querySelectorAll(".uniphin-category-section");
  const navLinks = document.querySelectorAll(".uniphin-menu-categories a");

  // ═══════════════════════════════════════════
  // 1. SIDEBAR HIGHLIGHT THEO VỊ TRÍ CUỘN
  // ═══════════════════════════════════════════
  function updateSidebar() {
    // +150px bù cho chiều cao header cố định ở trên cùng,
    // tránh section bị tính là "đang xem" khi thực ra bị header che
    const scrollPos = window.scrollY + 150;
    let currentId = "";

    sections.forEach(function (section) {
      if (
        scrollPos >= section.offsetTop &&
        scrollPos < section.offsetTop + section.offsetHeight
      ) {
        currentId = section.id;
      }
    });

    // Ngoại lệ: khi đã cuộn đến đáy trang (còn < 50px),
    // section cuối cùng có thể không bao giờ chạm ngưỡng trên
    // nên ép buộc highlight nó
    const atBottom =
      window.innerHeight + window.scrollY >= document.body.offsetHeight - 50;

    if (atBottom && sections.length > 0) {
      currentId = sections[sections.length - 1].id;
    }

    if (!currentId) return; // Chưa cuộn vào section nào — không làm gì

    navLinks.forEach(function (link) {
      // Xóa cả 'active' lẫn 'menu-active' phòng trường hợp
      // script bên thứ ba (sidebar.js gốc) tự gắn class 'active'
      link.classList.remove("menu-active", "active");

      if (link.getAttribute("href") === "#" + currentId) {
        link.classList.add("menu-active");
      }
    });
  }

  window.addEventListener("scroll", updateSidebar);
  updateSidebar(); // Chạy ngay khi load để set trạng thái ban đầu

  // ═══════════════════════════════════════════
  // 2. TÌM KIẾM / LỌC SẢN PHẨM TRỰC TIẾP
  // ═══════════════════════════════════════════
  const searchInput = document.querySelector(".uniphin-menu-search input");
  const productsContainer = document.querySelector(".uniphin-menu-products");

  if (!searchInput || !productsContainer) return; // Không tìm thấy phần tử — dừng sớm

  // Tạo thông báo "không tìm thấy" — ẩn sẵn, chỉ hiện khi kết quả = 0
  const noResultEl = document.createElement("div");
  noResultEl.style.display = "none";
  noResultEl.style.width = "100%";
  noResultEl.innerHTML = `
        <div style="text-align:center; padding:60px 20px; color:#666;">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none"
                 stroke="#ccc" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round"
                 style="margin-bottom:15px;">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <h3 style="font-size:18px; font-weight:700; color:#222; margin-bottom:5px;">
                Không tìm thấy sản phẩm
            </h3>
            <p style="font-size:14px;">
                Rất tiếc, chúng tôi không có thức uống nào khớp với từ khóa của bạn.
            </p>
        </div>
    `;
  productsContainer.appendChild(noResultEl);

  searchInput.addEventListener("input", function () {
    const keyword = this.value.trim().toLowerCase();
    let totalVisible = 0;

    sections.forEach(function (section) {
      const cards = section.querySelectorAll(".uniphin-product-card");
      let sectionHasMatch = false;

      cards.forEach(function (card) {
        // So khớp từ khóa với tên sản phẩm (không phân biệt hoa thường)
        const name = card
          .querySelector(".uniphin-product-name")
          .textContent.toLowerCase();
        const matched = name.includes(keyword);

        // Dùng display:flex vì card dùng flexbox — display:block sẽ phá layout
        card.style.display = matched ? "flex" : "none";

        if (matched) {
          sectionHasMatch = true;
          totalVisible++;
        }
      });

      // Ẩn hẳn cả section (tiêu đề + grid) nếu không có card nào khớp
      section.style.display = sectionHasMatch ? "block" : "none";
    });

    // Chỉ hiện thông báo khi toàn bộ không có kết quả
    noResultEl.style.display = totalVisible === 0 ? "block" : "none";
  });
});
