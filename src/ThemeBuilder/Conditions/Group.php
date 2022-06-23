<?php

namespace MBEI\ThemeBuilder\Conditions;

use ElementorPro\Modules\ThemeBuilder\Module;
use ElementorPro\Core\Utils;
use ElementorPro\Modules\ThemeBuilder\Conditions\Post;
use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use MBEI\GroupField;
use MBEI\ThemeBuilder\Conditions\MBFields;

class Group extends Condition_Base {

	protected $sub_conditions = [
		'front_page',
	];

	public static function get_type() {
		return 'mb_group_condition';
	}

	public function get_name() {
		return 'mb_group_condition';
	}

	public static function get_priority() {
		return 60;
	}

	public function get_label() {
		return __( 'Meta Box Group Skin', 'mb-elementor-integrator' );
	}

	public function get_all_label() {
		return __( 'No Conditions', 'mb-elementor-integrator' );
	}

	public function register_sub_conditions() {
		$post_types = Utils::get_public_post_types();

		$post_types['attachment'] = get_post_type_object( 'attachment' )->label;

		foreach ( $post_types as $post_type => $label ) {
			$condition = new Post([
				'post_type' => $post_type,
			]);

			$this->register_sub_condition( $condition );
		}

		$fields = ( new GroupField() )->get_field_group();
		if ( 0 < count( $fields ) ) {
			$condition = new MBFields( $fields );
			$this->register_sub_condition( $condition );
		}

		$this->sub_conditions[] = 'child_of';

		$this->sub_conditions[] = 'any_child_of';

		$this->sub_conditions[] = 'by_author';

		// Last condition.
		$this->sub_conditions[] = 'not_found404';
	}

	public function check( $args ) {
		return false;
	}

}
