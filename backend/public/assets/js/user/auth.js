(function () {
  var toggleButtons = document.querySelectorAll("[data-target]");
  for (var i = 0; i < toggleButtons.length; i += 1) {
    toggleButtons[i].addEventListener("click", function () {
      var inputId = this.getAttribute("data-target");
      var input = document.getElementById(inputId);
      if (!input) {
        return;
      }

      if (input.type === "password") {
        input.type = "text";
        this.textContent = "🙈";
      } else {
        input.type = "password";
        this.textContent = "👁";
      }
    });
  }
})();
