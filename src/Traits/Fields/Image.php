<?php
namespace MBEI\Traits\Fields;

use Elementor\Modules\DynamicTags\Module;
use MBEI\CurrentWidget;

trait Image {
	public function get_categories() {
		return [
			Module::IMAGE_CATEGORY,
			Module::GALLERY_CATEGORY,
		];
	}

	public function get_value( array $options = [] ) {
		$images = $this->handle_get_value();

		if ( empty( $images ) || ! is_array( $images ) ) {
			return [];
		}

		$widget = CurrentWidget::name();
		$types  = [
			'image'                => 'single',
			'image-box'            => 'single',
			'hotspot'              => 'single',
			'price-list'           => 'single',
			'media-carousel'       => 'single',
			'testimonial-carousel' => 'single',
			'image-carousel'       => 'multiple',
			'image-gallery'        => 'multiple',
		];
		$method = $types[ $widget ] ?? 'default';

		return $this->$method( $images );
	}

	private function single( array $images ) : array {
		if ( isset( $images['ID'] ) ) {
			return $this->format( $images );
		}

		$image = reset( $images );
		return $this->format( $image );
	}

	private function multiple( array $images ) : array {
		if ( isset( $images['ID'] ) ) {
			$images = [ $images ];
		}
		return array_map( [ $this, 'format' ], $images );
	}

	private function default( array $images ) : array {
		return isset( $images['ID'] ) ? $this->format( $images ) : array_map( [ $this, 'format' ], $images );
	}

	private function format( array $image ) : array {
		return [
			'id'  => $image['ID'],
			'url' => $image['full_url'] ?? '',
		];
	}
}
