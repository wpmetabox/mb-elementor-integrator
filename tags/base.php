<?php
use Elementor\Controls_Manager;

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

		if ( ! mbb_is_extension_active( 'mb-settings-page' ) ) {
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

		// Remove fields that don't have value.
		array_walk( $fields, function ( &$list ) {
			$list = array_filter( $list, function( $field ) {
				if ( in_array( $field['type'], array( 'heading', 'divider', 'custom_html', 'button' ), true ) ) {
					return false;
				}
				$supported_fields = $this->get_supported_fields();
				if ( $supported_fields && ! in_array( $field['type'], $supported_fields ) ) {
					return false;
				}
				return true;
			} );
		} );
		return $fields;
	}

	protected function get_all_fields_setting() {
		if ( ! mbb_is_extension_active( 'mb-settings-page' ) ) {
			return;
		}

		$fields = rwmb_get_registry( 'field' )->get_by_object_type( 'setting' );
		// Remove fields that don't have value.
		array_walk( $fields, function ( &$list ) {
			$list = array_filter( $list, function( $field ) {
				if ( in_array( $field['type'], array( 'heading', 'divider', 'custom_html', 'button' ), true ) ) {
					return false;
				}
				$supported_fields = $this->get_supported_fields();
				if ( $supported_fields && ! in_array( $field['type'], $supported_fields ) ) {
					return false;
				}
				return true;
			} );
		} );
		return $fields;
	}

	protected function get_supported_fields() {
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

	protected function handle_get_value( $field_id, $id ) {
		if ( ! $this->get_settings( 'option_name' ) ) {
			return rwmb_meta( $field_id, $id );
		}

		return rwmb_meta( $field_id, array( 'object_type' => 'setting' ), $this->get_settings( 'option_name' ) );
	}
}