<?php

namespace Controllers;

use Controllers\Traits\Localize;
use Sober\Controller\Controller;

class App extends Controller {

    use Localize;

//    protected $acf = true;

    public function siteName() {
        return get_bloginfo( 'name' );
    }

    public static function title() {
        if ( is_home() ) {
            if ( $home = get_option( 'page_for_posts', true ) ) {
                return get_the_title( $home );
            }

            return __( 'Latest Posts', 'sage' );
        }
        if ( is_archive() ) {
            return get_the_archive_title();
        }
        if ( is_search() ) {
            return sprintf( __( 'Search Results for %s', 'sage' ), get_search_query() );
        }
        if ( is_404() ) {
            return __( 'Not Found', 'sage' );
        }

        return get_the_title();
    }

    public function options() {
        if ( function_exists( 'get_fields' ) ) {
            $acf = get_fields( 'options' );

            return json_decode( json_encode( $acf ) );
        } else {
            return false;
        }
    }

    public function themePath() {
        return get_template_directory_uri();
    }

    public function siteContacts() {
        if ( function_exists( 'get_fields' ) ) {
            $page_id = get_page_by_path( 'contacts' );
            $acf     = get_fields( $page_id );

            return json_decode( json_encode( $acf ) );
        } else {
            return false;
        }
    }

    public function childrenPage() {
        $posts = get_children( [
            'post_type'   => 'page',
            'post_parent' => get_the_ID(),
            'fields'      => 'ids',
        ] );

        $children = [];
        foreach ( $posts as $post_id ) {
            if ( function_exists( 'get_fields' ) ) {
                $subtitle = get_field( 'subtitle', $post_id );
            } else {
                $subtitle = false;
            }

            $children[] = (object) [
                'ID'        => $post_id,
                'title'     => get_the_title( $post_id ),
                'subtitle'  => $subtitle,
                'thumbnail' => get_the_post_thumbnail_url( $post_id, 'full' ),
                'permalink' => get_the_permalink( $post_id ),
            ];
        }

        return $children;
    }

}
