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
		$key = $this->get_settings( 'key' );
		if ( ! $key ) {
			return null;
		}
		list( $option_name, $field_id ) = explode( ':', $key, 2 );
        if ( false === strpos( $field_id, '.' ) && false === strpos( $field_id, ':' ) ) {
            return rwmb_meta( trim( $field_id ), [ 'object_type' => 'setting' ], $option_name );
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
               
        $group_field = new GroupField();
        $valueField  = $group_field->get_value_nested_group( $valueField, $sub_fields, true );            
        if ( false !== is_int( key( $valueField ) ) ) {
            $valueField = array_shift( $valueField );
        }
        $image_id = $valueField;
        
        $image    = wp_get_attachment_image_src( $image_id, 'full' );
        return [
            'ID'       => $image_id,
            'full_url' => $image[0],
        ];        
	}

	private function the_value() {
		$key = $this->get_settings( 'key' );
		if ( ! $key ) {
			return null;
		}
		list( $option_name, $field_id ) = explode( ':', $key, 2 );
		$group_field                    = new GroupField();
		$value                          = $group_field->get_value_dynamic_tag( $option_name, $field_id, $this->get_settings( 'mb_skin_template' ) );
		if ( $value ) {
			return;
		}

		rwmb_the_value( trim( $field_id ), [ 'object_type' => 'setting' ], $option_name );
	}
}
