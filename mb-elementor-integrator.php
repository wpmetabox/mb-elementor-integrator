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

if ( ! function_exists( 'mb_elementor_integrator_register_tags' ) ) {
	add_action( 'elementor/dynamic_tags/register_tags', 'mb_elementor_integrator_register_tags' );

	/**
	 * Register dynamic tags for Elementor.
	 *
	 * @param object $dynamic_tags Elementor dynamic tags instance.
	 */
	function mb_elementor_integrator_register_tags( $dynamic_tags ) {
		if ( ! defined( 'RWMB_VER' ) ) {
			return;
		}

		if ( file_exists( __DIR__ . '/vendor' ) ) {
			require __DIR__ . '/vendor/autoload.php';
		}

		$dynamic_tags->register_tag( 'MBEI\Tags\Post\Text' );
		$dynamic_tags->register_tag( 'MBEI\Tags\Post\Image' );
		$dynamic_tags->register_tag( 'MBEI\Tags\Post\Video' );

		if ( function_exists( 'mb_term_meta_load' ) ) {
			$dynamic_tags->register_tag( 'MBEI\Tags\Archive\Text' );
			$dynamic_tags->register_tag( 'MBEI\Tags\Archive\Image' );
			$dynamic_tags->register_tag( 'MBEI\Tags\Archive\Video' );
		}

		if ( function_exists( 'mb_settings_page_load' ) ) {
			$dynamic_tags->register_tag( 'MBEI\Tags\Settings\Text' );
			$dynamic_tags->register_tag( 'MBEI\Tags\Settings\Image' );
			$dynamic_tags->register_tag( 'MBEI\Tags\Settings\Video' );
		}
	}
}