<?php
// Block direct access
if (!defined('ABSPATH')) exit;

/**
 * Handle frontend book submission form
 */
function book_manager_handle_form_submission() {
    if (isset($_POST['submit_book'])) {
        // Verify nonce
        if (!isset($_POST['book_manager_nonce']) || !wp_verify_nonce($_POST['book_manager_nonce'], 'submit_book_form')) {
            wp_die('Nonce verification failed.');
        }

        // Ensure the user is logged in and is not an admin
        if (!is_user_logged_in()) {
            wp_die(__('You must be logged in to submit a book.', 'book-manager'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'book_submissions'; // Custom table name

        // Sanitize input data
        $book_name = sanitize_text_field($_POST['book_name']);
        $author_name = sanitize_text_field($_POST['author_name']);
        $user_id = get_current_user_id();

        // Insert data into custom database table
        $result = $wpdb->insert(
            $table_name,
            array(
                'user_id'     => $user_id,         // Correct column name
                'book_name'   => $book_name,
                'author_name' => $author_name,
            ),
            array('%d', '%s', '%s')             
        );

        // Check if data was inserted successfully
        if ($result === false) {
            wp_die(__('Error: Unable to save book submission to the database.', 'book-manager'));
        }

        // Redirect after successful submission
        wp_redirect(add_query_arg('book_submitted', 'true'));
        exit;
    }
}
add_action('template_redirect', 'book_manager_handle_form_submission');
