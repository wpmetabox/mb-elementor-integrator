<?php

namespace MBEI\Widgets\Skins;

use Elementor\Widget_Base;
use ElementorPro\Modules\Posts\Skins\Skin_Base;
use Elementor\Controls_Manager;
use MBEI\Traits\Skin;
use MBEI\GroupField;
use Elementor\Plugin;
use Elementor\Core\DynamicTags\Manager;

class Group_Skin extends Skin_Base {

	use Skin;

	public function get_title() {
		return __( 'Metabox Group Skin', 'mb-elementor-integrator' );
	}

	public function get_id() {
		return 'meta_box_skin_group';
	}

	protected function _register_controls_actions() {
		parent::_register_controls_actions();
		add_action( 'elementor/element/metabox-group/section_metabox/before_section_end', [ $this, 'register_controls' ] );
	}

	public function register_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->add_control('mb_skin_template', [
			'label'       => __( 'Select a Group Skin', 'mb-elementor-integrator' ),
			'description' => '<div style="text-align:center;"><a target="_blank" style="text-align: center;font-style: normal;" href="' . esc_url( admin_url( '/edit.php?post_type=elementor_library&tabs_group=theme&elementor_library_type=metabox_group_template' ) ) .
			'" class="elementor-button elementor-button-default elementor-repeater-add">' .
			__( 'Create/edit a Group Skin', 'mb-elementor-integrator' ) . '</a></div>',
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'default'     => [],
			'options'     => $this->get_skin_template(),
		]);

		$this->remove_control( 'map-field-group' );

