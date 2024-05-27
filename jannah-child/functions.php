<?php

require_once get_stylesheet_directory() . '/custom-code/jannah-overrides.php';

add_action('wp_enqueue_scripts', 'tie_theme_child_styles_scripts', 80);

function tie_theme_child_styles_scripts()
{

    /* Load the RTL.css file of the parent theme */
    if (is_rtl()) {
        wp_enqueue_style('tie-theme-rtl-css', get_template_directory_uri() . '/rtl.css', '');
    }

    /* THIS WILL ALLOW ADDING CUSTOM CSS TO THE style.css */
    wp_enqueue_style('tie-theme-child-css', get_stylesheet_directory_uri() . '/style.css', '');

    /* Uncomment this line if you want to add custom javascript */
    wp_enqueue_script('jannah-child-js', get_stylesheet_directory_uri() . '/js/scripts.js', '', false, true);

    /* Dequeueing Jannah parallax script */
    //wp_dequeue_script( 'tie-js-parallax', TIELABS_TEMPLATE_URL . '/assets/js/parallax.js', array( 'jquery', 'imagesloaded' ), true );
    // wp_register_script('parallax', get_stylesheet_directory_uri() . '/js/lib/parallax.min.js', array(), '1.4.2', true);
    // wp_enqueue_script('parallax');
    wp_register_script('iframeresize', 'https://cdnjs.cloudflare.com/ajax/libs/iframe-resizer/4.3.1/iframeResizer.min.js', array(), '4.3.1', true);
    wp_enqueue_script('iframeresize');
    wp_enqueue_script('mb-float-grid-script', get_stylesheet_directory_uri() . '/js/lib/mb-float-grid.js', array('jquery'), null, true);
    wp_enqueue_script('d3-js', 'https://d3js.org/d3.v7.min.js', array(), null, true);
    wp_enqueue_script('carrusel-script', get_stylesheet_directory_uri() . '/js/lib/mongabay_tools.js', array('d3-js'), null, true);
}

add_action('enqueue_block_editor_assets', function () {

    wp_enqueue_script('mongabay-custom-mce', get_stylesheet_directory_uri() . '/js/lib/custom-wp-blocks.js', array('wp-blocks', 'wp-dom'));
});

function enqueue_custom_admin_scripts()
{
    // Encola el script personalizado para el área de administración
    wp_enqueue_script('mongabay-custom-mce', get_stylesheet_directory_uri() . '/js/lib/mongabay_custom_mce.js', array('jquery', 'tinymce'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_scripts');

// Add Conditional Search Page Scripts
add_action('wp_enqueue_scripts', 'search_page_script');

function search_page_script()
{
    if (is_search() && isset($_GET['s'])) {
        wp_enqueue_script('search-js', get_stylesheet_directory_uri() . '/js/lib/search.js', array(), '1.0.0', true);
    }
}

// function enqueue_quicktag_script(){
// 	// Parallax Content Shortcode Button in a text editor (old open_close_px_content())
// 	wp_enqueue_script( 'open_close_px_content', get_stylesheet_directory_uri() . '/js/lib/open_close_px_content.js', array( 'jquery', 'quicktags' ), '1.0.0', true );
// 	//Parallax Slide Shortcode Button in a text editor (old px_shortcode_button())
// 	wp_enqueue_script( 'px_shortcode_button', get_stylesheet_directory_uri() . '/js/lib/px_shortcode_button.js', array( 'jquery', 'quicktags' ), '1.0.0', true );
// }
// add_action( 'admin_enqueue_scripts', 'enqueue_quicktag_script' );


/**************************************************************************************************************/
/***************************************** MONGABAY SPECIFIC FUNCTIONS ****************************************/
/**************************************************************************************************************/

include(get_stylesheet_directory() . '/custom-code/taxonomy-location.php');
include(get_stylesheet_directory() . '/custom-code/taxonomy-serial.php');
include(get_stylesheet_directory() . '/custom-code/taxonomy-topic.php');
include(get_stylesheet_directory() . '/custom-code/taxonomy-entity.php');
include(get_stylesheet_directory() . '/custom-code/custom-post-type-formats.php');
include(get_stylesheet_directory() . '/custom-code/url-rewrites.php');
include(get_stylesheet_directory() . '/custom-code/feed-query.php');
if (function_exists('add_theme_support')) {
    add_theme_support('post-formats', array('aside'));
    add_image_size('large', 1200, 800, true); // Large Thumbnail
    add_image_size('medium', 768, 512, true); // Medium Thumbnail
    add_image_size('cover-image-retina', 2400, 890, true); // Retina Cover Thumbnail
    add_image_size('thumbnail', 100, 100, true); // Small Thumbnail
    load_theme_textdomain('mongabay', get_template_directory() . '/languages');
}

/* Disable Gutenberg globally */
function mongabay_disable_gutenberg($can_edit, $post_type)
{
    $excluded_post_type = 'custom-story';
    if ($post_type === $excluded_post_type) {
        return true;
    }

    return false;
}
add_filter('use_block_editor_for_post_type', 'mongabay_disable_gutenberg', 10, 2);

/* Register post tags for new post types */
function mongabay_register_tags_cpts()
{
    register_taxonomy_for_object_type('post_tag', 'videos');
    register_taxonomy_for_object_type('post_tag', 'podcasts');
    register_taxonomy_for_object_type('post_tag', 'short-article');
    register_taxonomy_for_object_type('post_tag', 'custom-story');
    register_taxonomy_for_object_type('post_tag', 'specials');
}
add_action('init', 'mongabay_register_tags_cpts');


// Get current host
/*function j_mongabay_subdomain_name() {
    $parsedUrl = parse_url($_SERVER['SERVER_NAME']);
    $host = explode('.', $parsedUrl['path']);
    $domain = $host[0];
    return $domain;
}*/

// Main WP_query modifier to process multiple vars
function j_mongabay_mega_query($query)
{
    if ($query->is_home() && $query->is_main_query() && !is_admin()) {
        $home_url = esc_url(home_url('/'));
        $section = get_query_var('section');
        $firstvar = get_query_var('nc1');
        $secondvar = get_query_var('nc2');

        if ($section == 'moved') {
            $moved_query = array('post_type' => 'post', 'posts_per_page' => 1, 'offset' => 0, array('key' => 'mongabay_post_legacy_url', 'value' => $secondvar, 'compare' => '='));
            $query->set('meta_query', $moved_query);
        }
        if ($section == 'list' && empty($firstvar)) {
            wp_redirect($home_url);
            exit;
        }

        if ($section == 'list' && !empty($firstvar) && empty($secondvar)) {

            $item1 = get_terms(array('topic', 'location'), array('slug' => $firstvar));

            $tax_query = array(
                array(
                    'taxonomy' => $item1[0]->taxonomy,
                    'field' => 'slug',
                    'terms' => $item1[0]->slug
                )
            );

            $query->set('tax_query', $tax_query);
        }

        if ($section == 'list' && !empty($firstvar) && !empty($secondvar)) {

            $item1 = get_terms(array('topic', 'location'), array('slug' => $firstvar));
            $item2 = get_terms(array('topic', 'location'), array('slug' => $secondvar));

            if (!empty($item1) && !empty($item2)) {
                $tax_query = array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => $item1[0]->taxonomy,
                        'field' => 'slug',
                        'terms' => $item1[0]->slug
                    ),

                    array(
                        'taxonomy' => $item2[0]->taxonomy,
                        'field' => 'slug',
                        'terms' => $item2[0]->slug
                    )
                );

                $query->set('tax_query', $tax_query);
            }
            if ($item1[0]->taxonomy == 'location' && $item2[0]->taxonomy == 'topic') {
                wp_redirect($home_url . 'list/' . $secondvar . '/' . $firstvar);
                exit;
            }
        }
    }
}
add_action('pre_get_posts', 'j_mongabay_mega_query');

//fix topics links
function j_mongabay_topic_link($link, $term, $taxonomy)
{
    if ($taxonomy !== 'topic')
        return $link;

    return str_replace('topic/', 'list/', $link);
}
add_filter('term_link', 'j_mongabay_topic_link', 10, 3); // Fix topic taxonomy link

//fix locations links
function j_mongabay_location_link($link, $term, $taxonomy)
{
    if ($taxonomy !== 'location')
        return $link;

    return str_replace('location/', 'list/', $link);
}
add_filter('term_link', 'j_mongabay_location_link', 10, 3); // Fix location taxonomy link

//fix byline links
function j_mongabay_byline_link($link, $term, $taxonomy)
{
    if ($taxonomy !== 'byline')
        return $link;

    return str_replace('byline/', 'by/', $link);
}
add_filter('term_link', 'j_mongabay_byline_link', 10, 3); // Fix byline taxonomy link

// Customize RSS feed
remove_all_actions('do_feed_rss2');
add_action('do_feed_rss2', 'j_mongabay_feed_rss2', 10, 1);

function j_mongabay_feed_rss2()
{

    $rss_template = get_stylesheet_directory() . '/custom-code/feed-rss2.php';
    load_template($rss_template);
}


function custom_rss_feed_init()
{
    add_feed('custom-rss-feed', 'custom_rss_feed_callback');
}

function custom_rss_feed_callback()
{
    $search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $topics_str = isset($_GET['topic']) ? ($_GET['topic']) : '';
    $locations_str = isset($_GET['location']) ? ($_GET['location']) : '';

    $topics = array();
    $locations = array();

    if (!empty($topics_str)) {
        if (strpos($topics_str, ',') !== false) {
            $topics = explode(',', $topics_str);
        } else {
            $topics[] = $topics_str;
        }
    }

    if (!empty($locations_str)) {
        if (strpos($locations_str, ',') !== false) {
            $locations = explode(',', $locations_str);
        } else {
            $locations[] = $locations_str;
        }
    }

    // Custom query arguments
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 10,
        's' => $search_term,
        'tax_query' => array(
            'relation' => 'OR',
            array(
                'taxonomy' => 'topic',
                'field' => 'slug',
                'terms' => $topics,
            ),
            array(
                'taxonomy' => 'location',
                'field' => 'slug',
                'terms' => $locations,
            ),
        ),
    );

    $query = new WP_Query($args);

    // Generate RSS feed
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);

    echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?>';
