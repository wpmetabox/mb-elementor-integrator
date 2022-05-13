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

		$options = ( new GroupField() )->get_list_field_group();
		$this->add_control( 'field-group', [
			'label'       => esc_html__( 'Fields Group', 'mb-elementor-integrator' ),
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'options'     => $options,
			'default'     => key( $options ),
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
		if ( ! isset( $settings['field-group'] ) || empty( $settings['field-group'] ) ) {
			return;
		}

		$data_groups = rwmb_get_value( $settings['field-group'], [], $post->ID );
		if ( 0 === count( $data_groups ) ) {
			return;
		}

		if ( false === is_int( key( $data_groups ) ) ) {
			$data_groups = [ $data_groups ];
		}

		$fields      = $group_fields->get_field_group( $settings['field-group'] );
		$data_column = array_combine( array_column( $fields['fields'], 'id' ), $fields['fields'] );

		if ( count( $data_groups ) > 0 ) {
			?>
			<div class="mbei-groups">
				<?php foreach ( $data_groups as $data_group ) : ?>
					<div class="mbei-group">
						<?php foreach ( $data_group as $key => $value ) : ?>
							<div class="mbei-subfield mbei-subfield--<?= $key; ?>">
								<?php $group_fields->display_field( $value, $data_column[ $key ] ); ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>                    
			</div>
			<?php
		}
	}

	protected function content_template() {
		parent::content_template();
	}

}
