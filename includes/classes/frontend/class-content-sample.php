<?php
/**
 * Sample post content filter
 *
 * @package    SPR_Core
 * @subpackage Classes
 * @category   Front
 * @since      1.0.0
 */

namespace SPR_Core\Classes\Front;

// Restrict direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Content_Sample extends Content_Filter {

	/**
	 * Post types
	 *
	 * Array of the post types to be filtered,
	 * as they are registered.
	 *
	 * @example [ 'post', 'sample_type' ]
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    array Array of the post types to be filtered.
	 */
	private $post_types = [
		'post',
		'sample_type'
	];

	/**
	 * Content filter priority
	 *
	 * When to filter the content.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    integer The numeral to set filter priority.
	 */
	private $priority = 10;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// Run the parent constructor method.
		parent :: __construct();
	}

	/**
	 * Filter content
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $content The value of the content field.
	 * @return mixed Returns the content to be filtered or
	 *               returns the unfiltered content if post types don't match.
	 */
	public function the_content( $content ) {

		// Get the array of post types to be filtered.
		$types = $this->post_types;

		// Default content for post types not modified.
		$content = $content;

		// Modify the content for each post type in the post_types property.
		foreach ( $types as $type ) {

			$id = get_the_ID();

			// If the post type matches one in the loop.
			if ( $type == get_post_type( $id ) ) {

				/**
				 * If the post is in its post type archive
				 * and if the content is in the loop.
				 */
				if ( is_post_type_archive( $type ) && is_main_query() && in_the_loop() ) {
					$content = $this->archive_content();

				// If the post is singular and if it is in the loop.
				} elseif ( is_singular( $type ) && is_main_query() && in_the_loop() ) {
					$content = $this->single_content();

				// If the post is in taxonomy archive pages and if it is in the loop.
				} elseif ( is_tax( 'sample_tax' ) && is_main_query() && in_the_loop() ) {
					$content = $this->taxonomy_content();

				}
			}
		}

		// Return the modified or unmodified content.
		return $content;
	}

	/**
	 * Post type archive content
	 *
	 * A partials subdirectory is used because many themes
	 * have more markup in the content directory files than
	 * simply the content section, which this replaces.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function archive_content() {

		// Look for a archive content template in the active theme.
		$template = locate_template( 'template-parts/content/partials/archive-sample.php' );

		// If the active theme has a template, use that.
		if ( ! empty( $template ) ) {
			get_template_part( 'template-parts/content/partials/archive-sample' );

		// Use the plugin template if no theme template is found.
		} else {
			include SPRC_PATH . '/views/frontend/archive-sample.php';
		}
	}

	/**
	 * Single post type content
	 *
	 * A partials subdirectory is used because many themes
	 * have more markup in the content directory files than
	 * simply the content section, which this replaces.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	 public function single_content() {

		// Look for a single content template in the active theme.
		$template = locate_template( 'template-parts/content/partials/single-sample.php' );

		// If the active theme has a template, use that.
		if ( ! empty( $template ) ) {
			get_template_part( 'template-parts/content/partials/single-sample' );

		// Use the plugin template if no theme template is found.
		} else {
			include SPRC_PATH . '/views/frontend/single-sample.php';
		}
	}

	/**
	 * Taxonomy archive content
	 *
	 * A partials subdirectory is used because many themes
	 * have more markup in the content directory files than
	 * simply the content section, which this replaces.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function taxonomy_content() {

		// Look for a taxonomy content template in the active theme.
		$template = locate_template( 'template-parts/content/partials/taxonomy-sample.php' );

		// If the active theme has a template, use that.
		if ( ! empty( $template ) ) {
			get_template_part( 'template-parts/content/partials/taxonomy-sample' );

		// Use the plugin template if no theme template is found.
		} else {
			include SPRC_PATH . '/views/frontend/taxonomy-sample.php';
		}
	}
}
