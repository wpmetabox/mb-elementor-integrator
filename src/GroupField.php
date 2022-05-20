<?php

namespace MBEI;

class GroupField {

	public function get_current_post() {
		global $post, $wp_query;

		if ( ! empty( $post ) ) {
			return $post;
		}

		list( $post_type, $slug ) = explode( '/', $wp_query->query['pagename'] );
		$current_post             = get_page_by_path( $slug, OBJECT, $post_type );
		return $current_post;
	}

	public function parse_options( $fields = [], $field_group_id ) {
		if ( empty( $fields ) || ! isset( $fields['fields'] ) || empty( $fields['fields'] ) ) {
			return [];
		}

		$sub_fields = [];
		foreach ( $fields['fields'] as $field ) {
			$sub_fields[ $field_group_id . ':' . $field['id'] ] = $field['name'];
		}

		return $sub_fields;
	}

	public function get_field_group( $key = null ) {
		$field_registry = rwmb_get_registry( 'field' );
		$post_types     = $field_registry->get_by_object_type( 'post' );

		$return_fields = [];
		if ( 0 < count( $post_types ) ) {
			foreach ( $post_types as $fields ) {
				// Fields is empty
				if ( 0 === count( $fields ) ) {
					continue;
				}
				// get list field type=group
				$group_fields = $this->get_field_type_group( $fields );
				if ( 0 === count( $group_fields ) ) {
					continue;
				}

				foreach ( $group_fields as $group_field ) {
					if ( ! empty( $key ) && $key !== $group_field['id'] ) {
						continue;
					}

					array_push( $return_fields, $group_field );
				}
			}
		}

		if ( ! empty( $key ) && 0 < count( $return_fields ) ) {
			return $return_fields[0];
		}

		return array_filter( $return_fields );
	}

	public function get_list_field_group() {
		$fields = $this->get_field_group();
		$list   = [];
		foreach ( $fields as $k => $field ) {
			if ( in_array( $field['id'], $list ) ) {
				continue;
			}

			if ( strpos( $field['id'], '.' ) !== false ) {
				$field_group    = explode( '.', $field['id'] );
				$is_field_group = array_search( $field_group[0], array_column( $fields, 'id' ) );

				$label_group          = ! empty( $fields[ $is_field_group ]['name'] ) ? $fields[ $is_field_group ]['name'] : $fields[ $is_field_group ]['group_title'];
				$list[ $field['id'] ] = ( ! empty( $field['name'] ) ? $field['name'] : $field['group_title'] ) . ' ( ' . $label_group . ' )';
				continue;
			}

			$list[ $field['id'] ] = ! empty( $field['name'] ) ? $field['name'] : $field['group_title'];
		}
		return $list;
	}

	/**
	 * Check Type field group
	 * @param array $fields
	 * @return array $return_fields fields of type group
	 */
	private function get_field_type_group( $fields, $nested = '' ) {
		// not field type is group.
		$is_field_group = array_search( 'group', array_column( $fields, 'type' ) );
		if ( false === $is_field_group ) {
			return [];
		}

		$return_fields = [];
		foreach ( $fields as $field ) {
			if ( 'group' === $field['type'] ) {
				if ( ! empty( $nested ) ) {
					$field['id'] = $nested . '.' . $field['id'];
				}
				$return_fields[] = $field;
				if ( isset( $field['fields'] ) && 0 < count( $field['fields'] ) ) {
					$return_fields = array_merge( $return_fields, $this->get_field_type_group( $field['fields'], $field['id'] ) );
				}
			}
		}

		return $return_fields;
	}

	public function get_value_nested_group( $values = [], $keys = [] ) {
		if ( empty( $keys ) ) {
			return $values;
		}

		foreach ( $keys as $key ) {
			if ( ! isset( $values[ $key ] ) ) {
				return $values;
			}

			$values = $values[ $key ];
		}

		return $values;
	}

	public function split_field_nested( $fields = [] ) {
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return $fields;
		}

		$return = [];
		foreach ( $fields as $key => $value ) {
			if ( strpos( $value, '.' ) === false ) {
				continue;
			}

			$keys                             = explode( '.', $value, 2 );
			$fields[ $key ]                   = $keys[0];
			$return['sub_cols'][ $keys[0] ][] = $keys[1];
		}

		$return['cols'] = $fields;
		return $return;
	}

	public static function change_url_ssl( $url ) {
		if ( is_ssl() && false === strpos( $url, 'https' ) ) {
			return str_replace( 'http', 'https', $url );
		}
		return $url;
	}

	public function display_field( $data, $field = [], $return = false ) {

		switch ( $field['type'] ) {
			case 'image':
			case 'image_advanced':
			case 'image_select':
			case 'image_upload':
			case 'single_image':
				$file_type = 'image';
				break;
			default:
				$file_type = 'text';
				break;
		}

		$path_file = plugin_dir_path( __DIR__ ) . 'src/Templates/display_field-' . $file_type . '.php';

		if ( file_exists( $path_file ) ) {
			require $path_file;
		}
	}

}
