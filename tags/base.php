<?php
use Elementor\Controls_Manager;
use ElementorPro\Modules\ThemeBuilder\Module;

trait MB_Elementor_Integrator_Base {
	/**
	 * Object type: post, archive.
	 *
	 * @var string
	 */
	protected $object_type = 'post';

	/**
	 * Object type: post, term or setting.
	 *
	 * @var string
	 */
	protected $object_field = 'post';

	public function get_title() {
		return __( 'Meta Box Field', 'mb-elementor-integrator' );
	}

	public function get_group() {
		$type = self::handle_get_group();
		return $type;
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	public function is_settings_required() {
		return true;
	}

	protected function _register_controls() {
		$this->add_control( 'key', [
			'label'   => __( 'Field', 'mb-elementor-integrator' ),
			'type'    => Controls_Manager::SELECT,
			'options' => $this->get_fields(),
		] );

		// For MB Settings Page extension.
		if ( ! function_exists( 'mb_settings_page_load' ) ) {
			return;
		}

		$this->add_control(
			'option_name',
			[
				'label'       => __( 'Option name', 'mb-elementor-integrator' ),
				'type'        => 'text',
				'placeholder' => __( 'Enter your page option here', 'mb-elementor-integrator' ),
			]
		);
	}

	protected function get_fields() {
		$fields         = $this->get_all_fields();
		$fields_setting = $this->get_all_fields_setting();

		$options = [
			'' => __( '---', 'mb-elementor-integrator' ),
		];

		foreach ( $fields as $list ) {
			foreach ( $list as $field ) {
				$options[ $field['id'] ] = $field['name'] ? $field['name'] : $field['id'];
			}
		}

		if ( $fields_setting ) {
			foreach ( $fields_setting as $list ) {
				foreach ( $list as $field ) {
					$options[ $field['id'] ] = $field['name'] ? $field['name'] : $field['id'];
				}
			}
		}

		return $options;
	}

	protected function get_all_fields() {
		$type = self::handle_get_group();
		if ( $type != $this->object_type ) {
			$this->object_field = 'term';
		}

		$fields = rwmb_get_registry( 'field' )->get_by_object_type( $this->object_field );
		$fields = array_map( [ $this, 'remove_unsupported_fields' ], $fields );

		return array_diff_key( $fields, array_flip( ['mb-post-type', 'mb-taxonomy'] ) );

		// Get supported post types for the current layout.
		// $post_types = $this->get_template_post_types();
		// $post_types = $post_types ?: ['post'];

		// foreach ( $fields as $post_type => $list ) {
		// 	if ( ! in_array( $post_type, $post_types ) ) {
		// 		unset( $fields[ $post_type ] );
		// 	}
		// }

		// return $fields;
	}

	protected function get_all_fields_setting() {
		if ( ! function_exists( 'mb_settings_page_load' ) ) {
			return;
		}

		$fields = rwmb_get_registry( 'field' )->get_by_object_type( 'setting' );

		return array_map( [ $this, 'remove_unsupported_fields' ], $fields );
	}

	private function remove_unsupported_fields( $fields ) {
		return array_filter( $fields, [ $this, 'is_supported' ] );
	}

	private function is_supported( $field ) {
		if ( in_array( $field['type'], [ 'heading', 'divider', 'custom_html', 'button' ], true ) ) {
			return false;
		}
		$supported_fields = $this->get_supported_fields();
		return empty( $supported_fields ) || in_array( $field['type'], $supported_fields );
	}

	private function get_supported_fields() {
		return [];
	}

	protected function handle_get_group() {
		if ( ! isset( $_GET['post'] ) ) {
			return;
		}

		$type = get_post_meta( $_GET['post'], '_elementor_template_type', true );
		if ( 'archive' === $type ) {
			return $type;
		}

		return $this->object_type;
	}

	protected function handle_get_value() {
		$object = $this->get_object();
		return rwmb_meta( $this->get_settings( 'key' ), array( 'object_type' => $object['object_type'] ), $object['id'] );
	}

	protected function get_object() {
		$object = array(
			'object_type' => 'post',
			'id'          => get_queried_object_id(),
		);

		if ( ! empty( get_queried_object()->term_id ) ) {
			$object['object_type'] = 'term';
		}

		if ( $this->get_settings( 'option_name' ) ) {
			$object['object_type'] = 'setting';
			$object['id']          = $this->get_settings( 'option_name' );
		}

		return $object;
	}

	/**
	 * Get post types that the current template supports.
	 * Used for templates: singular pages, post type archive, taxonomy page.
	 *
	 * @return array
	 */
	private function get_template_post_types() {
		$template_type = get_post_meta( get_the_ID(), '_elementor_template_type', true );
		if ( $template_type != 'single' ) {
			return [];
		}

		$theme_builder_module = Module::instance();
		$document             = $theme_builder_module->get_document( get_the_ID() );
		if ( ! $document ) {
			return [];
		}

		$conditions = $document->get_main_meta( '_elementor_conditions' );
		if ( ! is_array( $conditions ) ) {
			return [];
		}

		$post_types = [];
		foreach ( $conditions as $condition ) {
			$post_types[] = $this->get_post_type_from_condition( $condition );
		}

		return $post_types;
	}

	private function get_post_type_from_condition( $condition ) {
		list ( $form, $type, $post_type, $sub_id ) = array_pad( explode( '/', $condition ), 4, '' );

		return $post_type;
	}
}