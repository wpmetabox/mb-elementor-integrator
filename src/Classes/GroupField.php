<?php

namespace MBEI\Classes;

class GroupField {

	public function init() {
		add_action( 'wp_ajax_group_subfield', [ $this, 'group_subfield' ] );
		add_action( 'wp_ajax_nopriv_group_subfield', [ $this, 'group_subfield' ] );
	}

	public function group_subfield() {
		// Check for nonce security.
		if ( ! wp_verify_nonce( $_POST['nonce'], 'mbei-ajax' ) ) {
			die();
		}
		$id = $_POST['groupfield'];

		$groupfield = self::get_field_group( $id );
		$sub_fields = self::parse_options( $groupfield, [ 'id' => $id ] );

		wp_send_json_success( $sub_fields );
	}

	public static function parse_options( $fields = [], $options = [] ) {
		if ( empty( $fields ) ) {
			return [];
		}

		$sub_fields = [];
		if ( isset( $fields['fields'] ) && ! empty( $fields['fields'] ) ) {
			foreach ( $fields['fields'] as $field ) {
				$sub_fields[ $options['id'] . ':' . $field['id'] ] = $field['name'];
			}
		}
		return $sub_fields;
	}

	public static function get_field_group( $key = null ) {
		$meta_boxs = apply_filters( 'rwmb_meta_boxes', [] );

		$fields = [];
		if ( 0 < count( $meta_boxs ) ) {
			foreach ( $meta_boxs as $meta_box ) {
				$field = self::get_field_type_group( $meta_box );
				if ( 0 < count( $field ) ) {
					foreach ( $field as $f ) {
						if ( ! empty( $key ) ) {
							if ( $key === $f['id'] ) {
								array_push( $fields, $f );
							}
						} else {
							array_push( $fields, $f );
						}
					}
				}
			}
		}

		if ( ! empty( $key ) && 0 < count( $fields ) ) {
			return $fields[0];
		}

		return array_filter( $fields );
	}

	public static function get_list_field_group() {
		$fields = self::get_field_group();
		$list   = [
			'' => 'Select Field Cloneable Group',
		];
		foreach ( $fields as $field ) {
			$list[ $field['id'] ] = $field['name'];
		}
		return $list;
	}

	/**
	 * Check Type field group
	 * @param array $meta_box
	 * @return boolen $fields
	 */
	private static function get_field_type_group( $meta_box ) {
		$fields = [];

		if ( isset( $meta_box['fields'] ) ) {
			$is_field_group = array_search( 'group', array_column( $meta_box['fields'], 'type' ) );
			if ( false !== $is_field_group ) {
				foreach ( $meta_box['fields'] as $key => $field ) {
					if ( 'group' === $field['type'] && isset( $field['clone'] ) && true === $field['clone'] ) {
						$fields[] = $field;
					}
				}
			}
		}

		return $fields;
	}

	public static function pagination( $options = [] ) {
		extract( $options );
		$path_file = plugin_dir_path( __DIR__ ) . 'Templates/pagination-' . $type . '.php';

		if ( file_exists( $path_file ) ) {
			require $path_file;
		}
	}

	public static function display_field( $data, $data_field = [] ) {
		extract( $data_field );

		$path_file = plugin_dir_path( __DIR__ ) . 'Templates/display_field-' . $type . '.php';

		if ( file_exists( $path_file ) ) {
			require $path_file;
		}
	}
}
