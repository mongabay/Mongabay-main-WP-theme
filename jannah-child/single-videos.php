<?php
/**
 * The template part for displaying the post contents
 *
 * This template can be overridden by copying it to your-child-theme/templates/single-post/content.php.
 *
 * HOWEVER, on occasion TieLabs will need to update template files and you
 * will need to copy the new files to your child theme to maintain compatibility.
 *
 * @author   TieLabs
 * @version  2.1.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

get_header(); ?>

<?php

if ( have_posts() ) :

	while ( have_posts() ): the_post(); ?>

		<div <?php tie_content_column_attr(); ?>>

			<article id="the-post" <?php tie_post_class( 'container-wrapper post-content', false, false, true ); ?>>
				<header class="entry-header-outer">
					<div class="entry-header">
						<h1 class="post-title entry-title">
							<?php

								$custom_title = apply_filters( 'TieLabs/Post/custom_title', tie_get_postdata( 'tie_post_custom_title' ) );

								echo ! empty( $custom_title ) ? $custom_title : the_title();
							?>
						</h1>
						<div class="single-post-meta post-meta clearfix">
							<?php $post_meta = tie_get_author();
									echo $post_meta; ?>
						</div>
						<?php get_template_part( 'templates/single-post/share'); ?>
					</div>
				</header>
				<div class="featured-area">
					<div class="featured-area-inner">
						<div class="tie-fluid-width-video-wrapper tie-ignore-fitvid">
							<?php echo pods_field_display( 'video_source' ); ?>
						</div>
					</div>
				</div>
				<div class="entry-content entry clearfix">
					<?php the_content(); ?>
				</div>
			</article>
			<div class="post-components">
				<?php echo do_shortcode('[ads1]'); ?>
				<?php echo do_shortcode('[toggle title="Oceans" state="open"]The stories in this series are powered by Places to Watch, a Global Forest Watch (GFW) initiative designed to quickly identify concerning forest loss around the world and catalyze further investigation of these areas. Places to Watch draws on a combination of near real-time deforestation alerts, automated algorithms and field intelligence to identify new areas on a monthly basis. In partnership with Mongabay, GFW is supporting data-driven journalism by providing data and maps generated by Places to Watch. Mongabay maintains complete editorial independence over the stories reported using this data.[/toggle]'); ?>
				<div class="latest-video-articles">
					<h1>Latest Videos</h1>
					<?php echo do_shortcode('[post_grid post_type="videos" posts_per_page="6"]'); ?>
				</div>
				<?php echo do_shortcode('[ads2]'); ?>
				<?php echo do_shortcode('[post_grid post_type="videos" posts_per_page="8"]'); ?>
				<?php echo do_shortcode('[ads5]'); ?>
			</div>

		</div>

	<?php
	endwhile;

endif;

get_sidebar();
get_footer();

