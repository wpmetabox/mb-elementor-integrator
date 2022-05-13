<?php

namespace MBEI\Widgets;

use ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document;
use ElementorPro\Modules\ThemeBuilder\Documents\Loop;
use MBEI\Traits\Location;
use MBEI\ThemeBuilder\Documents\Group as Group_Document;
use Elementor\Plugin;
use Exception;

class GroupLocation {

	use Location;

	public function __construct() {
		add_action( 'elementor/theme/register_locations', [ $this, 'register_locations' ] );
		add_action( 'elementor/documents/register', [ $this, 'register_document' ] );
	}

	public function get_id() {
		return 'metabox_group_template';
	}

	public function get_class_full_name() {
		return Group_Document::get_class_full_name();
	}

	public function register_locations( $location_manager ) {
		$location_manager->register_location(
			$this->get_id(),
			[
				'label'           => __( 'Meta Box Group Skin', 'mb-elementor-integrator' ),
				'multiple'        => true,
				'edit_in_content' => true,
			]
		);
	}

	public function add_more_types( $settings ) {
		$post_id  = get_the_ID();
		$document = $this->get_document( $post_id );

		if ( ! $document || ! array_key_exists( 'theme_builder', $settings ) ) {
			return $settings;
		}

		$new_types    = [ $this->get_id() => Loop::get_properties() ];
		$add_settings = [ 'theme_builder' => [ 'types' => $new_types ] ];
		if ( ! array_key_exists( $this->get_id(), $settings['theme_builder']['types'] ) ) {
			$settings = array_merge_recursive( $settings, $add_settings );
		}
		return $settings;
	}

	public function get_document( $post_id ) {

		$document = null;

		try {
			$document = Plugin::$instance->documents->get( $post_id );
		} catch ( Exception $e ) {
			return null;
		}

		if ( ! empty( $document ) && ! $document instanceof Theme_Document ) {
			$document = null;
		}

		return $document;
	}

}
