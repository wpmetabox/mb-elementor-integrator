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
		$field_id    = $this->get_settings( 'key' );
		$field_value = self::handle_get_value( $field_id, get_the_ID() );

		rwmb_the_value( $field_id );
	}
}