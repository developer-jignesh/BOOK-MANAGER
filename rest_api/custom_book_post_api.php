<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * @package BookManager
 * @version 1.0
 * @author JigneshSharma
 * 
 * Description: Register custom REST API endpoints for fetching the book post
 * 1. register routes for all books 
 * 2. register routes for get single book's by {ID}
 * @return void
 * @supported HTTP METHODS : ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] 
 * @unsupported HTTP METHODS : ['ANY', 'OPTIONS', 'HEAD','TRACE', 'CONNECT']
 * other regular expression for slug can be used is this : \b\w+\b
 */
// add_action('rest_api_init', 'book_manager_register_rest_routes');

function book_manager_register_rest_routes(): void
{
    // GET endpoint for getting all the books 
    register_rest_route('book-manager/v1', '/books', array(
        'methods' => 'GET',
        'callback' => 'book_manager_get_books',
        'permission_callback' => '__return_true',
    ));

    // GET endpoint for getting a single book by ID
    register_rest_route('book-manager/v1', '/books/(?P<id>\d+)', array(
        'methods' => 'GET',  
        
        'callback' => 'book_manager_get_book_by_id',
        'permission_callback' => '__return_true',
    ));
    register_rest_route('book-manager/v1', '/books/(?P<slug>[a-z0-9\s?]*-?([a-z0-9\s?]?)+)/details', [
        'methods' => 'GET',
        'callback' => 'get_the_book_by_slug_name',
        'permission_callback' => '__return_true',
    ]);
}
book_manager_register_rest_routes();

/**
 * Callback to fetch all the books
 * @return WP_REST_Response
 */
function book_manager_get_books(WP_REST_Request $request): WP_REST_Response
{
    $args =[
        'post_type' => 'book',
        'post_status' => 'publish',
        'posts_per_page' => -1, 
];

    $query = new WP_Query($args);
    $books = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $books[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'author' => get_post_meta(get_the_ID(), '_book_author', true),
                'content' => get_the_content(),
                'date' => get_the_date(),
            );
        }
    }

    wp_reset_postdata(); // Reset the post data after the loop

    return new WP_REST_Response($books, 200);
}

/**
 * Callback to fetch a specific book by ID
 * @return WP_Error|WP_REST_Response
 */
function book_manager_get_book_by_id(WP_REST_Request $request): WP_Error|WP_REST_Response
{
    $book_id = $request['id'];
    $book = get_post($book_id);

    if (empty($book) || $book->post_type !== 'book') {
        return new WP_Error('no_book', 'Book not found',['status' => 404]);
    }

    $response = array(
        'id' => $book->ID,
        'title' => get_the_title($book_id),
        'author' => get_post_meta($book_id, '_book_author', true),
        'content' => $book->post_content,
        'date' => get_the_date('', $book_id),
    );

    return new WP_REST_Response($response, 200);
}
/**
 * @method get_the_book_by_slug_name()
 * @return WP_Error|WP_REST_Response
 */
function get_the_book_by_slug_name(WP_REST_Request $request) : WP_Error|WP_REST_Response {

    $book_slug = $request['slug'];
    
    $fetchBook = get_page_by_path($book_slug, OBJECT, 'book');

    if($fetchBook) {
      $response = [
        'id' => $fetchBook->ID,
        'title' => get_the_title($fetchBook->ID),
        'author' => get_post_meta($fetchBook->ID, '_book_author', true),
        'content' => $fetchBook->post_content,
        
      ];
      return new WP_REST_Response($response, 200);
    } else {
        return new WP_REST_Response(['message' => 'book post not found'], 404);
    }
}

/**
 * @method check_admin_api_accesss();
 * this will check who one try to access the custom api 
 * if the user is !admin then , show them a message , access denied,
 * else let them access.
 * 
 * @return bool|WP_Error
 */
function check_admin_api_access(): bool|WP_Error {
    if(current_user_can('manage_options')) {
        return true;
    }
    return new WP_Error(
        'reset_forbiden',
        'Access denied. Please contact the administrator for access to this API.',
        ['status' => 403]
    );
}