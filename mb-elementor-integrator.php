<?php
/**
 * Plugin Name: Meta Box - Elementor Integrator
 * Plugin URI:  https://metabox.io/plugins/mb-elementor-integrator/
 * Description: Integrates Meta Box and Elementor Page Builder.
 * Version:     2.0.8
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL2+
 *
 * @package    Meta Box
 * @subpackage MB Elementor Integrator
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || die;

if ( file_exists( __DIR__ . '/vendor' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

require plugin_dir_path( __FILE__ ) . 'src/loader.php';

if ( class_exists( 'MBEI_Loader' ) ) {
	new MBEI_Loader();
}
