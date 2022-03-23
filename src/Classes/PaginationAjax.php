<?php

namespace MBEI\Classes;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use WP_Query;
use MBEI\Classes\Animation;
use MBEI\Classes\Document;

class PaginationAjax {

	private $post_id      = '';
	private $current_page = 1;
	private $widget_id    = '';
	private $theme_id     = '';
	private $query        = [];

	public function __construct( $args = [] ) {
		$this->init();

		if ( ! isset( $args['post_id'] ) ) { // debug line comment it
			if ( ! isset( $_POST['mbei_ajax_settings'] ) ) {
				return;
			} else {
				$args = json_decode( wp_unslash( $_POST['mbei_ajax_settings'] ), true );
			}
		}

		$this->post_id      = $args['post_id'];
		$this->current_page = $args['current_page'] + 1;
		$this->widget_id    = $args['widget_id'];
		$this->theme_id     = isset( $args['theme_id'] ) ? $args['theme_id'] : $args['post_id'];
		$this->query        = isset( $_POST['query'] ) ? json_decode( wp_unslash( $_POST['query'] ), true ) : [];
		if ( isset( $args['max_num_pages'] ) && $this->current_page > $args['max_num_pages'] ) {
			return;
		}
		$this->init_ajax();
	}

	public function init() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 99 );
		add_action( 'elementor/element/before_section_end', [ $this, 'post_pagination' ], 10, 3 );
		add_action( 'elementor/element/after_section_end', [ $this, 'button_pagination_style' ], 10, 3 );
	}

	public function init_ajax() {
		add_action( 'wp_ajax_mbeiload', [ $this, 'get_document_data' ] );
		add_action( 'wp_ajax_nopriv_mbeiload', [ $this, 'get_document_data' ] );
	}

	public function post_pagination( $element, $section_id = '', $args = '' ) {

		if ( ( 'archive-posts' === $element->get_name() || 'posts' === $element->get_name() ) && 'section_pagination' === $section_id ) {

			$element->remove_control( 'pagination_type' );

			$element->add_control(
				'pagination_type',
				[
					'label'   => __( 'Pagination', 'mb-elementor-integrator' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						''                      => __( 'None', 'mb-elementor-integrator' ),
						'numbers'               => __( 'Numbers', 'mb-elementor-integrator' ),
						'loadmore'              => __( 'Load More (Meta Box Skin)', 'mb-elementor-integrator' ),
						// 'lazyload' => __('Infinite Load (Meta Box Skin Pro)', 'mb-elementor-integrator'),
																'prev_next' => __( 'Previous/Next', 'mb-elementor-integrator' ),
						'numbers_and_prev_next' => __( 'Numbers', 'mb-elementor-integrator' ) . ' + ' . __( 'Previous/Next', 'mb-elementor-integrator' ),
					],
				]
			);
			/* lazyload stuff */
			$element->add_control(
				'lazyload_title',
				[
					'label'     => __( 'Infinite Load', 'mb-elementor-integrator' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'pagination_type' => 'lazyload',
					],
				]
			);

			$element->add_control(
				'lazyload_animation',
				[
					'label'     => __( 'Loading Animation', 'mb-elementor-integrator' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'default',
					'options'   => Animation::get_lazy_load_animations_list(),
					'condition' => [
						'pagination_type' => 'lazyload',
					],
				]
			);
			$element->add_control(
				'lazyload_color',
				[
					'label'     => __( 'Animation Color', 'mb-elementor-integrator' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .mbei-lazyload .mbei-ll-brcolor' => 'border-color: {{VALUE}};',
						'{{WRAPPER}} .mbei-lazyload .mbei-ll-bgcolor' => 'background-color: {{VALUE}} !important;',
					],
					'condition' => [
						'pagination_type' => 'lazyload',
					],
				]
			);

			$element->add_control(
				'lazyload_spacing',
				[
					'label'     => __( 'Animation Spacing', 'mb-elementor-integrator' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max' => 250,
						],
					],
					'default'   => [
						'unit' => 'px',
						'size' => '20',
					],
					'selectors' => [
						'{{WRAPPER}} .mbei-lazyload' => 'margin-top: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'pagination_type' => 'lazyload',
					],
				]
			);
			$element->add_control(
				'lazyload_size',
				[
					'label'     => __( 'Animation Size', 'mb-elementor-integrator' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .mbei-lazyload .mbei-lazy-load-animation' => 'font-size: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'pagination_type' => 'lazyload',
					],
				]
			);

			/* load more button stuff */

			$element->add_control(
				'loadmore_title',
				[
					'label'     => __( 'Load More Button', 'mb-elementor-integrator' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'pagination_type' => 'loadmore',
					],
				]
			);

			$element->add_control(
				'loadmore_text',
				[
					'label'       => __( 'Text', 'mb-elementor-integrator' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => __( 'Load More', 'elementor' ),
					'placeholder' => __( 'Load More', 'elementor' ),
					'condition'   => [
						'pagination_type' => 'loadmore',
					],
				]
			);

			$element->add_control(
				'loadmore_loading_text',
				[
					'label'       => __( 'Loading Text', 'mb-elementor-integrator' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => __( 'Loading...', 'elementor' ),
					'placeholder' => __( 'Loading...', 'elementor' ),
					'condition'   => [
						'pagination_type' => 'loadmore',
					],
				]
			);

			$element->add_control(
				'loadmore_spacing',
				[
					'label'     => __( 'Button Spacing', 'mb-elementor-integrator' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max' => 250,
						],
					],
					'default'   => [
						'unit' => 'px',
						'size' => '20',
					],
					'selectors' => [
						'{{WRAPPER}} .mbei-load-more-button .elementor-button' => 'margin-top: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'pagination_type' => 'loadmore',
					],
				]
			);
			$element->add_control(
				'change_url',
				[
					'label'        => __( 'Change URL on ajax load?', 'mb-elementor-integrator' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_off'    => __( 'No', 'mb-elementor-integrator' ),
					'label_on'     => __( 'Yes', 'mb-elementor-integrator' ),
					'return_value' => true,
					'separator'    => 'before',
					'default'      => false,
				]
			);

			$element->add_control(
				'reinit_js',
				[
					'label'        => __( 'Reinitialize Elementor JS on Ajax Pagination?', 'mb-elementor-integrator' ),
					'description'  => __( 'This is used for the elements loaded through AJAX Pagination. This is experimental feature and it may not work properly.', 'mb-elementor-integrator' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_off'    => __( 'No', 'mb-elementor-integrator' ),
					'label_on'     => __( 'Yes', 'mb-elementor-integrator' ),
					'return_value' => true,
					'separator'    => 'before',
					'default'      => false,
				]
			);
		}
	}

	public function button_pagination_style( $element, $section_id = '', $args = '' ) {

		if ( ( 'archive-posts' === $element->get_name() || 'posts' === $element->get_name() ) && 'section_pagination_style' === $section_id ) {

			$element->start_controls_section(
				'loadmore_section_style',
				[
					'label'     => __( 'Load More Button', 'mb-elementor-integrator' ),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'pagination_type' => 'loadmore',
					],
				]
			);

			$element->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'loadmore_typography',
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .mbei-load-more-button .elementor-button',
				]
			);

			$element->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name'     => 'loadmore_text_shadow',
					'selector' => '{{WRAPPER}} .mbei-load-more-button .elementor-button',
				]
			);

			$element->start_controls_tabs( 'mb_load_more_tabs_button_style' );

			$element->start_controls_tab(
				'loadmore_tab_button_normal',
				[
					'label' => __( 'Normal', 'mb-elementor-integrator' ),
				]
			);

			$element->add_control(
				'loadmore_button_text_color',
				[
					'label'     => __( 'Text Color', 'mb-elementor-integrator' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .mbei-load-more-button .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
					],
				]
			);

			$element->add_control(
				'loadmore_background_color',
				[
					'label'     => __( 'Background Color', 'mb-elementor-integrator' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => [
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [
						'{{WRAPPER}} .mbei-load-more-button .elementor-button' => 'background-color: {{VALUE}};',
					],
				]
			);

			$element->end_controls_tab();

			$element->start_controls_tab(
				'loadmore_tab_button_hover',
				[
					'label' => __( 'Hover', 'mb-elementor-integrator' ),
				]
			);

			$element->add_control(
				'loadmore_hover_color',
				[
					'label'     => __( 'Text Color', 'mb-elementor-integrator' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .mbei-load-more-button .elementor-button:hover, {{WRAPPER}} .mbei-load-more-button .elementor-button:focus' => 'color: {{VALUE}};',
						'{{WRAPPER}} .mbei-load-more-button .elementor-button:hover svg, {{WRAPPER}} .mbei-load-more-button .elementor-button:focus svg' => 'fill: {{VALUE}};',
					],
				]
			);

			$element->add_control(
				'loadmore_button_background_hover_color',
				[
					'label'     => __( 'Background Color', 'mb-elementor-integrator' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .mbei-load-more-button .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
					],
				]
			);

			$element->add_control(
				'loadmore_button_hover_border_color',
				[
					'label'     => __( 'Border Color', 'mb-elementor-integrator' ),
					'type'      => Controls_Manager::COLOR,
					'condition' => [
						'border_border!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .mbei-load-more-button .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
					],
				]
			);

			$element->add_control(
				'loadmore_hover_animation',
				[
					'label' => __( 'Hover Animation', 'mb-elementor-integrator' ),
					'type'  => Controls_Manager::HOVER_ANIMATION,
				]
			);

			$element->end_controls_tab();

			$element->end_controls_tabs();

			$element->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => 'loadmore_border',
					'selector'  => '{{WRAPPER}} .elementor-button',
					'separator' => 'before',
				]
			);

			$element->add_control(
				'loadmore_border_radius',
				[
					'label'      => __( 'Border Radius', 'mb-elementor-integrator' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .mbei-load-more-button .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$element->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'loadmore_button_box_shadow',
					'selector' => '{{WRAPPER}} .mbei-load-more-button .elementor-button',
				]
			);

			$element->add_responsive_control(
				'loadmore_text_padding',
				[
					'label'      => __( 'Padding', 'mb-elementor-integrator' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .mbei-load-more-button .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator'  => 'before',
				]
			);
			$element->end_controls_section();
		}
	}

	public function get_document_data() {
		global $wp_query;

		$id = $this->widget_id;

		$post_id  = $this->post_id;
		$theme_id = $this->theme_id;

		$this->query['paged']       = $this->current_page; // we need current(next) page to be loaded
		$this->query['post_status'] = 'publish';

		$query    = new WP_Query( $this->query );
		$wp_query = $query;

		wp_reset_postdata(); // this fixes some issues with some get_the_ID users.
		if ( is_archive() ) {
			$post_id = $theme_id;
		}

		echo Document::get_document_data([
			'widget_id' => $id,
			'post_id'   => $post_id,
			'theme_id'  => $theme_id,
		]);

		die;
	}

	public function enqueue_scripts() {

		global $wp_query;

		wp_register_script( 'mb_pagination_ajax', plugin_dir_url( __DIR__ ) . 'assets/js/mb_pagination_ajax.js', [ 'jquery' ], RWMB_VER, true );

		wp_localize_script('mb_pagination_ajax', 'mbei_ajax_params', [
			'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
			'posts'   => wp_json_encode( $wp_query->query_vars ),
		]);

		wp_enqueue_script( 'mb_pagination_ajax' );
	}

}
