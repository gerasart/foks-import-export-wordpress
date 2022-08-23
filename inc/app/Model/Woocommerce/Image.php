<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Woocommerce;

class Image
{
    /**
     * @param string $file
     * @param int|null $parentPostId
     *
     * @return bool|int|\WP_Error
     */
    public static function upload(string $file, int $parentPostId = null)
    {
        $filename = basename($file);
        $isExist = self::exist($filename);

        $uploadFile = wp_upload_bits($filename, null, file_get_contents($file));
        if (!$uploadFile['error'] && !$isExist) {
            $wp_filetype = wp_check_filetype($filename);

            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
                'post_status' => 'inherit'
            );

            if ($parentPostId) {
                $attachment['post_parent'] = $parentPostId;
            }

            $attachmentId = wp_insert_attachment($attachment, $uploadFile['file'], $parentPostId);

            if (!is_wp_error($attachmentId)) {
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata($attachmentId, $uploadFile['file']);
                wp_update_attachment_metadata($attachmentId, $attachment_data);

                return $attachmentId;
            }

            return false;
        }

        if ($isExist) {
            if ($parentPostId) {
                $attachment = [
                    'ID' => true,
                    'post_parent' => $parentPostId
                ];
                wp_update_post($attachment);
            }

            return true;
        }

        return false;
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public static function exist(string $filename): bool
    {
        $exp = explode('.', $filename);
        $title = array_shift($exp);

        return self::getImageIdByTitle($title);
    }

    /**
     * @param string $title
     * @param string $return
     *
     * @return null|string
     */
    public static function getImageIdByTitle(string $title, string $return = 'ID')
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
     * @param int $productId
     * @param array|null $images
     *
     * @return void
     */
    public static function addImages(int $productId, ?array $images): void
    {
        if (isset($images[0])) {

            self::addThumb($productId, $images[0]);

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
     * @param string|null $url
     * @param int $productId
     *
     * @return int
     */
    public static function getAttachmentIdFromUrl(?string $url, int $productId): int
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

                $id = wc_rest_set_uploaded_image_as_attachment($upload, $productId);

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
