<?php
add_action('init', 'mongabay_tax_register_entities', 0);
function mongabay_tax_register_entities()
{

	$labels = array(
		'name'              => _x('Entities', 'taxonomy general name'),
		'singular_name'     => _x('Entity', 'taxonomy singular name'),
		'search_items'      => __('Search Entities'),
		'popular_items'     => __('Popular Entities'),
		'all_items'         => __('All Entities'),
		'parent_item'       => NULL,
		'parent_item_colon' => NULL,
		'edit_item'         => __('Edit Entity'),
		'update_item'       => __('Update Entity'),
		'add_new_item'      => __('Add New Entity'),
		'new_item_name'     => __('New Entity Name'),
		'separate_items_with_commas' => __('Separate entities with commas'),
		'add_or_remove_items'        => __('Add or remove entities'),
		'choose_from_most_used'      => __('Choose from the most used entities'),
		'not_found'                  => __('No entities found.'),
		'menu_name'         => __('Entities'),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array(
			'with_front' => true,
			'slug' => 'entity'
		),
		'show_in_rest'          => true,
		'rest_base'             => 'entity',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'show_in_graphql' => true,
		'graphql_single_name' => 'Entity',
		'graphql_plural_name' => 'Entities',
	);

	register_taxonomy( 'entity', array('post', 'videos', 'podcasts', 'short-article', 'custom-story', 'series-article'), $args );
}