?>
    <rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" <?php
                                                                                                                                                                                                                                                                                                                                    /**
                                                                                                                                                                                                                                                                                                                                     * Fires at the end of the RSS root to add namespaces.
                                                                                                                                                                                                                                                                                                                                     *
                                                                                                                                                                                                                                                                                                                                     * @since 2.0.0
                                                                                                                                                                                                                                                                                                                                     */
                                                                                                                                                                                                                                                                                                                                    do_action('rss2_ns');
                                                                                                                                                                                                                                                                                                                                    ?>>
        <channel>
            <title><?php wp_title_rss(); ?></title>
            <link><?php bloginfo_rss('url'); ?></link>
            <description><?php bloginfo_rss('description'); ?></description>
            <language><?php bloginfo_rss('language'); ?></language>
            <pubDate><?php echo date('r'); ?></pubDate>
            <generator>https://wordpress.org/?v=<?php bloginfo_rss('version'); ?></generator>
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <item>
                    <title><?php the_title_rss(); ?></title>
                    <link><?php the_permalink_rss(); ?></link>
                    <pubDate><?php echo get_post_time('r', true); ?></pubDate>
                </item>
            <?php endwhile; ?>
        </channel>
    </rss>
    <?php
    // Restore original post data
    wp_reset_postdata();
}

add_action('init', 'custom_rss_feed_init');

// Rss for posts with meta key 'grant'
function j_mongabay_rss_pre_get_posts($query)
{
    if ($query->is_feed && $query->is_main_query()) {
        if (isset($query->query_vars['grant']) && !empty($query->query_vars['grant'])) {
            // if you only want to allow 'alpha-numerics':
            $grant =  preg_replace("/[^a-zA-Z0-9]/", "", $query->query_vars['grant']);
            $query->set('meta_key', 'grant');
            $query->set('meta_value', $grant);
        }
    }
}
add_action('pre_get_posts', 'j_mongabay_rss_pre_get_posts'); // Add 'grant' to meta query

/*// Parallax Shortcode
function parallax_img($atts){
    extract(shortcode_atts(array('imagepath' => 'Image Needed','id' => '1', 'px_title' => 'Slide Title', 'title_color' => '#FFFFFF' , 'img_caption' => 'Your image caption'),$atts));
    return "<div class='clearfix'></div><div class='parallax-section full-height' data-parallax='scroll' id='".$id."' data-image-src='".$imagepath."' style='background-size: cover'><div class='featured-article-meta'><span class='subtitle' style='color:".$title_color."'>".$img_caption."</span></div></div><div class='clearfix'></div>";
}

function parallax_open() {
    //return "<div class='container'><div class='row justify-content-center'><div id='main' class='col-lg-8 single'>";
    return "<div class='tie-row main-content-row'><div class='main-content tie-col-md-12'><div class='entry-content entry clearfix'>";
}

function parallax_close() {
    return "</div></div></div>";
}
add_shortcode('parallax-img','parallax_img');
add_shortcode('open-parallax-content','parallax_open');
add_shortcode('close-parallax-content','parallax_close');*/

