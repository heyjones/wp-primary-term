<?php
/**
 * Core plugin functionality.
 *
 * @package PrimaryTerm
 */

namespace PrimaryTerm\Core;

use \WP_Error as WP_Error;

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'init', $n( 'i18n' ) );
	add_action( 'init', $n( 'init' ) );
	add_action( 'wp_enqueue_scripts', $n( 'scripts' ) );
	add_action( 'wp_enqueue_scripts', $n( 'styles' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_styles' ) );

	// Editor styles. add_editor_style() doesn't work outside of a theme.
	add_filter( 'mce_css', $n( 'mce_css' ) );
	// Hook to allow async or defer on asset loading.
	add_filter( 'script_loader_tag', $n( 'script_loader_tag' ), 10, 2 );

	do_action( 'primary_term_loaded' );
}

/**
 * Registers the default textdomain.
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'primary-term' );
	load_textdomain( 'primary-term', WP_LANG_DIR . '/primary-term/primary-term-' . $locale . '.mo' );
	load_plugin_textdomain( 'primary-term', false, plugin_basename( PRIMARY_TERM_PATH ) . '/languages/' );
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @return void
 */
function init() {
	do_action( 'primary_term_init' );
}

/**
 * Activate the plugin
 *
 * @return void
 */
function activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	init();
	flush_rewrite_rules();
}

/**
 * Deactivate the plugin
 *
 * Uninstall routines should be in uninstall.php
 *
 * @return void
 */
function deactivate() {

}


/**
 * The list of knows contexts for enqueuing scripts/styles.
 *
 * @return array
 */
function get_enqueue_contexts() {
	return [ 'admin', 'frontend', 'shared' ];
}

/**
 * Generate an URL to a script, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $script Script file name (no .js extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @return string|WP_Error URL
 */
function script_url( $script, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in PrimaryTerm script loader.' );
	}

	return PRIMARY_TERM_URL . "dist/js/${script}.js";

}

/**
 * Generate an URL to a stylesheet, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $stylesheet Stylesheet file name (no .css extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @return string URL
 */
function style_url( $stylesheet, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in PrimaryTerm stylesheet loader.' );
	}

	return PRIMARY_TERM_URL . "dist/css/${stylesheet}.css";

}

/**
 * Enqueue scripts for front-end.
 *
 * @return void
 */
function scripts() {

	wp_enqueue_script(
		'primary_term_shared',
		script_url( 'shared', 'shared' ),
		[],
		PRIMARY_TERM_VERSION,
		true
	);

	wp_enqueue_script(
		'primary_term_frontend',
		script_url( 'frontend', 'frontend' ),
		[],
		PRIMARY_TERM_VERSION,
		true
	);

}

/**
 * Enqueue scripts for admin.
 *
 * @return void
 */
function admin_scripts() {

	wp_enqueue_script(
		'primary_term_shared',
		script_url( 'shared', 'shared' ),
		[],
		PRIMARY_TERM_VERSION,
		true
	);

	wp_register_script(
		'primary_term_admin',
		script_url( 'admin', 'admin' ),
		[],
		PRIMARY_TERM_VERSION,
		true
	);

	$current_screen = get_current_screen();

	if( ! $current_screen->is_block_editor ){
		// Get the current post
		global $post;
		// Get the current post type
		$post_type = $current_screen->post_type;
		// Get the post type taxonomies
		$taxonomies = get_object_taxonomies( $post_type );
		// Set an empty array
		$post_type_taxonomies = array();
		// Create an array of taxonomy terms
		foreach( (array) $taxonomies as $taxonomy ) {
			// Get the taxonomy object
			$taxonomy = get_taxonomy ( $taxonomy );
			$post_type_taxonomies[$taxonomy->name]['label'] = $taxonomy->labels->singular_name;
			$post_type_taxonomies[$taxonomy->name]['metabox'] = $taxonomy->hierarchical ? $taxonomy->name . 'div' : 'tagsdiv-' . $taxonomy->name;
			// Get the taxonomy terms
			$terms = get_terms ( array (
				'taxonomy' => $taxonomy->name,
				'hide_empty' => false,
			) );
			// Add the terms to the current taxonomy
			$post_type_taxonomies[$taxonomy->name]['terms'] = array_column( (array) $terms, 'name', 'term_taxonomy_id' );
		}
		// Set an empty array of terms
		$post_terms = array();
		// Create an array of post terms
		foreach( (array) $taxonomies as $taxonomy ) {
			// Get the taxonomy terms
			$terms = get_the_terms( $post, $taxonomy );
			// Add the terms to the current taxonomy
			$post_terms[$taxonomy] = array_column( (array) $terms, 'name', 'term_taxonomy_id' );
		}
		// Localize the script
		wp_localize_script( 'primary_term_admin', 'primaryTerm', array (
			'taxonomies' => $post_type_taxonomies,
			'terms' => $post_terms,
		) );
	}

	wp_enqueue_script( 'primary_term_admin' );	

}

/**
 * Enqueue styles for front-end.
 *
 * @return void
 */
function styles() {

	wp_enqueue_style(
		'primary_term_shared',
		style_url( 'shared-style', 'shared' ),
		[],
		PRIMARY_TERM_VERSION
	);

	if ( is_admin() ) {
		wp_enqueue_style(
			'primary_term_admin',
			style_url( 'admin-style', 'admin' ),
			[],
			PRIMARY_TERM_VERSION
		);
	} else {
		wp_enqueue_style(
			'primary_term_frontend',
			style_url( 'style', 'frontend' ),
			[],
			PRIMARY_TERM_VERSION
		);
	}

}

/**
 * Enqueue styles for admin.
 *
 * @return void
 */
function admin_styles() {

	wp_enqueue_style(
		'primary_term_shared',
		style_url( 'shared-style', 'shared' ),
		[],
		PRIMARY_TERM_VERSION
	);

	wp_enqueue_style(
		'primary_term_admin',
		style_url( 'admin-style', 'admin' ),
		[],
		PRIMARY_TERM_VERSION
	);

}

/**
 * Enqueue editor styles. Filters the comma-delimited list of stylesheets to load in TinyMCE.
 *
 * @param string $stylesheets Comma-delimited list of stylesheets.
 * @return string
 */
function mce_css( $stylesheets ) {
	if ( ! empty( $stylesheets ) ) {
		$stylesheets .= ',';
	}

	return $stylesheets . PRIMARY_TERM_URL . ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ?
			'assets/css/frontend/editor-style.css' :
			'dist/css/editor-style.min.css' );
}

/**
 * Add async/defer attributes to enqueued scripts that have the specified script_execution flag.
 *
 * @link https://core.trac.wordpress.org/ticket/12009
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string
 */
function script_loader_tag( $tag, $handle ) {
	$script_execution = wp_scripts()->get_data( $handle, 'script_execution' );

	if ( ! $script_execution ) {
		return $tag;
	}

	if ( 'async' !== $script_execution && 'defer' !== $script_execution ) {
		return $tag; // _doing_it_wrong()?
	}

	// Abort adding async/defer for scripts that have this script as a dependency. _doing_it_wrong()?
	foreach ( wp_scripts()->registered as $script ) {
		if ( in_array( $handle, $script->deps, true ) ) {
			return $tag;
		}
	}

	// Add the attribute if it hasn't already been added.
	if ( ! preg_match( ":\s$script_execution(=|>|\s):", $tag ) ) {
		$tag = preg_replace( ':(?=></script>):', " $script_execution", $tag, 1 );
	}

	return $tag;
}