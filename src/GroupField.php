<?php

namespace MBEI;

class GroupField {

    public static function get_current_post() {
        global $post, $wp_query;

        if (!empty($post)) {
            return $post;
        }

        list($post_type, $slug) = explode('/', $wp_query->query['pagename']);
        $current_post = get_page_by_path($slug, OBJECT, $post_type);
        return $current_post;
    }

    public static function parse_options($fields = [], $field_group_id) {
        if (empty($fields) || !isset($fields['fields']) || empty($fields['fields'])) {
            return [];
        }

        $sub_fields = [];
        foreach ($fields['fields'] as $field) {
            $sub_fields[$field_group_id . ':' . $field['id']] = $field['name'];
        }
        
        return $sub_fields;
    }

    public static function get_field_group($key = null) {
        $field_registry = rwmb_get_registry('field');
        $post_types = $field_registry->get_by_object_type('post');

        $return_fields = [];
        if (0 < count($post_types)) {
            foreach ($post_types as $fields) {
                // Fields is empty
                if (0 === count($fields)) {
                    continue;
                }
                // get list field type=group
                $group_fields = self::get_field_type_group($fields);
                if (0 === count($group_fields)) {
                    continue;
                }

                foreach ($group_fields as $group_field) {
                    if (!empty($key) && $key !== $group_field['id']) {
                        continue;
                    }

                    if (!empty($key) && $key === $group_field['id']) {
                        array_push($return_fields, $group_field);
                    } else {
                        array_push($return_fields, $group_field);
                    }
                }
            }
        }

        if (!empty($key) && 0 < count($return_fields)) {
            return $return_fields[0];
        }

        return array_filter($return_fields);
    }

    public static function get_list_field_group() {
        $fields = self::get_field_group();
        $list = [];
        foreach ($fields as $field) {
            $list[$field['id']] = $field['name'];
        }
        return $list;
    }

    /**
     * Check Type field group
     * @param array $fields
     * @return array $return_fields fields of type group
     */
    private static function get_field_type_group($fields) {
        // not field type is group.
        $is_field_group = array_search('group', array_column($fields, 'type'));
        if (false === $is_field_group) {
            return [];
        }

        $return_fields = [];
        foreach ($fields as $field) {
            if ('group' === $field['type'] && isset($field['clone']) && true === $field['clone']) {
                $return_fields[] = $field;
            }
        }

        return $return_fields;
    }

    public static function pagination($options = []) {
        extract($options);
        $path_file = plugin_dir_path(__DIR__) . 'Templates/pagination-' . $type . '.php';

        if (file_exists($path_file)) {
            require $path_file;
        }
    }

    public static function display_field($data, $data_field = []) {
        extract($data_field);
        $file_type = 'text';
        switch ($type) {
            case 'text':
            case 'textarea':
            case 'number':
            case 'wysiwyg':
            case 'email':
            case 'select':
            case 'select_advanced':
                $file_type = 'text';
                break;
            case 'image':
            case 'image_advanced':
            case 'image_select':
            case 'image_upload':
            case 'single_image':
                $file_type = 'image';
                break;
            default:
                $file_type = $type;
                break;
        }

        $path_file = plugin_dir_path(__DIR__) . 'src/Templates/display_field-' . $file_type . '.php';

        if (file_exists($path_file)) {
            require $path_file;
        }
    }

}