//Make custom taxonomy registered by PODs available in GraphQL
function j_add_pods_graphql_support($options)
{
    $options['show_in_graphql'] = true;
    $options['graphql_single_name'] = $options['labels']['name'];
    $options['graphql_plural_name'] = $options['labels']['singular_name'];
    return $options;
}
add_filter('pods_register_taxonomy_byline', 'j_add_pods_graphql_support'); //Byline available in GraphQL

//Resolve some post custom meta values for GraphQL
add_action(
    'graphql_register_types',
    function () {
        register_graphql_field('Post', 'featuredAs', [
            'type' => 'String',
            'description' => __('If article is featured', 'wp-graphql'),
            'resolve' => function ($post) {
                $featured = get_post_meta($post->ID, 'featured_as', true);
                return !empty($featured) ? $featured : 'simple';
            }
        ]);

        register_graphql_field('Post', 'bulletPoint1', [
            'type' => 'String',
            'description' => __('Bulletpoint 1', 'wp-graphql'),
            'resolve' => function ($post) {
                $bulletpoint = get_post_meta($post->ID, 'mog_bullet_0_mog_bulletpoint', true);
                return !empty($bulletpoint) ? $bulletpoint : null;
            }
        ]);

        register_graphql_field('Post', 'bulletPoint2', [
            'type' => 'String',
            'description' => __('Bulletpoint 2', 'wp-graphql'),
            'resolve' => function ($post) {
                $bulletpoint = get_post_meta($post->ID, 'mog_bullet_1_mog_bulletpoint', true);
                return !empty($bulletpoint) ? $bulletpoint : null;
            }
        ]);

        register_graphql_field('Post', 'bulletPoint3', [
            'type' => 'String',
            'description' => __('Bulletpoint 3', 'wp-graphql'),
            'resolve' => function ($post) {
                $bulletpoint = get_post_meta($post->ID, 'mog_bullet_2_mog_bulletpoint', true);
                return !empty($bulletpoint) ? $bulletpoint : null;
            }
        ]);

        register_graphql_field('Post', 'bulletPoint4', [
            'type' => 'String',
            'description' => __('Bulletpoint 4', 'wp-graphql'),
            'resolve' => function ($post) {
                $bulletpoint = get_post_meta($post->ID, 'mog_bullet_3_mog_bulletpoint', true);
                return !empty($bulletpoint) ? $bulletpoint : null;
            }
        ]);
    }
);

// Conditional logic to show or hide translated_by/ adapted_by POD fields
function j_post_edit_screen()
{
    $current_screen = get_current_screen();
    if ($current_screen->id === 'post') {
        //var_dump($current_screen);
        wp_register_script('trada', get_template_directory_uri() . '/js/lib/translated_adopted.min.js', array('jquery'), '1.0', true);
        wp_enqueue_script('trada');
    }
}
add_action('current_screen', 'j_post_edit_screen'); // Determine post editing screen to load conditional script

// Require featured image before publishing an article
if ($GLOBALS['pagenow'] == 'post-new.php' || $pagenow == 'post.php') :
    add_filter('wp_insert_post_data', function ($data, $postarr) {
        $post_id = isset($postarr['ID']) ? $postarr['ID'] : 0;
        $post_status = isset($data['post_status']) ? $data['post_status'] : '';
        $original_post_status = isset($postarr['original_post_status']) ? $postarr['original_post_status'] : '';

        if ($post_id && 'publish' === $post_status && 'publish' !== $original_post_status) {
            $post_type = get_post_type($post_id);
            if (post_type_supports($post_type, 'thumbnail') && !has_post_thumbnail($post_id)) {
                $data['post_status'] = 'draft';
            }
        }
        return $data;
    }, 10, 2);
endif;

if ($GLOBALS['pagenow'] == 'post-new.php' || $pagenow == 'post.php') :
    add_action('admin_notices', function () {
        $post = get_post();
        if ('publish' !== get_post_status($post->ID) && !has_post_thumbnail($post->ID)) { ?>
            <div id="message" class="error">
                <p><strong><?php _e('Please set Featured Image. Article cannot be published without one.'); ?></strong></p>
            </div>
    <?php
        }
    });
endif;

//Prevent storing badly formatted HTML in articles
add_filter('wp_insert_post_data', 'j_my_post_data_validator', '99');
function j_my_post_data_validator($data)
{
    $error_1 = strpos($data['post_content'], '<br');
    $error_2 = strpos($data['post_content'], '<span');
    $error_3 = strpos($data['post_content'], '<div');
    if ($data['post_type'] === 'post') {
        if ($error_1 || $error_2 || $error_3) {
            $data['post_status'] = 'pending';
            add_filter('redirect_post_location', 'j_my_post_redirect_filter', '99');
        }
    }
    return $data;
}

function j_my_post_redirect_filter($location)
{
    remove_filter('redirect_post_location', 'j_my_post_redirect_filter', '99');
    return add_query_arg('mongabay_error', 1, $location);
}

add_action('admin_notices', 'j_my_post_admin_notices');
function j_my_post_admin_notices()
{
    if (!isset($_GET['mongabay_error'])) return;
    switch (absint($_GET['mongabay_error'])) {
        case 1:
            $message = 'Invalid post data. Make sure post HTML content does not contain elements like span, br and div! This is most likely because of copy/paste content from elsewhere.';
            break;
        default:
            $message = 'Something went wrong';
    }
    echo '<div id="notice" class="error"><p>' . $message . '</p></div>';
}

// Remove meta boxes from post editing screen
function j_mongabay_remove_custom_fields()
{

    $post_types = get_post_types('', 'names');
    foreach ($post_types as $post_type) {
        remove_meta_box('postcustom', $post_type, 'normal');
    }
}
add_action('admin_menu', 'j_mongabay_remove_custom_fields'); // Remove custom fields from post editing screen

// Prevent from aading new location tags
function j_mongabay_prevent_terms($term, $taxonomy)
{

    if ('location' === $taxonomy && !current_user_can('activate_plugins')) {
        return new WP_Error('term_addition_blocked', __('You cannot add terms to this taxonomy'));
    }

    if ('topic' === $taxonomy && !current_user_can('activate_plugins')) {
        return new WP_Error('term_addition_blocked', __('You cannot add terms to this taxonomy'));
    }

    return $term;
}
add_action('pre_insert_term', 'j_mongabay_prevent_terms', 1, 2); // Prevent new terms to be added

