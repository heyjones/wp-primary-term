<?php
/**
 * Plugin Name: Primary Term
 * Plugin URI:  https://github.com/heyjones/wp-primary-term
 * Description: 
 * Version:     0.1.0
 * Author:      heyjones
 * Author URI:  https://heyjones.com
 * Text Domain: primary-term
 * Domain Path: /languages
 *
 * @package PrimaryTerm
 */

// Useful global constants.
define( 'PRIMARY_TERM_VERSION', '0.1.0' );
define( 'PRIMARY_TERM_URL', plugin_dir_url( __FILE__ ) );
define( 'PRIMARY_TERM_PATH', plugin_dir_path( __FILE__ ) );
define( 'PRIMARY_TERM_INC', PRIMARY_TERM_PATH . 'includes/' );

// Include files.
require_once PRIMARY_TERM_INC . 'functions/core.php';

// Activation/Deactivation.
register_activation_hook( __FILE__, '\PrimaryTerm\Core\activate' );
register_deactivation_hook( __FILE__, '\PrimaryTerm\Core\deactivate' );

// Bootstrap.
PrimaryTerm\Core\setup();

// Require Composer autoloader if it exists.
if ( file_exists( PRIMARY_TERM_PATH . '/vendor/autoload.php' ) ) {
	require_once PRIMARY_TERM_PATH . 'vendor/autoload.php';
}
