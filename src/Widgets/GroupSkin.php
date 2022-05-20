<?php

namespace MBEI\Widgets;

use Elementor\Widget_Base;
use ElementorPro\Modules\Posts\Skins\Skin_Base;
use Elementor\Controls_Manager;
use MBEI\Traits\Skin;
use MBEI\GroupField;
use Elementor\Plugin;
use Elementor\Core\DynamicTags\Manager;

class GroupSkin extends Skin_Base {

	use Skin;

	public function get_title() {
		return __( 'Meta Box Group Skin', 'mb-elementor-integrator' );
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

		$this->add_control( 'mb_skin_template', [
			'label'       => __( 'Select a Group Skin', 'mb-elementor-integrator' ),
			'description' => '<div style="text-align:center;"><a target="_blank" style="text-align: center;font-style: normal;" href="' . esc_url( admin_url( '/edit.php?post_type=elementor_library&tabs_group=theme&elementor_library_type=metabox_group_template' ) ) .
			'" class="elementor-button elementor-button-default elementor-repeater-add">' .
			__( 'Create/edit a Group Skin', 'mb-elementor-integrator' ) . '</a></div>',
			'type'        => Controls_Manager::SELECT2,
			'label_block' => true,
			'default'     => [],
			'options'     => $this->get_skin_template(),
		] );

		$this->register_divider_controls();
	}

	private function get_skin_template() {

		$cache_key = 'mbei_skin_template';
		$templates = wp_cache_get( $cache_key );
		if ( false === $templates ) {
			$templates = get_posts( [
				'post_type'   => 'elementor_library',
				'numberposts' => -1,
				'post_status' => 'publish',
				'tax_query'   => array(
					array(
						'taxonomy' => 'elementor_library_type',
						'field'    => 'slug',
						'terms'    => 'metabox_group_template',
					),
				),
			] );
			wp_cache_set( $cache_key, $templates );
		}

		$options = [ 0 => __( 'Select a template', 'mb-elementor-integrator' ) ];
		foreach ( $templates as $template ) {
			$options[ $template->ID ] = $template->post_title;
		}
		return $options;
	}

	protected function get_template() {
		$template = $this->get_instance_value( 'mb_skin_template' );
		$template = $this->get_current_ID( $template );

		if ( ! $template ) {
			return;
		}
		// Get elements settings.
		$document = Plugin::instance()->documents->get( $template );
		$settings = $document->get_elements_data();
		// Get data dynamic tags
		$dynamic_tags = [];
		$this->find_element_dynamic_tag( $settings, $dynamic_tags );
		$data_replace = $this->dynamic_tag_to_data( $dynamic_tags, Plugin::instance()->dynamic_tags );
		// Get Content Template.
		$content_template = Plugin::instance()->frontend->get_builder_content_for_display( $template );

		return [
			'data'    => $data_replace,
			'content' => $content_template,
		];
	}

	private function dynamic_tag_to_data( $dynamic_tags = [], Manager $dynamic_tags_mageger ) {
		if ( empty( $dynamic_tags ) ) {
			return [];
		}

		$data_replace = [];
		foreach ( $dynamic_tags as $dynamic_tag ) {
			// Chek $dynamic_tag is array.
			if ( is_array( $dynamic_tag ) ) {
				$data_replace = array_merge( $data_replace, $this->dynamic_tag_to_data( $dynamic_tag, $dynamic_tags_mageger ) );
				continue;
			}
			// Check $dynamic_tag is dynamic tag meta box.
			$tag_data = $dynamic_tags_mageger->tag_text_to_tag_data( $dynamic_tag );
			if ( false === strpos( $tag_data['name'], 'meta-box-' ) ) {
				continue;
			}
			// Get tag content.
			$tag_data_content = $dynamic_tags_mageger->get_tag_data_content( $tag_data['id'], $tag_data['name'], $tag_data['settings'] );
			$key              = explode( ':', $tag_data['settings']['key'] )[1];
			if ( isset( $tag_data_content['url'] ) ) {
				$data_replace[ $key ] = GroupField::change_url_ssl( $tag_data_content['url'] );
			} else {
				$data_replace[ $key ] = $tag_data_content;
			}
		}
		return $data_replace;
	}