// Prevent from adding new article format
function restrict_article_format_type_taxonomy_capabilities($args)
{
    if ( ! current_user_can( 'manage_options' ) ) {
        $args['capabilities'] = array(
            'manage_terms' => 'do_not_allow',
            'edit_terms' => 'do_not_allow',
            'delete_terms' => 'do_not_allow',
            'assign_terms' => 'read',
        );
    }
    return $args;
}
add_filter('register_taxonomy_args', 'restrict_article_format_type_taxonomy_capabilities', 10, 1);

function remove_add_new_category_button()
{
    global $pagenow;
    if ($pagenow == 'edit-tags.php' && isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'article_format_type') {
        echo '<style>.term-add-clone { display: none; }</style>';
    }
}
add_action('admin_head', 'remove_add_new_category_button');

// Register custom query vars
function j_mongabay_query_var($vars)
{

    $vars[] = 'section';
    $vars[] = 'nc1';
    $vars[] = 'nc2';
    return $vars;
}
add_filter('query_vars', 'j_mongabay_query_var'); // Register custom query vars

// Listings proper page title
function j_mongabay_custom_title()
{

    $firstvar = get_query_var('nc1');
    $secondvar = get_query_var('nc2');

    if (get_query_var('section') == 'list') {
        error_log('firstvar no está vacio second si está vacio');

        if (!empty($firstvar) && empty($secondvar)) {
            $item1 = get_terms(array('topic', 'location'), array('slug' => $firstvar));
            $title = $item1[0]->name;
            //_e( 'Conservation news on', TIELABS_TEXTDOMAIN );
            echo 'Conservation news on' . $title;
        }

        if (!empty($firstvar) && !empty($secondvar)) {
            error_log('first y sencondvar no están vacios');
            $item1 = get_terms(array('topic', 'location'), array('slug' => $firstvar));
            $item2 = get_terms(array('topic', 'location'), array('slug' => $secondvar));
            $title1 = $item1[0]->name;
            $title2 = $item2[0]->name;
            $title = $title1 . ' and ' . $title2;
            _e('Conservation news on', TIELABS_TEXTDOMAIN);
            echo ' ' . $title;
        }
    } else {
        wp_title('');
    }
}




/**************************************************************************************************************/
/******************************************* NEW JANNAH FUNCTIONS *********************************************/
/**************************************************************************************************************/

add_action('pre_get_posts', 'mongabay_author_archives');
function mongabay_author_archives($query)
{
    if (!$query->is_main_query() || is_admin() || !is_author()) return;

    $query->set('post_type', array('post', 'videos', 'podcasts', 'short-article', 'custom-story', 'specials'));
}

// add_action('init', 'jannah_post_components_order');
// function jannah_post_components_order()
// {

//     // Remove the default registeration, DON'T change the numbers here
//     remove_action('TieLabs/post_components', 'tie_post_about_author', 20);
//     remove_action('TieLabs/post_components', 'tie_read_next_posts', 30);
//     remove_action('TieLabs/post_components', 'tie_post_newsletter', 40);
//     remove_action('TieLabs/post_components', 'tie_post_next_prev', 50);
//     remove_action('TieLabs/post_components', 'tie_add_show_comments_button', 69);
//     remove_action('TieLabs/post_components', 'tie_post_comments', 70);

//     // The new Order, change the Numbers
//     add_action('TieLabs/post_components', 'tie_post_about_author', 20);
//     add_action('TieLabs/post_components', 'tie_read_next_posts', 70);
//     add_action('TieLabs/post_components', 'tie_post_newsletter', 40);
//     add_action('TieLabs/post_components', 'tie_post_next_prev', 50);
//     add_action('TieLabs/post_components', 'tie_add_show_comments_button', 69);
//     add_action('TieLabs/post_components', 'tie_post_comments', 30);
// }

add_shortcode('tie_tags', 'mongabay_extensions_sc_tags');
function mongabay_extensions_sc_tags($atts, $content = null)
{

    $tags = get_the_tags(); // Agregar punto y coma

    // Utilizar un búfer de salida para capturar la salida de the_tags
    ob_start();
    the_tags('<span class="tagcloud">', ' ', '</span>');
    $tags_output = ob_get_clean();

    return '
        <div class="tags-shortcode">
        <div class="mag-box-title the-global-title">
        <h3>' . esc_html__('Topics', TIELABS_TEXTDOMAIN) . '</h3>
        </div>' . $tags_output . '
        </div><!-- .tags-shortcode /-->
    ';
}

