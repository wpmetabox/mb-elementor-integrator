<?php
namespace MBEI\Traits\Fields;

use Elementor\Modules\DynamicTags\Module;

trait Video {
	public function get_categories() {
		return [
			Module::URL_CATEGORY,
			Module::POST_META_CATEGORY,
		];
	}

	public function get_value( array $options = [] ) {
		$url = $this->handle_get_value();

		if ( empty( $url ) ) {
			return;
		}

		if ( ! is_array( $url ) ) {
			return $url;
		}

		$value = [];
		foreach ( $url as $link ) {
			if ( ! empty( $link['src'] ) ) {
				$value['url'] = $link['src'];
			}
			if ( ! empty( $link['url'] ) ) {
				$value['url'] = $link['url'];
			}
		}

		return $value;
	}
}
