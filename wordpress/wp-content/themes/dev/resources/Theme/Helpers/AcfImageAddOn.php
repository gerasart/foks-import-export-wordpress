<?php
/**
 * Created by PhpStorm.
 * User: skipin
 * Date: 13.07.18
 * Time: 12:51
 */

namespace Theme\Helpers;


class AcfImageAddOn {

    public static function init() {
        $action = 'upload_image';
        add_action('wp_ajax_' . $action, array(__CLASS__, 'ajaxUploadImage'));
    }

    public static function ajaxUploadImage() {
        $url = $_POST['file_url'];
        $post_id = $_POST['post_id'];

        $attachment_id = self::upload($url, $post_id);

        wp_send_json_success(array('attachment_id' => $attachment_id));

        wp_die();
    }

    public static function upload($file, $parent_post_id = false) {
        $filename = basename($file);
        $exist = self::exist($filename);

        $upload_file = wp_upload_bits($filename, null, file_get_contents($file));
        if (!$upload_file['error'] && !$exist) {
            $wp_filetype = wp_check_filetype($filename, null);

            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
                'post_status' => 'inherit'
            );

            if ($parent_post_id) {
                $attachment['post_parent'] = $parent_post_id;
            }

            $attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $parent_post_id);

            if (!is_wp_error($attachment_id)) {
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);

                return $attachment_id;
            } else {
                return false;
            }
        } elseif ($exist) {
            if ($parent_post_id) {
                $attachment = array(
                    'ID' => $exist,
                    'post_parent' => $parent_post_id
                );
                wp_update_post( $attachment );
            }

            return $exist;
        }

        return false;
    }

    public static function exist($filename) {
        $exp = explode('.', $filename);
        $title = array_shift($exp);

        return self::get_by_title($title);
    }

    public static function get_by_title($title, $return = 'ID') {
        global $wpdb;

        $attachments = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_title = '$title' AND post_type = 'attachment' ", OBJECT);
        //print_r($attachments);
        if ($attachments) {
            $attachment = $attachments[0]->$return;
        } else {
            return false;
        }

        return $attachment;
    }

}