function insertar_carrusel_shortcode()
{
    ob_start();
    ?>
    <div id="container-carrusel">
        <div id="carrusel-top" class="carrusel"></div>
        <div id="carrusel-bottom" class="carrusel"></div>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('carrusel_imagenes', 'insertar_carrusel_shortcode');

if (!function_exists('tie_post_format_icon')) {
    function tie_post_format_icon($force = false, $echo = true)
    {
        $is_enabled = false;

        if (tie_get_option('thumb_overlay')) {
            $is_enabled = true;
        } elseif ($force) {
            $post_type = get_post_type();
            if ($post_type == 'podcasts' || $post_type == 'videos') {
                $is_enabled = true;
            }
        }

        // ----
        if (!$is_enabled) {
            return;
        }

        $code = '
            <div class="post-thumb-overlay-wrap">
                <div class="post-thumb-overlay">
                    <span class="tie-icon tie-media-icon"></span>
                </div>
            </div>
        ';

        if (!$echo) {
            return $code;
        }

        echo $code;
    }
}

function tools_widget_area()
{
    register_sidebar(array(
        'name'          => __('Tools Widget Area', 'text_domain'),
        'id'            => 'tools_widget_area',
        'description'   => __('Area for tools.', 'text_domain'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'tools_widget_area');

class Mongabay_Tools extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'mongabay_tools',
            __('Mongabay Tools', 'text_domain'),
            array('description' => __('Widget for Mongabay tools.', 'text_domain'),)
        );
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : __('New Title', 'text_domain');
        $images_top = !empty($instance['images_top']) ? $instance['images_top'] : array();
        $images_bottom = !empty($instance['images_bottom']) ? $instance['images_bottom'] : array();
    ?>
        <!-- Campos de configuración del widget -->
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <strong><?php _e('Top Images:', 'text_domain'); ?></strong>
        </p>
        <?php for ($i = 0; $i < 3; $i++) : ?>
            <p>
                <label for="<?php echo $this->get_field_id('image_top_url_' . $i); ?>"><?php echo esc_html__('Image URL ', 'text_domain') . ($i + 1); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('image_top_url_' . $i); ?>" name="<?php echo $this->get_field_name('images_top'); ?>[<?php echo $i; ?>][image_url]" type="text" value="<?php echo isset($images_top[$i]['image_url']) ? esc_attr($images_top[$i]['image_url']) : ''; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image_top_link_' . $i); ?>"><?php echo esc_html__('Link ', 'text_domain') . ($i + 1); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('image_top_link_' . $i); ?>" name="<?php echo $this->get_field_name('images_top'); ?>[<?php echo $i; ?>][link_url]" type="text" value="<?php echo isset($images_top[$i]['link_url']) ? esc_attr($images_top[$i]['link_url']) : ''; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image_top_text_' . $i); ?>"><?php echo esc_html__('Text  ', 'text_domain') . ($i + 1); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('image_top_text_' . $i); ?>" name="<?php echo $this->get_field_name('images_top'); ?>[<?php echo $i; ?>][link_text]" type="text" value="<?php echo isset($images_top[$i]['link_text']) ? esc_attr($images_top[$i]['link_text']) : ''; ?>">
            </p>
        <?php endfor; ?>
        <p>
            <strong><?php _e('Bottom Images:', 'text_domain'); ?></strong>
        </p>
        <?php for ($i = 0; $i < 3; $i++) : ?>
            <p>
                <label for="<?php echo $this->get_field_id('image_bottom_url_' . $i); ?>"><?php echo esc_html__('Image URL ', 'text_domain') . ($i + 1); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('image_bottom_url_' . $i); ?>" name="<?php echo $this->get_field_name('images_bottom'); ?>[<?php echo $i; ?>][image_url]" type="text" value="<?php echo isset($images_bottom[$i]['image_url']) ? esc_attr($images_bottom[$i]['image_url']) : ''; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image_bottom_link_' . $i); ?>"><?php echo esc_html__('Link ', 'text_domain') . ($i + 1); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('image_bottom_link_' . $i); ?>" name="<?php echo $this->get_field_name('images_bottom'); ?>[<?php echo $i; ?>][link_url]" type="text" value="<?php echo isset($images_bottom[$i]['link_url']) ? esc_attr($images_bottom[$i]['link_url']) : ''; ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image_bottom_text_' . $i); ?>"><?php echo esc_html__('Text ', 'text_domain') . ($i + 1); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('image_bottom_text_' . $i); ?>" name="<?php echo $this->get_field_name('images_bottom'); ?>[<?php echo $i; ?>][link_text]" type="text" value="<?php echo isset($images_bottom[$i]['link_text']) ? esc_attr($images_bottom[$i]['link_text']) : ''; ?>">
            </p>
        <?php endfor; ?>
<?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['images_top'] = array();
        $instance['images_bottom'] = array();
        for ($i = 0; $i < 3; $i++) {
            $instance['images_top'][$i]['image_url'] = isset($new_instance['images_top'][$i]['image_url']) ? esc_url_raw($new_instance['images_top'][$i]['image_url']) : '';
            $instance['images_top'][$i]['link_url'] = isset($new_instance['images_top'][$i]['link_url']) ? esc_url_raw($new_instance['images_top'][$i]['link_url']) : '';
            $instance['images_top'][$i]['link_text'] = isset($new_instance['images_top'][$i]['link_text']) ? sanitize_text_field($new_instance['images_top'][$i]['link_text']) : '';
            $instance['images_bottom'][$i]['image_url'] = isset($new_instance['images_bottom'][$i]['image_url']) ? esc_url_raw($new_instance['images_bottom'][$i]['image_url']) : '';
            $instance['images_bottom'][$i]['link_url'] = isset($new_instance['images_bottom'][$i]['link_url']) ? esc_url_raw($new_instance['images_bottom'][$i]['link_url']) : '';
            $instance['images_bottom'][$i]['link_text'] = isset($new_instance['images_bottom'][$i]['link_text']) ? sanitize_text_field($new_instance['images_bottom'][$i]['link_text']) : '';
        }
        return $instance;
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        if (!empty($instance['images_top'])) {
            echo '<div class="images-top">';
            foreach ($instance['images_top'] as $image) {
                if (!empty($image['image_url']) && !empty($image['link_url']) && !empty($image['link_text'])) {
                    echo '<a href="' . esc_url($image['link_url']) . '" target="_blank">';
                    echo '<img src="' . esc_url($image['image_url']) . '" alt="' . esc_attr($image['link_text']) . '">';
                    echo '</a>';
                    echo '<p><a href="' . esc_url($image['link_url']) . '" target="_blank">' . esc_html($image['link_text']) . '</a></p>';
                }
            }
            echo '</div>';
        }
        if (!empty($instance['images_bottom'])) {
            echo '<div class="images-bottom">';
            foreach ($instance['images_bottom'] as $image) {
                if (!empty($image['image_url']) && !empty($image['link_url']) && !empty($image['link_text'])) {
                    echo '<a href="' . esc_url($image['link_url']) . '" target="_blank">';
                    echo '<img src="' . esc_url($image['image_url']) . '" alt="' . esc_attr($image['link_text']) . '">';
                    echo '</a>';
                    echo '<p><a href="' . esc_url($image['link_url']) . '" target="_blank">' . esc_html($image['link_text']) . '</a></p>';
                }
            }
            echo '</div>';
        }
        echo $args['after_widget'];
    }
}

function register_mongabay_tools() {

    register_widget('Mongabay_Tools');
}
add_action('widgets_init', 'register_mongabay_tools');

function jannah_custom_post_types_support($supported_post_types){
    $custom_post_types = array('videos', 'podcasts');
    $supported_post_types = array_merge($supported_post_types, $custom_post_types);

    return $supported_post_types;
}
add_filter('TieLabs/Settings/default_post_types', 'jannah_custom_post_types_support');

