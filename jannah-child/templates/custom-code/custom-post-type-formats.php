<?php

// Registering Video CPT
function register_custom_post_type_video() {
    $labels = array(
        'name'              => _x( 'Videos', 'Post type general name' ),
        'singular_name'     => _x( 'Video', 'Post type singular name' ),
        'search_items'      => __( 'Search Videos' ),
        'popular_items'     => __( 'Popular Videos' ),
        'all_items'         => __( 'All Videos' ),
        'parent_item'       => null,
        'parent_item_colon' => null,
        'edit_item'         => __( 'Edit Video' ),
        'update_item'       => __( 'Update Video' ),
        'add_new_item'      => __( 'Add New Video' ),
        'new_item_name'     => __( 'New Video Name' ),
    );

    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'menu_icon'         => 'dashicons-video-alt3',
        'menu_position'     => 4,
        'show_in_menu'      => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'videos' ),
        'capability_type'   => 'post',
        'supports'          => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author' ),
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'rest_base'         => 'videos',
        'show_in_graphql'   => true,
        'graphql_single_name' => 'Video',
        'graphql_plural_name' => 'Videos',
    );

    register_post_type( 'videos', $args );
    
}
add_action( 'init', 'register_custom_post_type_video', 0 );

// Registering Podcast CPT
function register_custom_post_type_podcast() {
    $labels = array(
        'name'              => _x( 'Podcasts', 'Post type general name' ),
        'singular_name'     => _x( 'Podcast', 'Post type singular name' ),
        'search_items'      => __( 'Search Podcasts' ),
        'popular_items'     => __( 'Popular Podcasts' ),
        'all_items'         => __( 'All Podcasts' ),
        'parent_item'       => null,
        'parent_item_colon' => null,
        'edit_item'         => __( 'Edit Podcast' ),
        'update_item'       => __( 'Update Podcast' ),
        'add_new_item'      => __( 'Add New Podcast' ),
        'new_item_name'     => __( 'New Podcast Name' ),
    );

    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'menu_icon'         => 'dashicons-format-audio',
        'menu_position'     => 4,
        'show_in_menu'      => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'podcasts' ),
        'capability_type'   => 'post',
        'supports'          => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author' ),
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'rest_base'         => 'podcasts',
        'show_in_graphql'   => true,
        'graphql_single_name' => 'Podcast',
        'graphql_plural_name' => 'Podcasts',
    );

    register_post_type( 'podcasts', $args );
}

add_action( 'init', 'register_custom_post_type_podcast', 0 );

// Registering Short News CPT
function register_custom_post_type_short_article() {
    $labels = array(
        'name'              => _x( 'Short Articles', 'Post type general name' ),
        'singular_name'     => _x( 'Short Article', 'Post type singular name' ),
        'search_items'      => __( 'Search Short Articles' ),
        'popular_items'     => __( 'Popular Short Articles' ),
        'all_items'         => __( 'All Short Articles' ),
        'parent_item'       => null,
        'parent_item_colon' => null,
        'edit_item'         => __( 'Edit Short Article' ),
        'update_item'       => __( 'Update Short Article' ),
        'add_new_item'      => __( 'Add New Short Article' ),
        'new_item_name'     => __( 'New Short Article Name' ),
    );

    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'menu_icon'         => 'dashicons-format-aside',
        'menu_position'     => 4,
        'show_in_menu'      => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'short-articles' ),
        'capability_type'   => 'post',
        'supports'          => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author' ),
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'rest_base'         => 'short_articles',
        'show_in_graphql'   => true,
        'graphql_single_name' => 'ShortArticle',
        'graphql_plural_name' => 'ShortArticles',
    );

    register_post_type( 'short-article', $args );
}

add_action( 'init', 'register_custom_post_type_short_article', 0 );

// Registering Feature Stories CPT
function register_custom_post_type_custom_story() {
    $labels = array(
        'name'              => _x( 'Custom Stories', 'Post type general name' ),
        'singular_name'     => _x( 'Custom Story', 'Post type singular name' ),
        'search_items'      => __( 'Search Custom Stories' ),
        'popular_items'     => __( 'Popular Custom Stories' ),
        'all_items'         => __( 'All Custom Stories' ),
        'parent_item'       => null,
        'parent_item_colon' => null,
        'edit_item'         => __( 'Edit Custom Stories' ),
        'update_item'       => __( 'Update Custom Stories' ),
        'add_new_item'      => __( 'Add New Custom Stories' ),
        'new_item_name'     => __( 'New Custom Stories Name' ),
    );

    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'menu_icon'         => 'dashicons-format-image',
        'menu_position'     => 4,
        'show_in_menu'      => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'custom-stories' ),
        'capability_type'   => 'post',
        'supports'          => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author' ),
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'rest_base'         => 'custom_stories',
        'show_in_graphql'   => true,
        'graphql_single_name' => 'CustomStory',
        'graphql_plural_name' => 'CustomStories',
    );

    register_post_type( 'custom-story', $args );
}

add_action( 'init', 'register_custom_post_type_custom_story', 0 );

// Registering Specials CPT
function register_custom_post_type_specials() {
    $labels = array(
        'name'              => _x( 'Special Issues', 'Post type general name' ),
        'singular_name'     => _x( 'Special Issue', 'Post type singular name' ),
        'search_items'      => __( 'Search Special Issues' ),
        'popular_items'     => __( 'Popular Special Issues' ),
        'all_items'         => __( 'All Special Issues' ),
        'parent_item'       => null,
        'parent_item_colon' => null,
        'edit_item'         => __( 'Edit Special Issue' ),
        'update_item'       => __( 'Update Special Issue' ),
        'add_new_item'      => __( 'Add New Special Issue' ),
        'new_item_name'     => __( 'New Special Issue Name' ),
    );

    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'menu_icon'         => 'dashicons-images-alt',
        'menu_position'     => 4,
        'show_in_menu'      => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'specials' ),
        'capability_type'   => 'post',
        'supports'          => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author' ),
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'rest_base'         => 'specials',
        'show_in_graphql'   => true,
        'graphql_single_name' => 'SpecialsArticle',
        'graphql_plural_name' => 'SpecialsArticles',
    );

    register_post_type( 'specials', $args );
}

add_action( 'init', 'register_custom_post_type_specials', 0 );

?>