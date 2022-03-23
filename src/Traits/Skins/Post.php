<?php
/**
 * Skin Post trait for all object type that handle register widget skin.
 */

namespace MBEI\Traits\Skins;

use Elementor\Controls_Manager;

trait Post {
	protected function register_template_controls() {
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
	}

	protected function register_grid_controls() {
		$this->add_control('mb_use_grid', [
			'label'        => __( 'Use custom grid?', 'mb-elementor-integrator' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_off'    => __( 'No', 'mb-elementor-integrator' ),
			'label_on'     => __( 'Yes', 'mb-elementor-integrator' ),
			'return_value' => 'yes',
			'separator'    => 'before',
			'default'      => '',
		]);

		$this->add_control('mb_grid', [
			'label'       => __( 'Select a default template', 'mb-elementor-integrator' ),
			'description' => '<div style="text-align:center;"><a target="_blank" style="text-align: center;font-style: normal;" href="' . esc_url( admin_url( '/edit.php?post_type=elementor_library&tabs_group=theme&elementor_library_type=mb_grid' ) ) .
				'" class="elementor-button elementor-button-default elementor-repeater-add">' .
				__( 'Create/edit a Custom Grid', 'mb-elementor-integrator' ) . '</a></div>',
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'default'     => [],
			'options'     => $this->get_mb_grid(),
			'condition'   => [
				$this->get_id() . '_mb_use_grid' => 'yes',
			],
		]);
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
								$wpdb->term_taxonomy.term_id=$wpdb->terms.term_id AND $wpdb->terms.slug='mb_loop'
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

	private function get_mb_grid() {
		global $wpdb;

		$cache_key = 'mbei_grid';
		$templates = wp_cache_get( $cache_key );
		if ( false === $templates ) {
			$templates = $wpdb->get_results(
				"SELECT $wpdb->term_relationships.object_id as ID, $wpdb->posts.post_title as post_title FROM $wpdb->term_relationships
							INNER JOIN $wpdb->term_taxonomy ON
								$wpdb->term_relationships.term_taxonomy_id=$wpdb->term_taxonomy.term_taxonomy_id
							INNER JOIN $wpdb->terms ON
								$wpdb->term_taxonomy.term_id=$wpdb->terms.term_id AND $wpdb->terms.slug='mb_grid'
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
}
