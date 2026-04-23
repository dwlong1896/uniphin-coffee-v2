(function () {
  var sidebar = document.getElementById("userSidebar");
  var overlay = document.getElementById("sidebarOverlay");
  var openBtn = document.getElementById("menuToggleBtn");
  var closeBtn = document.getElementById("sidebarCloseBtn");

  if (!sidebar || !overlay || !openBtn || !closeBtn) {
    return;
  }

  function openSidebar() {
    sidebar.classList.add("open");
    overlay.classList.add("show");
  }

  function closeSidebar() {
    sidebar.classList.remove("open");
    overlay.classList.remove("show");
  }

  openBtn.addEventListener("click", openSidebar);
  closeBtn.addEventListener("click", closeSidebar);
  overlay.addEventListener("click", closeSidebar);

  var links = sidebar.querySelectorAll("a");
  for (var i = 0; i < links.length; i += 1) {
    links[i].addEventListener("click", closeSidebar);
  }
})();
