document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap === 'undefined') {
        return;
    }

    const detailModalElement = document.getElementById('productDetailModal');
    const confirmModalElement = document.getElementById('deleteConfirmModal');
    const successModalElement = document.getElementById('deleteSuccessModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    if (!detailModalElement || !confirmModalElement || !successModalElement || !confirmDeleteBtn) {
        return;
    }

    const detailModal = new bootstrap.Modal(detailModalElement);
    const confirmModal = new bootstrap.Modal(confirmModalElement);
    const successModal = new bootstrap.Modal(successModalElement);

    const detailFields = {
        name: document.getElementById('detailName'),
        category: document.getElementById('detailCategory'),
        description: document.getElementById('detailDescription'),
        status: document.getElementById('detailStatus'),
        price: document.getElementById('detailPrice'),
        image: document.getElementById('detailImage')
    };

    let selectedRow = null;

    document.addEventListener('click', function(event) {
        const detailButton = event.target.closest('#dataTable tbody a.product-detail-trigger');
        if (detailButton) {
            event.preventDefault();
            const row = detailButton.closest('tr');
            if (!row) {
                return;
            }

            const cells = row.querySelectorAll('td');
            if (cells.length >= 6) {
                detailFields.name.textContent = cells[0].textContent.trim();
                detailFields.category.textContent = cells[1].textContent.trim();
                detailFields.description.textContent = cells[2].textContent.trim();
                detailFields.status.textContent = cells[3].textContent.trim();
                detailFields.price.textContent = cells[4].textContent.trim();
                detailFields.image.textContent = cells[5].textContent.trim();
            }

            detailModal.show();
            return;
        }

        const deleteButton = event.target.closest('#dataTable tbody a.product-delete-trigger');
        if (!deleteButton) {
            return;
        }

        event.preventDefault();
        selectedRow = deleteButton.closest('tr');
        if (selectedRow) {
            confirmModal.show();
        }
    });

    confirmDeleteBtn.addEventListener('click', function() {
        if (selectedRow) {
            selectedRow.remove();
            selectedRow = null;
        }

        confirmModal.hide();

        confirmModalElement.addEventListener('hidden.bs.modal', function handleHidden() {
            successModal.show();
            confirmModalElement.removeEventListener('hidden.bs.modal', handleHidden);
        });
    });
});
