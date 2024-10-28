<?php
// file-name : custom-post-type.php
/**
 * @package BookManager
 * @version 1.0
 * @author JigneshSharma
 */

/**
 * Register custom post types.
 * show_in_admin_bar : default value of show_in_menu i.e true
 * 
 */
function book_manager_register_post_types_and_taxonomies()
{
  register_post_type('book', [
    'labels' => [
      'name' => __('Books', 'book-manager'),
      'singular_name' => __('Book', 'book-manager'),
      'add_new' => __('Add New Book', 'book-manager'),
      'all_items' => __('All Books', 'book-manager'),
    ],
    'public' => true,
    'has_archive' => true,
    'supports' => ['title', 'editor', 'thumbnail'],
    'taxonomies' => ['genre', 'tag'],
    'show_in_rest' => true,
  ]);

  /**
   * To Register_taxonomy like tag , set the hierarchical value to the false
   * if it is true then it will treatd like a Taxonomy(category)
   * public => true , allow us to show the cat, to the front and admin , also allow us to make post searchable
   * on frontend side. 
   */
  //register "Tag" 
  register_taxonomy('tag', ['book', 'post'], 
  ['label' => 'Tags', 
  'hierarchical' => false, 
  'public' => true], 
);
  //register "genre" taxonomy
  register_taxonomy('genre', 'book', [
    'labels' => [
      'name' => __('Genres', 'book-manger'),
      'singular_name' => __('Genre', 'book-manager'),
    ],
    'public' => true,
    'hierarchical' => true,
    'show_in_rest' => true,
  ]);
}
add_action('init', 'book_manager_register_post_types_and_taxonomies');

