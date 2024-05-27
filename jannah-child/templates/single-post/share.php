<?php
/**
 * Post Share
 *
 * This template can be overridden by copying it to your-child-theme/templates/single-post/share.php.
 *
 * HOWEVER, on occasion TieLabs will need to update template files and you
 * will need to copy the new files to your child theme to maintain compatibility.
 *
 * @author   TieLabs
 * @version  7.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

?>

<?php
// Disable on bbPress pages
if( TIELABS_BBPRESS_IS_ACTIVE && is_bbpress() ){
	return;
}

// Check if the share buttons is hidden on mobiles
if( TIELABS_HELPER::is_mobile_and_hidden( 'share_post_top' ) ){
	return;
}

if (get_post_type() == 'videos' || get_post_type() == 'podcasts' || get_post_type() == 'short-article'|| get_post_type() == 'custom-story'|| get_post_type() == 'specials') {
    $share_position = 'top';
}

// Reset the main Post query - Some plugins' widgets change the main post query
wp_reset_postdata();

// $share_position = 'top';

// Check if the sharing buttons are active
if( tie_get_postdata( 'tie_hide_share_'.$share_position ) == 'no' ||
    ( get_post_type() == 'page' && tie_get_option( 'share_buttons_pages' ) && tie_get_option( 'share_post_'.$share_position ) && ! tie_get_postdata( 'tie_hide_share_'.$share_position ) ) ||
    ( TIELABS_HELPER::is_supported_post_type() && tie_get_option( 'share_post_'.$share_position ) && ! tie_get_postdata( 'tie_hide_share_'.$share_position ) ) || ( in_array( get_post_type(), array( 'videos', 'podcasts', 'short-article', 'custom-story', 'specials' ) ) )) {


	// --
	$counter      = 0;
	$share_class  = '';
	$share_style  = tie_get_option( 'share_style_'.$share_position );
	$button_class = '';
	$text_class   = '';


	// Mobile buttons
	if( $share_position == 'mobile' ){
		$share_style = 'style_3';
	}
	// Sticky Menu buttons
	if( $share_position == 'sticky_menu' ){
		$share_style = 'style_5';
	}
	// Sticky Share buttons
	elseif( $share_position == 'sticky' ){
		if( $share_style != 'style_5' ){
			$share_style = 'style_3';
		}
	}


	// Centered buttons
	if( $position = tie_get_option( 'share_position_'.$share_position ) ){
		$share_class .= ( $position == 'center' ) ? ' share-centered' : ' share-'.$position;
	}

	// Share layout
	if( $share_style == 'style_2' || $share_style == 'style_6' || $share_style == 'style_7' ){
		$share_class .= ' icons-text';
		$button_class = ' large-share-button';
		$text_class   = 'social-text';
	}
	elseif( $share_style == 'style_3' ){
		$share_class .= ' icons-only';
		$button_class = '';
		$text_class   = 'screen-reader-text';
	}
	elseif( $share_style == 'style_4' ){
		$share_class .= ' icons-only';
		$button_class = ' equal-width';
		$text_class   = 'screen-reader-text';
	}
	elseif( $share_style == 'style_5' ){
		$share_class .= ' icons-only share-rounded';
		$button_class = '';
		$text_class   = 'screen-reader-text';
	}

	// Additional Classes
	if( $share_style == 'style_6' ){
		$share_class .= ' share-skew';
	}
	elseif( $share_style == 'style_7' ){
		$share_class .= ' share-pill';
	}

	// Get Share Buttons
	$share_buttons = tie_get_share_buttons( $share_position );

	//
	$button_position = ( $share_position == 'bottom' ) ? '' : '_'.$share_position;

	$active_share_buttons = array();

	foreach ( $share_buttons as $network => $button ){

		$network_id = $network;
		$custom_button_class = $network .'-share-btn';

		if( ! empty( $button['id'] ) ){
			$network_id   = $button['id'];
			$custom_button_class .= ' '. $button['id'] .'-share-btn';
		}

		if( tie_get_option( 'share_'.$network_id.$button_position ) ){

			$counter ++;

			// Buttons Style 1
			if( empty( $share_style ) ) {
				$button_class = '';
				$text_class   = 'screen-reader-text';

				if( $counter <= 2 ){
					$button_class = ' large-share-button';
					$text_class   = 'social-text';
				}
			}

			$esc = ! isset( $button['avoid_esc'] ) ? true : false;

			$active_share_buttons[] = '
				<a href="'. tie_share_button_url( $button['url'], $esc ) .'" rel="external noopener nofollow" title="'. $button['text'] .'" target="_blank" class="'. $custom_button_class .' '. $button_class .'" data-raw="'. $button['url'] .'">
					<span class="share-btn-icon '. $button['icon'] .'"></span> <span class="'. $text_class .'">'. $button['text'] .'</span>
				</a>'
			;
		}
	}

	if( is_array( $active_share_buttons ) && ! empty( $active_share_buttons ) ){ ?>
		<div id="share-buttons-<?php echo esc_attr( $share_position ) ?>" class="share-buttons share-buttons-<?php echo esc_attr( $share_position ) ?>">			
			<button class="toggle-share-links"><?php esc_html_e( 'Share article', TIELABS_TEXTDOMAIN ); ?></button>
			<div class="share-links <?php echo esc_attr( $share_class ) ?>" style="display:none;">
				<h2><?php esc_html_e( 'Share this story.', TIELABS_TEXTDOMAIN ); ?></h2>
				<p><?php esc_html_e( 'If you liked this story, share it with other people.', TIELABS_TEXTDOMAIN ); ?></p>
				<div class="share-links-buttons">
				<?php
					if( tie_get_option( 'share_title_'.$share_position ) ){ ?>
						<div class="share-title">
							<span class="tie-icon-share" aria-hidden="true"></span>
							<span> <?php esc_html_e( 'Share', TIELABS_TEXTDOMAIN ); ?></span>
						</div>

						<?php
					}

					

					echo implode( '', $active_share_buttons ); ?>
				</div>
				<p class="share-link-title"><?php esc_html_e( 'Page link', TIELABS_TEXTDOMAIN ); ?></p>
				<div class="share-link-input">
					<input type="text" id="share-post-url" value="<?php echo esc_url(get_permalink()); ?>" readonly>
					<button id="copy-url-button"></button>
				</div>
				
				
			</div><!-- .share-links /-->
		</div><!-- .share-buttons /-->
		<div id="share-overlay"></div>

		<?php

		// For mobile share buttons add a space below it
		if( $share_position == 'mobile' ){
			echo '<div class="mobile-share-buttons-spacer"></div>';
		}
		?>
		<script>
	    	document.addEventListener('DOMContentLoaded', function() {
		        var toggleButton = document.querySelector('.toggle-share-links');
		        var shareLinks = document.querySelector('.share-links');
		        var overlay = document.getElementById('share-overlay');
		        var copyButton = document.getElementById('copy-url-button');
            	var postUrlInput = document.getElementById('share-post-url');

		        toggleButton.addEventListener('click', function() {
		            if (shareLinks.style.display === 'none') {
		                shareLinks.style.display = 'block';
		                overlay.style.display = 'block';
		            } else {
		                shareLinks.style.display = 'none';
		                overlay.style.display = 'none';
		            }
		        });

		        copyButton.addEventListener('click', function() {
	                postUrlInput.select();
	                document.execCommand('copy');
	            });

		        window.addEventListener('click', function(event) {
			        if (!shareLinks.contains(event.target) && event.target !== toggleButton) {
			            shareLinks.style.display = 'none';
			            overlay.style.display = 'none';
			        }
			    });
		    });
		</script>
		<?php

	}
}
