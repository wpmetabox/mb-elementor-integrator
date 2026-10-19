<?php
namespace MBEI\Traits;

use Elementor\Plugin;
use MBEI\GroupField;

trait Post {
	public function get_group() {
		return 'post';
	}

	private function get_option_groups() {
		$document = Plugin::instance()->documents->get_current();
		if ( $this->is_metabox_group_template( $document ) ) {
			$group_field = new GroupField();
			return $group_field->get_option_dynamic_tag();
		}

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
			$options = [];
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

		list( $post_type, $field_id ) = explode( ':', $key, 2 );
		if ( ! empty( get_post_type_object( $post_type ) ) ) {
			return rwmb_meta( $field_id );
		}

		// Sub-field inside a Meta Box group (e.g. group_id:image_field).
		$group_value = rwmb_get_value( $post_type );
		if ( empty( $group_value ) ) {
			return null;
		}

		// Cloneable group: use the first clone for dynamic-tag preview / skin placeholder.
		if ( is_array( $group_value ) && is_int( key( $group_value ) ) ) {
			$group_value = reset( $group_value );
		}

		if ( ! is_array( $group_value ) || ! array_key_exists( $field_id, $group_value ) ) {
			// Fallback to previous output-capture behaviour.
			ob_start();
			( new GroupField() )->get_value_dynamic_tag( $post_type, $field_id, $this->get_settings( 'mb_skin_template' ) );
			$value_field = ob_get_clean();
			if ( is_numeric( $value_field ) ) {
				$id  = (int) $value_field;
				$src = wp_get_attachment_image_src( $id, 'full' );
				return [
					'ID'       => $id,
					'full_url' => $src[0] ?? '',
				];
			}
			return $value_field ?: null;
		}

		$value = $group_value[ $field_id ];

		// One attachment ID.
		if ( is_numeric( $value ) ) {
			$id  = (int) $value;
			$src = wp_get_attachment_image_src( $id, 'full' );
			return [
				'ID'       => $id,
				'full_url' => $src[0] ?? '',
			];
		}

		return $value;
	}

	private function the_value() {
		$key = $this->get_settings( 'key' );
		if ( ! $key ) {
			return null;
		}
		if ( false === strpos( $key, ':' ) ) {
			return rwmb_meta( $key );
		}

		list( $post_type, $field_id ) = explode( ':', $key, 2 );

		$group_field = new GroupField();
		$value       = $group_field->get_value_dynamic_tag( $post_type, $field_id, $this->get_settings( 'mb_skin_template' ) );
		if ( $value ) {
			return;
		}

		$field = rwmb_get_field_settings( $field_id, [], null );
		if ( ! empty( $field ) && ( 'color' === $field['type'] ) ) {
			echo esc_html( rwmb_get_value( $field_id ) );
			return;
		}

		rwmb_the_value( $field_id );
	}
}
