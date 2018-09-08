<?php
/**
 * Plugin Name: MB - Elementor Intergrator
 * Plugin URI: https://metabox.io/plugins/mb-elementor-intergrator/
 * Description: Integrates Meta Box and Elementor Page Builder.
 * Version: 1.2
 * Author: MetaBox.io
 * Author URI: https://metabox.io
 * License: GPL2+
 *
 * @package Meta Box
 * @subpackage MB Elementor Integration
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || exit;

add_action( 'elementor/dynamic_tags/register_tags', 'mb_elementor_integrator_register_tags' );

/**
 * Register dynamic tags for Elementor.
 *
 * @param  object $tags Elementor dynamic tags instance.
 */
function mb_elementor_integrator_register_tags( $dynamic_tags ) {
	if ( !defined( 'RWMB_VER' ) ) {
		return;
	}

	require 'tags/base.php';
	require 'tags/text.php';
	require 'tags/image.php';
	require 'tags/video.php';

	$dynamic_tags->register_tag( 'MB_Elementor_Integrator_Text' );
	$dynamic_tags->register_tag( 'MB_Elementor_Integrator_Image' );
	$dynamic_tags->register_tag( 'MB_Elementor_Integrator_Video' );
}


