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
		$images = rwmb_meta( $field_id, [ 'size' => 'full' ], get_the_ID() );
		if ( ! $field_id || ! $images ) {
			return;
		}

		$value = [];

		if ( ! isset( $images['ID'] ) ) {
			foreach ( $images as $image ) {
				$value[] = [
					'id' => $image['ID'],
				];
			}
			return $value;
		}

		$images['id'] = $images['ID'];
		return $images;
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