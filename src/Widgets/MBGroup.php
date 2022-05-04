<?php

namespace MBEI\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use MBEI\GroupField;
use Elementor\Repeater;

class MBGroup extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'style-mb-group', plugin_dir_url( __DIR__ ) . 'assets/css/mb-group.css', [], RWMB_VER );
	}

	public function get_style_depends() {
		return [ 'style-mb-group' ];
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
		return __( 'Metbox Group', 'mb-elementor-integrator' );
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

		$this->start_controls_section('section_metabox', [
			'label' => esc_html__( 'Meta Box Group', 'mb-elementor-integrator' ),
		]);

		$options = GroupField::get_list_field_group();
		$this->add_control('field-group', [
			'label'       => esc_html__( 'Fields Group', 'mb-elementor-integrator' ),
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'options'     => $options,
			'default'     => key( $options ),
		]);

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
		$post = GroupField::get_current_post();

		$data_groups = [];

		$settings = $this->get_settings_for_display();
		if ( isset( $settings['field-group'] ) && ! empty( $settings['field-group'] ) ) {
			$data_groups = rwmb_get_value( $settings['field-group'], [], $post->ID );
		}

		$data_column = [];
		if ( ! empty( $settings['map-field-group'] ) ) {
			$fields = GroupField::get_field_group( $settings['field-group'] );

			foreach ( $settings['map-field-group'] as $column ) {
				$subfield = explode( ':', $column['subfield'] )[1];
				$field    = array_search( $subfield, array_column( $fields['fields'], 'id' ) );

				$data_column[] = [
					'type'      => $fields['fields'][ $field ]['type'],
					'field'     => $subfield,
					'text_link' => $column['display_text_for_link'],
				];
			}
		}

		 print_r( $data_groups );
		?>
<!--        <div class="mbei-loop-group">
			<?php if ( count( $data_groups ) > 0 ) : ?>
				<div class="mbei-fields">
					<?php foreach ( $data_groups as $data_group ) : ?>
						<div class="field-item mb-column">
							<?php foreach ( $data_column as $col ) : ?>
								<div class="mb-subfield-<?= $col['field']; ?>">
									<?php GroupField::display_field( $data_group[ $col['field'] ], $col ); ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>                    
				</div>
			<?php endif ?>
		</div>-->
		<?php
	}

	protected function content_template() {
		parent::content_template();
	}

}
