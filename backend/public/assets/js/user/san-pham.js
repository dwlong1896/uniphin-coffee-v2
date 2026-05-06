$(document).ready(function () {
  $(window).on("load", function () {
    AOS.init({
      duration: 800,
      once: true,
      offset: 80,
    });

    setTimeout(function () {
      AOS.refresh();
      window.dispatchEvent(new Event("scroll"));
    }, 200);
  });

  $(".uniphin-banner-slider").slick({
    dots: true,
    arrows: true,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 3000,
    speed: 800,
    cssEase: "linear",
  });

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

  $bestsellerSlider.on("beforeChange", function () {
    $infoWrapper.css("opacity", 0);
  });

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

  function startCountdown(durationInSeconds) {
    let timer = durationInSeconds;

    setInterval(function () {
      const days = Math.floor(timer / (24 * 3600));
      const hours = Math.floor((timer % (24 * 3600)) / 3600);
      const mins = Math.floor((timer % 3600) / 60);
      const secs = Math.floor(timer % 60);

      $("#days").text(days < 10 ? "0" + days : days);
      $("#hours").text(hours < 10 ? "0" + hours : hours);
      $("#mins").text(mins < 10 ? "0" + mins : mins);
      $("#secs").text(secs < 10 ? "0" + secs : secs);

      if (--timer < 0) {
        timer = 0;
      }
    }, 1000);
  }

  startCountdown(125000);
});

document.addEventListener("DOMContentLoaded", function () {
  const sections = document.querySelectorAll(".uniphin-category-section");
  const navLinks = document.querySelectorAll(".uniphin-menu-categories a");
  const modalTriggerSelector =
    ".uniphin-product-card, .bestseller-item, .sale-card";
  const modalTriggers = document.querySelectorAll(modalTriggerSelector);
  const searchInput = document.querySelector(".uniphin-menu-search input");
  const productsContainer = document.querySelector(".uniphin-menu-products");
  const productModal = document.getElementById("uniphinProductModal");
  const modalTitle = document.getElementById("uniphinProductModalTitle");
  const modalPrice = document.getElementById("uniphinProductModalPrice");
  const modalDescription = document.getElementById(
    "uniphinProductModalDescription"
  );
  const modalCategory = document.getElementById("uniphinProductModalCategory");
  const modalImage = document.getElementById("uniphinProductModalImage");
  const modalQtyInput = document.getElementById("uniphinProductModalQty");
  const modalCloseButton = productModal
    ? productModal.querySelector("[data-modal-close]")
    : null;
  const productFallbackImage =
    "https://minio.thecoffeehouse.com/image/admin/1751598833_matcha-latte-tay-bac-nong_400x400.png";
  let lastFocusedCard = null;

  function updateSidebar() {
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

    const atBottom =
      window.innerHeight + window.scrollY >= document.body.offsetHeight - 50;

    if (atBottom && sections.length > 0) {
      currentId = sections[sections.length - 1].id;
    }

    if (!currentId) {
      return;
    }

    navLinks.forEach(function (link) {
      link.classList.remove("menu-active", "active");

      if (link.getAttribute("href") === "#" + currentId) {
        link.classList.add("menu-active");
      }
    });
  }

  function closeProductModal() {
    if (!productModal || productModal.hasAttribute("hidden")) {
      return;
    }

    productModal.setAttribute("hidden", "");
    productModal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("uniphin-modal-open");

    if (lastFocusedCard) {
      lastFocusedCard.focus();
    }
  }

  function openProductModal(card) {
    if (
      !productModal ||
      !modalTitle ||
      !modalPrice ||
      !modalDescription ||
      !modalCategory ||
      !modalImage
    ) {
      return;
    }

    lastFocusedCard = card;
    modalTitle.textContent = card.dataset.name || "";
    modalPrice.textContent = card.dataset.price || "";
    modalDescription.textContent = card.dataset.description || "";
    modalCategory.textContent = card.dataset.category || "";
    modalImage.onerror = function () {
      this.onerror = null;
      this.src = productFallbackImage;
    };
    modalImage.src = card.dataset.image || productFallbackImage;
    modalImage.alt = card.dataset.name || "";
    if (modalQtyInput) {
      modalQtyInput.value = "1";
    }

    productModal.removeAttribute("hidden");
    productModal.setAttribute("aria-hidden", "false");
    document.body.classList.add("uniphin-modal-open");

    if (modalCloseButton) {
      modalCloseButton.focus();
    }
  }

  window.addEventListener("scroll", updateSidebar);
  updateSidebar();

  if (productModal && modalTriggers.length > 0) {
    document.addEventListener("click", function (event) {
      const modalTrigger = event.target.closest(modalTriggerSelector);

      if (modalTrigger) {
        openProductModal(modalTrigger);
      }
    });

    document.addEventListener("keydown", function (event) {
      const modalTrigger = event.target.closest(modalTriggerSelector);

      if (
        modalTrigger &&
        (event.key === "Enter" || event.key === " ") &&
        !productModal.contains(event.target)
      ) {
        event.preventDefault();
        openProductModal(modalTrigger);
        return;
      }

      if (event.key === "Escape") {
        closeProductModal();
      }
    });

    productModal.addEventListener("click", function (event) {
      const qtyButton = event.target.closest("[data-qty-action]");

      if (qtyButton && modalQtyInput) {
        const currentValue = Math.max(
          1,
          parseInt(modalQtyInput.value || "1", 10) || 1
        );
        const nextValue =
          qtyButton.dataset.qtyAction === "increase"
            ? currentValue + 1
            : Math.max(1, currentValue - 1);

        modalQtyInput.value = String(nextValue);
        return;
      }

      if (
        event.target === productModal ||
        event.target.closest("[data-modal-close]")
      ) {
        closeProductModal();
      }
    });
  }

  if (!searchInput || !productsContainer) {
    return;
  }

  const noResultEl = document.createElement("div");
  noResultEl.style.display = "none";
  noResultEl.style.width = "100%";
  noResultEl.innerHTML = `
    <div style="text-align:center; padding:60px 20px; color:#666;">
      <svg width="60" height="60" viewBox="0 0 24 24" fill="none"
           stroke="#ccc" stroke-width="2" stroke-linecap="round"
           stroke-linejoin="round" style="margin-bottom:15px;">
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
        const name = card
          .querySelector(".uniphin-product-name")
          .textContent.toLowerCase();
        const matched = name.includes(keyword);

        card.style.display = matched ? "flex" : "none";

        if (matched) {
          sectionHasMatch = true;
          totalVisible++;
        }
      });

      section.style.display = sectionHasMatch ? "block" : "none";
    });

    noResultEl.style.display = totalVisible === 0 ? "block" : "none";
  });
});
