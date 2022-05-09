<?php

namespace MBEI\ThemeBuilder\Documents;

use ElementorPro\Modules\ThemeBuilder\Documents\Single;
use ElementorPro\Modules\ThemeBuilder\Module;

class Group extends Single {

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['condition_type']      = 'mb_group_condition';
		$properties['location']            = 'single';
		$properties['support_kit']         = true;
		$properties['support_site_editor'] = true;
		return $properties;
	}

	public function get_name() {
		return 'metabox_group_template';
	}

	public static function get_type() {
		return 'metabox_group_template';
	}

	protected static function get_site_editor_thumbnail_url() {
		return plugin_dir_url( dirname( __DIR__ ) ) . 'assets/images/mbgroup.svg';
	}

	public static function get_title() {
		return __( 'Meta Box Group Skin', 'mb-elementor-integrator' );
	}

	/**
	 * Let's be undependable from Preview As options.
	 * @return array
	 */
	public static function get_preview_as_options() {
		$post_types = self::get_public_post_types();

		$post_types['attachment'] = get_post_type_object( 'attachment' )->label;
		$post_types_options       = [];

		foreach ( $post_types as $post_type => $label ) {
			$post_types_options[ 'single/' . $post_type ] = get_post_type_object( $post_type )->labels->singular_name;
		}

		return [
			'single'   => [
				'label'   => __( 'Single', 'mb-elementor-integrator' ),
				'options' => $post_types_options,
			],
			'page/404' => __( '404', 'mb-elementor-integrator' ),
		];
	}

	/**
	 * Get list post type is public
	 * @return array $post_types_options
	 */
	public static function get_public_post_types() {
		$post_types_options = [];
		$args               = array(
			'public' => true,
		);
		$output             = 'objects'; // names or objects.
		$post_types         = get_post_types( $args, $output );
		foreach ( $post_types as $post_type ) {
			if ( 'elementor_library' !== $post_type->name ) {
				$post_types_options[ $post_type->name ] = $post_type->label;
			}
		}
		return $post_types_options;
	}

}
