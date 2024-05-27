<?php
// TODO: Modificar esta función con un modelo/array que alimente los archivos sobrescritos. Ideal: Autodetección
add_action('after_setup_theme', 'jannah_child_framework_overrides',99);
function jannah_child_framework_overrides(){
    if (file_exists(get_stylesheet_directory() . '/framework/admin/page-builder.php')) {
        // Tenemos que desenganchar las funciones antes de volver a declararlas

        remove_action('after_setup_theme', 'tie_page_builder_option');

        require_once get_stylesheet_directory() . '/framework/admin/page-builder.php';
    }
    // Inclusión de los archivos del framework que vamos modificando
    if (file_exists(get_stylesheet_directory() . '/framework/blocks.php')) {
        require_once get_stylesheet_directory() . '/framework/blocks.php';
    }
    if (file_exists(get_stylesheet_directory() . '/framework/classes/class-tielabs-helper.php')) {
        require_once get_stylesheet_directory() . '/framework/classes/class-tielabs-helper.php';
    }
}

// single post author module with translated_adapted

if (!function_exists('tie_author_box')) {
    function tie_author_box($author = false, $signature = false)
    {
        // Current object
        if (empty($author)) {
            $author = get_queried_object();
        }

        // Profile URL
        $profile = tie_get_author_profile_url($author);

        // Author name
        $display_name = tie_get_the_author($author);
        $post_id = get_the_ID();
        $translated_adapted = get_post_meta($post_id, "translated_adapted", true);
        $translator = get_post_meta($post_id, "translated_by", true);
        $adaptor = get_post_meta($post_id, "adapted_by", true);

    ?>
        <div class="mongabay-post-credits">
            <div class="post-credits">
                <span class="credits-title">
                    <?php _e('Credits', TIELABS_TEXTDOMAIN); ?>
                </span>
            </div>
            <div class="about-author container-wrapper about-author-<?php echo esc_attr($author->ID) ?>">

                <?php

                // Show the avatar if it is active only
                if (get_option('show_avatars')) { ?>
                    <div class="author-avatar">
                        <a href="<?php echo esc_url($profile); ?>">
                            <?php echo tie_get_author_avatar($author, apply_filters('TieLabs/Author_Box/avatar_size', 180)); ?>
                        </a>
                    </div><!-- .author-avatar /-->
                <?php
                }

                ?>

                <div class="author-info">

                    <?php
                    if (is_author()) {
                    ?>
                        <h1 class="author-name"><a href="<?php echo esc_url($profile); ?>"><?php esc_html_e($display_name) ?></a></h1>
                    <?php
                    } else {
                    ?>
                        <h3 class="author-name"><a href="<?php echo esc_url($profile); ?>"><?php esc_html_e($display_name) ?></a></h3>
                    <?php
                    }
                    ?>

                    <div class="author-bio">
                        <span>
                            <?php
                            _e('Editor', TIELABS_TEXTDOMAIN);
                            ?>
                        </span>
                    </div><!-- .author-bio /-->

                    <?php

                    // Add the website URL
                    $author_social = tie_author_social_array();
                    $website = array(
                        'url' => array(
                            'text' => esc_html__('Website', TIELABS_TEXTDOMAIN),
                            'icon' => 'home',
                        )
                    );

                    $author_social = array_merge($website, $author_social);

                    // Generate the social icons
                    echo '<ul class="social-icons">';

                    foreach ($author_social as $network => $button) {
                        if (get_the_author_meta($network, $author->ID)) {

                            $icon = empty($button['icon']) ? $network : $button['icon'];

                            $profile_url = apply_filters('TieLabs/author/social_url', get_the_author_meta($network, $author->ID), $network, $author->ID);

                            echo '
                                    <li class="social-icons-item">
                                        <a href="' . esc_url($profile_url) . '" rel="external noopener nofollow" target="_blank" class="social-link ' . $network . '-social-icon">
                                            <span class="tie-icon-' . $icon . '" aria-hidden="true"></span>
                                            <span class="screen-reader-text">' . $button['text'] . '</span>
                                        </a>
                                    </li>
                                ';
                        }
                    }

                    echo '</ul>';
                    ?>
                </div><!-- .author-info /-->
                <div class="clearfix"></div>
            </div>
            <?php
            // Display translated_by or adapted_by if available
            if (($translated_adapted == 'adapted' || $translated_adapted == 'translated')) {
                if ($translated_adapted == 'adapted' && !empty($adaptor)) {
                    $string_title = 'Adapted by ';
                    $translator_adaptor = $adaptor;
                } elseif ($translated_adapted == 'translated' && !empty($translator)) {
                    $string_title = 'Translated by ';
                    $translator_adaptor = $translator;
                }
            ?>
                <div class="about-author container-wrapper">
                    <div class="author-avatar">
                        <?php
                        echo '<a href="' . home_url('/') . 'by/' . $translator_adaptor['slug'] . '"></a>';
                        ?>
                    </div>
                    <div class="author-info">
                        <h3 class="author-name">
                            <?php
                            echo '<a href="' . home_url('/') . 'by/' . $translator_adaptor['slug'] . '">' . $translator_adaptor['name'] . '</a>';
                            ?>
                        </h3>
                        <div class="author-bio">
                            <span>
                                <?php
                                _e($string_title, TIELABS_TEXTDOMAIN);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div> <!-- .mongabay-post-credits /-->
    <?php
    }
}

if (!function_exists('tie_get_author')) {

    function tie_get_author()
    {

        $post_meta = '';
        global $post;
        $byline_terms = get_the_terms(get_the_ID(), 'byline');

        // Check if byline terms exist
        if ($byline_terms && !is_wp_error($byline_terms)) {
            $post_meta .= '<span class="meta-item meta-author-wrapper">';
            foreach ($byline_terms as $term) {
                $cover_image_url = get_term_meta($term->term_id, 'cover_image_url', true);
                if ($cover_image_url) {
                    $post_meta .= '<span class="meta-author-avatar"><a href="' . home_url('/') . 'by/' . esc_html($term->slug) . '"><img src="' . esc_url($cover_image_url) . '" alt="Cover Image"></a></span>';
                } else {
                    $post_meta .= '<span class="meta-author-circle"><a href="' . home_url('/') . 'by/' . esc_html($term->slug) . '"></a></span>';
                }

                $post_meta .= '<span class="meta-author"><span class="meta-author-name"><a href="' . home_url('/') . 'by/' . esc_html($term->slug) . '">' . esc_html($term->name) . '</a></span><span class="meta-date">' . get_the_date() . '</span></span>';
            }
            $post_meta .= '</span>';
        }

        return $post_meta;
    }
}

add_shortcode('ads5', 'mb_jannah_extensions_sc_ads5');
function mb_jannah_extensions_sc_ads5($atts, $content = null)
{

    if (!function_exists('tie_get_option') || (function_exists('is_amp_endpoint') && is_amp_endpoint())) {
        return;
    }
    $video_posts = get_posts(array(
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_type'      => 'videos',
    ));

    $audio_posts = get_posts(array(
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_type'      => 'podcasts',
    ));

    $last_posts = get_posts(array(
        'posts_per_page' => 1,
        'orderby' => 'date',
        'order' => 'DESC',
        'post__not_in' => array_merge(
            wp_list_pluck($video_posts, 'ID'),
            wp_list_pluck($audio_posts, 'ID')
        ),
    ));

    $output = '<div class="stream-item stream-item-in-post stream-item-in-post-5"><h1>' .
        do_shortcode(apply_filters('TieLabs/custom_ad_code', tie_get_option('ads5_shortcode'))) . '</h1>';

    if (is_array($video_posts)) {
        foreach ($video_posts as $post) {
            $output .= '<div class="video-post">';

            if (has_post_thumbnail($post->ID)) {
                $output .= '<div class="video-thumb grid-entry">';
                $output .= '<a href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'medium') . '</a>';
                $output .= '</div>';
            }

            $output .= '<div class="video-content">';
            $output .= '<h3 class="video-title"><a href="' . get_permalink($post->ID) . '">' . esc_html__('videos', TIELABS_TEXTDOMAIN) . '</a></h3>';
            $output .= '</div>';

            $output .= '</div>';
        }
    }

    if (is_array($audio_posts)) {
        foreach ($audio_posts as $post) {
            $output .= '<div class="audio-post">';

            if (has_post_thumbnail($post->ID)) {
                $output .= '<div class="audio-thumbnail grid-entry">';
                $output .= '<a href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'medium') . '</a>';
                $output .= '</div>';
            }

            $output .= '<div class="audio-content">';
            $output .= '<h3 class="audio-title"><a href="' . get_permalink($post->ID) . '">' . esc_html__('podcasts', TIELABS_TEXTDOMAIN) . '</a></h3>';
            $output .= '</div>';

            $output .= '</div>';
        }
    }

    if (is_array($last_posts)) {
        foreach ($last_posts as $post) {
            $output .= '<div class="last-post">';

            if (has_post_thumbnail($post->ID)) {
                $output .= '<div class="last-thumbnail grid-entry">';
                $output .= '<a href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'medium') . '</a>';
                $output .= '</div>';
            }

            $output .= '<div class="last-content">';
            $output .= '<h3 class="last-title"><a href="' . get_permalink($post->ID) . '">' . esc_html__('articles', TIELABS_TEXTDOMAIN) . '</a></h3>';
            $output .= '</div>';

            $output .= '</div>';
        }
    }
    $output .= '</div>';

    return $output;
}