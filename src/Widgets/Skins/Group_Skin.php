<?php

namespace MBEI\Widgets\Skins;

use Elementor\Widget_Base;
use ElementorPro\Modules\Posts\Skins\Skin_Base;
use Elementor\Controls_Manager;
use MBEI\Traits\Skin;
use MBEI\GroupField;

class Group_Skin extends Skin_Base {

	use Skin;

	public function get_title() {
		return __( 'Metabox Group Skin', 'mb-elementor-integrator' );
	}

	public function get_id() {
		return 'meta_box_skin_group';
	}

	protected function _register_controls_actions() {
		parent::_register_controls_actions();
		add_action( 'elementor/element/metabox-group/section_metabox/before_section_end', [ $this, 'register_controls' ] );
	}

	public function register_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->add_control('mb_skin_template', [
			'label'       => __( 'Select a default template', 'mb-elementor-integrator' ),
			'description' => '<div style="text-align:center;"><a target="_blank" style="text-align: center;font-style: normal;" href="' . esc_url( admin_url( '/edit.php?post_type=elementor_library&tabs_group=theme&elementor_library_type=mb_loop' ) ) .
				'" class="elementor-button elementor-button-default elementor-repeater-add">' .
				__( 'Create/edit a Loop Template', 'mb-elementor-integrator' ) . '</a></div>',
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'default'     => [],
			'options'     => $this->get_mb_skin_template(),
		]);

		$this->remove_control( 'map-field-group' );

		$this->register_divider_controls();
	}

	private function get_mb_skin_template() {
		global $wpdb;

		$cache_key = 'mbei_skin_template';
		$templates = wp_cache_get( $cache_key );
		if ( false === $templates ) {
			$templates = $wpdb->get_results(
				"SELECT $wpdb->term_relationships.object_id as ID, $wpdb->posts.post_title as post_title FROM $wpdb->term_relationships
							INNER JOIN $wpdb->term_taxonomy ON
								$wpdb->term_relationships.term_taxonomy_id=$wpdb->term_taxonomy.term_taxonomy_id
							INNER JOIN $wpdb->terms ON
								$wpdb->term_taxonomy.term_id=$wpdb->terms.term_id AND $wpdb->terms.slug='metabox_group_template'
							INNER JOIN $wpdb->posts ON
								$wpdb->term_relationships.object_id=$wpdb->posts.ID
				WHERE  $wpdb->posts.post_status='publish'"
			);

			wp_cache_set( $cache_key, $templates );
		}

		$options = [ 0 => 'Select a template' ];
		foreach ( $templates as $template ) {
			$options[ $template->ID ] = $template->post_title;
		}
		return $options;
	}

	public function render() {
		$post = GroupField::get_current_post();

		$data_groups = [];

		$settings = $this->parent->get_settings_for_display();
		if ( ! empty( $settings['field-group'] ) ) {
			$data_groups = rwmb_get_value( $settings['field-group'], [], $post->ID );
		}

		// Check Paging
		if ( 0 < count( $data_groups ) && $settings['mb_limit'] > 0 ) {
			$page        = isset( $_GET['mb_page'] ) ? intval( $_GET['mb_page'] ) : 1;
			$limit       = intval( $settings['mb_limit'] );
			$offest      = $limit * ( $page - 1 );
			$total       = intval( ceil( count( $data_groups ) / $limit ) );
			$data_groups = array_slice( $data_groups, $offest, $limit );
		}

		$data_column = [];
		if ( ! empty( $settings['map-field-group'] ) ) {
			$fields = GroupField::get_field_group( $settings['field-group'] );

			foreach ( $settings['map-field-group'] as $column ) {
				$subfield = explode( ':', $column['subfield'] )[1];
				$field    = array_search( $subfield, array_column( $fields['fields'], 'id' ) );

				$data_column[] = [
					'type'      => $fields['fields'][ $field ]['type'],
					'field'     => $subfield,
					'text_link' => $column['display_text_for_link'],
				];
			}
		}

		// print_r($data_column);die();
		?>
		<div class="mbei-loop-group">
			<?php
			if ( 'yes' === $settings['mb_title_list_show'] ) {
				printf( '<%s style="text-align:%s" class="mb_title_typography">%s</%s>', $settings['mb_title_tag'], esc_attr( $settings['title_align'] ), $settings['mb_title_list'], $settings['mb_title_tag'] );
			}
			?>

			<?php if ( count( $data_groups ) > 0 ) : ?>
				<div class="mbei-fields <?= $settings['mb_type_list'] ?>">
					<?php if ( 'mb-columns' === $settings['mb_type_list'] ) : ?>
						<?php foreach ( $data_groups as $data_group ) : ?>
							<div class="field-item mb-column">
								<?php foreach ( $data_column as $col ) : ?>
									<div class="mb-subfield-<?= $col['field']; ?>">
										<?php GroupField::display_field( $data_group[ $col['field'] ], $col ); ?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endforeach; ?>                    
					<?php else : ?>
						<table>
							<thead>
								<tr>
									<?php foreach ( $data_column as $col ) : ?>
										<th class="mb-subfield-title-<?= $col['field']; ?>"><?= ucfirst( $col['field'] ) ?></th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $data_groups as $data_group ) : ?>
									<tr>
										<?php foreach ( $data_column as $col ) : ?>
											<td class="mb-subfield-<?= $col['field']; ?>"><?= $data_group[ $col['field'] ]; ?></td>
										<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>   
							</tbody>
						</table>
					<?php endif ?>
				</div>

				<?php
				if ( ! empty( $settings['mb_pagination'] ) && isset( $total ) && 1 < $total ) {
					GroupField::pagination([
						'page'      => $page,
						'limit'     => $limit,
						'total'     => $total,
						'type'      => $settings['mb_pagination'],
						'text_prev' => $settings['mb_pagination_prev'],
						'text_next' => $settings['mb_pagination_next'],
					]);
				}
				?>
			<?php endif ?>
		</div>
		<?php
	}

}
