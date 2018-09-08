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
		$field_id = $this->get_settings( 'key' );
		$url_video = rwmb_meta( $field_id, get_the_ID() );
		if ( ! $field_id || ! $url_video ) {
			return;
		}
		$link_url = [];
		if ( is_array( $url_video ) ) {
			foreach ( $url_video as $link ) {
				if ( ! empty( $link['src'] ) ) {
					$link_url['url'] = $link['src'];
				}
				if ( ! empty( $link['url'] ) ) {
					$link_url['url'] = $link['url'];
				}
			}
		} else {
			$link_url = $url_video;
		}
		return $link_url;
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