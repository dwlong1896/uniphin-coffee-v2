document.addEventListener("DOMContentLoaded", function () {
  const root = document.getElementById("cartPageRoot");

  if (!root) {
    return;
  }

  const itemList = document.getElementById("cartItemList");
  const selectAll = document.getElementById("cartSelectAll");
  const subtotalNode = document.getElementById("cartSummarySubtotal");
  const totalNode = document.getElementById("cartSummaryTotal");
  const summaryNote = document.getElementById("cartSummaryNote");
  const checkoutButton = document.getElementById("cartCheckoutButton");
  const updateUrl = root.dataset.updateUrl || "";
  const removeUrl = root.dataset.removeUrl || "";
  const loginUrl = root.dataset.loginUrl || "";
  const checkoutUrl = root.dataset.checkoutUrl || "";

  function formatCurrency(value) {
    return new Intl.NumberFormat("vi-VN").format(value) + " d";
  }

  function checkedItems() {
    return Array.from(root.querySelectorAll("[data-cart-item]")).filter(
      function (item) {
        const checkbox = item.querySelector(".cart-item-checkbox");
        return checkbox && checkbox.checked;
      }
    );
  }

  function recalculate() {
    const items = Array.from(root.querySelectorAll("[data-cart-item]"));
    const total = checkedItems().reduce(function (sum, item) {
      const subtotal = parseFloat(
        item.querySelector("[data-item-subtotal]")?.dataset.value || "0"
      );
      return sum + subtotal;
    }, 0);

    if (subtotalNode) {
      subtotalNode.textContent = formatCurrency(total);
    }

    if (totalNode) {
      totalNode.textContent = formatCurrency(total);
    }

    if (selectAll) {
      const checkboxes = root.querySelectorAll(".cart-item-checkbox");
      const checked = root.querySelectorAll(".cart-item-checkbox:checked");
      selectAll.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
    }

    if (summaryNote) {
      summaryNote.textContent =
        String(items.length) + " mon dang co trong gio hang cua ban.";
    }
  }

  function postForm(url, payload) {
    return fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: new URLSearchParams(payload),
    }).then(async function (response) {
      const data = await response.json().catch(function () {
        return null;
      });

      if (!response.ok) {
        if (response.status === 401) {
          window.location.href = (data && data.redirect_url) || loginUrl || "/login";
          return null;
        }

        throw new Error((data && data.message) || "Khong the xu ly gio hang.");
      }

      return data;
    });
  }

  if (itemList) {
    itemList.querySelectorAll("[data-item-subtotal]").forEach(function (node) {
      const raw = node.textContent.replace(/[^\d]/g, "");
      node.dataset.value = raw || "0";
    });
  }

  if (selectAll) {
    selectAll.addEventListener("change", function () {
      root.querySelectorAll(".cart-item-checkbox").forEach(function (checkbox) {
        checkbox.checked = selectAll.checked;
      });
      recalculate();
    });
  }

  root.addEventListener("change", function (event) {
    if (event.target.classList.contains("cart-item-checkbox")) {
      recalculate();
      return;
    }

    if (!event.target.classList.contains("cart-quantity-input")) {
      return;
    }

    const input = event.target;
    const item = input.closest("[data-cart-item]");
    const productId = item ? item.dataset.productId : "0";
    const quantity = Math.max(1, parseInt(input.value || "1", 10) || 1);
    const previousValue = input.defaultValue || input.value || "1";

    input.value = String(quantity);
    input.disabled = true;

    postForm(updateUrl, {
      product_id: productId,
      quantity: String(quantity),
    })
      .then(function (data) {
        if (!data || !item) {
          return;
        }

        const subtotalNode = item.querySelector("[data-item-subtotal]");

        if (subtotalNode) {
          subtotalNode.dataset.value = String(data.subtotal || 0);
          subtotalNode.textContent = formatCurrency(Number(data.subtotal || 0));
        }

        input.value = String(data.quantity || quantity);
        input.defaultValue = input.value;
        recalculate();
      })
      .catch(function (error) {
        input.value = previousValue;
        window.alert(error.message || "Khong the cap nhat so luong.");
      })
      .finally(function () {
        input.disabled = false;
      });
  });

  root.addEventListener("click", function (event) {
    const removeButton = event.target.closest("[data-remove-item]");

    if (!removeButton) {
      return;
    }

    const item = removeButton.closest("[data-cart-item]");
    const productId = item ? item.dataset.productId : "0";

    removeButton.disabled = true;

    postForm(removeUrl, {
      product_id: productId,
    })
      .then(function (data) {
        if (!data || !item) {
          return;
        }

        item.remove();
        recalculate();

        if (!root.querySelector("[data-cart-item]")) {
          window.location.reload();
        }
      })
      .catch(function (error) {
        window.alert(error.message || "Khong the xoa san pham.");
        removeButton.disabled = false;
      });
  });

  if (checkoutButton) {
    checkoutButton.addEventListener("click", function () {
      const selectedIds = checkedItems()
        .map(function (item) {
          return item.dataset.productId || "";
        })
        .filter(Boolean);

      if (selectedIds.length === 0) {
        window.alert("Vui long chon it nhat 1 mon de thanh toan.");
        return;
      }

      window.location.href =
        checkoutUrl + "?items=" + encodeURIComponent(selectedIds.join(","));
    });
  }

  recalculate();
});
