<?php
namespace MBEI\Traits;

trait Archive {
	public function get_group() {
		return 'archive';
	}

	private function get_option_groups() {
		$groups = [];

		$fields = rwmb_get_registry( 'field' )->get_by_object_type( 'term' );
		foreach ( $fields as $taxonomy => $list ) {
			$taxonomy_object = get_taxonomy( $taxonomy );
			if ( ! $taxonomy_object ) {
				continue;
			}
			$options = [
				'' => __( '-- Select a field --', 'mb-elementor-integrator' ),
			];
			foreach ( $list as $field ) {
				$options[ "{$taxonomy}:{$field['id']}" ] = $field['name'] ?: $field['id'];
			}
			$groups[] = [
				'label'   => $taxonomy_object->labels->singular_name,
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
		list( $taxonomy, $field_id ) = explode( ':', $key );
		return rwmb_meta( $field_id, [ 'object_type' => 'term' ], get_queried_object_id() );
	}

	private function the_value() {
		$key = $this->get_settings( 'key' );
		if ( ! $key ) {
			return null;
		}
		list( $taxonomy, $field_id ) = explode( ':', $key );
		rwmb_the_value( $field_id, [ 'object_type' => 'term' ], get_queried_object_id() );
	}
}