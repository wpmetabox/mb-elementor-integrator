<?php
namespace MBEI;

class Loader {

	public function __construct() {

		// Check plugin elementor is loaded.
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			add_action( 'elementor/dynamic_tags/register_tags', [ $this, 'register_tags' ] );
		}

		// Check plugin elementor and elementor pro is loaded.
		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_skins' ] );
			add_action( 'elementor/theme/register_conditions', [ $this, 'register_conditions' ], 100 );
			$this->init();
		}
	}

	public function init() {
		$this->register_locations();
		$this->register_widgets();
		$this->modules();
	}

	public function modules() {
		new \MBEI\GroupField();
	}

	private function is_valid() {
		if ( ! defined( 'RWMB_VER' ) ) {
			return false;
		}
		return true;
	}

	public function register_conditions( $conditions_manager ) {
		$conditions_manager->get_condition( 'general' )->register_sub_condition( new \MBEI\ThemeBuilder\Conditions\Group() );
	}

	public function register_locations() {
		new \MBEI\Widgets\Locations\Group();
	}

	public function register_widgets() {
		add_action('elementor/widgets/register', function( $widgets_manager ) {
			$widgets_manager->register( new \MBEI\Widgets\MBGroup() );
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
		add_action('elementor/widget/metabox-group/skins_init', function( $widget ) {
			$widget->add_skin( new \MBEI\Widgets\GroupSkin( $widget ) );
		});
	}

}
