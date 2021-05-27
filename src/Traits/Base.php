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
			'label'   => __( 'Field', 'mb-elementor-integrator' ),
			'type'    => Controls_Manager::SELECT,
			'groups'  => $this->get_option_groups(),
		] );
	}
}