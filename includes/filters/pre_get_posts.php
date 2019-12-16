<?php
/**
 * Modify the main query.
 *
 * @package PrimaryTerm
 */

namespace PrimaryTerm\PreGetPosts;

add_filter( 'pre_get_posts', __NAMESPACE__ . '\\pre_get_posts' );

function pre_get_posts( $query ) {

    // Let's check to see if ?primary=true is in the querystring.
    $primary = get_query_var( 'primary' );

    // If it is, and if we're dealing with the main query...
    if( $primary && $query->is_main_query() ){

        // ... lets get our taxonomy and term.
        $queried_object = get_queried_object();
        $taxonomy = $queried_object->taxonomy;
        $term_id = $queried_object->term_id;

        // If they exist...
        if( isset ( $taxonomy ) && isset ( $term_id ) ){

            // ... then lets modify the meta query to only pull posts where the current term is the primary term.
            $meta_query = (array) $query->get( 'meta_query' );
            $meta_query[] = array(
                'key' => 'primary-term_' . $taxonomy,
                'type' => 'NUMERIC',
                'value' => $term_id,
                'compare' => '=='
            );
            $query->set( 'meta_query', array( $meta_query ) );

        }

    }

}