	private function find_element_dynamic_tag( $settings, &$results = [] ) {
		if ( ! is_array( $settings ) ) {
			return;
		}

		foreach ( $settings as $elements ) {
			if ( isset( $elements['elType'] ) && $elements['elType'] === 'widget' && isset( $elements['settings']['__dynamic__'] ) ) {
				$results = array_merge_recursive( $results, $elements['settings']['__dynamic__'] );
				continue;
			}

			$this->find_element_dynamic_tag( $elements, $results );
		}
	}

	// Support multilang for WPML
	private function get_current_ID( $id ) {
		$newid = apply_filters( 'wpml_object_id', $id, 'elementor_library', true );
		return $newid ? $newid : $id;
	}

	private function render_header() {
		return '<div class="mbei-groups">';
	}

	private function render_footer() {
		return '</div>';
	}

	protected function render_loop_header() {
		return '<div class="mbei-group">';
	}

	protected function render_loop_footer() {
		return '</div>';
	}

	public function render() {
		$group_fields = new GroupField();
		$post         = $group_fields->get_current_post();

		$data_groups = [];
		$data_column = [];

		$settings = $this->parent->get_settings_for_display();
		if ( ! isset( $settings['field-group'] ) || empty( $settings['field-group'] ) ) {
			return;
		}

        //check group nested
        $field_group = (array)$settings['field-group'];
        if( strpos($settings['field-group'], '.') !== false ) {
            $field_group = explode( '.', $settings['field-group'] );
        }
        
		$data_groups = rwmb_meta( $field_group[ 0 ], [], $post->ID );
        array_shift( $field_group );
        $data_groups = $group_fields->get_value_nested_group( $data_groups, $field_group );

		if ( 0 === count( $data_groups ) ) {
			return;
		}

		if ( false === is_int( key( $data_groups ) ) ) {
			$data_groups = [ $data_groups ];
		}

		$fields      = $group_fields->get_field_group( $settings['field-group'] );
		$data_column = array_combine( array_column( $fields['fields'], 'id' ), $fields['fields'] );

		echo $this->render_header();
		if ( $this->get_instance_value( 'mb_skin_template' ) ) {
			$content_template = $this->get_template();
			$cols             = array_keys( $content_template['data'] );

            if (stripos(json_encode($cols),'.') !== false) {
                $tmp_cols = $group_fields->split_field_nested($cols);
            }
            
			foreach ( $data_groups as $k => $data_group ) {
				$check_cols = array_intersect( array_keys( $data_group ), isset($tmp_cols) ? $tmp_cols['cols'] : $cols );
				if ( 0 === count( $check_cols ) ) {
					continue;
				}

				$content = $this->render_loop_header() . $content_template['content'] . $this->render_loop_footer();
				foreach ( $cols as $col ) {
                    if ( !isset( $data_group[ $col ] ) && false === strpos( $col, '.' ) ) {
                        continue;
                    }
                    
                    if ( false !== strpos( $col, '.' ) ) {
                        $tmp_col = explode( '.', $col, 2 );
                        $sub_col = $tmp_col[0];
                        if ( !isset( $data_group[ $sub_col ] ) ) {
                            continue;
                        }
                        
                        $data_sub_column = array_combine( array_column( $data_column[ $sub_col ]['fields'], 'id' ), $data_column[ $sub_col ]['fields'] );
                        $data_sub_column = array_filter($data_sub_column, function($k) use($tmp_col) {
                            return $k == $tmp_col[1];
                        }, ARRAY_FILTER_USE_KEY);
                        
                        ob_start();
                        $this->parent->render_nested_group( $data_group[ $sub_col ], $data_sub_column, $group_fields );
                        $value = ob_get_contents();
                        ob_end_clean();
                        
                        //Display text from sub field group.
                        if ( !isset( $data_sub_column[ $tmp_col[1] ]['mime_type'] ) || 'image' !== $data_sub_column[ $tmp_col[1] ]['mime_type'] ) {
                            $content = str_replace( $content_template['data'][ $col ], $value, $content );
                            continue;
                        }
                        
                        //Display image from sub field group.
                        $search_data = [];
                        libxml_use_internal_errors(true);
                        $dom = new \DOMDocument();
                        $dom->loadHTML($content);
                        foreach ( $dom->getElementsByTagName( 'img' ) as $i => $img ) {
                            if ( false === strpos( $img->getAttribute( 'srcset' ), $content_template['data'][ $col ] ) ) {
                                continue;
                            }
                            $search_data = [
                                'html' => str_replace( '>', ' />', $dom->saveHTML( $img ) ),
                                'width' => 'width="'.$img->getAttribute( 'width' ).'"',
                                'height' => 'height="'.$img->getAttribute( 'height' ).'"',
                                'class' => 'class="'.$img->getAttribute( 'class' ).'"'
                            ];
                        }       

                        if ( empty( $search_data ) ) {
                            continue;
                        }                                               
                        
                        //Replace Attribute Image
                        $domNew = new \DOMDocument();
                        $domNew->loadHTML($value);
                        foreach ( $domNew->getElementsByTagName( 'img' ) as $i => $img ) {
                            $value = str_replace([
                                'width="'.$img->getAttribute( 'width' ).'"',
                                'height' => 'height="'.$img->getAttribute( 'height' ).'"',
                                'class="'.$img->getAttribute( 'class' ).'"'
                            ], [
                                $search_data['width'],
                                $search_data['height'],
                                $search_data['class']
                            ], $value);
                        }
                        
                        $content = str_replace( $search_data['html'], $value, $content );
                        continue;                        
                    }                    
                    
                    //Get content field group
                    if( is_array( $data_group[ $col ] ) && !empty( $data_group[ $col ] ) ) {
                        $data_sub_column = array_combine( array_column( $data_column[ $col ]['fields'], 'id' ), $data_column[ $col ]['fields'] );
                        
                        ob_start();
                        $this->parent->render_nested_group( $data_group[ $col ], $data_sub_column, $group_fields );
                        $value = ob_get_contents();
                        ob_end_clean(); 
                        
                        $content = str_replace( $content_template['data'][ $col ], $value, $content );
                        continue;                        
                    }
                    

//                    if( isset( $data_group[ $col ] ) && is_array( $data_group[ $col ] ) && !empty( $data_group[ $col ] ) ) {
//                        $data_column[ $col ]['fields'] = array_combine( array_column( $data_column[ $col ]['fields'], 'id' ), $data_column[ $col ]['fields'] );
//                        
//                        if ( isset( $tmp_cols ) ) {
//                            if ( isset( $tmp_cols['sub_cols'][ $col ] ) && count( $data_column[ $col ]['fields'] ) !== count( $tmp_cols['sub_cols'][ $col ] ) ) {
//                                foreach ( $data_column[ $col ]['fields'] as $k => $v ) {
//                                    if ( in_array( $k, $tmp_cols['sub_cols'][ $col ] ) ){
//                                        continue;
//                                    }
//                                    unset( $data_column[ $col ]['fields'][ $k ] );
//                                }
//                            }
//                        }                       
//
//                        ob_start();
//                        $this->parent->render_nested_group( $data_group[ $col ], $data_column[ $col ]['fields'], $group_fields );
//                        $value = ob_get_contents();
//                        ob_end_clean();
//                        
//                        if ( !isset( $tmp_cols ) ) {
//                            $content = str_replace( $content_template['data'][ $col ], $value, $content );
//                            continue;
//                        }
//                        
//                        print_r($content_template['data'][ $col ]);die();
//                        
//                        if ( !isset( $tmp_cols['sub_cols'][ $col ] ) ) {
//                            continue;
//                        }
//                        
//                        foreach ( $tmp_cols['sub_cols'][ $col ] as $k => $v ) {
//                            if ( !isset( $content_template['data'][ "$col.$v" ] ) ){
//                                continue;
//                            }
//                            $content = str_replace( $content_template['data'][ "$col.$v" ], $value, $content );
//                        }
//                        continue;
//                    }                    

                    ob_start();
                    $group_fields->display_field( $data_group[ $col ], $data_column[ $col ], true );
                    $value = ob_get_contents();
                    ob_end_clean();

                    $content = str_replace( $content_template['data'][ $col ], $value, $content );
				}
				echo $content;
			}
		} else {
			?>
			<?php foreach ( $data_groups as $data_group ) : ?>
				<div class="mbei-group">
					<?php foreach ( $data_group as $key => $value ) : ?>
						<div class="mbei-subfield mbei-subfield--<?= $key; ?>">
                            <?php
                                if( is_array( $value ) && !empty( $value ) ) {
                                    $data_column[ $key ]['fields'] = array_combine( array_column( $data_column[ $key ]['fields'], 'id' ), $data_column[ $key ]['fields'] );
                                    $this->parent->render_nested_group( $value, $data_column[ $key ]['fields'], $group_fields );
                                    continue;
                                }
                                $group_fields->display_field( $value, $data_column[ $key ] );
                            ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>                    
			<?php
		}
		echo $this->render_footer();
	}

}