add_shortcode('tie_list', 'related_post_grid_shortcode');
function related_post_grid_shortcode($atts)
{
    $post_id = get_the_ID();
    $taxonomies = array('location', 'byline', 'topic');
    $terms = wp_get_post_terms($post_id, $taxonomies);
    $author_id = get_post_field('post_author', $post_id);
    $template = 'default';
    $atts = shortcode_atts(
        array(
            'posts_per_page' => -1,
            'columns' => 3,
        ),
        $atts,
        'tie_list'
    );

    $query = new WP_Query(
        array(
            'post_type' => array('post', 'videos', 'podcasts', 'custom-story'),
            'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'location',
                    'field' => 'term_id',
                    'terms' => wp_list_pluck($terms, 'term_id')
                ),
                array(
                    'taxonomy' => 'byline',
                    'field' => 'term_id',
                    'terms' => wp_list_pluck($terms, 'term_id')
                ),
                array(
                    'taxonomy' => 'topic',
                    'field' => 'term_id',
                    'terms' => wp_list_pluck($terms, 'term_id')
                ),
                'author__in' => array($author_id)
            ),
            'post__not_in' => array($post_id),
            'posts_per_page' => 8
        )
    );

    $output .= '<div class="related-slider section-item is-first-section full-width">
                    <h1>' . esc_html__('Related Stories', TIELABS_TEXTDOMAIN) . '</h1>
                    <div class="mag-box scrolling-box">
                        <ul class="slider-arrow-nav"></ul>
                        <div class="scrolling-slider scrolling-box-slider" role="toolbar" style="display: block;">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $author = tie_get_author();
            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            $title = get_the_title();

            $output .= '<div class="slide">';
            $output .= '<div class="slide-img">';
            $output .= '<a href="' . get_permalink() . '" title="' . $title . '" class="all-over-thumb-link"><span class="screen-reader-text">' . $title . '</span>';
            $output .= '<img src="' . $thumbnail . '">';
            $output .= '</a>';
            $output .= '</div>';
            $output .= '<div class="slide-content post-details">';
            $output .= '<h2 class="post-title"><a href="' . get_permalink() . '">' . $title . '</a></h2>';
            $output .= '<div class="post-meta clearfix">' . $author . '</div>';
            $output .= '</div></div>';
        }
    } else {
        $output .= '<p>No results</p>';
    }

    // $output .= '<div class="slider-nav-wrapper"><ul class="tie-slider-nav"></ul></div>';
    $output .= '</div></div></div>';

    wp_reset_postdata();

    return $output;
}

add_shortcode('toggle', 'related_post_toggle_shortcode');
function related_post_toggle_shortcode($atts, $content = null)
{
    $post_id = get_the_ID();
    $taxonomies = array('location', 'byline', 'topic');
    $terms = wp_get_post_terms($post_id, $taxonomies);
    $author_id = get_post_field('post_author', $post_id);
    $template = 'default';
    $atts = shortcode_atts(
        array(
            'state' => 'open',
            'title' => '',
            'posts_per_page' => -1,
            'columns' => 3,
        ),
        $atts,
        'toggle'
    );

    extract($atts);

    $state = ($state == 'open') ? 'tie-sc-open' : 'tie-sc-close';

    $query = new WP_Query(
        array(
            'post_type' => array('post', 'videos', 'podcasts', 'custom-story'),
            'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'location',
                    'field' => 'term_id',
                    'terms' => wp_list_pluck($terms, 'term_id')
                ),
                array(
                    'taxonomy' => 'byline',
                    'field' => 'term_id',
                    'terms' => wp_list_pluck($terms, 'term_id')
                ),
                array(
                    'taxonomy' => 'topic',
                    'field' => 'term_id',
                    'terms' => wp_list_pluck($terms, 'term_id')
                ),
                'author__in' => array($author_id)
            ),
            'post__not_in' => array($post_id),
            'posts_per_page' => 4
        )
    );

    $output .= '<div class="related-toggle tie-main-slider main-slider boxed-four-taller-slider boxed-slider tie-slick-slider-wrapper">
                    <h1 class="toggle-head">' . $title . '</h1>
                    <div class="main-slider-inner">
                        <div "class="tie-slick-slider slick-initialized slick-slider slick-dotted" role="toolbar">';

    if ($query->have_posts()) {
        $count = 0;
        while ($query->have_posts()) {
            $query->the_post();
            $author = tie_get_author();
            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            $title = get_the_title();

            $class = ($count === 0) ? 'expanded' : 'collapsed';
            $output .= '<div style="background-image: url(\'' . $thumbnail . '\'); width: 332px; display: block;" class="slide tie-slide-5 tie-standard slick-slide slick-cloned ' . $class . '">';
            $output .= '<a href="' . get_permalink() . '" title="' . $title . '" class="all-over-thumb-link"></a>';
            $output .= '<div class="thumb-overlay"><div class="thumb-content">';
            $output .= '<h2 class="thumb-title"><a href="' . get_permalink() . '">' . $title . '</a></h2>';
            $output .= '<div class="thumb-meta clearfix">' . $author . '</div>';
            $output .= '</div></div></div>';
            $count++;
        }
    } else {
        $output .= '<p>No results</p>';
    }

    // $output .= '<div class="slider-nav-wrapper"><ul class="tie-slider-nav"></ul></div>';
    $output .= '</div></div>';
    $output .= '<div class="toggle-content">' .
        do_shortcode($content) . '
            </div></div>';

    $output .= '
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let slides = document.querySelectorAll(".slide");
            let currentIndex = 0;

            function showNextSlide() {
                slides[currentIndex].classList.remove("expanded");
                slides[currentIndex].classList.add("collapsed");

                currentIndex = (currentIndex + 1) % slides.length;

                slides[currentIndex].classList.remove("collapsed");
                slides[currentIndex].classList.add("expanded");
            }

            setInterval(showNextSlide, 3000);
        });
    </script>';

    wp_reset_postdata();

    return $output;
}


function bylines_grid_shortcode($atts) {
    /*$byline_terms = get_terms(array(
    'taxonomy' => 'byline',
    'hide_empty' => false, // Incluso los términos sin publicaciones asociadas se mostrarán
    ));

    // Verificar si se encontraron términos
    if (!empty($byline_terms)) {
        // Iterar sobre los términos
        foreach ($byline_terms as $term) {
            // Obtener todos los metadatos del término
            $term_meta = get_term_meta($term->term_id);
            
            // Mostrar el nombre del término
            echo "<h3>{$term->name}</h3>";
            
            // Verificar si hay metadatos asociados con el término
            if (!empty($term_meta)) {
                echo "<pre>";
                // Iterar sobre los metadatos y mostrarlos
                foreach ($term_meta as $key => $value) {
                    // Ignorar el prefijo '_'
                    if (substr($key, 0, 1) !== '_') {
                        echo "{$key}: " . json_encode($value) . "\n";
                    }
                }
                echo "</pre>";
            } else {
                echo "<p>No hay campos personalizados asociados con este término.</p>";
            }
        }
    } else {
        echo "<p>No se encontraron términos de byline.</p>";
    }*/
    $atts = shortcode_atts(
        array(
            'initial_count' => 9,
            'columns' => 3,
        ),
        $atts,
        'byline_grid'
    );

    $byline_terms = get_terms(array(
        'taxonomy' => 'byline',
        'hide_empty' => true,
        'number' => $atts['initial_count'],
        'meta_query' => array(
            'key' => 'author_type',
            'value' => 'staff_writer',
            'compare' => '=',
        ),
    ));

    $output = '<div class="byline-grid" style="display: grid; grid-template-columns: repeat(' . $atts['columns'] . ', 1fr); gap: 10px;">';

    if (!empty($byline_terms)) {
        foreach ($byline_terms as $term) {
            $pod = pods('byline', $term->term_id);
            $custom_image_url = $pod->display('cover_image_url');
            $term_name = $term->name;
            $output .= '<div class="byline-grid-item" style="border: 1px solid #ddd; padding: 10px;">';
            $output .= '<a href="' . home_url('/') . 'by/' . esc_html($term->slug) . '">';
            if ($custom_image_url) {
                $output .= '<img src="' . esc_url($custom_image_url) . '" alt="' . esc_attr($term_name) . '" style="max-width: 100%;">';
            } else {
                $output .= '<span class="meta-author-circle"></span>';
            }
            $output .= '<h2>' . esc_html($term_name) . '</h2>';
            $output .= '</a></div>';
        }
    } else {
        $output .= '<p>No hay términos de byline disponibles</p>';
    }

    $output .= '</div>';
    if (count($byline_terms) >= $atts['initial_count']) {
        $output .= '<a class="block-pagination-byline next-posts show-more-button load-more-button" href="#" data-text="' . esc_html__('Load More', TIELABS_TEXTDOMAIN) . '">' . esc_html__('Load More', TIELABS_TEXTDOMAIN) . '</a>';
    }

    return $output;
}
add_shortcode('byline_grid', 'bylines_grid_shortcode');

