<?php
/**
 * The template for displaying author pages
 *
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

get_header(); ?>

	<div <?php tie_content_column_attr(); ?>>

		<?php if ( have_posts() ) : ?>

			<header id="author-title-section" class="entry-header-outer container-wrapper archive-title-wrapper">
				<?php

					do_action( 'TieLabs/before_archive_title' );
				?>
				<div class="about-author container-wrapper">
					<div class="author-avatar">
						<?php 
							if( empty( $author ) ){
								$author = get_queried_object();
							}
							$profile = tie_get_author_profile_url( $author );
							$display_name = tie_get_the_author( $author );
							$author_email = get_the_author_meta('user_email', $author->ID );
							$author_role = get_the_author_meta('roles', $author->ID );
							if( get_option( 'show_avatars' ) ){ ?>
								<a href="<?php echo esc_url( $profile ); ?>">
									<?php echo tie_get_author_avatar( $author, apply_filters( 'TieLabs/Author_Box/avatar_size', 180 ) ); ?>
								</a>
							<?php
						}
						?>
					</div>
					<div class="author-info">
						<?php the_archive_title( '<h1 class="author-name">', '</h1>' ); ?>
						<div class="author-contact">
							<div class="author-type">
								<?php esc_html_e( 'Contributor', TIELABS_TEXTDOMAIN ); ?>
								<?php // echo implode(', ', $author_role); ?>
							</div>				
							<?php if(!empty($author_email)): ?>
								<div class="author-email">
									<a href="mailto:<?php echo esc_attr( $author_email ); ?>"><?php echo esc_html( $author_email ); ?></a>
								</div>
							<?php endif; ?>
						</div>

						<ul class="social-icons">
							<li class="social-icons-item">
								<?php if($author_x) echo 'a href="' . $author_x . '" rel="external noopener nofollow" target="_blank" class="social-link x-social-icon">
                                            <span class="tie-icon-x aria-hidden="true"></span>
                                            <span class="screen-reader-text">X</span>
                                        </a>' ?>
							</li>
							<li class="social-icons-item">
								<?php if($author_fb) echo 'a href="' . $author_fb . '" rel="external noopener nofollow" target="_blank" class="social-link facebook-social-icon">
                                            <span class="tie-icon-facebook aria-hidden="true"></span>
                                            <span class="screen-reader-text">Facebook</span>
                                        </a>' ?>
							</li>
						</ul>
					</div>
					<?php
						$author_description = get_the_author_meta('description');
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

	</div><!-- .main-content /-->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
