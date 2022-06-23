<?php

namespace MBEI\ThemeBuilder\Conditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class MBField extends Condition_Base {

	private $field;
	private $key;

	public static function get_type() {
		return 'mb_field_condition';
	}

	public static function get_priority() {
		return 60;
	}

	public function __construct( $field ) {
		$this->field = $field['field'];
		$this->key   = $field['key'];
		parent::__construct();
	}

	public function get_name() {
		return $this->key . '_' . $this->field['id'];
	}

	public function get_label() {
		return isset( $this->field['group_title'] ) ? $this->field['group_title'] : $this->field['name'];
	}

	public function check( $args ) {
		return false;
	}
}
