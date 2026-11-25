<?php

namespace MBEI;

use Elementor\Plugin;

class SkinTemplateStyles {

	private static array $rendered = [];

	private static array $skin_ids = [];

	public function __construct() {
		add_action( 'elementor/post/render', [ $this, 'render_skin_templates' ] );
		add_filter( 'elementor/document/related_posts', [ $this, 'add_related_skins' ], 10, 2 );
	}

	public function render_skin_templates( int $post_id ): void {
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

	public function add_related_skins( $related, ?int $post_id = null ): array {
		$related = (array) $related;

		return array_values( array_unique( array_merge( array_map( 'intval', $related ), $this->get_skin_ids( (int) $post_id ) ) ) );
	}

	private function get_skin_ids( int $post_id ): array {
		self::$skin_ids[ $post_id ] = [];

		$document = Plugin::instance()->documents->get_doc_for_frontend( $post_id );
		$elements = $document ? $document->get_elements_data() : [];

		$ids = [];

		Plugin::instance()->db->iterate_data( $elements, function ( $element ) use ( &$ids ) {
			$settings = $element['settings'] ?? [];
			if ( 'widget' !== ( $element['elType'] ?? '' ) || 'metabox-group' !== ( $element['widgetType'] ?? '' ) ) {
				return;
			}

			foreach ( (array) ($settings['mb_skin_template'] ?? []) as $id ) {
				if ( is_numeric( $id ) ) {
					$ids[] = (int) apply_filters( 'wpml_object_id', $id, 'elementor_library', true );
				}
			}
		} );

		self::$skin_ids[ $post_id ] = array_values( array_unique( array_filter( $ids ) ) );

		return self::$skin_ids[ $post_id ];
	}
}