<?php

/**
 * The template for displaying search results pages
 *
 */

defined('ABSPATH') || exit; // Exit if accessed directly

get_header(); ?>

<div class="search-wrap">
  <div class="search-input-wrapper">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
      <path d="M19.0008 19.0008L13.8038 13.8038M13.8038 13.8038C15.2104 12.3972 16.0006 10.4895 16.0006 8.50028C16.0006 6.51108 15.2104 4.60336 13.8038 3.19678C12.3972 1.79021 10.4895 1 8.50028 1C6.51108 1 4.60336 1.79021 3.19678 3.19678C1.79021 4.60336 1 6.51108 1 8.50028C1 10.4895 1.79021 12.3972 3.19678 13.8038C4.60336 15.2104 6.51108 16.0006 8.50028 16.0006C10.4895 16.0006 12.3972 15.2104 13.8038 13.8038Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
    <div class="search-wrapper">
		<input type="text" id="searchInput" placeholder="Type to search in any field." autocomplete="off" />
		<div class="search-actions"></div>
	</div>
    <div id="articles-suggestions" class="dropdown hide"></div>
  </div>
  <div class="tax-wrapper">
    <div class="tax-item-wrapper topics">
      <input type="text" id="searchTopic" placeholder="Topic" />
      <div class="tax-search-actions topic"></div>
      <div id="topics-suggestions" class="dropdown hide"></div>
      <div id="topics-results" class="dropdown hide"></div>
    </div>
    <div class="tax-item-wrapper locations">
      <input type="text" id="searchLocation" placeholder="Location" onchange="" />
      <div class="tax-search-actions location"></div>
      <div id="locations-suggestions" class="dropdown hide"></div>
      <div id="locations-results" class="dropdown hide"></div>
    </div>
    <div class="tax-item-wrapper format">
      <input type="text" id="searchFormat" placeholder="Format" onchange="" />
      <div class="tax-search-actions format"></div>
	  <div id="formats-suggestions" class="dropdown hide"></div>
      <div id="formats-results" class="dropdown hide"></div>
    </div>
  </div>
  <div id="default" class="">
    <h1>Try out our suggestions</h1>
    <div class="suggestions">
      <div class="suggestion-item"><a href="/?s=Forests">Forest articles</a></div>
      <div class="suggestion-item"><a href="/?s=Wildlife">Wildlife Videos</a></div>
      <div class="suggestion-item"><a href="/?s=Oceans">Conservation Podcasts</a></div>
      <div class="suggestion-item"><a href="/?s=a&format=customStories">Ocean Specials</a></div>
    </div>
  </div>
  <div id="no-results" class="hide">
    <h1>No results found</h1>
    <div class="suggestions">
      <div class="suggestion-item"><a href="/?s=Forests">Forest articles</a></div>
      <div class="suggestion-item"><a href="/?s=Wildlife">Wildlife Videos</a></div>
      <div class="suggestion-item"><a href="/?s=Oceans">Conservation Podcasts</a></div>
      <div class="suggestion-item"><a href="/?s=a&format=customStories">Ocean Specials</a></div>
    </div>
  </div>
  <div class="results-wrapper">
    <div id="results" class="hide"></div>
    <div class="results-footer"></div>
  </div>
</div>
<div id="page-block-tools">
  <h1>Discover more Mongabay websites</h1>
  <?php echo do_shortcode('[carrusel_imagenes]'); ?>
</div>
<div class="page-block-types">

  <?php echo do_shortcode('[ads5]'); ?>
</div>

<?php
get_sidebar();
get_footer();
?>