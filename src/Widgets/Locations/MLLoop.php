<?php

namespace MBEI\Widgets\Locations;

use ElementorPro\Modules\ThemeBuilder\Documents\Loop;
use MBEI\Traits\Location;
use MBEI\ThemeBuilder\Documents\DLoop;
use MBEI\Classes\Document;

class MLLoop {

	use Location;

	public function __construct() {
		add_action( 'elementor/theme/register_locations', [ $this, 'register_locations' ] );
		add_action( 'elementor/documents/register', [ $this, 'register_document' ] );
	}

	public function get_id() {
		return 'mb_loop';
	}

	public function get_class_full_name() {
		return DLoop::get_class_full_name();
	}

	public function register_locations( $location_manager ) {
		$location_manager->register_location(
			$this->get_id(),
			[
				'label'           => __( 'MB Loop', 'mb-elementor-integrator' ),
				'multiple'        => true,
				'edit_in_content' => true,
			]
		);
	}

	public function add_more_types( $settings ) {
		$post_id  = get_the_ID();
		$document = Document::get_document( $post_id );

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

}
