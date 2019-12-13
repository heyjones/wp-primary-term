<?php
/**
 * Register post meta.
 *
 * @package PrimaryTerm
 */

namespace PrimaryTerm\RegisterPostMeta;

add_action( 'init', __NAMESPACE__ . '\\init', 9999 );

/**
 * Register a meta key for each post type and taxonomy.
 *
 * @return null
 */
function init() {
    // Get each post type
    $post_types = get_post_types();
    foreach( (array) $post_types as $post_type ) {
        // Get the taxonomies associated with the post type
        $taxonomies = get_object_taxonomies( $post_type );
        foreach( (array) $taxonomies as $taxonomy ) {
            // Register the post meta
            register_post_meta(
                $post_type,
                'primary-term_' . $taxonomy,
                array(
                    'type'         => 'integer',
                    'single'       => true,
                    'show_in_rest' => true,
                )
            );
        }
    }
}