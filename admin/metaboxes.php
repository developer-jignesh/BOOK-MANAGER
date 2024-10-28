<?php
//exit if someone try to access root file 
if(!defined('ABSPATH')) exit;
/**
 * @package BookManager
 * @author JigneshSharma
 * @version 1.0
 */



/**
 * @ $book_manger_add_metaboxes
 * Add metabox for book details
 * @param $screen : // also we can pass an array like['book', 'post', 'other_custom_post']
 * @param $context: where you want to show the metabox other option -
 * ---normal---(main column)
 * ---side-----(sidebar)
 * --advanced--(main column, below the default boxes) -- all these options can be passed as a set of array 
 * and then we can define their prioriy based on (high , low , default)
 * example : let's context('side', 'normal', 'adavanced') and periority('high', 'low') -> the thing happen 
 * wordpress first try to kept the metabox on the side bar if side bar is not exist then it will try to render them on the normal.
 * @param $priority : (high,low,default)
 * @param $callback : all us to fill the metaboxes with content and render the content. 
 *  
*/

function book_manager_add_metaboxes() {
    add_meta_box(
        'book_details_metabox',
        __('Book Details', 'book-manager'),
        'book_manager_display_metaboxes',
        'book', 
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'book_manager_add_metaboxes');

/**
 * Display the metabox content
 */
function book_manager_display_metaboxes($post) {
    $author = get_post_meta($post->ID, '_book_author', true);
    $pages = get_post_meta($post->ID, '_book_pages', true);

    wp_nonce_field('book_manager_save_metaboxes', 'book_manager_metabox_nonce');
    ?>
    <p>
        <label for="book_author"><?php _e('Author', 'book-manager'); ?></label>
        <input type="text" name="book_author" id="book_author" value="<?php echo esc_attr($author); ?>">
    </p>
    <p>
        <label for="book_pages"><?php _e('Pages', 'book-manager'); ?></label>
        <input type="number" name="book_pages" id="book_pages" value="<?php echo esc_attr($pages); ?>">
    </p>
    <?php
}

/**
 * Save the metabox data
 */
function book_manager_save_metaboxes($post_id) {
    if (!isset($_POST['book_manager_metabox_nonce']) || !wp_verify_nonce($_POST['book_manager_metabox_nonce'], 'book_manager_save_metaboxes')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }

    if (isset($_POST['book_author'])) {
        update_post_meta($post_id, '_book_author', sanitize_text_field($_POST['book_author']));
    }

    if (isset($_POST['book_pages'])) {
        update_post_meta($post_id, '_book_pages', intval($_POST['book_pages']));
    }
}
add_action('save_post', 'book_manager_save_metaboxes');