		$this->register_divider_controls();
	}

	private function get_skin_template() {
		global $wpdb;

		$cache_key = 'mbei_skin_template';
		$templates = wp_cache_get( $cache_key );
		if ( false === $templates ) {
			$templates = $wpdb->get_results(
				"SELECT $wpdb->term_relationships.object_id as ID, $wpdb->posts.post_title as post_title FROM $wpdb->term_relationships
							INNER JOIN $wpdb->term_taxonomy ON
								$wpdb->term_relationships.term_taxonomy_id=$wpdb->term_taxonomy.term_taxonomy_id
							INNER JOIN $wpdb->terms ON
								$wpdb->term_taxonomy.term_id=$wpdb->terms.term_id AND $wpdb->terms.slug='metabox_group_template'
							INNER JOIN $wpdb->posts ON
								$wpdb->term_relationships.object_id=$wpdb->posts.ID
				WHERE  $wpdb->posts.post_status='publish'"
			);

			wp_cache_set( $cache_key, $templates );
		}

		$options = [ 0 => 'Select a template' ];
		foreach ( $templates as $template ) {
			$options[ $template->ID ] = $template->post_title;
		}
		return $options;
	}

	protected function get_template() {
		$template = $this->get_instance_value( 'mb_skin_template' );
		$template = $this->get_current_ID( $template );

		if ( ! $template ) {
			return;
		}
		// Get elements settings.
		$document = Plugin::instance()->documents->get( $template );
		$settings = $document->get_elements_data();
		// Get data dynamic tags
		$dynamic_tags = [];
		$this->find_element_dynamic_tag( $settings, $dynamic_tags );
		$data_replace = $this->dynamic_tag_to_data( $dynamic_tags, Plugin::instance()->dynamic_tags );
		// Get Content Template.
		$content_template = Plugin::instance()->frontend->get_builder_content_for_display( $template );

		return [
			'data'    => $data_replace,
			'content' => $content_template,
		];
	}

	private function dynamic_tag_to_data( $dynamic_tags = [], Manager $dynamic_tags_mageger ) {
		if ( empty( $dynamic_tags ) ) {
			return [];
		}

		$data_replace = [];
		foreach ( $dynamic_tags as $dynamic_tag ) {
			// Chek $dynamic_tag is array.
			if ( is_array( $dynamic_tag ) ) {
				$data_replace = array_merge( $data_replace, $this->dynamic_tag_to_data( $dynamic_tag, $dynamic_tags_mageger ) );
			} else {
				// Check $dynamic_tag is dynamic tag meta box.
				$tag_data = $dynamic_tags_mageger->tag_text_to_tag_data( $dynamic_tag );
				if ( false === strpos( $tag_data['name'], 'meta-box-' ) ) {
					continue;
				}
				// Get tag content.
				$tag_data_content = $dynamic_tags_mageger->get_tag_data_content( $tag_data['id'], $tag_data['name'], $tag_data['settings'] );
				$key              = explode( ':', $tag_data['settings']['key'] )[1];
				if ( isset( $tag_data_content['url'] ) ) {
					$data_replace[ $key ] = GroupField::change_url_ssl( $tag_data_content['url'] );
				} else {
					$data_replace[ $key ] = $tag_data_content;
				}
			}
		}
		return $data_replace;
	}

	private function find_element_dynamic_tag( $settings, &$results = [] ) {
		if ( ! is_array( $settings ) ) {
			return;
		}

		foreach ( $settings as $elements ) {
			if ( isset( $elements['elType'] ) && $elements['elType'] === 'widget' && isset( $elements['settings']['__dynamic__'] ) ) {
				$results = array_merge_recursive( $results, $elements['settings']['__dynamic__'] );
				continue;
			}

			$this->find_element_dynamic_tag( $elements, $results );
		}
	}

	// Support multilang for WPML
	private function get_current_ID( $id ) {
		$newid = apply_filters( 'wpml_object_id', $id, 'elementor_library', true );
		return $newid ? $newid : $id;
	}

	private function render_header() {
		return '<div class="mbei-loop-group">'
				. '<div class="mbei-fields mb-columns">';
	}

	private function render_footer() {
		return '</div></div>';
	}

	protected function render_loop_header() {
		return '<div class="field-item mb-column">';
	}

	protected function render_loop_footer() {
		return '</div>';
	}

	public function render() {
		$group_fields = new GroupField();
		$post         = $group_fields->get_current_post();

		$data_groups = [];
		$data_column = [];

		$settings = $this->parent->get_settings_for_display();
		if ( ! isset( $settings['field-group'] ) || empty( $settings['field-group'] ) ) {
			return;
		}

		$data_groups = rwmb_get_value( $settings['field-group'], [], $post->ID );
		if ( 0 === count( $data_groups ) ) {
			return;
		}

		$fields      = $group_fields->get_field_group( $settings['field-group'] );
		$data_column = array_combine( array_column( $fields['fields'], 'id' ), $fields['fields'] );

		echo $this->render_header();
		if ( $this->get_instance_value( 'mb_skin_template' ) ) {
			$content_template = $this->get_template();
			$cols             = array_keys( $content_template['data'] );
			foreach ( $data_groups as $k => $data_group ) {
				$cols = array_intersect( array_keys( $data_group ), $cols );
				if ( 0 === count( $cols ) ) {
					continue;
				}

				$content = $this->render_loop_header() . $content_template['content'] . $this->render_loop_footer();
				foreach ( $cols as $col ) {
					if ( isset( $data_group[ $col ] ) ) {
						ob_start();
						$group_fields->display_field( $data_group[ $col ], $data_column[ $col ], true );
						$value = ob_get_contents();
						ob_end_clean();

						$content = str_replace( $content_template['data'][ $col ], $value, $content );
					}
				}
				echo $content;
			}
		} else {
			?>
			<?php foreach ( $data_groups as $data_group ) : ?>
				<div class="field-item mb-column">
					<?php foreach ( $data_group as $key => $value ) : ?>
						<div class="mb-subfield-<?= $key; ?>">
							<?php $group_fields->display_field( $value, $data_column[ $key ] ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>                    
			<?php
		}
		echo $this->render_footer();
	}

}
