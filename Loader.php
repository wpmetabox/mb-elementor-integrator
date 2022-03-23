<?php
use MBEI\Classes\Dependencies;

class MBEI_Loader {

	public function __construct() {
		// Check plugin elementor is loaded.
		if ( Dependencies::elementor() ) {
			add_action( 'elementor/dynamic_tags/register_tags', [ $this, 'register_tags' ] );
		}

		// Check plugin elementor and elementor pro is loaded.
		if ( Dependencies::elementor( true ) ) {
			add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_skins' ] );
			$this->init();
		}
	}

	public function init() {
		$this->register_locations();
		$this->register_widgets();
		$this->modules();
	}

	public function modules() {
		new \MBEI\Classes\PaginationAjax();
	}

	private function is_valid() {
		if ( ! defined( 'RWMB_VER' ) ) {
			return false;
		}
		return true;
	}

	public function register_locations() {
		new \MBEI\Widgets\Locations\MLLoop();
		new \MBEI\Widgets\Locations\MLGrid();
	}

	public function register_widgets() {
		add_action('elementor/widgets/register', function( $widgets_manager ) {
			$widgets_manager->register( new MBEI\Widgets\LoopItem() );
		});
	}

	/**
	 * Register dynamic tags for Elementor.
	 * @param object $dynamic_tags Elementor dynamic tags instance.
	 */
	public function register_tags( $dynamic_tags ) {
		if ( ! $this->is_valid() ) {
			return;
		}

		$dynamic_tags->register_tag( 'MBEI\Tags\Post\Text' );
		$dynamic_tags->register_tag( 'MBEI\Tags\Post\Image' );
		$dynamic_tags->register_tag( 'MBEI\Tags\Post\Video' );

		if ( function_exists( 'mb_term_meta_load' ) ) {
			$dynamic_tags->register_tag( 'MBEI\Tags\Archive\Text' );
			$dynamic_tags->register_tag( 'MBEI\Tags\Archive\Image' );
			$dynamic_tags->register_tag( 'MBEI\Tags\Archive\Video' );
		}

		if ( function_exists( 'mb_settings_page_load' ) ) {
			$dynamic_tags->register_tag( 'MBEI\Tags\Settings\Text' );
			$dynamic_tags->register_tag( 'MBEI\Tags\Settings\Image' );
			$dynamic_tags->register_tag( 'MBEI\Tags\Settings\Video' );
		}
	}

	public function register_skins() {
		if ( ! $this->is_valid() ) {
			return;
		}
		// Add a custom skin for the POSTS widget
		add_action('elementor/widget/posts/skins_init', function( $widget ) {
			$widget->add_skin( new MBEI\Widgets\Skins\Post( $widget ) );
		});

		// Add a custom skin for the POST Archive widget
		add_action('elementor/widget/archive-posts/skins_init', function( $widget ) {
			$widget->add_skin( new MBEI\Widgets\Skins\Archive( $widget ) );
		});
	}

}
