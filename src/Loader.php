<?php
namespace MBEI;

use Elementor\Core\DynamicTags\Manager;

class Loader {

	public function __construct() {
		add_action( 'elementor/documents/register', [ $this, 'register_document_type' ] );
		add_action( 'elementor/theme/register_locations', [ $this, 'register_theme_locations' ] );
		add_action( 'init', [ $this, 'init' ], 20 );
	}

	private function is_valid() {
		return defined( 'RWMB_VER' ) && defined( 'ELEMENTOR_PRO_VERSION' );
	}

	public function register_document_type( $documents_manager ) {
		if ( ! $this->is_valid() ) {
			return;
		}

		$documents_manager->register_document_type(
		'metabox_group_template',
		\MBEI\ThemeBuilder\Documents\Group::get_class_full_name()
		);

		\Elementor\TemplateLibrary\Source_Local::add_template_type( 'metabox_group_template' );
	}

	public function register_theme_locations( $location_manager ) {
		if ( ! $this->is_valid() ) {
			return;
		}

		$location_manager->register_location(
			'metabox_group_template',
			[
				'label'           => __( 'Meta Box Group Skin', 'mb-elementor-integrator' ),
				'multiple'        => true,
				'edit_in_content' => true,
			]
		);
	}

	public function init() {
		if ( ! $this->is_valid() ) {
			return;
		}

		add_action( 'elementor/dynamic_tags/register', [ $this, 'register_tags' ] );

		add_action( 'elementor/widgets/register', [ $this, 'register_skins' ] );
		add_action( 'elementor/theme/register_conditions', [ $this, 'register_conditions' ], 100 );

		$this->register_widgets();
		$this->modules();

		CurrentWidget::track();
	}

	public function modules() {
		new GroupField();
	}

	public function register_conditions( $conditions_manager ) {
		$conditions_manager->get_condition( 'general' )->register_sub_condition( new ThemeBuilder\Conditions\Group() );
	}

	public function register_widgets() {
		add_action('elementor/widgets/register', function ( $widgets_manager ) {
			$widgets_manager->register( new Widgets\MBGroup() );
		});
	}

	/**
	 * Register dynamic tags for Elementor.
	 * @param object $dynamic_tags Elementor dynamic tags instance.
	 */
	public function register_tags( Manager $dynamic_tags ) {
		$dynamic_tags->register( new Tags\Post\Text() );
		$dynamic_tags->register( new Tags\Post\Image() );
		$dynamic_tags->register( new Tags\Post\Video() );

		if ( function_exists( 'mb_term_meta_load' ) ) {
			$dynamic_tags->register( new Tags\Archive\Text() );
			$dynamic_tags->register( new Tags\Archive\Image() );
			$dynamic_tags->register( new Tags\Archive\Video() );
		}

		if ( function_exists( 'mb_settings_page_load' ) ) {
			$dynamic_tags->register( new Tags\Settings\Text() );
			$dynamic_tags->register( new Tags\Settings\Image() );
			$dynamic_tags->register( new Tags\Settings\Video() );
		}
	}

	public function register_skins() {
		// Add a custom skin for the POSTS widget
		add_action('elementor/widget/metabox-group/skins_init', function ( $widget ) {
			$widget->add_skin( new Widgets\GroupSkin( $widget ) );
		});
	}
}
