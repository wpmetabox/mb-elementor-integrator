<?php
namespace MBEI\Traits\Fields;

use Elementor\Modules\DynamicTags\Module;

trait Image {
	public function get_categories() {
		return [
			Module::IMAGE_CATEGORY,
			Module::GALLERY_CATEGORY,
		];
	}

	public function get_value( array $options = [] ) {
		$images = $this->handle_get_value();

		if ( empty( $images ) ) {
			return;
		}

		// Single image.
		if ( isset( $images['ID'] ) ) {
			return [
				'id'  => $images['ID'],
				'url' => $images['full_url'],
			];
		}

		// Multiple images.
		$value = [];
		foreach ( $images as $image ) {
			$value[] = [
				'id' => $image['ID'],
			];
		}

		return $value;
	}
}