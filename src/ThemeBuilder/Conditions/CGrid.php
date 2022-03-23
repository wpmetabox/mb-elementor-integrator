<?php

namespace MBEI\ThemeBuilder\Conditions;

use ElementorPro\Modules\ThemeBuilder\Module;
use ElementorPro\Core\Utils;
use ElementorPro\Modules\ThemeBuilder\Conditions\Post;
use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class CGrid extends Condition_Base {

	protected $sub_conditions = [];

	public static function get_type() {
		return 'mb_grid';
	}

	public function get_name() {
		return 'mb_grid';
	}

	public static function get_priority() {
		return 60;
	}

	public function get_label() {
		return __( 'MB Grid', 'mb-elementor-integrator' );
	}

	public function get_all_label() {
		return __( 'No Conditions', 'mb-elementor-integrator' );
	}

	public function register_sub_conditions() {
		// Last condition.
		$this->sub_conditions[] = 'not_found404';
	}

	public function check( $args ) {
		return false;
	}

}

add_action( 'elementor/theme/register_conditions', function( $conditions_manager ) {
	$conditions_manager->get_condition( 'general' )->register_sub_condition( new CGrid() );
}, 100);
