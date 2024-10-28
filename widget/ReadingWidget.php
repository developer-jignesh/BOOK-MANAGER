<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * @package BookManager
 * @version 1.0
 * @author JigneshSharma
 * 
 * Register the Reading Widget
 */
class Book_Manager_Reading_Widget extends WP_Widget {

    // Constructor to initialize the widget
    public function __construct() {
        parent::__construct(
            id_base: 'book_manager_reading_widget',
            name: __(text: 'Reading Widget', domain: 'book-manager'),
            widget_options: [
                'description' => __(text: 'A widget that lists books with pagination.', domain: 'book-manager'),
                'classname' => 'custom-reading-widget',        
                'customize_selective_refresh' => true     
        ],
        );
    }

    // Display the widget content
    public function widget($args, $instance) {
        echo $args['before_widget'];

        echo '<div id="book-manager-widget">';
        echo '<div class="book-list-container">';
        // Initial book list rendering happens through AJAX
        echo '</div>';
        echo '<div id="book-manager-pagination">';
        echo '<button class="prev-books" disabled>&laquo; ' . __('Previous', 'book-manager') . '</button>';
        echo '<button class="next-books">' . __('Next', 'book-manager') . ' &raquo;</button>';
        echo '</div>';
        echo '</div>';

        echo $args['after_widget'];
    }

    // Form to customize the widget (if necessary)
    // public function form($instance) {
        // Optional: add widget settings if needed
    // }

    // Save widget options (if necessary)
    public function update($new_instance, $old_instance) {
        $instance = [];
        return $instance;
    }
}

// Register the widget
function register_book_manager_reading_widget() {
    register_widget('Book_Manager_Reading_Widget');
}
add_action(hook_name: 'widgets_init', callback: 'register_book_manager_reading_widget');
