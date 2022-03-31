<?php

namespace MBEI\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use MBEI\Classes\Document;

class LoopItem extends Widget_Base {

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
		return 'mb-loop-item';
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
		return __( 'Loop Item', 'mb-elementor-integrator' );
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
		return 'eicon-image-hotspot';
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
		return [ 'mb-grid' ];
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

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'mb-elementor-integrator' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'important_note',
			[
				'label'           => __( 'Loop Item Place Holder', 'mb-elementor-integrator' ),
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Place this widget where the Loop Item you want to show in Meta Box - Elementor Integrator.', 'mb-elementor-integrator' ),
				'content_classes' => 'your-class',
				'selector'        => '{{wrapper}} .ecs-loop-preview',
			]
		);

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

		if ( $this->show_nice() ) {
			$this->content_template();
		} else {
			echo '{{mb-article}}';
		}
	}

	protected function show_nice() {
		$is_preview = false;
		$document   = Document::get_document( get_the_ID() );
		if ( $document ) {
			if ( 'mb-grid' === $document->get_location() ) {
				if ( isset( $_GET['action'] ) ) {
					$is_preview = ( 'elementor' === $_GET['action'] ) ? true : false;
				}
			}
		}
		return $is_preview;
	}

	protected function content_template() {
		?>
		<div class="mbei-loop-preview">
			<div class="mbei-image-holder">=^_^=</div>
			<h3>
				Lorem Ipsum
			</h3>
			<span>Nunc vos habere ultimum potentiae, ad excogitandum vestri malesuada euismod. Liber esto atque exprimere te.</span>
		</div>
		<?php
	}

}
