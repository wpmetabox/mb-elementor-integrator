<?php
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;

class MB_Elementor_Integrator_Video extends Data_Tag {
	use MB_Elementor_Integrator_Base;

	public function get_name() {
		return 'meta-box-url';
	}

	public function get_categories() {
		return [
			Module::URL_CATEGORY,
			Module::POST_META_CATEGORY,
		];
	}

	public function get_value( array $options = [] ) {
		$field_id  = $this->get_settings( 'key' );
		$url_video = self::handle_get_value( $field_id );


		if ( ! $field_id || ! $url_video ) {
			return;
		}

		if ( ! is_array( $url_video ) ) {
			return $url_video;
		}

		$value = [];
		foreach ( $url_video as $link ) {
			if ( ! empty( $link['src'] ) ) {
				$value['url'] = $link['src'];
			}
			if ( ! empty( $link['url'] ) ) {
				$value['url'] = $link['url'];
			}
		}

		return $value;
	}

	protected function get_supported_fields() {
		return [
			'video',
			'text',
			'oembed',
			'url',
			'file',
		];
	}

}