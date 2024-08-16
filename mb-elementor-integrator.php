<?php
/**
 * Plugin Name: Meta Box - Elementor Integrator
 * Plugin URI:  https://metabox.io/plugins/mb-elementor-integrator/
 * Description: Integrates Meta Box and Elementor Page Builder.
 * Version:     2.1.12
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL2+
 */

// Prevent loading this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( file_exists( __DIR__ . '/vendor' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

if ( class_exists( 'MBEI\Loader' ) ) {
	new \MBEI\Loader();
}
