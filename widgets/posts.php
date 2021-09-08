<?php


use Elementor\Controls_Manager;
use ElementorPro\Modules\QueryControl\Module as Module_Query;
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Related;
use ElementorPro\Modules\Posts\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Posts
 */
class MBPosts extends \ElementorPro\Modules\Posts\Widgets\Posts_Base {

	public function get_name() {
		return 'mb-posts';
	}

	public function get_title() {
		return __( 'MB Posts', 'elementor-pro' );
	}

	public function get_keywords() {
		return [ 'posts', 'cpt', 'item', 'loop', 'query', 'cards', 'custom post type' ];
	}

	public function on_import( $element ) {
		if ( ! get_post_type_object( $element['settings']['posts_post_type'] ) ) {
			$element['settings']['posts_post_type'] = 'post';
		}

		return $element;
	}

	protected function register_skins() {
		// $this->add_skin( new \ElementorPro\Modules\Posts\Skins\Skin_Classic( $this ) );
		// $this->add_skin( new \ElementorPro\Modules\Posts\Skins\Skin_Cards( $this ) );
		// $this->add_skin( new \ElementorPro\Modules\Posts\Skins\Skin_Full_Content( $this ) );
	}

	protected function register_controls() {
		parent::register_controls();

		$this->register_query_section_controls();
		$this->register_pagination_section_controls();
	}

	public function query_posts() {

		$query_args = [
			'posts_per_page' => $this->get_current_skin()->get_instance_value( 'posts_per_page' ),
			'paged' => $this->get_current_page(),
		];

		/** @var Module_Query $elementor_query */
		$elementor_query = Module_Query::instance();
		$this->query = $elementor_query->get_query( $this, 'posts', $query_args, [] );
	}

	protected function register_query_section_controls() {
		$this->start_controls_section(
			'mb_section_query',
			[
				'label' => __( 'Query', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_group_control(
			Group_Control_Related::get_type(),
			[
				'name' => $this->get_name(),
				'presets' => [ 'full' ],
				'exclude' => [
					'posts_per_page', //use the one from Layout section
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Fix WP 5.5 pagination issue.
	 *
	 * Return true to mark that it's handled and avoid WP to set it as 404.
	 *
	 * @see https://github.com/elementor/elementor/issues/12126
	 * @see https://core.trac.wordpress.org/ticket/50976
	 *
	 * Based on the logic at \WP::handle_404.
	 *
	 * @param $handled - Default false.
	 * @param $wp_query
	 *
	 * @return bool
	 */
	public function allow_posts_widget_pagination( $handled, $wp_query ) {
		// Check it's not already handled and it's a single paged query.
		if ( $handled || empty( $wp_query->query_vars['page'] ) || ! is_singular() || empty( $wp_query->post ) ) {
			return $handled;
		}

		$document = Plugin::elementor()->documents->get( $wp_query->post->ID );

		return $this->is_valid_pagination( $document->get_elements_data(), $wp_query->query_vars['page'] );
	}

	/**
	 * Checks a set of elements if there is a posts/archive widget that may be paginated to a specific page number.
	 *
	 * @param array $elements
	 * @param       $current_page
	 *
	 * @return bool
	 */
	public function is_valid_pagination( array $elements, $current_page ) {
		$is_valid = false;

		// Get all widgets that may add pagination.
		$widgets = Plugin::elementor()->widgets_manager->get_widget_types();
		$posts_widgets = [];
		foreach ( $widgets as $widget ) {
			if ( $widget instanceof Posts_Base ) {
				$posts_widgets[] = $widget->get_name();
			}
		}

		Plugin::elementor()->db->iterate_data( $elements, function( $element ) use ( &$is_valid, $posts_widgets, $current_page ) {
			if ( isset( $element['widgetType'] ) && in_array( $element['widgetType'], $posts_widgets, true ) ) {
				// Has pagination.
				if ( ! empty( $element['settings']['pagination_type'] ) ) {
					// No max pages limits.
					if ( empty( $element['settings']['pagination_page_limit'] ) ) {
						$is_valid = true;
					} elseif ( (int) $current_page <= (int) $element['settings']['pagination_page_limit'] ) {
						// Has page limit but current page is less than or equal to max page limit.
						$is_valid = true;
					}
				}
			}
		} );

		return $is_valid;
	}

	public function __construct() {
		parent::__construct();

		add_filter( 'pre_handle_404', [ $this, 'allow_posts_widget_pagination' ], 10, 2 );
	}
}
