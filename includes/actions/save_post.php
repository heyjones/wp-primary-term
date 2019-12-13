<?php
/**
 * Save post meta.
 *
 * @package PrimaryTerm
 */

namespace PrimaryTerm\SavePost;

add_action( 'save_post', __NAMESPACE__ . '\\save_post', 10, 2 );

function save_post( $post_id, $post ) {

    // Lets loop through the post data to find our prefixed fields
    foreach( $_POST as $key => $value ) {

        // If the key starts with our prefix...
        if( strpos( $key, 'primary-term_' ) === 0 ) {

            // Lets get the taxonomy and make sure the term is assigned to the post.
            $taxonomy = str_replace( 'primary-term_', '', $key );

            // If the term is assigned to the post...
            if( $value && has_term( $value, $taxonomy, $post ) ) {

                // ... then lets sanitize the value and save it.
                $sanitized_value = sanitize_meta( $key, $value, 'post', $post->post_type );
                update_post_meta( $post_id, $key, $sanitized_value );

            } else {

                // ... unless it's empty, then lets delete it.
                delete_post_meta( $post_id, $key );
            }

        }

    }

}