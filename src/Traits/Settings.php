<?php
namespace MBEI\Traits;

trait Settings {
	public function get_group() {
		return 'site';
	}

	private function get_option_groups() {
		$groups = [];

		$fields = rwmb_get_registry( 'field' )->get_by_object_type( 'setting' );
		foreach ( $fields as $option_name => $list ) {
			$options = [
				'' => __( '-- Select a field --', 'mb-elementor-integrator' ),
			];
			foreach ( $list as $field ) {
				$options[ "{$option_name}:{$field['id']}" ] = $field['name'] ?: $field['id'];
			}
			$groups[] = [
				'label'   => $option_name,
				'options' => $options,
			];
		}

		return $groups;
	}

	private function handle_get_value() {
		$key = $this->get_settings( 'key' );
		if ( ! $key ) {
			return null;
		}
		list( $option_name, $field_id ) = explode( ':', $key );
		return rwmb_meta( trim( $field_id ), [ 'object_type' => 'setting' ], $option_name );
	}

	private function the_value() {
		$key = $this->get_settings( 'key' );
		if ( ! $key ) {
			return null;
		}
		list( $option_name, $field_id ) = explode( ':', $key );
		rwmb_the_value( trim( $field_id ), [ 'object_type' => 'setting' ], $option_name );
	}
}