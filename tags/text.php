<?php
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

class MB_Elementor_Integrator_Text extends Tag {
	use MB_Elementor_Integrator_Base;

	public function get_name() {
		return 'meta-box-text';
	}

	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
		];
	}

	public function render() {
		$field_id = $this->get_settings( 'key' );
		$field_value = rwmb_meta( $field_id, get_the_ID() );
		if ( ! $field_id || ! $field_value ) {
			return;
		}
		echo $field_value;
	}
}