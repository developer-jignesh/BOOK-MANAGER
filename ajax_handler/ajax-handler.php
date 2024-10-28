<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/** 
 * @package BookManager
 * @author JigneshSharma
 * @version 1.0
 */

/**
 * Handle AJAX request for book pagination.
 */
function book_manager_ajax_pagination() {
    // Check nonce for security
    check_ajax_referer('book_manager_pagination_nonce', 'nonce');

    // Get the page number from the AJAX request
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $posts_per_page = 10; // Show 10 books per page

    // Query for books
    $args = [
        'post_type'      => 'book',
        'posts_per_page' => $posts_per_page,
        'paged'          => $page,
        'post_status'    => 'publish',
    ];

    $books = new WP_Query(query: $args);

    // Prepare the response
    if ($books->have_posts()) {
        ob_start();
        while ($books->have_posts()) {
            $books->the_post();

            $author = get_post_meta(post_id: get_the_ID(), key: '_book_author', single: true);
            $pages  = get_post_meta(post_id: get_the_ID(), key: '_book_pages', single: true);
            ?>
            <div class="book-manager-book">
                <h2 class="book-title"><?php the_title(); ?></h2>
                <p class="book-author"><strong><?php _e(text: 'Author:', domain: 'book-manager'); ?></strong> <?php echo esc_html(text: $author); ?></p>
                <p class="book-pages"><strong><?php _e(text: 'Pages:', domain: 'book-manager'); ?></strong> <?php echo intval(value: $pages); ?></p>
            </div>
            <?php
        }

        $output = ob_get_clean();
        wp_send_json_success(value: ['html' => $output, 'page' => $page]);
    } else {
        wp_send_json_error(value: ['message' => __(text: 'No more books found.', domain: 'book-manager')]);
        }

    wp_die();
}

// Register AJAX handlers for logged in and non-logged in users
add_action(hook_name: 'wp_ajax_book_manager_pagination', callback: 'book_manager_ajax_pagination');
add_action(hook_name: 'wp_ajax_nopriv_book_manager_pagination', callback: 'book_manager_ajax_pagination');
