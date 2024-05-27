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

?>

<div <?php tie_content_column_attr(); ?>>

	<?php
		/**
		 * TieLabs/before_the_article hook.
		 *
		 * @hooked tie_above_post_ad - 5
		 */
		do_action( 'TieLabs/before_the_article' );
	?>

	<article id="the-post" <?php tie_post_class( 'container-wrapper post-content', false, false, true ); ?>>

		<?php
			/**
			 * TieLabs/before_single_post_title hook.
			 *
			 * @hooked tie_post_index_shortcode - 10
			 * @hooked tie_show_post_head_featured - 20
			 */
			do_action( 'TieLabs/before_single_post_title' );
		?>

		<div class="entry-content entry clearfix">

			<?php
				/**
				 * TieLabs/before_post_content hook.
				 *
				 * @hooked tie_before_post_content_ad - 10
				 * @hooked tie_story_highlights - 20
				 */
				do_action( 'TieLabs/before_post_content' );
			?>

			<?php
			    $post_id = get_the_ID();
			    $mog_count = 0;
			    for ($n=0; $n < 4; $n++) {
			        $single_bullet = get_post_meta($post_id, "mog_bullet_" . $n . "_mog_bulletpoint", true);
			        if (!empty($single_bullet)) {
			            $mog_count = $mog_count + 1;                
			        }
			    }
			    if ((int)$mog_count > 0 && get_post_meta($post_id, "mog_bullet_0_mog_bulletpoint", true)) {
			        echo '<div class="bulletpoints tie-toggle-option" data-tie-toggle=".bulletpoints ul"><ul>';
			        for ($i=0; $i < $mog_count; $i++) {
			            if ($i >= 2) { // Ocultar los bulletpoints después del segundo
			                echo "<li style='display:none;'><em>".get_post_meta($post_id, "mog_bullet_".$i."_mog_bulletpoint", true)."</em></li>"; 
			            } else {
			                echo "<li><em>".get_post_meta($post_id, "mog_bullet_".$i."_mog_bulletpoint", true)."</em></li>";                   
			            }
			        }
			        echo '</ul>';
			        if ($mog_count > 2) { // Mostrar el botón solo si hay más de dos bulletpoints
			            echo '<button class="toggle-bulletpoints"><span>' . __('See All Key Ideas', TIELABS_TEXTDOMAIN ) . '</span></button>';
			        }
			        echo '</div>'; 
			    } 
			?>

			<script>
			    (function($) {
			        $(document).ready(function() {
			            $('.bulletpoints .toggle-bulletpoints').click(function() {
			                $(this).prev('ul').find('li:nth-child(n+3)').toggle();
			                if ($(this).prev('ul').find('li:nth-child(n+3)').is(':visible')) {
			                    $(this).addClass('bullets-visible');
			                } else {
			                    $(this).removeClass('bullets-visible');
			                }
			            });
			        });
			    })(jQuery);
			</script>

			<?php the_content(); ?>

			<?php
				/**
				 * TieLabs/after_post_content hook.
				 *
				 * @hooked tie_after_post_content_ad - 5
				 * @hooked tie_post_multi_pages - 10
				 * @hooked tie_post_source_via - 20
				 * @hooked tie_post_tags - 30
				 * @hooked tie_edit_post_button - 40
				 * @hooked tie_post_shortlink - 50
				 */
				do_action( 'TieLabs/after_post_content' );
			?>

		</div><!-- .entry-content /-->

		<?php
			/**
			 * TieLabs/after_post_entry hook.
			 *
			 * @hooked tie_mobile_toggle_content_button - 10
			 * @hooked tie_article_schemas - 10
			 * @hooked tie_post_share_bottom - 20
			 */
			do_action( 'TieLabs/after_post_entry' );
		?>

	</article><!-- #the-post /-->

	<?php
		/**
		 * TieLabs/before_post_components hook.
		 *
		 * @hooked tie_after_post_entry_ad - 5
		 */
		do_action( 'TieLabs/before_post_components' );
	?>

	<div class="post-components">

		<?php
			/**
			 * TieLabs/post_components hook.
			 *
			 * @hooked tie_post_about_author - 10
			 * @hooked tie_post_newsletter - 20
			 * @hooked tie_post_next_prev - 30
			 * @hooked tie_related_posts - 40
			 * @hooked tie_post_comments - 50
			 * @hooked tie_related_posts - 60
			 */
			do_action( 'TieLabs/post_components' );
		?>

	</div><!-- .post-components /-->

	<?php
		/**
		 * TieLabs/after_post_components hook.
		 */
		do_action( 'TieLabs/after_post_components' );
	?>

</div><!-- .main-content -->

<?php
	/**
	 * TieLabs/after_post_column hook.
	 *
	 * @hooked tie_post_fly_box - 10
	 */
	do_action( 'TieLabs/after_post_column' );

