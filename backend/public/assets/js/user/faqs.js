/**
 * FAQs page – Accordion toggle & Search filter
 */
document.addEventListener('DOMContentLoaded', function () {

    // --- Accordion toggle ---
    document.querySelectorAll('.faq-question').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = this.closest('.faq-item');
            var answer = item.querySelector('.faq-answer');
            var isActive = item.classList.contains('active');

            // Đóng tất cả
            document.querySelectorAll('.faq-item.active').forEach(function (openItem) {
                openItem.classList.remove('active');
                openItem.querySelector('.faq-answer').style.maxHeight = null;
            });

            // Mở nếu chưa active
            if (!isActive) {
                item.classList.add('active');
                answer.style.maxHeight = answer.scrollHeight + 'px';
            }
        });
    });

    // --- Search filter ---
    var searchInput = document.getElementById('faqSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var query = this.value.toLowerCase().trim();
            var items = document.querySelectorAll('.faq-item');
            var visibleCount = 0;

            items.forEach(function (item) {
                var question = item.querySelector('.faq-question span');
                var answer = item.querySelector('.faq-answer-content');
                var text = (question ? question.textContent : '') + ' ' + (answer ? answer.textContent : '');

                if (text.toLowerCase().indexOf(query) !== -1) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            var noResults = document.getElementById('noResults');
            if (noResults) {
                noResults.style.display = (visibleCount === 0 && query !== '') ? '' : 'none';
            }
        });
    }
});
