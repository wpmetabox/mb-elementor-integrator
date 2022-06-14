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
		list( $option_name, $field_id ) = explode( ':', $key );
		return rwmb_meta( trim( $field_id ), [ 'object_type' => 'setting' ], $option_name );
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
