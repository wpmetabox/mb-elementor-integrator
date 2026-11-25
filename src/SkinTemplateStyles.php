<?php

namespace MBEI;

use Elementor\Plugin;

class SkinTemplateStyles {

	private static $rendered = [];

	private static $skin_ids = [];

	public function __construct() {
		add_action( 'elementor/post/render', [ $this, 'render_skin_templates' ] );
		add_filter( 'elementor/document/related_posts', [ $this, 'add_related_skins' ], 10, 2 );
	}

	public function render_skin_templates( $post_id ) {
		$post_id = (int) $post_id;

		if ( ! $post_id || isset( self::$rendered[ $post_id ] ) ) {
			return;
		}

		self::$rendered[ $post_id ] = true;

		foreach ( $this->get_skin_ids( $post_id ) as $skin_id ) {
			if ( ! isset( self::$rendered[ $skin_id ] ) ) {
				do_action( 'elementor/post/render', $skin_id );
			}
		}
	}

	public function add_related_skins( $related, $post_id = null ) {
		$related = (array) $related;

		return array_values( array_unique( array_filter( array_map( 'intval', array_merge( $related, $this->get_skin_ids( (int) $post_id ) ) ) ) ) );
	}

	private function get_skin_ids( $post_id ) {
		if ( isset( self::$skin_ids[ $post_id ] ) ) {
			return self::$skin_ids[ $post_id ];
		}

		self::$skin_ids[ $post_id ] = [];

		$document = Plugin::instance()->documents->get( $post_id );
		$elements = $document ? $document->get_elements_data() : [];

		if ( empty( $elements ) ) {
			return [];
		}

		$ids = [];

		Plugin::instance()->db->iterate_data( $elements, function ( $element ) use ( &$ids ) {
			$settings = $element['settings'] ?? [];
			$is_group = 'widget' === ( $element['elType'] ?? '' ) && 'metabox-group' === ( $element['widgetType'] ?? '' );
			$skin     = ( $settings['_skin'] ?? '' ) ?: 'meta_box_skin_group';

			if ( $is_group && ! empty( $settings['mb_skin_template'] ) && 'meta_box_skin_group' === $skin ) {
				foreach ( (array) $settings['mb_skin_template'] as $id ) {
					if ( is_numeric( $id ) ) {
						$ids[] = (int) $id;
					}
				}
			}
		} );

		self::$skin_ids[ $post_id ] = array_values( array_unique( array_filter( $ids ) ) );

		return self::$skin_ids[ $post_id ];
	}
}
