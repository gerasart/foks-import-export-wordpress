<?php


namespace Theme\Helpers;


class AdjacentPosts {

    public static function getAdjacentPosts($same = true, $loop = false, $args = [], $post_id = false) {
        $default = [
            'post_type' => 'post',
            'post_per_page' => -1,
            'fields' => 'ids',
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish',
        ];
        $args = array_merge($default, $args);
        $post_id = $post_id ? $post_id : get_the_ID();

        if ( $args['post_type'] === 'post' && $same ) {
            $terms = wp_get_object_terms($post_id, 'category', ['fields' => 'ids']);

            // Remove any exclusions from the term array to include.
            $term_array = array_diff( $terms, [1] );
            $term_array = array_map( 'intval', $term_array );

            $args['category__in'] = $term_array;
        }


        $posts = get_posts($args);
        return [
            'prev' => self::getAdjacent($posts, $post_id, true, $loop),
            'next' => self::getAdjacent($posts, $post_id, false, $loop),
        ];
    }

    public static function getAdjacent($posts, $post_id, $prev = true, $loop = false) {
        $current = array_search($post_id, $posts);
        if ( count($posts) > 1 ) {
            $last = count($posts) - 1;

            if ($current > 0 && $current < $last) {
                if ($prev) {
                    return $posts[$current - 1];
                } else {
                    return $posts[$current + 1];
                }
            } elseif ($current == $last) {
                if ($prev) {
                    return $posts[$current - 1];
                } elseif ($loop) {
                    return $posts[0];
                }
            } elseif (!$current) {
                if ($prev && $loop) {
                    return $posts[$last];
                } elseif ( !$prev ) {
                    return $posts[$current + 1];
                }
            }
        }

        return false;
    }

}
