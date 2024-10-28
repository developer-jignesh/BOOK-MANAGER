<?php
/**
 * Plugin Name: Book Manager
 * Description: A plugin to manage books with custom post types, shortcodes, and widgets.
 * Version: 1.0
 * Author: Jignesh Sharma
 * Text Domain: book-manager
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

define('PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));

require_once PLUGIN_DIR_PATH . 'widget/ReadingWidget.php';
if( is_admin() ) {
    require_once PLUGIN_DIR_PATH . 'includes/custom-post-type.php';
    require_once PLUGIN_DIR_PATH . 'admin/custom_book_manager_page.php';
    require_once PLUGIN_DIR_PATH . 'admin/metaboxes.php';
}
else {
    require_once PLUGIN_DIR_PATH . 'includes/shortcode.php';
    require_once PLUGIN_DIR_PATH . 'includes/form-handle.php';
    require_once PLUGIN_DIR_PATH . 'includes/frontend-submission.php';
}

// this will include the ajax when only ajax call happen.
if(wp_doing_ajax()) {
    require_once PLUGIN_DIR_PATH . 'ajax_handler/ajax-handler.php';
}

add_action('rest_api_init',function() {
    
    require_once PLUGIN_DIR_PATH . 'rest_api/custom_book_post_api.php';
});



// Register the activation hook to create a custom DB table and flush rewrite rules.
register_activation_hook(__FILE__, 'book_manager_activate');

/**
 * Create custom database table and flush rewrite rules on activation.
 * steps -> 
 * 1. create a object of the global $wpdb object like this -> global $wpdb
 * 2. Now create a table by including the wp_ prefix.
 * 3. apply the condition check to check if the table is exist or not 
 *    if exist then do not need to execute the sql query to create the table again and again 
 *    else let them execute.
 */
function book_manager_activate(): void
{
    global $wpdb;

    // Create the custom database table for book submissions.
    $table_name = $wpdb->prefix . 'book_submissions';
    // condition to check table exist or not 
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
         user_id bigint(20) NOT NULL,
        book_name varchar(255) NOT NULL,
        author_name varchar(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
    // Register post types and taxonomies.
    book_manager_register_post_types_and_taxonomies();

    // Flush rewrite rules to prevent 404 errors.
    flush_rewrite_rules();
}

// Register the deactivation hook to flush rewrite rules.
register_deactivation_hook(__FILE__, 'book_manager_deactivate');

/**
 * Flush rewrite rules on plugin deactivation.
 */
function book_manager_deactivate(): void
{
    flush_rewrite_rules();
}

// Load text domain for internationalization.
add_action('plugins_loaded', 'book_manager_load_textdomain');

/**
 * Load plugin text domain.
 */
function book_manager_load_textdomain()
{
    load_plugin_textdomain(
        'book-manager',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
}

/**
 * Enqueue plugin style for shortcode
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'book-manager-styles',
        plugin_dir_url(__FILE__) . 'assets/css/shortcode.css',
        array(),
        '1.0',
        'all'
    );
    wp_enqueue_script(
        'book-manager-widget-js',
        plugin_dir_url(__FILE__) . 'assets/js/book-manager-widget.js',
        array('jquery'),
        '1.0.0',
        true
    );

    // Pass AJAX URL and nonce to the script
    wp_localize_script('book-manager-widget-js', 'bookManagerWidget', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('book_manager_pagination_nonce')
    ));

    wp_enqueue_style(
        'book-manager-widget-css',
        plugin_dir_url(__FILE__) . 'assets/css/book-manager-widget.css',
        array(),
        '1.0.0'
    );
});