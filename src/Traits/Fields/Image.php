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

		if ( isset( $images['ID'] ) && isset( $images['full_url'] ) ) {
			return [
				'id'  => $images['ID'],
				'url' => $images['full_url'],
			];
		}

		// Single image.
		if ( count( $images ) === 1 ) {
			$images = $images [ array_key_first( $images ) ];

			return [
				'id'  => $images['ID'],
				'url' => isset( $images['url'] ) ?? $images['full_url'],
			];
		}

		// Multiple images.
		$value = [];
		foreach ( $images as $image ) {
			$value[] = [
				'id'  => $image['ID'],
				'url' => isset( $image['url'] ) ?? $image['full_url'],
			];
		}

		return $value;
	}
}