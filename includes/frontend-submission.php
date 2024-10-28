<?php
// Block direct access
if (!defined("ABSPATH")) exit;

/**
 * Frontend form shortcode to submit new books
 */
function book_manager_frontend_form() {
    if (!is_user_logged_in()) {
        return '<p>You must be logged in to submit a book.</p>';
    }

    ob_start();

    // Check for success message after redirect
    if (isset($_GET['book_submitted']) && $_GET['book_submitted'] === 'true') {
        echo '<p>Thank you! Your book has been submitted.</p>';
        return ob_get_clean();
    }

    // Display form
    ?>
    <form id="book-submission-form" method="post">
        <p>
            <label for="book_name">Book Name</label>
            <input type="text" name="book_name" id="book_name" required />
        </p>
        <p>
            <label for="author_name">Author Name</label>
            <input type="text" name="author_name" id="author_name" required />
        </p>
        <p>
            <input type="submit" name="submit_book" value="Submit Book" />
        </p>
        <?php wp_nonce_field('submit_book_form', 'book_manager_nonce'); ?>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('book_submission_form', 'book_manager_frontend_form');
