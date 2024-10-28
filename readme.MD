# shortcode 
 * shortcode is a powerful technique to add a dyanmic content to the post, pages.
 * it's allows you to execute the code with in posts, and pages. 

## types of shortcode 

* Self-Closing Shortcodes(Single Tag) 

``` A self-closing shortcode is similar to a self-closing HTML tag. It does not wrap content and simply executes some functionality. These are often used when you just want to display dynamic content or perform some action without needing opening and closing tags.```

<textarea disabled style="background-color:black; color:white;height:150px; width:100%;resize:none">
function my_single_shortcode() {
    return "This is a self-closing shortcode.";
}
add_shortcode('my_single', 'my_single_shortcode');

//usage
// [my_single]
</textarea>

* Enclosing shortcodes(wrapping content) 

<textarea disabled style="background-color:black; color:white;height:150px; width:100%;resize:none;">
function my_enclosing_shortcode($atts, $content = null) {
    return '<div class="my-wrapper">' . $content . '</div>';
}
add_shortcode('my_enclosing', 'my_enclosing_shortcode');
 

//[my_enclosing]This content will be wrapped.[/my_enclosing]

</textarea>

* shortcodes with Attributes

``` shortcodes can accepts attributes to make them dyanamic. These attributes allow users to pass differnt values to the shortcodes, making it more verstile.
```
<textarea disabled style="background-color:gray; color:black;resize:none;width:100%;height:150px">
function my_attributed_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Default Title',
        'color' => 'blue',
    ), $atts);
    
    return '<h2 style="color:' . esc_attr($atts['color']) . '">' . esc_html($atts['title']) . '</h2>';
}
add_shortcode('my_attributed', 'my_attributed_shortcode');

//usages : [my_attributed title="Custom Title" color="red"]

</textarea>

* Shortcodes with content and Attributes (MIXED)

``` You can also create shortcodes that both accept attributes and wrap contnet. This is especially useful when you want to perform operations based on both. ```

<textarea disabled style="background:black;color:white;resize:none;width:100%;height:200px;">
function my_mixed_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'color' => 'blue',
    ), $atts);
    
    return '<p style="color:' . esc_attr($atts['color']) . '">' . $content . '</p>';
}
add_shortcode('my_mixed', 'my_mixed_shortcode');
</textarea>
<code>
[my_mixed color="green"]This is wrapped content.[/my_mixed]
</code>

* Self-closing shortcodes with attributes
```A self-closing shortcode can slo accept attributes, making it dyanmic. it doesn't wrap content, but you can stil pass value to it via attribute ```

<textarea disabled style="background:black; color:white; height:200px; width:100%; resize:none;">
function my_dynamic_shortcode($atts) {
    $atts = shortcode_atts(array(
        'size' => '16',
    ), $atts);
    
    return '<p style="font-size:' . intval($atts['size']) . 'px">This is a dynamic font size.</p>';
}
add_shortcode('my_dynamic', 'my_dynamic_shortcode');
</textarea>

Usages : 
> [my_dynamic size="20"]

> Result : 
``<p style="font-size:20px">This is a dynamic font size.</p>``
 * Nested Shortcodes

 ``` You can use shortcodes inside other shortcodes. wordpress will process nested shortcodes, but you must account for this in your shortcode function using <b>do_shortcode().</b>```
 <textarea disabled style="resize:none; background:black;color:white;height:200px; width:100%">
 function my_outer_shortcode($atts, $content = null) {
    return '<div class="outer-wrapper">' . do_shortcode($content) . '</div>';
}
add_shortcode('my_outer', 'my_outer_shortcode');

</textarea>

> [my_outer][my_inner]Nested Content[/my_inner][/my_outer]

* Custom Shortcode for Template Usage
``` shortcodes are often used with posts and pages ,but you can also use them directly in you theme templete files. ```

> Example : ```echo do_shortcode('[my_shortcode]');```
<br>

> This allows you to insert a shortcode into a template File(such as header.php or single.php) and render its output there. 