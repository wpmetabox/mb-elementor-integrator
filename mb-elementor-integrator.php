<?php
/**
 * Plugin Name: Meta Box - Elementor Integrator
 * Plugin URI: https://metabox.io/plugins/mb-elementor-integrator/
 * Description: Integrates Meta Box and Elementor Page Builder.
 * Version: 1.1.1
 * Author: MetaBox.io
 * Author URI: https://metabox.io
 * License: GPL2+
 *
 * @package Meta Box
 * @subpackage MB Elementor Integrator
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

	require 'traits/object.php';
	require 'traits/post.php';

	require 'traits/fields/text.php';
	require 'traits/fields/image.php';
	require 'traits/fields/video.php';

	require 'tags/post/text.php';
	require 'tags/post/image.php';
	require 'tags/post/video.php';

	$dynamic_tags->register_tag( 'MBEI_Tag_Text' );
	$dynamic_tags->register_tag( 'MBEI_Tag_Image' );
	$dynamic_tags->register_tag( 'MBEI_Tag_Video' );

	if ( function_exists( 'mb_term_meta_load' ) ) {
		require 'traits/archive.php';
		require 'tags/archive/text.php';
		require 'tags/archive/image.php';
		require 'tags/archive/video.php';
		$dynamic_tags->register_tag( 'MBEI_Tag_Archive_Text' );
		$dynamic_tags->register_tag( 'MBEI_Tag_Archive_Image' );
		$dynamic_tags->register_tag( 'MBEI_Tag_Archive_Video' );
	}

	if ( function_exists( 'mb_settings_page_load' ) ) {
		require 'traits/settings.php';
		require 'tags/settings/text.php';
		require 'tags/settings/image.php';
		require 'tags/settings/video.php';
		$dynamic_tags->register_tag( 'MBEI_Tag_Settings_Text' );
		$dynamic_tags->register_tag( 'MBEI_Tag_Settings_Image' );
		$dynamic_tags->register_tag( 'MBEI_Tag_Settings_Video' );
	}
}


