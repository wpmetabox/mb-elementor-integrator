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
		if ( ! empty( $document ) && 'metabox_group_template' === $document->get_type() ) {
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
		if ( ! empty( get_post_type_object( $post_type ) ) ) {
			return rwmb_meta( $field_id );
		}

		$valueField = rwmb_get_value( $post_type );
		if ( 0 < count( $valueField ) ) {
			if ( true === is_int( key( $valueField ) ) ) {
				$valueField = array_shift( $valueField );
			}

			if ( false !== strpos( $field_id, '.' ) ) {
				$sub_fields  = explode( '.', $field_id );
				$group_field = new GroupField();
				$valueField  = $group_field->get_value_nested_group( $valueField, $sub_fields, true );
				if ( false !== is_int( key( $valueField ) ) ) {
					$valueField = array_shift( $valueField );
				}
				$field_id = end( $sub_fields );
			}

			$image_id = $valueField[ $field_id ];
			$image    = wp_get_attachment_image_src( $image_id, 'full' );
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

		list( $post_type, $field_id ) = explode( ':', $key, 2 );
        
		$group_field = new GroupField();
		$value       = $group_field->get_value_dynamic_tag( $post_type, $field_id, $this->get_settings( 'mb_skin_template' ) );
		if ( $value ) {
			return;
		}
        
        if ( empty( get_post_type_object( $post_type ) ) ) {
            $valueField = rwmb_get_value( $post_type );
            if ( 0 == count( $valueField ) ) {
                return;
            }
            
            if ( false !== is_int( key( $valueField ) ) ) {
                $valueField = array_shift( $valueField );
            }
                       
            if ( !isset( $valueField[ $field_id ] ) ) {
                return;
            }
            
            if ( is_array( $valueField[ $field_id ] ) ) {
                $field                                  = rwmb_get_field_settings( $post_type, [ ], null );
                $field['fields']                        = array_combine( array_column( $field['fields'], 'id' ), $field['fields'] );
                $field['fields'][ $field_id ]['fields'] = array_combine( array_column( $field['fields'][ $field_id ]['fields'], 'id' ), $field['fields'][ $field_id ]['fields'] );
                
                $group_field = new GroupField();
                $group_field->extract_value_dynamic_tag( $valueField[ $field_id ], $field['fields'][ $field_id ]['fields'], null );
                return;
            }            
            
            echo $valueField[ $field_id ];
            return;
        }        

		$field = rwmb_get_field_settings( $field_id, [], null );
		if ( ! empty( $field ) && ( 'color' === $field['type'] ) ) {
			rwmb_the_value( $field_id );
			return;
		}

		rwmb_the_value( $field_id );
	}
}
