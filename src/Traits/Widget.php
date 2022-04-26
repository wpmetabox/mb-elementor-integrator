<?php
/**
 *  Trait for all object type that handle register widget skin.
 */

namespace MBEI\Traits;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use MBB\Registry;
trait Widget {
	/**
	 * Get widget name.
	 *
	 * Retrieve Term List widget name.
	 *
	 * @since 0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'mb-widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Term List widget title.
	 *
	 * @since 0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'MB Widget', 'mb-elementor-integrator' );
	}

	private function get_list_field_group() {
		$meta_boxs = apply_filters( 'rwmb_meta_boxes', [] );
		$fields    = [];
		if ( 0 < count( $meta_boxs ) ) {
			foreach ( $meta_boxs as $meta_box ) {
				$field = $this->get_field_type_group( $meta_box );
				if ( 0 < count( $field ) ) {
					foreach ( $field as $f ) {
						array_push( $fields, $f );
					}
				}
			}
		}
		$fields = array_filter( $fields );
		$list   = [
			'' => 'Select Field Cloneable Group',
		];
		foreach ( $fields as $field ) {
			$list[ $field['id'] ] = $field['name'];
		}
		return $list;
	}

	/**
	 * Check Type google map
	 * @param array $meta_box
	 * @return boolen $is_map
	 */
	private function get_field_type_group( $meta_box ) {
		$fields = [];

		if ( isset( $meta_box['fields'] ) ) {
			$is_field_group = array_search( 'group', array_column( $meta_box['fields'], 'type' ) );
			if ( false !== $is_field_group ) {
				foreach ( $meta_box['fields'] as $field ) {
					if ( 'group' === $field['type'] && isset( $field['clone'] ) && true === $field['clone'] ) {
						$fields[] = $field;
					}
				}
			}
		}

		return $fields;

	}
}