add_action('wp_ajax_load_more_byline_terms', 'load_more_byline_terms');
add_action('wp_ajax_nopriv_load_more_byline_terms', 'load_more_byline_terms'); // Para usuarios no autenticados

function load_more_byline_terms() {

    if (isset($_POST['action']) && $_POST['action'] == 'load_more_byline_terms') {
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;

        $args = array(
            'taxonomy' => 'byline',
            'hide_empty' => true,
            'number' => 9,
            'offset' => $offset,
        );

        $byline_terms = get_terms($args);

        if (!empty($byline_terms)) {
            $output = '';
            foreach ($byline_terms as $term) {
                $output .= '<div class="byline-grid-item" style="border: 1px solid #ddd; padding: 10px;">';
                $output .= '<a href="' . home_url('/') . 'by/' . esc_html($term->slug) . '">';
                if ($custom_image_url) {
                    $output .= '<img src="' . esc_url($custom_image_url) . '" alt="' . esc_attr($term_name) . '" style="max-width: 100%;">';
                } else {
                    $output .= '<span class="meta-author-circle"></span>';
                }
                $output .= '<h2>' . esc_html($term->name) . '</h2>';
                $output .= '</a></div>';
            }

            echo $output;
        } else {

            echo 'No hay más términos de byline disponibles';
        }
    }

    wp_die();
}

function post_grid_formats_shortcode($atts) {
    $template = 'default';
    $atts = shortcode_atts(
        array(
            'posts_per_page' => -1,
            'columns' => 3,
            'post_type' => 'post'
        ),
        $atts,
        'post_grid'
    );

    $current_post_id = get_the_ID();

    $query = new WP_Query(
        array(
            'post_type' => $atts['post_type'],
            'posts_per_page' => $atts['posts_per_page'],
            'post__not_in' => array($current_post_id),
        )
    );

    $block_args = apply_filters('TieLabs/archives/args', array(
        'uncropped_image' => isset($uncropped_image) ? $uncropped_image : TIELABS_THEME_SLUG . '-image-post',
        'category_meta'   => isset($category_meta)   ? $category_meta   : true,
        'post_meta'       => isset($post_meta)       ? $post_meta       : true,
        'excerpt'         => isset($excerpt)         ? $excerpt         : true,
        'excerpt_length'  => isset($excerpt_length)  ? $excerpt_length  : true,
        'read_more'       => isset($read_more)       ? $read_more       : true,
        'read_more_text'  => isset($read_more_text)  ? $read_more_text  : false,
        'media_overlay'   => isset($media_overlay)   ? $media_overlay   : true,
        'title_length'    => 0,
        'is_full'         => !TIELABS_HELPER::has_sidebar(),
        'is_category'     => is_category(),
    ));
    $settings = str_replace('"', '\'', wp_json_encode($block_args));
    $output = '<div class="last-post-type mag-box wide-post-box">
                <div class="container-wrapper">
                    <div class="mag-box-container clearfix">
                        <ul id="posts-container" data-layout="' . $template . '" data-settings="' . $settings . '" class="posts-items">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $author = tie_get_author();
            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            $title = get_the_title();
            $class = tie_get_post_class();
            //$post_type = get_post_type();

            $output .= '<li class="item-class-' . esc_html($class) . '">';
            $output .= '<a href="' . get_permalink() . '" class="post-thumb">';
            $output .= '<div class="post-thumb-overlay-wrap">
                            <div class="post-thumb-overlay">
                                <span class="tie-icon tie-media-icon"></span>
                            </div>
                        </div>';
            $output .= '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr($title) . '" class="attachment-jannah-image-large size-jannah-image-large wp-post-image">';
            $output .= '</a>';
            $output .= '<div class="post-details">';
            $output .= '<h2 class="thumb-title"><a href="' . get_permalink() . '">' . $title . '</a></h2>';
            $output .= '<div class="thumb-meta clearfix">' . $author . '</div>';
            $output .= '</div>';
            $output .= '</li>';
        }
    } else {
        $output .= '<p>No hay posts disponibles</p>';
    }

    $output .= '</ul></div></div></div>';

    wp_reset_postdata();

    return $output;
}
add_shortcode('post_grid', 'post_grid_formats_shortcode');

