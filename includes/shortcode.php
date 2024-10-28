<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) exit;
/**
 * @package BookManager
 * @author JigneshSharma
 * @version 1.0
 * 
 */

/**
 * Shortcode to display a bookshelf of books with custom styling.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output of the bookshelf.
 */
function book_manager_bookshelf_shortcode($atts) {
    // Normalize and extract shortcode attributes (if any).
    $atts = shortcode_atts([
        'posts_per_page' => -1, // Show all books by default.
        'book_id' => '',

    ], $atts, 'bookshelf');

    // Query arguments for fetching books.
    $args = array(
        'post_type'      => 'book',
        'posts_per_page' => intval($atts['posts_per_page']),
        'post_status'    => 'publish',
    );
    if (!empty($atts['book_id'])) {
        $args['p'] = intval($atts['book_id']); // Fetch the book by ID.
    }
    // Fetch books.
    $books = new WP_Query($args);

    // Check if there are any books.
    if (!$books->have_posts()) {
        return '<p>' . __('No books found.', 'book-manager') . '</p>';
    }

    // Start output buffering.
    ob_start();

    // Begin the bookshelf output.
    echo '<div class="book-manager-bookshelf">';

    // Loop through books.
    while ($books->have_posts()) {
        $books->the_post();

        // Get custom meta data.
        $author = get_post_meta(get_the_ID(), '_book_author', true);
        $pages  = get_post_meta(get_the_ID(), '_book_pages', true);
        $thumbnail = get_the_post_thumbnail(get_the_ID(), 'medium');

        // Output book details.
        ?>
        <div class="book-manager-book">
            <div class="book-thumbnail"><?php echo $thumbnail; ?></div>
            <div class="book-info">
                <h2 class="book-title"><?php the_title(); ?></h2>
                <?php if ($author) : ?>
                    <p class="book-author"><strong><?php _e('Author:', 'book-manager'); ?></strong> <?php echo esc_html($author); ?></p>
                <?php endif; ?>
                <?php if ($pages) : ?>
                    <p class="book-pages"><strong><?php _e('Pages:', 'book-manager'); ?></strong> <?php echo intval($pages); ?></p>
                <?php endif; ?>
                <div class="book-excerpt"><?php the_excerpt(); ?></div>
            </div>
        </div>
        <?php
    }

    // End the bookshelf output.
    echo '</div>';

    // Reset post data.
    wp_reset_postdata();

    // Get the buffered content.
    $output = ob_get_clean();

    return $output;
}

// Register the bookshelf shortcode.
add_shortcode('bookshelf', 'book_manager_bookshelf_shortcode');
