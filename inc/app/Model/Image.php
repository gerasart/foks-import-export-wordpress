<?php
/**
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model;

class Image
{
    /**
     * @param $file
     * @param $parentPostId
     * @return bool|int|\WP_Error
     */
    public static function upload($file, $parentPostId = null)
    {
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

            if ($parentPostId) {
                $attachment['post_parent'] = $parentPostId;
            }

            $attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $parentPostId);

            if (!is_wp_error($attachment_id)) {
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);

                return $attachment_id;
            }

            return false;

        }

        if ($exist) {
            if ($parentPostId) {
                $attachment = array(
                    'ID' => $exist,
                    'post_parent' => $parentPostId
                );
                wp_update_post($attachment);
            }

            return $exist;
        }

        return false;
    }

    /**
     * @param $filename
     * @return bool
     */
    public static function exist($filename): bool
    {
        $exp = explode('.', $filename);
        $title = array_shift($exp);

        return self::getImageIdByTitle($title);
    }

    /**
     * @param $title
     * @param string $return
     * @return null|string
     */
    public static function getImageIdByTitle($title, string $return = 'ID')
    {
        global $wpdb;

        $attachments = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_title = '$title' AND post_type = 'attachment' ", OBJECT);

        if ($attachments) {
            $attachment = (string)$attachments[0]->$return;
        } else {
            return false;
        }

        return $attachment;
    }


    /**
     * @param $productId
     * @param $images
     * @return void
     */
    public static function addImages($productId, $images): void
    {
        if (isset($images[0])) {

            self::addThumb((int)$productId, $images[0]);

            if (isset($images[1])) {
                $i = 0;
                $updated_gallery_ids = [];

                foreach ($images as $img) {
                    $i++;

                    if (($i > 1) && $img) {
                        $updated_gallery_ids[] = self::getAttachmentIdFromUrl($img, $productId);
                    }
                }

                update_post_meta($productId, '_product_image_gallery', implode(',', $updated_gallery_ids));
            }
        }
    }

    /**
     * @param int $productId
     * @param string $image
     * @return void
     */
    public static function addThumb(int $productId, string $image): void
    {
        $id = self::getAttachmentIdFromUrl($image, $productId);
        update_post_meta($productId, '_thumbnail_id', $id);
    }

    /**
     * @param $url
     * @param $product_id
     *
     * @return int|mixed
     */
    public static function getAttachmentIdFromUrl($url, $product_id)
    {
        if (empty($url)) {
            return 0;
        }

        $id = 0;
        $upload_dir = wp_upload_dir(null, false);
        $base_url = $upload_dir['baseurl'] . '/';

        // Check first if attachment is inside the WordPress uploads directory, or we're given a filename only.
        if (false === strpos($url, $base_url) && false !== strpos($url, '://')) {
            $pathInfo = pathinfo($url);
            $title = $pathInfo['filename'];

            // Does the attachment already exist ?
            if (post_exists($title)) {
                $attachment = get_page_by_title($title, OBJECT, 'attachment');

                if (!empty($attachment)) {
                    $id = $attachment->ID;
                }
            }

            // Upload if attachment does not exists.
            if (!$id && strpos($url, '://') !== false) {
                $upload = wc_rest_upload_image_from_url($url);

                if (is_wp_error($upload)) {
                    return $id;
                }

                $id = wc_rest_set_uploaded_image_as_attachment($upload, $product_id);

                // Save attachment source for future reference.
                if ($id) {
                    update_post_meta($id, '_wc_attachment_source', $url);
                }
            }

            return $id;
        }

        return $id;
    }
}
