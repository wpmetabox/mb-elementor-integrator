<?php

namespace MBEI\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use MBEI\Classes\Document;
use MBEI\Traits\Widget;
use MBEI\Widgets\Controls\MBControls;
use MBEI\Classes\GroupField;
use Elementor\Repeater;

class LoopGroup extends Widget_Base {

	use Widget;

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'script-loop-group-frontend', plugin_dir_url( __DIR__ ) . 'assets/js/loop-group-frontend.js', [ 'elementor-frontend' ], '1.0.0', true );
		// wp_register_script('script-mbei', plugin_dir_url(__DIR__) . 'assets/js/mbei.js', ['elementor-frontend'], '1.0.0', true);
		wp_register_style( 'style-loop-group', plugin_dir_url( __DIR__ ) . 'assets/css/loop-group.css' );

		// add_action( 'elementor/widget/before_render_content', [$this, 'script_admin'] );
	}

	// public function script_admin() {
	// wp_enqueue_script('script-mbei', plugin_dir_url(__DIR__) . 'assets/js/mbei.js', ['elementor-common'], '1.0.0', true);
	// wp_enqueue_script('script-loop-group-common', plugin_dir_url(__DIR__) . 'assets/js/loop-group-admin.js', ['elementor-common'], '1.0.0', true);
	// }


	public function get_script_depends() {
		return [ 'script-loop-group-frontend' ];
	}

	public function get_style_depends() {
		return [ 'style-loop-group' ];
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve Term List widget name.
	 *
	 * @since 0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'mb-loop-group';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Term List widget title.
	 *
	 * @since 0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Loop MB Group', 'mb-elementor-integrator' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Term List widget icon.
	 *
	 * @since 0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-group';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Term List widget belongs to.
	 *
	 * @since 0.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'basic' ];
	}

	/**
	 * Register Term List widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 0.1
	 * @access protected
	 */
	public function register_controls() {

		$this->start_controls_section('section_metabox', [
			'label' => esc_html__( 'Meta Box Field Group', 'mb-elementor-integrator' ),
		]);

		$this->add_control('field-group', [
			'label'   => esc_html__( 'Fields Group', 'mb-elementor-integrator' ),
			'type'    => MBControls::GROUP_FIELD,
			'default' => '',
			'options' => GroupField::get_list_field_group(),
		]);

		$repeater = new Repeater();

		$repeater->add_control('subfield', [
			'label'   => esc_html__( 'Field name', 'mb-elementor-integrator' ),
			'type'    => Controls_Manager::SELECT,
			// 'label_block' => true,
			'options' => [],
		]);

		$repeater->add_control('display_text_for_link', [
			'label'     => esc_html__( 'Text Display', 'mb-elementor-integrator' ),
			'type'      => Controls_Manager::TEXT,
			'condition' => [
				'subfield!' => '',
			],
		]);

		$this->add_control('map-field-group', [
			'label'        => esc_html__( 'Field Sub', 'mb-elementor-integrator' ),
			'type'         => Controls_Manager::REPEATER,
			'fields'       => $repeater->get_controls(),
			'item_actions' => [
				// 'add' => false,
				'duplicate' => false,
				'remove'    => false,
			],
			'condition'    => [
				'field-group!' => '',
			],
			'default'      => [],
			'title_field'  => '<i class="eicon-circle"></i> <span style="text-transform: capitalize;">{{{ subfield ? subfield.split(":")[1]: "" }}}</span>',
		]);

		$this->end_controls_section();

		$this->start_controls_section('section_mblist', [
			'label' => esc_html__( 'Display List', 'mb-elementor-integrator' ),
		]);

		$this->add_control('mb_title_list_show', [
			'label'        => __( 'Show Title', 'mb-elementor-integrator' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_off'    => __( 'No', 'mb-elementor-integrator' ),
			'label_on'     => __( 'Yes', 'mb-elementor-integrator' ),
			'return_value' => 'yes',
			'separator'    => 'before',
			'default'      => 'yes',
		]);

		$this->add_control('mb_title_list', [
			'label'     => __( 'Title List', 'mb-elementor-integrator' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => 'Title List',
			'condition' => [
				'mb_title_list_show' => 'yes',
			],
		]);

		$this->add_control('mb_title_tag', [
			'label'   => esc_html__( 'Title Tag', 'mb-elementor-integrator' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h3',
			'options' => [
				'h1'   => 'H1',
				'h2'   => 'H2',
				'h3'   => 'H3',
				'h4'   => 'H4',
				'h5'   => 'H5',
				'h6'   => 'H6',
				'div'  => 'Div',
				'span' => 'Span',
				'p'    => 'P',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'title_style',
			'label'    => 'Title Style',
			'selector' => '{{WRAPPER}} .mb_title_typography',
		]);

		$this->add_control('title_align', [
			'label'   => esc_html__( 'Title align', 'plugin-name' ),
			'type'    => Controls_Manager::CHOOSE,
			'options' => [
				'left'   => [
					'title' => esc_html__( 'Left', 'plugin-name' ),
					'icon'  => 'eicon-text-align-left',
				],
				'center' => [
					'title' => esc_html__( 'Center', 'plugin-name' ),
					'icon'  => 'eicon-text-align-center',
				],
				'right'  => [
					'title' => esc_html__( 'Right', 'plugin-name' ),
					'icon'  => 'eicon-text-align-right',
				],
			],
			'default' => 'left',
			'toggle'  => true,
		]);

		$this->add_control('mb_hr2', [
			'type' => Controls_Manager::DIVIDER,
		]);

		$this->add_control('mb_type_list', [
			'label'   => esc_html__( 'Type List', 'mb-elementor-integrator' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'column',
			'options' => [
				'mb-columns' => 'Column',
				'mb-table'   => 'Table',
			],
		]);

		$this->add_responsive_control('mb_column', [
			'label'           => __( 'Columns', 'mb-elementor-integrator' ),
			'type'            => Controls_Manager::NUMBER,
			'devices'         => [ 'desktop', 'tablet', 'mobile' ],
			'desktop_default' => 3,
			'tablet_default'  => 2,
			'mobile_default'  => 1,
			'condition'       => [
				'mb_type_list' => 'mb-columns',
			],
			'selectors'       => [
				'{{WRAPPER}} .mb-column' => 'flex: 1 1 calc(100%/{{SIZE}}); max-width: calc(100%/{{SIZE}});',
			],
		]);

		$this->end_controls_section();

		// Paging
		$this->start_controls_section('section_mbpaging', [
			'label' => esc_html__( 'Pagination', 'mb-elementor-integrator' ),
		]);

		$this->add_control('mb_limit', [
			'label'   => __( 'Limit', 'mb-elementor-integrator' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => '0',
		]);

		$this->add_control('mb_pagination', [
			'label'   => esc_html__( 'Pagination', 'mb-elementor-integrator' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '',
			'options' => [
				''                      => 'None',
				'numbers'               => 'Numbers',
				'prev_next'             => 'Previous/Next',
				'numbers_and_prev_next' => 'Numbers + Previous/Next',
			],
		]);

		$this->add_control('mb_pagination_prev', [
			'label'     => __( 'Text Prev', 'mb-elementor-integrator' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => '< Prev',
			'conditions' => [
                'relation' => 'or',
                'terms'    => [
                    [
                    'name'     => 'mb_pagination',
                    'operator' => '==',
                     'value'   => 'prev_next',
                    ],
                    [
                    'name'     => 'mb_pagination',
                    'operator' => '==',
                     'value'   => 'numbers_and_prev_next',
                    ]                    
                ],
			],
		]);

		$this->add_control('mb_pagination_next', [
			'label'     => __( 'Text Next', 'mb-elementor-integrator' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => 'Next >',
			'conditions' => [
                'relation' => 'or',
                'terms'    => [
                    [
                    'name'     => 'mb_pagination',
                    'operator' => '==',
                     'value'   => 'prev_next',
                    ],
                    [
                    'name'     => 'mb_pagination',
                    'operator' => '==',
                     'value'   => 'numbers_and_prev_next',
                    ]                    
                ],
			],
		]);

		$this->end_controls_section();
	}

	/**
	 * Render Term List widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.1
	 * @access protected
	 */
	protected function render() {
		global $post;
		$data_groups = [];

		$settings = $this->get_settings_for_display();
		if ( ! empty( $settings['field-group'] ) ) {
			$data_groups = rwmb_get_value( $settings['field-group'], $post->ID );
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
			foreach ( $settings['map-field-group'] as $column ) {
				$subfield      = explode( ':', $column['subfield'] )[1];
				$data_column[] = [
					'type'      => ( false !== strpos( $subfield, 'link' ) || false !== strpos( $subfield, 'url' ) ) ? 'url' : 'text',
					'field'     => explode( ':', $column['subfield'] )[1],
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

	protected function content_template() {
		parent::content_template();
	}

}
