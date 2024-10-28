<?php
// exit, if direct access to the root path.
if(!defined('ABSPATH')) exit;

/**
 * @package BookManager
 * @version 1.0
 * @author JigneshSharma
 * A description , Register a custom admin page for book submissions.
 */
function book_manager_admin_menu(): void {
    add_menu_page('Book Submissions',
    menu_title: 'Book Submissions',
    capability: 'manage_options',
    menu_slug: 'book-submissions',
     callback: 'book_manager_display_submissions_page',
     icon_url: 'dashicons-book-alt',
     position: 20
    );

}
add_action('admin_menu', 'book_manager_admin_menu');

/**
 * Display the custom admin page for book submissions.
 */
function book_manager_display_submissions_page() {
    global $wpdb;

    // Set up pagination
    $items_per_page = 10;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $items_per_page;

    // Handle search query
    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $where = '';
    if (!empty($search)) {
        $where = $wpdb->prepare("WHERE book_name LIKE %s OR author_name LIKE %s", '%' . $search . '%', '%' . $search . '%');
    }

    // Fetch total number of submissions for pagination
    $total_items = $wpdb->get_var(query: "SELECT COUNT(*) FROM {$wpdb->prefix}book_submissions $where");

    // Fetch the submissions with limit and offset for pagination
    $submissions = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}book_submissions $where LIMIT %d OFFSET %d",
            $items_per_page,
            $offset
        )
    );

    // Check for SQL errors
    if ($wpdb->last_error) {
        echo 'Database error: ' . esc_html($wpdb->last_error);
    }

    // Pagination links
    $total_pages = ceil($total_items / $items_per_page);
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Book Submissions</h1>

        <!-- Search form -->
        <form method="get">
            <input type="hidden" name="page" value="book-submissions" />
            <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Search books..." />
            <input type="submit" value="Search" class="button" />
        </form>

        <!-- Table of submissions -->
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Book Name</th>
                    <th>Author Name</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($submissions) : ?>
                <?php foreach ($submissions as $submission) : ?>
                    <tr>
                        <td><?php echo esc_html($submission->id); ?></td>
                        <td><?php echo esc_html($submission->book_name); ?></td>
                        <td><?php echo esc_html($submission->author_name); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="3">No submissions found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="tablenav">
                <div class="tablenav-pages">
                    <?php
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => "<button id='prev_text' class='style-btn'>Prev</button>",
                        'next_text' => "<button id='next_text' class='style-btn'>Next</button>",
                        'total' => $total_pages,
                        'current' => $current_page,
                    ));
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
