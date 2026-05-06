document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap === 'undefined') {
        return;
    }

    const addProductTrigger = document.getElementById('addProductTrigger');
    const addProductModalElement = document.getElementById('addProductModal');
    const addSuccessModalElement = document.getElementById('addSuccessModal');
    const addProductForm = document.getElementById('addProductForm');
    const saveProductBtn = document.getElementById('saveProductBtn');

    const confirmModalElement = document.getElementById('deleteConfirmModal');
    const deleteSuccessModalElement = document.getElementById('deleteSuccessModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    if (
        !addProductTrigger ||
        !addProductModalElement ||
        !addSuccessModalElement ||
        !addProductForm ||
        !saveProductBtn ||
        !confirmModalElement ||
        !deleteSuccessModalElement ||
        !confirmDeleteBtn
    ) {
        return;
    }

    const addProductModal = new bootstrap.Modal(addProductModalElement);
    const addSuccessModal = new bootstrap.Modal(addSuccessModalElement);
    const confirmModal = new bootstrap.Modal(confirmModalElement);
    const deleteSuccessModal = new bootstrap.Modal(deleteSuccessModalElement);

    let selectedRow = null;

    addProductTrigger.addEventListener('click', function() {
        addProductModal.show();
    });

    saveProductBtn.addEventListener('click', function() {
        if (!addProductForm.checkValidity()) {
            addProductForm.reportValidity();
            return;
        }

        addProductModal.hide();

        addProductModalElement.addEventListener('hidden.bs.modal', function handleHidden() {
            addProductForm.reset();
            addSuccessModal.show();
            addProductModalElement.removeEventListener('hidden.bs.modal', handleHidden);
        });
    });

    document.addEventListener('click', function(event) {
        const deleteButton = event.target.closest('#dataTable tbody a.product-delete-trigger');
        if (!deleteButton) {
            return;
        }

        event.preventDefault();
        selectedRow = deleteButton.closest('tr');
        confirmModal.show();
    });

    confirmDeleteBtn.addEventListener('click', function() {
        if (selectedRow) {
            selectedRow.remove();
            selectedRow = null;
        }

        confirmModal.hide();

        confirmModalElement.addEventListener('hidden.bs.modal', function handleHidden() {
            deleteSuccessModal.show();
            confirmModalElement.removeEventListener('hidden.bs.modal', handleHidden);
        });
    });
});
