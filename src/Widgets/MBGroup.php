<?php

namespace MBEI\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use MBEI\GroupField;

class MBGroup extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
	}

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
		return 'metabox-group';
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
		return __( 'Meta Box Group', 'mb-elementor-integrator' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Term List widget icon.
	 *
	 * @since 0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-group';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Term List widget belongs to.
	 *
	 * @since 0.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'basic' ];
	}

	/**
	 * Register Term List widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 0.1
	 * @access protected
	 */
	public function register_controls() {

		$this->start_controls_section( 'section_metabox', [
			'label' => esc_html__( 'Meta Box Group', 'mb-elementor-integrator' ),
		] );

		$this->update_control( '_skin', [
			'label' => esc_html__( 'Type', 'mb-elementor-integrator' ),
		] );

		$this->add_control( 'object-type', [
			'label'   => esc_html__( 'Object Type', 'mb-elementor-integrator' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'post'    => esc_html__( 'Post', 'mb-elementor-integrator' ),
				'setting' => esc_html__( 'Settings page', 'mb-elementor-integrator' ),
			],
			'default' => 'post',
		] );

		$group_fields = new GroupField();
		$options      = $group_fields->get_list_field_group( 'post' );
		$this->add_control( 'field-group', [
			'label'       => esc_html__( 'Group', 'mb-elementor-integrator' ),
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'options'     => $options,
			'default'     => key( $options ),
			'condition'   => [
				'object-type' => 'post',
			],
		] );

		$options = $group_fields->get_list_field_group( 'setting' );
		$this->add_control( 'field-group-setting', [
			'label'       => esc_html__( 'Group', 'mb-elementor-integrator' ),
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'options'     => $options,
			'default'     => key( $options ),
			'condition'   => [
				'object-type' => 'setting',
			],
		] );

		$this->add_control( 'mb_skin_template', [
			'label'       => __( 'Skin', 'mb-elementor-integrator' ),
			'description' => '<div style="text-align:center;"><a target="_blank" style="text-align: center;font-style: normal;" href="' . esc_url( admin_url( '/edit.php?post_type=elementor_library&tabs_group=theme&elementor_library_type=metabox_group_template' ) ) .
			'" class="elementor-button elementor-button-default elementor-repeater-add">' .
			__( 'Create/edit a Group Skin', 'mb-elementor-integrator' ) . '</a></div>',
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'default'     => [],
			'options'     => $group_fields->get_skin_template(),
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_display', [
			'label' => esc_html__( 'Display', 'mb-elementor-integrator' ),
		] );

		$this->add_responsive_control( 'mb_column', [
			'label'           => __( 'Columns', 'mb-elementor-integrator' ),
			'type'            => Controls_Manager::NUMBER,
			'devices'         => [ 'desktop', 'tablet', 'mobile' ],
			'desktop_default' => 3,
			'tablet_default'  => 2,
			'mobile_default'  => 1,
			'selectors'       => [
				'{{WRAPPER}} .mbei-groups' => 'display: grid; grid-template-columns: repeat({{SIZE}}, 1fr);',
			],
		] );

		$this->add_responsive_control( 'mb_spacing', [
			'label'           => __( 'Spacing Item (px)', 'mb-elementor-integrator' ),
			'type'            => Controls_Manager::NUMBER,
			'devices'         => [ 'desktop', 'tablet', 'mobile' ],
			'desktop_default' => 20,
			'tablet_default'  => 20,
			'mobile_default'  => 10,
			'selectors'       => [
				'{{WRAPPER}} .mbei-groups' => 'gap: {{SIZE}}px;',
			],
		] );

		$this->end_controls_section();
	}

	public function render_nested_group( $data_groups, $data_column, $group_fields ) {
		if ( false === is_int( key( $data_groups ) ) ) {
			$data_groups = [ $data_groups ];
		}

		foreach ( $data_groups as $data_group ) {
			echo '<div class="mbei-group mbei-group-nested">';
			foreach ( $data_group as $key => $value ) {
				if ( ! isset( $data_column[ $key ] ) ) {
					continue;
				}

				echo '<div class="mbei-subfield mbei-subfield--' . $key . '">';
				if ( is_array( $value ) && ! empty( $value ) ) {
					$data_column[ $key ]['fields'] = array_combine( array_column( $data_column[ $key ]['fields'], 'id' ), $data_column[ $key ]['fields'] );
					$this->render_nested_group( $value, $data_column[ $key ]['fields'], $group_fields );
					continue;
				}
                $group_fields->display_field( $value, $data_column[ $key ] );
				echo '</div>';
			}
			echo '</div>';
		}
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

	/**
	 * Render Term List widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.1
	 * @access protected
	 */
	protected function render() {
		$group_fields = new GroupField();
		$post         = $group_fields->get_current_post();

		$data_groups = [];
		$data_column = [];

		$settings = $this->get_settings_for_display();
		if ( empty( $settings['object-type'] ) ) {
			return;
		}

		if ( $settings['object-type'] === 'post' && ( ! isset( $settings['field-group'] ) || empty( $settings['field-group'] ) ) ) {
			return;
		}

		if ( $settings['object-type'] === 'setting' && ( ! isset( $settings['field-group-setting'] ) || empty( $settings['field-group-setting'] ) ) ) {
			return;
		}

		// check group nested
		$field_group = $settings['object-type'] === 'post' ? $settings['field-group'] : $settings['field-group-setting'];
		$field_group = false !== strpos( $field_group, '.' ) ? explode( '.', $field_group ) : (array) $field_group;

		if ( 'setting' === $settings['object-type'] ) {
			$object_setting = array_shift( $field_group );
		}

		$data_groups = 'post' === $settings['object-type'] ? rwmb_meta( $field_group[0], [], $post->ID ) : rwmb_meta( $field_group[0], [ 'object_type' => 'setting' ], $object_setting );

		array_shift( $field_group );
		if ( ! empty( $field_group ) ) {
			$data_groups = $group_fields->get_value_nested_group( $data_groups, $field_group );
		}

		if ( empty( $data_groups ) ) {
			return;
		}

		if ( false === is_int( key( $data_groups ) ) ) {
			$data_groups = [ $data_groups ];
		}

		$fields      = $group_fields->get_field_group( 'post' === $settings['object-type'] ? $settings['field-group'] : $settings['field-group-setting'], $settings['object-type'] );
		$data_column = array_combine( array_column( $fields['fields'], 'id' ), $fields['fields'] );

		echo $this->render_header();
		if ( $this->get_settings_for_display( 'mb_skin_template' ) ) {
			$group_fields->display_data_template( $this->get_settings_for_display( 'mb_skin_template' ), $data_groups, $data_column, [
				'loop_header' => $this->render_loop_header(),
				'loop_footer' => $this->render_loop_footer(),
			] );
		} else {
			$group_fields->display_data_widget( $data_groups, $data_column, [
				'loop_header' => $this->render_loop_header(),
				'loop_footer' => $this->render_loop_footer(),
			]);
		}
		echo $this->render_footer();
	}

	protected function content_template() {
		parent::content_template();
	}

}
