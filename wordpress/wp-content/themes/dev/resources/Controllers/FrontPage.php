<?php

namespace Controllers;


use Sober\Controller\Controller;

class FrontPage extends Controller
{
    public function getPosts()
    {
        $args = [
            'post_type' => 'post',
            'posts_per_page' => 10,
            'order' => 'DESC'
        ];
        $posts = get_posts($args);
        $items = [];
        foreach ($posts as $post) {
            $items[] = (object)[
                'id' => $post->ID,
                'title' => get_the_title($post),
                'href' => get_permalink($post),
                'desc' => apply_filters('the_content', get_post_field('post_content', $post)),
                'img' => get_the_post_thumbnail_url($post),
                'category' => get_the_terms($post, 'category'),
            ];
        }
        
        return $items;
    }
}
