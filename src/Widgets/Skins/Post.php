<?php

namespace MBEI\Widgets\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;
use Elementor\Plugin;
use ElementorPro\Modules\ThemeBuilder\Module as ThemeBuilderModule;
use ElementorPro\Modules\Posts\Skins\Skin_Base;
use MBEI\Traits\Skin;
use MBEI\Traits\Skins\Post as PostSkin;
use WP_Query;
use MBEI\Classes\Animation;

class Post extends Skin_Base {

	use Skin, PostSkin;

	private $pid;
	public $settings;
	public $conditions;
	public $grid          = [];
	public $grid_settings = [
		'length'  => 0,
		'current' => 0,
		'allow'   => false,
	];

	public function get_id() {
		return 'meta_box_skin_post';
	}

	protected function _register_controls_actions() {
		parent::_register_controls_actions();
		add_action( 'elementor/element/posts/section_layout/before_section_end', [ $this, 'register_controls' ] );
	}

	public function register_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->register_template_controls();

		// this would make use of 100% if width.
		$this->add_control('mb_view', [
			'label'        => __( 'View', 'mb-elementor-integrator' ),
			'type'         => Controls_Manager::HIDDEN,
			'default'      => 'top',
			'prefix_class' => 'elementor-posts--thumbnail-',
		]);

		$this->register_grid_controls();

		$this->register_divider_controls();

		parent::register_controls( $widget );

