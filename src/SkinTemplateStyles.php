<?php

namespace MBEI;

use Elementor\Modules\AtomicWidgets\Utils\Utils;

class SkinTemplateStyles {

	private static array $rendered = [];

	private static array $skin_ids = [];

	public function __construct() {
		add_action( 'elementor/post/render', [ $this, 'render_skin_templates' ] );
		add_filter( 'elementor/document/related_posts', [ $this, 'add_related_skins' ], 10, 2 );
	}

	public function render_skin_templates( $post_id ): void {
		$post_id = (int) $post_id;
		if ( ! $post_id || isset( self::$rendered[ $post_id ] ) ) {
			return;
		}

		self::$rendered[ $post_id ] = true;

		foreach ( $this->get_skin_ids( $post_id ) as $skin_id ) {
			do_action( 'elementor/post/render', $skin_id );
		}
	}

	public function add_related_skins( array $related, ?int $post_id = null ): array {
		return array_values( array_unique( array_merge( array_map( 'intval', $related ), $this->get_skin_ids( (int) $post_id ) ) ) );
	}

	private function get_skin_ids( int $post_id ): array {
		if ( isset( self::$skin_ids[ $post_id ] ) ) {
			return self::$skin_ids[ $post_id ];
		}

		$ids = [];

		Utils::traverse_post_elements( (string) $post_id, function ( $element ) use ( &$ids ) {
			if ( 'metabox-group' !== ( $element['widgetType'] ?? '' ) ) {
				return;
			}

			foreach ( (array) ( $element['settings']['mb_skin_template'] ?? [] ) as $id ) {
				$ids[] = (int) apply_filters( 'wpml_object_id', $id, 'elementor_library', true );
			}
		} );

		self::$skin_ids[ $post_id ] = array_values( array_unique( array_filter( $ids ) ) );

		return self::$skin_ids[ $post_id ];
	}
}
