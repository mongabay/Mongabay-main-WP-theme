<?php
/**
 * Archives
 *
 * This template can be overridden by copying it to your-child-theme/templates/archives.php.
 *
 * HOWEVER, on occasion TieLabs will need to update template files and you
 * will need to copy the new files to your child theme to maintain compatibility.
 *
 * @author   TieLabs
 * @version  7.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

// Detect if custom taxonomies are displayed
$tax_obj = get_query_var('tax_query');

if (is_main_query() && ($tax_obj[0]['taxonomy'] == 'topic' || $tax_obj[0]['taxonomy'] == 'location')) {
	get_template_part('templates/taxonomy', 'custom');
	exit;
}

// Prepare the posts settings
$block_args = apply_filters( 'TieLabs/archives/args', array(
	'uncropped_image' => isset( $uncropped_image ) ? $uncropped_image : TIELABS_THEME_SLUG.'-image-post',
	'category_meta'   => isset( $category_meta )   ? $category_meta   : true,
	'post_meta'       => isset( $post_meta )       ? $post_meta       : true,
	'excerpt'         => isset( $excerpt )         ? $excerpt         : true,
	'excerpt_length'  => isset( $excerpt_length )  ? $excerpt_length  : true,
	'read_more'       => isset( $read_more )       ? $read_more       : true,
	'read_more_text'  => isset( $read_more_text )  ? $read_more_text  : false,
	'media_overlay'   => isset( $media_overlay )   ? $media_overlay   : true,
	'title_length'    => 0,
	'is_full'         => ! TIELABS_HELPER::has_sidebar(),
	'is_category'     => is_category(),
) );

$count    = 0;
$settings = str_replace( '"', '\'', wp_json_encode( $block_args ));
$layout   = str_replace( '_', '-', $layout );

// Overlay & Overlay with Spaces
if( $layout == 'overlay' || $layout == 'overlay-spaces' || $layout == 'masonry' ){

	if( $layout == 'overlay-spaces' ){
		$before = '<div id="media-page-layout" class="masonry-grid-wrapper media-page-layout masonry-with-spaces">';
		$template = 'overlay'; // to overwride overlay-spaces
	}
	elseif( $layout == 'masonry' ){
		$before = '<div class="masonry-grid-wrapper masonry-with-spaces">';
	}
	else{
		$before = '<div id="media-page-layout" class="masonry-grid-wrapper media-page-layout masonry-without-spaces">';
	}

	if( empty( $template ) ){
		$template = $layout;
	}

	// Loader icon
	if( $template == 'overlay' ){
		$before .= tie_get_ajax_loader( false );
	}

	$before .= '
		<div id="masonry-grid" data-layout="'. $template .'" data-settings="'. $settings .'">';


					$after = '
				<div class="grid-sizer"></div>
				<div class="gutter-sizer"></div>
			</div><!-- #masonry-grid /-->
		</div><!-- .masonry-grid-wrapper /-->
	';

	// Load the masonry.js library
	wp_enqueue_script( 'jquery-masonry' );

	$masonry_js = "
		jQuery(window).on( 'load', function(){
			if( jQuery.fn.masonry ){
				jQuery('#masonry-grid').masonry('layout');
			}
		});
	";

	TIELABS_HELPER::inline_script( 'tie-scripts', $masonry_js );
}


// All other layouts have the same HTML structure except Class
else{

	// Full Thumb Layout
	if( $layout == 'full-thumb' ){
		$class = 'full-width-img-news-box';
	}

	// Content Layout
	elseif( $layout == 'content' ){
		$class = 'full-width-img-news-box';
	}

	// TimeLine Layout
	elseif( $layout == 'timeline' ){
		$class = 'wide-post-box timeline-box';
	}

	// Overlay Title Layout
	elseif( $layout == 'overlay-title' ){
		$class = 'full-width-img-news-box full-overlay-title';
	}

	// Overlay Title Center Layout
	elseif( $layout == 'overlay-title-center' ){
		$class  = 'full-width-img-news-box full-overlay-title center-overlay-title';
		$template = 'overlay-title';
	}

	// Overlay Title Center Layout
	elseif( $layout == 'first-big' ){
		$class 	= 'miscellaneous-box first-post-gradient has-first-big-post';
		$template = 'large-above';
	}

	// Classic Small
	elseif( $layout == 'classic-small' ){
		$class 	= 'small-wide-post-box wide-post-box top-news-box';
		$template = 'default';
	}

	// Default Layout
	else{
		$class  = 'wide-post-box';
		$template = 'default';
	}

	if( empty( $template ) ){
		$template = $layout;
	}

	// Media Overlay
	$class .= ! empty( $media_overlay ) ? ' media-overlay' : '';

	# HTML Markup
	$before = '
		<div class="mag-box '. $class .'">
			<div class="container-wrapper">
				<div class="mag-box-container clearfix">
					<ul id="posts-container" data-layout="'. $template .'" data-settings="'. $settings .'" class="posts-items">';
						$after = '
					</ul><!-- #posts-container /-->
					<div class="clearfix"></div>
				</div><!-- .mag-box-container /-->
			</div><!-- .container-wrapper /-->
		</div><!-- .mag-box /-->
	';
}

// Get the layout template
do_action( 'TieLabs/archives/before', $layout, $template, $block_args );

echo ( $before );

while ( have_posts() ) : the_post();

	$count++;
	$GLOBALS['latest_post_count'] = $count;

	$loop_args = array(
		'block' => $block_args,
		'count' => $count,
	);
	
	TIELABS_HELPER::get_template_part( 'templates/loops/loop', $template, $loop_args );

	do_action( 'TieLabs/after_post_in_archives', $template, $count );

endwhile;

echo ( $after );

do_action( 'TieLabs/archives/after', $layout, $template, $block_args );
