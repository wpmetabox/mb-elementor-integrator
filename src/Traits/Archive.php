<?php
namespace MBEI\Traits;

trait Archive {
	public function get_group() {
		return 'archive';
	}

	private function get_option_groups() {
		$groups = [];

		$fields = rwmb_get_registry( 'field' )->get_by_object_type( 'term' );
		foreach ( $fields as $taxonomy => $list ) {
			$taxonomy_object = get_taxonomy( $taxonomy );
			if ( ! $taxonomy_object ) {
				continue;
			}
			$options = [
				'' => __( '-- Select a field --', 'mb-elementor-integrator' ),
			];
			foreach ( $list as $field ) {
				$options[ "{$taxonomy}:{$field['id']}" ] = $field['name'] ?: $field['id'];
			}
			$groups[] = [
				'label'   => $taxonomy_object->labels->singular_name,
				'options' => $options,
			];
		}

		return $groups;
	}

	/**
	 * Get the current term ID.
	 * Get from $wp_query->loop_term - a global that Elementor Pro sets (see
	 * ElementorPro\Modules\LoopBuilder\Skins\Skin_Loop_Taxonomy_Base::render_loop_content()).
	 *
	 * get_queried_object_id() only works on a real single term archive page
	 */
	private function get_current_term_id( $taxonomy ) {
		global $wp_query;

		if (
			isset( $wp_query->loop_term )
			&& is_object( $wp_query->loop_term )
			&& isset( $wp_query->loop_term->term_id )
			&& ( empty( $taxonomy ) || $wp_query->loop_term->taxonomy === $taxonomy )
		) {
			return (int) $wp_query->loop_term->term_id;
		}

		return get_queried_object_id();
	}

	private function handle_get_value() {
		$key = $this->get_settings( 'key' );
		if ( ! $key ) {
			return null;
		}
		list( $taxonomy, $field_id ) = explode( ':', $key );
		return rwmb_meta( $field_id, [ 'object_type' => 'term' ], $this->get_current_term_id( $taxonomy ) );
	}

	private function the_value() {
		$key = $this->get_settings( 'key' );
		if ( ! $key ) {
			return null;
		}
		list( $taxonomy, $field_id ) = explode( ':', $key );
		rwmb_the_value( $field_id, [ 'object_type' => 'term' ], $this->get_current_term_id( $taxonomy ) );
		if ( ! is_singular() ) {
			return null;
		}
		$post_id = get_the_ID();
		$terms   = get_the_terms( $post_id, $taxonomy );
		if ( empty( $terms ) ) {
			return null;
		}
		foreach ( $terms as $term ) {
			rwmb_the_value( $field_id, [ 'object_type' => 'term' ], $term->term_id );
		}
	}
}