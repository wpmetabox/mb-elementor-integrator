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

		if ( empty( $images ) ) {
			return [];
		}

		// Normalize scalar attachment ID to list.
		if ( is_numeric( $images ) ) {
			$images = [ (int) $images ];
		}

		if ( ! is_array( $images ) ) {
			return [];
		}

		$widget = CurrentWidget::name() ?? 'null';
		$types  = [
			'null'                 => 'single',
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

	private function single( array $images ): array {
		if ( isset( $images['ID'] ) || isset( $images['id'] ) ) {
			return $this->format( $images );
		}

		return $this->format( reset( $images ) );
	}

	private function multiple( array $images ): array {
		if ( isset( $images['ID'] ) || isset( $images['id'] ) ) {
			$images = [ $images ];
		}

		// Flat list of attachment IDs: [ 12, 34, 56 ]
		if ( $this->is_list_of_ids( $images ) ) {
			return array_map( [ $this, 'format' ], array_values( $images ) );
		}

		// List of info arrays: [ 0 => [ID=>..], 1 => [ID=>..] ]
		if ( array_key_first( $images ) === 0 && ( count( $images ) - 1 ) === array_key_last( $images ) ) {
			$first = reset( $images );
			if ( is_array( $first ) && ( isset( $first['ID'] ) || isset( $first['id'] ) ) ) {
				return array_map( [ $this, 'format' ], $images );
			}
			// Avoid array_merge_recursive on non-array items (IDs).
			if ( is_array( $first ) ) {
				$images = array_merge_recursive( ...$images );
			}
		}

		return array_map( [ $this, 'format' ], array_values( $images ) );
	}

	private function default( array $images ): array {
		if ( isset( $images['ID'] ) || isset( $images['id'] ) ) {
			return $this->format( $images );
		}
		return array_map( [ $this, 'format' ], array_values( $images ) );
	}

	private function is_list_of_ids( array $images ): bool {
		if ( ! $images ) {
			return false;
		}
		foreach ( $images as $item ) {
			if ( ! is_numeric( $item ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Normalize an attachment ID or info array to Elementor image format.
	 *
	 * @param array|int|string $image Attachment info array or ID.
	 */
	private function format( $image ): array {
		if ( is_numeric( $image ) ) {
			$id  = (int) $image;
			$src = wp_get_attachment_image_src( $id, 'full' );
			return [
				'id'  => $id,
				'url' => $src[0] ?? '',
			];
		}

		if ( ! is_array( $image ) ) {
			return [
				'id'  => 0,
				'url' => '',
			];
		}

		$id = (int) ( $image['ID'] ?? $image['id'] ?? 0 );
		return [
			'id'  => $id,
			'url' => $image['full_url'] ?? $image['url'] ?? ( $id ? ( wp_get_attachment_image_src( $id, 'full' )[0] ?? '' ) : '' ),
		];
	}
}
