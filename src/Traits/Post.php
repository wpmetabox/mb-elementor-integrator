<?php
namespace MBEI\Traits;

trait Post {
	public function get_group() {
		return 'post';
	}

	private function get_option_groups() {
		$groups = [];

		$fields = rwmb_get_registry( 'field' )->get_by_object_type( 'post' );
		$fields = array_diff_key( $fields, array_flip( ['mb-post-type', 'mb-taxonomy'] ) );

		foreach ( $fields as $post_type => $list ) {
			$post_type_object = get_post_type_object( $post_type );
			if ( ! $post_type_object ) {
				continue;
			}
			$options = [
				'' => __( '-- Select a field --', 'mb-elementor-integrator' ),
			];
			foreach ( $list as $field ) {
				$options[ "{$post_type}:{$field['id']}" ] = $field['name'] ?: $field['id'];
			}
			$groups[] = [
				'label'   => $post_type_object->labels->singular_name,
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
		if ( false === strpos( $key, ':' ) ) {
			return rwmb_meta( $key );
		}
		list( $post_type, $field_id ) = explode( ':', $key );
		return rwmb_meta( $field_id );
	}

	private function the_value() {
		$key = $this->get_settings( 'key' );
		if ( ! $key ) {
			return null;
		}
		if ( false === strpos( $key, ':' ) ) {
			return rwmb_meta( $key );
		}
		list( $post_type, $field_id ) = explode( ':', $key );
		$field = rwmb_get_field_settings( $field_id, array(), null );

		if ( ! empty( $field ) && ( 'color' === $field['type'] ) ) {
			echo rwmb_get_value( $field_id );
		} else {
			rwmb_the_value( $field_id );
		}
	}
}