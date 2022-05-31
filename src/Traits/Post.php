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
			return $this->get_option_field_group();
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

	private function get_option_field_group() {
		$groups[] = [
			'label'   => __( '-- Meta Box Field Group --', 'mb-elementor-integrator' ),
			'options' => [],
		];

		$group_field = new GroupField();
		$fields      = $group_field->get_field_group();

		if ( 0 === count( $fields ) ) {
			return $groups;
		}

		foreach ( $fields as $field ) {
			if ( empty( $field['fields'] ) ) {
				continue;
			}

			$field_id = '';
			if ( false !== strpos( $field['id'], '.' ) ) {
				$field_id = str_replace( '.', ':', $field['id'] );
			}

			$child_options = [];
			foreach ( $field['fields'] as $key => $subfield ) {
				if ( ! empty( $field_id ) ) {
					$child_options[ "{$field_id}.{$subfield['id']}" ] = $subfield['name'] ?: $subfield['id'];
					continue;
				}
				$child_options[ "{$field['id']}:{$subfield['id']}" ] = $subfield['name'] ?: $subfield['id'];
			}
			$label                                    = ! empty( $field['name'] ) ? $field['name'] : $field['group_title'];
			$label_slug                               = str_replace( '', '-', $label );
			$groups[ "{$field['id']}-{$label_slug}" ] = [
				'label'   => $label,
				'options' => $child_options,
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
				$valueField  = $group_field->get_value_nested_group( $valueField, $sub_fields );
				if ( true === is_int( key( $valueField ) ) ) {
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
		list( $post_type, $field_id ) = explode( ':', $key );
        if ( empty( get_post_type_object( $post_type ) ) ) { 
            $valueField = rwmb_get_value( $post_type );            
            if ( 0 < count( $valueField ) ) {
                $sub_fields  = explode( '.', $field_id );
                $group_field = new GroupField();
                $valueField  = $group_field->get_value_nested_group( $valueField, $sub_fields, true );   
     
                if ( false !== is_int( key( $valueField ) ) ) {
                    $valueField = array_shift( $valueField );
                }
                
                if ( is_array( $valueField ) ) {
					$field                                  = rwmb_get_field_settings( $post_type, [ ], null );
					$field['fields']                        = array_combine( array_column( $field['fields'], 'id' ), $field['fields'] );
					$field['fields'][ $field_id ]['fields'] = array_combine( array_column( $field['fields'][ $field_id ]['fields'], 'id' ), $field['fields'][ $field_id ]['fields'] );
					$this->extract_value( $valueField, $field['fields'][ $field_id ]['fields'] );
                    return;
                }
                
                if ( isset( $valueField[ $field_id ] ) && !is_array( $valueField[ $field_id ] ) ) {
                    $valueField = $valueField[ $field_id ];
                }
                
                echo $valueField;
                return;
            }
            
            return null;
        }
        
        $field = rwmb_get_field_settings( $field_id, [], null );

        if ( ! empty( $field ) && ( 'color' === $field['type'] ) ) {
            echo rwmb_get_value( $field_id );
        } else {
            rwmb_the_value( $field_id );
        }        
	}

	private function extract_value( $field = [], $fieldSetting = [], $echo = true ) {
		if ( true === is_int( key( $field ) ) ) {
			$field = array_shift( $field );
		}

		if ( false === $echo ) {
			return $field;
		}

		echo '<div class="mbei-group mbei-group-nested">';
		foreach ( $field as $key => $value ) {
			if ( isset( $fieldSetting[ $key ] ) && isset( $fieldSetting[ $key ]['mime_type'] ) && 'image' === $fieldSetting[ $key ]['mime_type'] && ! empty( $value ) ) {
				echo '<div class="mbei-subfield mbei-subfield--' . $key . '">' . wp_get_attachment_image( $value, 'full' ) . '</div>';
				continue;
			}
			echo '<div class="mbei-subfield mbei-subfield--' . $key . '">' . $value . '</div>';
		}
		echo '</div>';
	}
}
