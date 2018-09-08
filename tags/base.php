<?php
use Elementor\Controls_Manager;

trait MB_Elementor_Integrator_Base {
	public function get_title() {
		return __( 'Meta Box Field', 'mb-elementor-integrator' );
	}

	public function get_group() {
		return 'post';
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
			'options' => $this->get_fields(),
		] );
	}

	protected function get_fields() {
		$fields  = $this->get_all_fields();
		$options = [
			'' => __( '---', 'mb-elementor-integrator' ),
		];

		foreach ( $fields as $list ) {
			foreach ( $list as $field ) {
				$options[ $field['id'] ] = $field['name'] ? $field['name'] : $field['id'];
			}
		}
		return $options;
	}

	protected function get_all_fields() {
		$fields = rwmb_get_registry( 'field' )->get_by_object_type( 'post' );

		// Remove fields for non-existing post types.
		$fields = array_filter( $fields, function( $post_type ) {
		    return post_type_exists( $post_type );
		}, ARRAY_FILTER_USE_KEY );

		// Remove fields that don't have value.
		array_walk( $fields, function ( &$list ) {
			$list = array_filter( $list, function( $field ) {
				if ( in_array( $field['type'], array( 'heading', 'divider', 'custom_html', 'button' ), true ) ) {
					return false;
				}
				$supported_fields = $this->get_supported_fields();
				if ( $supported_fields && ! in_array( $field['type'], $supported_fields ) ) {
					return false;
				}
				return true;
			} );
		} );
		return $fields;
	}

	protected function get_supported_fields() {
		return [];
	}
}