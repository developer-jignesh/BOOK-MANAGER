<?php
//exit if some try to access root path direct from the url 
if(!defined("ABSPATH")) exit;

/**
 * @package BookManager
 * @version 1.0
 * @author JigneshSharma
 */
/**
 * Practice all type of shortcodes
 * By implementing them. 
 */

 // example of the self-closing shortcodes (single Tag)

 function my_single_short_code() {
    return "this is a self-closing shortcode.";
 }
 add_shortcode("my_single","my_single_short_code");

 function my_enclosing_shortcode($atts, $content = null) {
    return '<div class="my-wrapper">' . $content . '</div>';
}
add_shortcode('my_enclosing', 'my_enclosing_shortcode');

function my_attributed_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Default Title',
        'color' => 'blue',
    ), $atts);
    
    return '<h2 style="color:' . esc_attr($atts['color']) . '">' . esc_html($atts['title']) . '</h2>';
}
add_shortcode('my_attributed', 'my_attributed_shortcode');

function my_mixed_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'color' => 'blue',
    ), $atts);
    
    return '<p style="color:' . esc_attr($atts['color']) . '">' . $content . '</p>';
}
add_shortcode('my_mixed', 'my_mixed_shortcode');
function my_dynamic_shortcode($atts) {
    $atts = shortcode_atts(array(
        'size' => '16',
    ), $atts);
    
    return '<p style="font-size:' . intval($atts['size']) . 'px">This is a dynamic font size.</p>';
}
add_shortcode('my_dynamic', 'my_dynamic_shortcode');
