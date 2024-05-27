<?php

/**
 * The template for displaying taxonomy topics
 *
 */

defined('ABSPATH') || exit; // Exit if accessed directly

get_header(); ?>

<?php if (have_posts()) : ?>
	<?php
	$title = get_query_var('term');
	//get query object
	global $wp_query;
	$taxonomies = $wp_query->tax_query;
	$topics = array();

	foreach ($taxonomies->queries as $tax_query) {
		if (isset($tax_query['taxonomy'])) {
			$topics[] = ucfirst($tax_query['terms'][0]);
		}
	}
	
	$line_end = ' News';
	$total = $wp_query->found_posts;
	?>
	<header class="entry-header-outer">
		<div class="entry-header">
			<h1><?php echo implode(' and ', $topics); ?> <?php _e($line_end, TIELABS_TEXTDOMAIN); ?></h1>
		</div>
	</header>

	<div id="results">
		<div id="results-header">
			<div id="results-header-left">
				<a href="<?php get_home_url() ?>/?feed=custom-rss-feed&s=&post_type=posts&topic=<?php echo $title; ?>" target="_blank" id="results-rss">RSS</a>
				<div id="results-total"><?php echo $total; ?> <?php _e($total > 1 ? 'stories' : 'story', TIELABS_TEXTDOMAIN); ?></div>
			</div>
			<div id="results-view-toggles">
				<button id="list-view" class="demo-icon">L</button>
				<button id="grid-view" class="active">G</button>
			</div>
		</div>
		<div id="post-results" class="grid-view">
			<?php
			// Start the Loop
			while (have_posts()) :
				the_post();
			?>
				<div id="post-<?php the_ID(); ?>" class='list-item'>
					<a href="<?php the_permalink(); ?>">
						<?php
						if (has_post_thumbnail()) {
							the_post_thumbnail('tie-small');
						}
						?>
						<h4><?php the_title(); ?></h4>
						<div class="post-meta">
							<span><?php echo join(', ', wp_list_pluck(get_the_terms(get_the_ID(), 'byline'), 'name')); ?></span>
							<span><?php the_time('j F Y'); ?></span>
						</div>
					</a>
				</div>
			<?php endwhile; ?>
		</div>
		<script>
			const gridViewButton = document.getElementById("grid-view");
			const listViewButton = document.getElementById("list-view");
			const resultsList = document.getElementById("post-results");

			gridViewButton.addEventListener("click", () => {
				resultsList.classList.remove("list-view");
				resultsList.classList.add("grid-view");
				gridViewButton.classList.add("active");
				listViewButton.classList.remove("active");
			});

			listViewButton.addEventListener("click", () => {
				resultsList.classList.remove("grid-view");
				resultsList.classList.add("list-view");
				listViewButton.classList.add("active");
				gridViewButton.classList.remove("active");
			});
		</script>
	</div>
	<?php

	// Page Pagination
	TIELABS_PAGINATION::show(array('type' => tie_get_option('blog_pagination')));
	?>
<?php
else :
	TIELABS_HELPER::get_template_part('templates/not-found');

endif;

?>
</div><!-- content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>