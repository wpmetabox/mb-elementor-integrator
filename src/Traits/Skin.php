<?php
/**
 * Skin trait for all object type that handle register widget skin.
 */

namespace MBEI\Traits;

use Elementor\Controls_Manager;
use Elementor\Plugin;

trait Skin {

	private $used_templates = [];

	public function get_id() {
		return 'meta_box_skin';
	}

	public function get_title() {
		return __( 'Meta Box Skin', 'mb-elementor-integrator' );
	}

	private function admin_bar_menu() {
		foreach ( $this->used_templates as $post_id ) {
			if ( post_password_required( $post_id ) ) {
				return '';
			}

			if ( ! Plugin::$instance->db->is_built_with_elementor( $post_id ) ) {
				return '';
			}

			$document = Plugin::$instance->documents->get_doc_for_frontend( $post_id );

			// Change the current post, so widgets can use `documents->get_current`.
			Plugin::$instance->documents->switch_to_document( $document );

			Plugin::$instance->documents->restore_document();
		}
	}

	protected function set_used_template( $skin ) {
		if ( ! $skin ) {
			return;
		}
		$this->used_templates[ $skin ] = $skin;
	}

	public function render() {
		echo 'SKIN WIDGET';
	}

	public function render_amp() {

	}

	protected function register_divider_controls() {
		$this->add_control('mb_hr2', [
			'type' => Controls_Manager::DIVIDER,
		]);
	}

}
