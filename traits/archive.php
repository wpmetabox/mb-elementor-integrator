<?php
trait MBEI_Archive {
	public function get_group() {
		return 'archive';
	}

	private function get_option_groups() {
		$groups = [];

		$fields = $this->get_fields_by_object_type( 'term' );
		foreach ( $fields as $taxonomy => $list ) {
			$taxonomy_object = get_taxonomy( $taxonomy );
			if ( ! $taxonomy_object ) {
				continue;
			}
			$options = [];
			$label = $taxonomy_object->labels->singular_name;
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
		list( $taxonomy, $field_id ) = explode( ':', $key );
		return rwmb_meta( $field_id, [ 'object_type' => 'term' ], get_queried_object_id() );
	}
}