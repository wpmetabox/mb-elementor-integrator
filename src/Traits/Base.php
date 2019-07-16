<?php
/**
 * Base trait for all object type that handle register groups and fields.
 */

namespace MBEI\Traits;

use Elementor\Controls_Manager;

trait Base {
	public function get_title() {
		return __( 'Meta Box Field', 'mb-elementor-integrator' );
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	public function is_settings_required() {
		return true;
	}

	protected function _register_controls() {
		$this->add_control( 'key', [
			'label'   => __( 'Field', 'mb-elementor-integrator' ),
			'type'    => Controls_Manager::SELECT,
			'groups'  => $this->get_option_groups(),
		] );
	}

	private function get_fields_by_object_type( $object_type ) {
		$fields = rwmb_get_registry( 'field' )->get_by_object_type( $object_type );
		$fields = array_map( [ $this, 'remove_unsupported_fields' ], $fields );
		$fields = array_filter( $fields );

		return $fields;
	}

	private function remove_unsupported_fields( $fields ) {
		return array_filter( $fields, [ $this, 'is_supported' ] );
	}

	private function is_supported( $field ) {
		if ( in_array( $field['type'], [ 'heading', 'divider', 'custom_html', 'button' ], true ) ) {
			return false;
		}
		$supported_fields = $this->get_supported_fields();
		return empty( $supported_fields ) || in_array( $field['type'], $supported_fields );
	}
}