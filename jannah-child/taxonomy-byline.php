<?php
/**
 * The template for displaying author pages
 *
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

get_header(); 

	$taxonomy = get_queried_object();
	$avatar = get_term_meta($taxonomy->term_id,'cover_image_url',true);
	$author_email = get_term_meta($taxonomy->term_id,'email',true);
	$author_web = get_term_meta($taxonomy->term_id,'web',true);
	$author_x = get_term_meta($taxonomy->term_id,'authors_twitter_account',true);
	$author_fb = get_term_meta($taxonomy->term_id,'authors_facebook_account',true);
	$author_role = get_term_meta($taxonomy->term_id,'author_type',true);

	?>

	<div <?php tie_content_column_attr(); ?>>

		<?php if ( have_posts() ) : ?>

			<header id="author-title-section" class="entry-header-outer container-wrapper archive-title-wrapper">
				<?php

					do_action( 'TieLabs/before_archive_title' );
				?>
				<div class="about-author container-wrapper">
					<div class="author-avatar">
						<?php if($avatar){ echo '<img src="' . $avatar . '" alt="cover image">';
							} else { echo '<span class="meta-author-circle"></span>';
							} 

						?>
					</div>
					<div class="author-info">
						<?php the_archive_title( '<h1 class="author-name">', '</h1>' ); ?>
						<?php if(!empty($author_role) || !empty($author_email)) : ?>
							<div class="author-contact">
								<?php if(!empty($author_role)): ?>
									<div class="author-type">
										<?php if($author_role){ echo $author_role; } ?></a>
									</div>
								<?php endif; ?>
								<?php if(!empty($author_email)): ?>
									<div class="author-email">
										<a href="mailto:<?php echo esc_attr( $author_email ); ?>"><?php echo esc_html( $author_email ); ?></a>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<ul class="social-icons">
							<li class="social-icons-item">
								<?php if($author_x) echo '<a href="' . $author_x . '" rel="external noopener nofollow" target="_blank" class="social-link x-social-icon">
                                            <span class="tie-icon-x aria-hidden="true"></span>
                                            <span class="screen-reader-text">X</span>
                                        </a>' ?>
							</li>
							<li class="social-icons-item">
								<?php if($author_fb) echo '<a href="' . $author_fb . '" rel="external noopener nofollow" target="_blank" class="social-link facebook-social-icon">
                                            <span class="tie-icon-facebook aria-hidden="true"></span>
                                            <span class="screen-reader-text">Facebook</span>
                                        </a>' ?>
							</li>
						</ul>
					</div>
					<?php
					$author_description = term_description($taxonomy->term_id,'byline',true);
						if (!empty($author_description)){
						?>
							<div class="mag-box-title the-global-title">
								<h3><?php esc_html_e( 'About', TIELABS_TEXTDOMAIN ) ?></h3>
							</div>
							<?php the_archive_description( '<div class="author-bio">', '</div>' ); 
						}
					?>
				</div>
				<?php		

					do_action( 'TieLabs/after_archive_title' );
				?>
				
			</header><!-- .entry-header-outer /-->

			<?php

			// Get the layout template part
			TIELABS_HELPER::get_template_part( 'templates/archives', '', array(
				'layout'          => tie_get_option( 'author_layout', 'excerpt' ),
				'excerpt'         => tie_get_option( 'author_excerpt' ),
				'excerpt_length'  => tie_get_option( 'author_excerpt_length' ),
				'read_more'       => tie_get_option( 'author_read_more' ),
				'read_more_text'  => tie_get_option( 'author_read_more_text' ),
			));

			// Page Pagination
			TIELABS_PAGINATION::show( array( 'type' => tie_get_option( 'author_pagination' ) ) );

		// If no content, include the "No posts found" template
		else :
			TIELABS_HELPER::get_template_part( 'templates/not-found' );

		endif;

		?>
		<div class="archive-components">			
			<div class="page-block-types">
			  <?php echo do_shortcode('[ads5]'); ?>
			</div>
		</div>

	</div><!-- .main-content /-->
	

<?php get_sidebar(); ?>
<?php get_footer(); ?>
