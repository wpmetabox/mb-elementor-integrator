<?php
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;

class MB_Elementor_Integrator_Image extends Data_Tag {
	use MB_Elementor_Integrator_Base;

	public function get_name() {
		return 'meta-box-image';
	}

	public function get_categories() {
		return [
			Module::IMAGE_CATEGORY,
			Module::GALLERY_CATEGORY,
		];
	}

	public function get_value( array $options = [] ) {
		$field_id = $this->get_settings( 'key' );
		$images   = self::handle_get_value( $field_id, get_the_ID() );

		if ( ! $field_id || ! $images ) {
			return;
		}


		if ( isset( $images['ID'] ) ) {
			$images['id'] = $images['ID'];
			return $images;
		}
		$value = [];
		foreach ( $images as $image ) {
			$value[] = [
				'id' => $image['ID'],
			];
		}
		return $value;
	}

	protected function get_supported_fields() {
		return [
			'image',
			'single_image',
			'image_advanced',
			'image_upload',
			'image_select',
		];
	}
}