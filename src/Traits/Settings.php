<?php
namespace MBEI\Traits;

use Elementor\Plugin;
use MBEI\GroupField;

trait Settings {
	public function get_group() {
		return 'site';
	}

	private function get_option_groups() {
		$document = Plugin::instance()->documents->get_current();
		if ( ! empty( $document ) && 'metabox_group_template' === $document->get_type() ) {
			$group_field = new GroupField();
			return $group_field->get_option_dynamic_tag( 'setting' );
		}

		$groups = [
			0 => [
				'options' => [
					'' => __( '-- Select a field --', 'mb-elementor-integrator' ),
				],
			],
		];

		$fields = rwmb_get_registry( 'field' )->get_by_object_type( 'setting' );
		foreach ( $fields as $option_name => $list ) {
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
        $group_field = new GroupField();
        
		$key = $this->get_settings( 'key' );
		if ( ! $key ) {
			return null;
		}
		list( $option_name, $field_id ) = explode( ':', $key, 2 );
        if ( false === strpos( $field_id, '.' ) && false === strpos( $field_id, ':' ) ) {
            $field = rwmb_get_field_settings( $field_id, [ 'object_type' => 'setting' ], $option_name );            
            $value = rwmb_meta( trim( $field_id ), [ 'object_type' => 'setting' ], $option_name );
            
            if ( $field['mime_type'] === 'image' || $field['type'] === 'image' ) {
                if ( is_array( $value ) && true === is_int( key( $value ) ) ) {
                    $value = array_shift( $value );
                }            
                return $group_field->get_image_for_dynamic_tag( $value, $field['type'] );                
            }           
            
            return $value;
        }
        
        // Get data from group or sub-group
        list( $field_id, $sub_fields ) = false !== strpos( $field_id, ':' ) ? explode( ':', $field_id, 2 ) : explode( '.', $field_id );               
        $sub_fields = false !== strpos( $sub_fields, '.' ) ? explode( '.', $sub_fields, 2 ) : (array) $sub_fields;
        
        $valueField = rwmb_meta( $field_id, [ 'object_type' => 'setting' ], $option_name );
        if ( 0 < count( $valueField ) ) {
			if ( true === is_int( key( $valueField ) ) ) {
				$valueField = array_shift( $valueField );
			}
        }

        $field = rwmb_get_field_settings( $field_id, [ 'object_type' => 'setting' ], $option_name );
        $field['fields'] = array_combine( array_column( $field['fields'], 'id' ), $field['fields'] );
        
        if ( 1 == count( $sub_fields ) ) {
            if ( $field['fields'][ end( $sub_fields ) ]['mime_type'] !== 'image' ) {
                return $valueField[ end( $sub_fields ) ];
            }

            $image_id = $valueField[ end( $sub_fields ) ];

        }
        
        $clone_field = $field;
        $type = '';
        foreach ( $sub_fields as $index => $sub_field ) {
            if ( !isset( $clone_field['fields'][ $sub_field ] ) ) {
                break;
            }

            if ( $index < count( $sub_fields ) - 1 ) {
                $clone_field = $clone_field['fields'][ $sub_field ];
                $clone_field['fields'] = array_combine( array_column( $clone_field['fields'], 'id' ), $clone_field['fields'] );
                continue;
            }

           $type = $clone_field['fields'][ $sub_field ]['type'];            
        }
        
        if ( empty( $type ) ) {
            return;
        }

        $valueField  = $group_field->get_value_nested_group( $valueField, $sub_fields, true );
        if ( false !== is_int( key( $valueField ) ) ) {            
            $valueField = array_shift( $valueField );
        }
        $image_id = $valueField;
        
        return $group_field->get_image_for_dynamic_tag( $image_id, $type );      
	}

	private function the_value() {
		$key = $this->get_settings( 'key' );
		if ( ! $key ) {
			return null;
		}
		list( $option_name, $field_id ) = explode( ':', $key, 2 );
		$group_field                    = new GroupField();
		$value                          = $group_field->get_value_dynamic_tag( $option_name, $field_id, $this->get_settings( 'mb_skin_template' ), 'setting' );
		if ( $value ) {
			return;
		}

		rwmb_the_value( trim( $field_id ), [ 'object_type' => 'setting' ], $option_name );
	}
}