function slider_formats_shortcode($atts){
    $atts = shortcode_atts(
        array(
            'posts_per_page' => -1,
            'post_type' => 'post' // Tipo de post por defecto
        ),
        $atts,
        'articles_slider'
    );

    $current_post_id = get_the_ID();

    $query = new WP_Query(
        array(
            'post_type' => $atts['post_type'],
            'posts_per_page' => $atts['posts_per_page'],
            'post__not_in' => array($current_post_id),
        )
    );

    $output = '';

    if ($query->have_posts()) {
        // Check if AMP endpoint
        if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
            $output .= '<amp-carousel width="700" height="418" type="slides" layout="responsive" class="i-amphtml-layout-responsive i-amphtml-layout-size-defined i-amphtml-element i-amphtml-built i-amphtml-layout" i-amphtml-layout="responsive">';
            
            while ($query->have_posts()) {
                $query->the_post();
                $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                $title = get_the_title();

                $output .= '<div class="slide post-content-slide">';
                $output .= '<a href="' . get_permalink() . '">';
                $output .= '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr($title) . '">';
                $output .= '<h2 class="thumb-title">' . $title . '</h2>';
                $output .= '</a>';
                $output .= '</div><!-- post-content-slide -->';
            }

            $output .= '</amp-carousel>';
        } else {
            $loader_icon = function_exists('tie_get_ajax_loader') ? tie_get_ajax_loader(false) : '';
            
            $output .= '
            <div class="tie-main-slider main-slider wide-next-prev-slider-wrapper wide-slider-wrapper centered-title-slider tie-slick-slider-wrapper mngb-page-slider" data-autoplay="true" data-speed="3000">
                <div class="main-slider-inner">
                    <div class="tie-slick-slider">';
                    
            while ($query->have_posts()) {
                $query->the_post();
                $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                $title = get_the_title();
                $author = tie_get_author();

                $output .= '<div style="background-image: url(' . esc_url($thumbnail) . ')" class="slide tie-thumb slick-center">';
                $output .= '<a href="' . get_permalink() . '" class="all-over-thumb-link"></a>';
                $output .= '<div class="thumb-overlay"><div class="thumb-content">';             
                $output .= '<h2 class="thumb-title"><a href="' . get_permalink() . '">' . $title . '</a></h2>';
                $output .= '<div class="thumb-meta clearfix">' . $author . '</div>';
                $output .= '</div></div></div><!-- post-content-slide -->';
            }

            $output .= '
                        <div class="slider-nav-wrapper">
                            <ul class="tie-slider-nav"></ul>
                        </div>
                    </div><!-- tie-slick-slider -->
                </div><!-- post-content-slideshow -->
            </div><!-- post-content-slideshow-outer -->
            ';
        }
    } else {
        $output .= '<p>No hay posts disponibles</p>';
    }

    wp_reset_postdata();

    return $output;
}
add_shortcode('articles_slider', 'slider_formats_shortcode');

/*function mongabay_post_block_comments($atts) {
    // Asegurarse de que el archivo de comentarios está disponible.
    if (post_password_required()) {
        return '<p>Esta publicación está protegida con contraseña. Ingrese la contraseña para ver los comentarios.</p>';
    }

    ob_start();

    // Mostrar comentarios existentes si hay alguno
    if (have_comments()) {
        wp_list_comments();
    }

    // Mostrar el formulario de comentarios si los comentarios están abiertos
    if (comments_open()) {
        comment_form();
    } else {
        echo '<p>Los comentarios están cerrados.</p>';
    }

    $comments_html = ob_get_clean();
    return $comments_html;
}

add_shortcode('tie_login', 'mongabay_post_block_comments');*/

add_shortcode('author', 'mongabay_extensions_sc_author_info');
function mongabay_extensions_sc_author_info($atts, $content = null) {
    global $post;

    $author = get_the_author_meta('display_name', $post->post_author);
    $post_id = get_the_ID();
    $translated_adapted = get_post_meta($post_id, "translated_adapted", true);
    $translator = get_post_meta($post_id, "translated_by", true);
    $adaptor = get_post_meta($post_id, "adapted_by", true);

    $output = '';

    $output .= '<div class="mongabay-post-credits">
                    <div class="post-credits">
                        <div class="mag-box-title the-global-title">
                        <h3>' . esc_attr('Credits', TIELABS_TEXTDOMAIN) . '</h3>
                        </div>
                    </div>
                    <div class="post-authors">
                        <div class="about-author container-wrapper about-author-' . esc_attr($author) . '">
                            <div class="author-avatar">
                                <a href="' . esc_url(get_author_posts_url($post->post_author)) . '">
                                ' . get_avatar($post->post_author, 96) . '
                                </a>
                            </div>
                            <div class="author-info">
                                <a href="' . esc_url(get_author_posts_url($post->post_author)) . '">
                                    <h3 class="author-name">' . esc_html($author) . '</h3>
                                </a>
                                <div class="author-bio">
                                    <span>' . esc_attr('Editor', TIELABS_TEXTDOMAIN) . '</span>
                                </div>
                            </div>
                        </div>';

    if (($translated_adapted == 'adapted' || $translated_adapted == 'translated')) {
        if ($translated_adapted == 'adapted' && !empty($adaptor)) {
            $string_title = 'Adapted by ';
            $translator_adaptor = $adaptor;
        } elseif ($translated_adapted == 'translated' && !empty($translator)) {
            $string_title = 'Translated by ';
            $translator_adaptor = $translator;
        }

        $output .= '<div class="about-author container-wrapper">
                        <div class="author-avatar">
                            <span class="meta-author-circle">
                                <a href="' . home_url('/') . 'by/' . $translator_adaptor['slug'] . '"></a>
                            </span>
                        </div>
                        <div class="author-info">
                            <h3 class="author-name">
                                <a href="' . home_url('/') . 'by/' . $translator_adaptor['slug'] . '">' . $translator_adaptor['name'] . '</a>
                            </h3>
                            <div class="author-bio">
                                <span>' . esc_attr($string_title, TIELABS_TEXTDOMAIN) . '</span>
                            </div>
                        </div>
                    </div>
                </div></div>';
    }
    // Display translated_by or adapted_by if available
    return $output;
}

function get_latest_podcast_audio_source() {
    $args = array(
        'post_type'      => 'podcasts',
        'posts_per_page' => 1,
        'order'          => 'DESC',
        'orderby'        => 'date'
    );

    $latest_podcast = new WP_Query($args);

    if ($latest_podcast->have_posts()) {
        while ($latest_podcast->have_posts()) {
            $latest_podcast->the_post();
            $pods_podcast = pods_field( 'podcast_source', get_the_ID() );
            $post_title = get_the_title();
            $post_thumbnail = get_the_post_thumbnail(get_the_ID(), 'medium');

            wp_reset_postdata();

            if ($pods_podcast) {
                return '<div class="audio-podcast">
                            <div class="audio-image">' . $post_thumbnail . '</div>
                            <div class="audio-content-right">
                                <div class="audio-title"><h3>' . $post_title . '</h3></div>
                                    <div class="podcasts-controls">' . do_shortcode('[audio src="' . esc_url($pods_podcast) . '"]') . '
                                    </div>
                                </div>
                        </div>';
            } else {
                return 'No audio source found.';
            }
        }
    } else {
        return 'No podcasts found.';
    }
}

function audio_source_shortcode() {
    return get_latest_podcast_audio_source();
}
add_shortcode('latest_podcast', 'audio_source_shortcode');