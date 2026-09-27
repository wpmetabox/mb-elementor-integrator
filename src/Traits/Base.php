<?php
/**
 * Base trait for all object type that handle register groups and fields.
 */

namespace MBEI\Traits;

use Elementor\Controls_Manager;

trait Base {
	public function get_title() {
		return __( 'Meta Box Field', 'mb-elementor-integrator' );
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
		$this->add_control( 'key', [
			'label'  => __( 'Field', 'mb-elementor-integrator' ),
			'type'   => Controls_Manager::SELECT,
			'groups' => $this->get_option_groups(),
		] );
	}

	protected function is_metabox_group_template( $document ) {
		if ( empty( $document ) ) {
			return false;
		}

		if ( 'metabox_group_template' === $document->get_type() ) {
			return true;
		}

		$post_id = method_exists( $document, 'get_main_id' ) ? $document->get_main_id() : $document->get_id();
		if ( ! $post_id ) {
			return false;
		}

		$template_type = get_post_meta( $post_id, '_elementor_template_type', true );
		if ( 'metabox_group_template' === $template_type ) {
			return true;
		}

		$terms = get_the_terms( $post_id, 'elementor_library_type' );
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( 'metabox_group_template' === $term->slug ) {
					return true;
				}
			}
		}

		return false;
	}
}
