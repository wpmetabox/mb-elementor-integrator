<?php

namespace MBEI\Widgets;

use Elementor\Widget_Base;
use ElementorPro\Modules\Posts\Skins\Skin_Base;
use Elementor\Controls_Manager;
use MBEI\Traits\Skin;
use MBEI\GroupField;
use Elementor\Plugin;

class GroupSkin extends Skin_Base {

	use Skin;

	private $skin_id;

	public function __construct( Widget_Base $parent ) {
		parent::__construct( $parent );
		$this->skin_id = dechex( rand( 1, 99999999 ) );
	}

	public function get_title() {
		return __( 'Sub-group', 'mb-elementor-integrator' );
	}

	public function get_id() {
		return 'meta_box_skin_group';
	}

	private function get_skin_id() {
		return $this->skin_id;
	}

	private function render_header() {
		return '<div class="mbei-sub-groups" data-id="' . $this->get_skin_id() . '">';
	}

	private function render_footer() {
		return '</div>';
	}

	protected function render_loop_header() {
		return '<div class="mbei-sub-group">';
	}

	protected function render_loop_footer() {
		return '</div>';
	}

	private function style_inline( $size, $spacing ) {
		echo '<style type="text/css">';
		echo '.mbei-sub-groups[data-id="' . $this->get_skin_id() . '"]{display: grid; grid-template-columns: repeat(' . $size . ', 1fr);}';
		echo '.mbei-sub-groups[data-id="' . $this->get_skin_id() . '"] .mbei-sub-group{gap: ' . $spacing . 'px;}';
		echo '</style>';
	}

	public function render() {
		$group_fields = new GroupField();
		$post         = $group_fields->get_current_post();

		$data_groups = [];
		$data_column = [];

		$settings = $this->parent->get_settings_for_display();
		if ( ! isset( $settings['field-group'] ) || empty( $settings['field-group'] ) ) {
			return;
		}

		// check group nested
		$field_group = (array) $settings['field-group'];
		if ( strpos( $settings['field-group'], '.' ) !== false ) {
			$field_group = explode( '.', $settings['field-group'] );
		}

		$data_groups = rwmb_meta( $field_group[0], [], $post->ID );
		array_shift( $field_group );
		if ( ! empty( $field_group ) ) {
			$data_groups = $group_fields->get_value_nested_group( $data_groups, $field_group );
		}

		if ( empty( $data_groups ) ) {
			return;
		}

		if ( false !== is_int( key( $data_groups ) ) ) {
			$data_groups = [ array_shift( $data_groups ) ];
		}

		$fields      = $group_fields->get_field_group( $settings['field-group'] );
		$data_column = array_combine( array_column( $fields['fields'], 'id' ), $fields['fields'] );

		$mb_column  = ! empty( $this->parent->get_settings_for_display( 'mb_column' ) ) ? $this->parent->get_settings_for_display( 'mb_column' ) : 3;
		$mb_spacing = ! empty( $this->parent->get_settings_for_display( 'mb_spacing' ) ) ? $this->parent->get_settings_for_display( 'mb_spacing' ) : 20;

		echo $this->style_inline( $mb_column, $mb_spacing );
		echo $this->render_header();
		if ( $this->parent->get_settings_for_display( 'mb_skin_template' ) ) {
			$group_fields->display_data_template( $this->parent->get_settings_for_display( 'mb_skin_template' ), $data_groups, $data_column, [
				'loop_header' => $this->render_loop_header(),
				'loop_footer' => $this->render_loop_footer(),
			] );
		} else {
			?>
			<?php foreach ( $data_groups as $data_group ) : ?>
				<div class="mbei-group">
					<?php foreach ( $data_group as $key => $value ) : ?>
						<div class="mbei-subfield mbei-subfield--<?= $key; ?>">
							<?php
							if ( is_array( $value ) && ! empty( $value ) ) {
								$data_column[ $key ]['fields'] = array_combine( array_column( $data_column[ $key ]['fields'], 'id' ), $data_column[ $key ]['fields'] );
								$group_fields->render_nested_group( $value, $data_column[ $key ]['fields'] );
								continue;
							}
								$group_fields->display_field( $value, $data_column[ $key ] );
							?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>                    
			<?php
		}
		echo $this->render_footer();
	}

}
