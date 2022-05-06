<?php
namespace MBEI\Traits;

trait Post {
	public function get_group() {
		return 'post';
	}

	private function get_option_groups() {
		$groups = [
			0 => [
				'options' => [
					'' => __( '-- Select a field --', 'mb-elementor-integrator' ),
				],
			],
		];

		$fields = rwmb_get_registry( 'field' )->get_by_object_type( 'post' );
		$fields = array_diff_key( $fields, array_flip( [ 'mb-post-type', 'mb-taxonomy' ] ) );

		foreach ( $fields as $post_type => $list ) {
			$post_type_object = get_post_type_object( $post_type );
			if ( ! $post_type_object ) {
				continue;
			}
			foreach ( $list as $field ) {
				if ( 'group' !== $field['type'] ) {
					$options[ "{$post_type}:{$field['id']}" ] = $field['name'] ?: $field['id'];
				} else {
					if ( empty( $field['fields'] ) ) {
						continue;
					}

					$child_options = [];
					foreach ( $field['fields'] as $key => $subfield ) {
						$child_options[ "{$field['id']}:{$subfield['id']}" ] = $subfield['name'] ?: $subfield['id'];
					}
					$label      = 'Entry {#}' !== $field['group_title'] ? $field['group_title'] : $field['name'];
					$label_slug = str_replace( '', '-', $label );
					$child_groups[ "{$field['id']}-{$label_slug}" ] = [
						'label'   => $label,
						'options' => $child_options,
					];
				}
			}
			$groups[] = [
				'label'   => $post_type_object->labels->singular_name,
				'options' => $options,
			];
		}

		if ( ! isset( $child_groups ) || 0 === count( $child_groups ) ) {
			return $groups;
		}

		$groups[] = [
			'label'   => __( '-- Metabox Field Group --', 'mb-elementor-integrator' ),
			'options' => [],
		];
		foreach ( $child_groups as $child_group ) {
			$groups[] = $child_group;
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
		if ( ! empty( get_post_type_object( $post_type ) ) ) {
			return rwmb_meta( $field_id );
		}

		$field      = rwmb_get_field_settings( $post_type, array(), null );
		$valueField = rwmb_get_value( $post_type );
		if ( 0 < count( $valueField ) ) {
			$image_id = array_shift( $valueField )[ $field_id ];
			$image    = wp_get_attachment_image_src( $image_id );
			return [
				'ID'       => $image_id,
				'full_url' => $image[0],
			];
		}

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
		if ( empty( get_post_type_object( $post_type ) ) ) {
			$valueField = rwmb_get_value( $post_type );

			if ( 0 < count( $valueField ) ) {
				echo array_shift( $valueField )[ $field_id ];
			}
		} else {
			$field = rwmb_get_field_settings( $field_id, array(), null );

			if ( ! empty( $field ) && ( 'color' === $field['type'] ) ) {
				echo rwmb_get_value( $field_id );
			} else {
				rwmb_the_value( $field_id );
			}
		}
	}
}
