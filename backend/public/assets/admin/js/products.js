document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap === 'undefined') {
        return;
    }

    const addProductTrigger = document.getElementById('addProductTrigger');
    const addProductModalElement = document.getElementById('addProductModal');
    const addProductForm = document.getElementById('addProductForm');
    const saveProductBtn = document.getElementById('saveProductBtn');

    const confirmModalElement = document.getElementById('deleteConfirmModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const deleteProductForm = document.getElementById('deleteProductForm');

    if (
        !addProductTrigger ||
        !addProductModalElement ||
        !addProductForm ||
        !saveProductBtn ||
        !confirmModalElement ||
        !confirmDeleteBtn ||
        !deleteProductForm
    ) {
        return;
    }

    const addProductModal = new bootstrap.Modal(addProductModalElement);
    const confirmModal = new bootstrap.Modal(confirmModalElement);

    let deleteUrl = '';

    addProductTrigger.addEventListener('click', function() {
        addProductModal.show();
    });

    saveProductBtn.addEventListener('click', function() {
        if (!addProductForm.checkValidity()) {
            addProductForm.reportValidity();
            return;
        }

        saveProductBtn.disabled = true;
        addProductForm.submit();
    });

    document.addEventListener('click', function(event) {
        const deleteButton = event.target.closest('#dataTable tbody a.product-delete-trigger');
        if (!deleteButton) {
            return;
        }

        event.preventDefault();
        deleteUrl = deleteButton.getAttribute('data-delete-url') || '';
        confirmModal.show();
    });

    confirmDeleteBtn.addEventListener('click', function() {
        if (!deleteUrl) {
            confirmModal.hide();
            return;
        }

        confirmDeleteBtn.disabled = true;
        deleteProductForm.setAttribute('action', deleteUrl);
        deleteProductForm.submit();
    });
});
