<?php

namespace MBEI\ThemeBuilder\Conditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use MBEI\ThemeBuilder\Conditions\MBField;

class MBFields extends Condition_Base {

	private $fields;

	public static function get_type() {
		return 'mb_fields_condition';
	}

	public static function get_priority() {
		return 60;
	}

	public function __construct( $fields ) {
		$this->fields = $fields;
		parent::__construct();
	}

	public function get_name() {
		return 'metabox-field';
	}

	public function get_label() {
		return 'Meta Box Field';
	}

	public function get_all_label() {
		return __( 'No Conditions', 'mb-elementor-integrator' );
	}

	public function check( $args ) {
		return false;
	}

	public function register_sub_conditions() {
		foreach ( $this->fields as $key => $field ) {
			$condition = new MBField([
				'key'   => $key,
				'field' => $field,
			]);
			$this->register_sub_condition( $condition );
		}
	}

}
