<?php

namespace MBEI\Widgets\Skins;

use Elementor\Widget_Base;
use ElementorPro\Modules\Posts\Skins\Skin_Base;
use Elementor\Controls_Manager;
use MBEI\Traits\Skin;
use MBEI\GroupField;

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
			'label'       => __( 'Select a default template', 'mb-elementor-integrator' ),
			'description' => '<div style="text-align:center;"><a target="_blank" style="text-align: center;font-style: normal;" href="' . esc_url( admin_url( '/edit.php?post_type=elementor_library&tabs_group=theme&elementor_library_type=mb_loop' ) ) .
				'" class="elementor-button elementor-button-default elementor-repeater-add">' .
				__( 'Create/edit a Loop Template', 'mb-elementor-integrator' ) . '</a></div>',
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'default'     => [],
			'options'     => $this->get_mb_skin_template(),
		]);

		$this->remove_control( 'map-field-group' );

		$this->register_divider_controls();
	}

	private function get_mb_skin_template() {
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

	public function render() {
		$group_fields = new GroupField();
		$post         = $group_fields->get_current_post();

		$data_groups = [];
		$data_column = [];

		$settings = $this->parent->get_settings_for_display();
		if ( isset( $settings['field-group'] ) && ! empty( $settings['field-group'] ) ) {
			$data_groups = rwmb_get_value( $settings['field-group'], [], $post->ID );

			$fields      = $group_fields->get_field_group( $settings['field-group'] );
			$data_column = array_combine( array_column( $fields['fields'], 'id' ), $fields['fields'] );
		}

		// echo "<pre>";
		// print_r($data_groups);
		// echo "</pre>"
		?>
		<div class="mbei-loop-group">
			<?php if ( count( $data_groups ) > 0 ) : ?>
				<div class="mbei-fields mb-columns">
					<?php foreach ( $data_groups as $data_group ) : ?>
						<div class="field-item mb-column">
							<?php foreach ( $data_group as $key => $value ) : ?>
								<div class="mb-subfield-<?= $key; ?>">
									<?php $group_fields->display_field( $value, $data_column[ $key ] ); ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>                    
				</div>
			<?php endif ?>
		</div>
		<?php
	}

}
