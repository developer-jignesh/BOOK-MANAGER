jQuery(document).ready(function ($) {
    // Initial load of books (first page)
    loadBooks(1);

    // Handle next button click
    $('.next-books').on('click', function () {
        var currentPage = parseInt($(this).data('current-page')) || 1;
        loadBooks(currentPage + 1);
    });

    // Handle prev button click
    $('.prev-books').on('click', function () {
        var currentPage = parseInt($(this).data('current-page')) || 2;
        if (currentPage > 1) {
            loadBooks(currentPage - 1);
        }
    });

    function loadBooks(page) {
        $.ajax({
            url: bookManagerWidget.ajaxUrl,
            type: 'POST',
            data: {
                action: 'book_manager_pagination',
                page: page,
                nonce: bookManagerWidget.nonce,
            },
            success: function (response) {
                if (response.success) {
                    // Update the book list
                    $('.book-list-container').html(response.data.html);

                    // Update current page data
                    $('.next-books').data('current-page', response.data.page);
                    $('.prev-books').data('current-page', response.data.page);

                    // Disable/Enable buttons based on page
                    if (response.data.page === 1) {
                        $('.prev-books').attr('disabled', true);
                    } else {
                        $('.prev-books').attr('disabled', false);
                    }
                } else {
                    alert(response.data.message);
                }
            },
            error: function () {
                alert('An error occurred while loading books.');
            }
        });
    }
});