		$this->remove_control( 'img_border_radius' );
		$this->remove_control( 'meta_data' );
		$this->remove_control( 'item_ratio' );
		$this->remove_control( 'image_width' );
		$this->remove_control( 'show_title' );
		$this->remove_control( 'title_tag' );
		$this->remove_control( 'masonry' );
		$this->remove_control( 'thumbnail' );
		$this->remove_control( 'thumbnail_size' );
		$this->remove_control( 'show_read_more' );
		$this->remove_control( 'read_more_text' );
		$this->remove_control( 'show_excerpt' );
		$this->remove_control( 'excerpt_length' );
		$this->remove_control( 'open_new_tab' );
	}

	private function get_post_id() {
		return $this->pid;
	}

	protected function set_custom_grid( $grid ) {
		// this is for terms we don't need passid so we can actually add them in cache
		if ( ! $grid ) {
			return;
		}

		$custom_grid = Plugin::instance()->frontend->get_builder_content_for_display( $grid );

		$this->set_used_template( $grid );

		$this->grid                    = explode( '{{mb-article}}', $custom_grid );
		$this->grid_settings['length'] = count( $this->grid );
	}

	public function get_grid() {
		$grid = '<!-- start part [' . $this->grid_settings['current'] . '] -->';
		if ( $this->grid_settings['current'] >= $this->grid_settings['length'] - 1 ) {
			$grid                          .= $this->grid[ $this->grid_settings['current'] ];
			$this->grid_settings['current'] = 0;
		}

		$grid .= $this->grid[ $this->grid_settings['current'] ];
		$grid .= '<!-- end part [' . $this->grid_settings['current'] . '] -->';
		$this->grid_settings['current']++;
		return $grid;
	}

	public function end_grid() {

		if ( $this->grid_settings['current'] ) {
			for ( $i = $this->grid_settings['current']; $i < $this->grid_settings['length']; $i++ ) {
				echo '<!-- start part [' . $i . '] finishing -->';
				echo $this->grid[ $i ];
				echo '<!-- end part [' . $i . '] finishing -->';
			}
		}
		$this->grid_settings['current'] = 0;
	}

	protected function get_template() {
		global $mb_render_loop, $wp_query, $mb_index;
		$mb_index++;
		$old_query = $wp_query;

		$new_query        = new WP_Query( array(
			'p'         => get_the_ID(),
			'post_type' => get_post_type(),
		) );
		$wp_query         = $new_query;
		$settings         = $this->parent->get_settings();
		$this->pid        = get_the_ID(); // set the current id in private var usefull to passid
		$default_template = $this->get_instance_value( 'mb_skin_template' );
		$template         = $default_template;

		$template = apply_filters( 'mb_action_template', $template, $this, $mb_index );
		$template = $this->get_current_ID( $template );

		$mb_render_loop = get_the_ID() . ',' . $template;

		/* end pro */
		if ( ! $template ) {
			return;
		}

		$this->set_used_template( $template );

		$return         = Plugin::instance()->frontend->get_builder_content_for_display( $template );
		$mb_render_loop = false;

		$wp_query = $old_query;
		return $return;
	}

	/**
	 * This is for multilang porpouses... curently WPML.
	 * @param Int $id
	 * @return Int $newid
	 */
	private function get_current_ID( $id ) {
		$newid = apply_filters( 'wpml_object_id', $id, 'elementor_library', true );
		return $newid ? $newid : $id;
	}

	public function get_container_class() {
		return 'elementor-posts--skin-' . $this->get_id();
	}

	protected function render_post_header() {
		$classes         = 'elementor-post elementor-grid-item mb-post-loop';
		$parent_settings = $this->parent->get_settings();
		$parent_settings[ $this->get_id() . '_post_slider' ] = isset( $parent_settings[ $this->get_id() . '_post_slider' ] ) ? $parent_settings[ $this->get_id() . '_post_slider' ] : '';
		if ( 'yes' === $parent_settings[ $this->get_id() . '_post_slider' ] ) {
			$classes .= ' swiper-slide';
		}
		if ( $this->grid_settings['allow'] ) {
			echo $this->get_grid();
			echo '<div id="post-' . get_the_ID() . '" class="' . esc_attr( implode( ' ', get_post_class( [ $classes ] ) ) ) . '">';
		} else {
			echo '<article id="post-' . get_the_ID() . '" class="' . esc_attr( implode( ' ', get_post_class( [ $classes ] ) ) ) . '">';
		}
	}

	protected function render_post_footer() {
		if ( ! $this->grid_settings['allow'] ) {
			echo '</article>';
		} else {
			echo '</div>';
		}
	}

	protected function render_post() {
		do_action( 'mb_before_render_post_header', $this );
		$this->render_post_header();
		do_action( 'mb_after_render_post_header', $this );

		if ( $this->get_instance_value( 'mb_skin_template' ) ) {
			if ( 'yes' === $this->get_instance_value( 'use_keywords' ) ) {
				global $post;
				apply_filters( 'mb_dynamic_filter', '', $post, '', $this->parent->get_settings() ); // this is for pre-use of custom values.
				$template     = $this->get_template();
				$new_template = apply_filters( 'mb_dynamic_filter', $template, $post, '', $this->parent->get_settings() );
				echo $new_template ? $new_template : $template;
			} else {
				echo $this->get_template();
			}
		} else {
			echo '<div style="display:table;border:1px solid #c6ced5; background:#dde1e5; width:100%; height:100%; min-height:200px;text-align:center; padding:20px;"><span style="vertical-align:middle;display: table-cell;color:#8995a0;">' .
			__( 'Please select a default template! ', 'mb-elementor-integrator' ) . '</span></div>';
		}
		do_action( 'mb_before_render_post_footer', $this );
		$this->render_post_footer();
		do_action( 'mb_after_render_post_footer', $this );
	}

	protected function ajax_pagination() {
		$settings       = $this->parent->get_settings();
		$theme_document = Plugin::$instance->documents->get_current();
		$page_limit     = $settings['pagination_page_limit'] ? $settings['pagination_page_limit'] : 999;
		$max_pages      = $this->parent->get_query()->max_num_pages;
		$max_num_pages  = $page_limit < $max_pages ? $page_limit : $max_pages;
		$args           = [
			'current_page'  => $this->parent->get_current_page(),
			'max_num_pages' => $max_num_pages,
			'load_method'   => $settings['pagination_type'], // or infinitescroll for pro
			'widget_id'     => $this->parent->get_id(),
			'post_id'       => get_the_id(),
			'theme_id'      => is_null( $theme_document ) ? '' : $theme_document->get_main_id(),
			'change_url'    => $settings['change_url'],
			'reinit_js'     => $settings['reinit_js'],
		];

		$pagination = wp_json_encode( $args );
		return $pagination;
	}

	protected function render_loop_header() {
		$parent_settings                                     = $this->parent->get_settings();
		$parent_settings[ $this->get_id() . '_post_slider' ] = isset( $parent_settings[ $this->get_id() . '_post_slider' ] ) ? $parent_settings[ $this->get_id() . '_post_slider' ] : '';

		if ( 'yes' === $parent_settings[ $this->get_id() . '_post_slider' ] ) {
			echo '<div class="swiper-container">';
			$this->grid_settings['allow'] = false;
		} else { // we don't use custom grid if slider is activated
			if ( isset( $parent_settings[ $this->get_id() . '_mb_use_grid' ] ) && 'yes' === $parent_settings[ $this->get_id() . '_mb_use_grid' ] && isset( $parent_settings[ $this->get_id() . '_mb_grid' ] ) && $parent_settings[ $this->get_id() . '_mb_grid' ] ) {
				$this->set_custom_grid( $parent_settings[ $this->get_id() . '_mb_grid' ] );
				$this->grid_settings['allow'] = true;
			} else {
				$this->grid_settings['allow'] = false;
			}
		}
		$this->parent->add_render_attribute('container', [
			'class'         => [
				'mbei-posts',
				'elementor-posts-container',
				'elementor-posts',
				'yes' === $parent_settings[ $this->get_id() . '_post_slider' ] ? 'swiper-wrapper' : '',
				$this->grid_settings['allow'] ? 'mb-grid' : '',
				'yes' !== $parent_settings[ $this->get_id() . '_post_slider' ] && ! $this->grid_settings['allow'] ? 'elementor-grid' : '',
				$this->get_container_class(),
			],
			'data-settings' => [ htmlentities( $this->ajax_pagination(), ENT_QUOTES ) ],
		]);

		echo '<div ' . $this->parent->get_render_attribute_string( 'container' ) . '>';
	}

	protected function render_loop_footer() {

		$this->admin_bar_menu(); // let's pass the templates we used so far to tha admin-bar-menu

		$parent_settings                                     = $this->parent->get_settings();
		$parent_settings[ $this->get_id() . '_post_slider' ] = isset( $parent_settings[ $this->get_id() . '_post_slider' ] ) ? $parent_settings[ $this->get_id() . '_post_slider' ] : '';

		if ( $this->grid_settings['allow'] ) {
			$this->end_grid();
		}
		echo '</div>';

		if ( 'yes' === $parent_settings[ $this->get_id() . '_post_slider' ] ) {
			$this->slider_elements();
			echo '</div>';
		}
		if ( '' === $parent_settings['pagination_type'] ) {
			return;
		}

		$page_limit = $this->parent->get_query()->max_num_pages;
		if ( '' !== $parent_settings['pagination_page_limit'] ) {
			$page_limit = min( $parent_settings['pagination_page_limit'], $page_limit );
		}

		if ( 2 > $page_limit ) {
			return;
		}

		$this->parent->add_render_attribute( 'pagination', 'class', 'elementor-pagination' );

		$has_numbers   = in_array( $parent_settings['pagination_type'], [ 'numbers', 'numbers_and_prev_next' ] );
		$has_prev_next = in_array( $parent_settings['pagination_type'], [ 'prev_next', 'numbers_and_prev_next' ] );

		$links = [];

		if ( $has_numbers ) {
			$paginate_args = [
				'type'               => 'array',
				'current'            => $this->parent->get_current_page(),
				'total'              => $page_limit,
				'prev_next'          => false,
				'show_all'           => 'yes' !== $parent_settings['pagination_numbers_shorten'],
				'before_page_number' => '<span class="elementor-screen-only">' . __( 'Page', 'mb-elementor-integrator' ) . '</span>',
			];

			if ( is_singular() && ! is_front_page() ) {
				global $wp_rewrite;
				if ( $wp_rewrite->using_permalinks() ) {
					$paginate_args['base']   = trailingslashit( get_permalink() ) . '%_%';
					$paginate_args['format'] = user_trailingslashit( '%#%', 'single_paged' );
				} else {
					$paginate_args['format'] = '?page=%#%';
				}
			}

			$links = paginate_links( $paginate_args );
		}

		if ( $has_prev_next ) {
			$prev_next = $this->parent->get_posts_nav_link( $page_limit );
			array_unshift( $links, $prev_next['prev'] );
			$links[] = $prev_next['next'];
		}
		?>
		<nav class="elementor-pagination" role="navigation" aria-label="<?php esc_attr_e( 'Pagination', 'mb-elementor-integrator' ); ?>">
			<?php echo implode( PHP_EOL, $links ); ?>
		</nav>
		<?php
		if ( 'loadmore' === $parent_settings['pagination_type'] ) {
			$this->render_loadmore_button();
		}
		if ( 'lazyload' === $parent_settings['pagination_type'] ) {
			$this->render_lazyload_animation();
		}
	}

	protected function render_lazyload_animation() {
		$settings       = $this->parent->get_settings();
		$next_page      = $this->parent->get_current_page() + 1;
		$next_page_link = trailingslashit( get_permalink() ) . '?page=' . $next_page;
		$animation      = Animation::get_lazy_load_animations_html( $settings['lazyload_animation'] );
		$target         = $this->parent->get_id();
		?>
		<nav class="mbei-lazyload elementor-pagination" data-targetid="<?php echo esc_attr( $target ); ?>">
			<?php echo $animation; ?>
			<a href="<?php echo esc_url( $next_page_link ); ?>" >
				&gt;
			</a>
		</nav>
		<?php
	}

	protected function render_loadmore_button() {
		$settings       = $this->parent->get_settings();
		$next_page      = $this->parent->get_current_page() + 1;
		$next_page_link = trailingslashit( get_permalink() ) . '?page=' . $next_page;
		$class          = '';
		$args           = [
			'loading_text' => $settings['loadmore_loading_text'],
			'text'         => $settings['loadmore_text'], // or infinitescroll for pro
			'widget_id'    => $this->parent->get_id(),
		];

		$data = htmlentities( wp_json_encode( $args ), ENT_QUOTES );

		if ( $settings['loadmore_hover_animation'] ) {
			$class = 'elementor-animation-' . $settings['loadmore_hover_animation'];
		}
		?>
		<nav class="elementor-button-wrapper elementor-pagination mbei-load-more-button" data-settings="<?php echo $data; ?>">
			<a href="<?php echo esc_url( $next_page_link ); ?>" class="elementor-button-link elementor-button <?php echo esc_attr( $class ); ?>" role="button">
				<span><?php echo esc_attr( $settings['loadmore_text'] ); ?></span>
			</a>
		</nav>
		<?php
	}

	private function slider_elements() {
		$this->settings = $this->parent->get_settings();
		do_action( 'mb_after_slider_elements', $this );
	}

	private function nothing_found() {
		$this->render_loop_header();
		$should_escape = apply_filters( 'elementor_pro/theme_builder/archive/escape_nothing_found_message', true );
		$message       = $this->parent->get_settings_for_display( 'nothing_found_message' );
		if ( $should_escape ) {
			$message = esc_html( $message );
		}

		$message = '<div class="elementor-posts-nothing-found">' . $message . '</div>';
		do_action( 'mb_not_found', $this );
		echo $message;
		$this->render_loop_footer();
	}

	public function render() {
		$this->parent->query_posts();

		$query = $this->parent->get_query();

		do_action( 'mb_before_loop_query', $query, $this );

		if ( ! $query->found_posts ) {
			$this->nothing_found();
			return;
		}

		$this->render_loop_header();

		// It's the global `wp_query` it self. and the loop was started from the theme.
		if ( $query->in_the_loop ) {
			$this->current_permalink = get_permalink();
			$this->render_post();
		} else {
			while ( $query->have_posts() ) {
				$query->the_post();

				$this->current_permalink = get_permalink();
				$this->render_post();
			}
		}

		do_action( 'mb_after_loop_query', $query, $this );

		wp_reset_postdata();

		$this->render_loop_footer();
	}

	public function get_settings_for_display( $setting_key = null ) {
		// let's position ourselfs inside the loop item.
		global $wp_query;
		$old_query = $wp_query;
		$new_query = new WP_Query( array(
			'p'         => get_the_ID(),
			'post_type' => get_post_type(),
		) );
		$wp_query  = $new_query;
		if ( $setting_key ) {
			$settings = [
				$setting_key => $this->parent->get_settings( $setting_key ),
			];
		} else {
			$settings = $this->parent->get_active_settings();
		}
		$controls        = $this->parent->get_controls();
		$controls        = array_intersect_key( $controls, $settings );
		$parsed_settings = $this->parent->parse_dynamic_settings( $settings, $controls );
		$wp_query        = $old_query; // get out of loop item.
		if ( $setting_key ) {
			return $parsed_settings[ $setting_key ];
		}
		return $parsed_settings;
	}

}
