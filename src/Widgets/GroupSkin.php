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

	public function get_title() {
		return __( 'Meta Box Group Skin', 'mb-elementor-integrator' );
	}

	public function get_id() {
		return 'meta_box_skin_group';
	}

	protected function _register_controls_actions() {
		parent::_register_controls_actions();
		add_action( 'elementor/element/metabox-group/section_metabox/before_section_end', [ $this, 'register_controls' ] );
	}

	public function register_controls( Widget_Base $widget ) {

		$this->parent = $widget;
        
        $group_fields = new GroupField();
		$this->add_control( 'mb_skin_template', [
			'label'       => __( 'Select a Group Skin', 'mb-elementor-integrator' ),
			'description' => '<div style="text-align:center;"><a target="_blank" style="text-align: center;font-style: normal;" href="' . esc_url( admin_url( '/edit.php?post_type=elementor_library&tabs_group=theme&elementor_library_type=metabox_group_template' ) ) .
			'" class="elementor-button elementor-button-default elementor-repeater-add">' .
			__( 'Create/edit a Group Skin', 'mb-elementor-integrator' ) . '</a></div>',
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'default'     => [],
			'options'     => $group_fields->get_skin_template(),
		] );

		$this->register_divider_controls();
	}

	private function render_header() {
		return '<div class="mbei-groups">';
	}

	private function render_footer() {
		return '</div>';
	}

	protected function render_loop_header() {
		return '<div class="mbei-group">';
	}

	protected function render_loop_footer() {
		return '</div>';
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

        //check group nested
        $field_group = ( array )$settings['field-group'];
        if( strpos( $settings['field-group'], '.' ) !== false ) {
            $field_group = explode( '.', $settings['field-group'] );
        }
        
		$data_groups = rwmb_meta( $field_group[ 0 ], [], $post->ID );
        array_shift( $field_group );
        if ( !empty( $field_group ) ){
            $data_groups = $group_fields->get_value_nested_group( $data_groups, $field_group );
        }

		if ( 0 === count( $data_groups ) ) {
			return;
		}

		if ( false === is_int( key( $data_groups ) ) ) {
			$data_groups = [ $data_groups ];
		}

		$fields      = $group_fields->get_field_group( $settings['field-group'] );
		$data_column = array_combine( array_column( $fields['fields'], 'id' ), $fields['fields'] );

		echo $this->render_header();
		if ( $this->get_instance_value( 'mb_skin_template' ) ) {
            $group_fields->display_data_template( $this->get_instance_value( 'mb_skin_template' ), $data_groups, $data_column, [
                'loop_header' => $this->render_loop_header(),
                'loop_footer' => $this->render_loop_footer()
            ] );            
		} else {
			?>
			<?php foreach ( $data_groups as $data_group ) : ?>
				<div class="mbei-group">
					<?php foreach ( $data_group as $key => $value ) : ?>
						<div class="mbei-subfield mbei-subfield--<?= $key; ?>">
                            <?php
                                if( is_array( $value ) && !empty( $value ) ) {
                                    $data_column[ $key ]['fields'] = array_combine( array_column( $data_column[ $key ]['fields'], 'id' ), $data_column[ $key ]['fields'] );
                                    $this->parent->render_nested_group( $value, $data_column[ $key ]['fields'], $group_fields );
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
