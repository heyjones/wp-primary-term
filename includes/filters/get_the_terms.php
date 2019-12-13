<?php
/**
 * Modify the object term order.
 *
 * @package PrimaryTerm
 */

namespace PrimaryTerm\GetTheTerms;

add_filter( 'get_the_terms', __NAMESPACE__ . '\\get_the_terms', 10, 3 );

function get_the_terms( $terms, $post_id, $taxonomy ){

    // Get the primary term... I couldn't get the single quotes out of the $taxonomy variable for some reason. :/
    $key = 'primary-term_' . str_replace( '\'', '', $taxonomy );
    $term_id = get_post_meta( $post_id, $key, true );

    // If a primary term exists...
    if( $term_id ){

        // ... lets create an empty term object and remove it. Again, could spend more time to find a better way to do this but (╯°□°)╯︵ ┻━┻
        $term = (object) array();
        foreach( $terms as $key => $value ){
            if( $term_id == $value->term_id ){
                $term = $value;
                unset( $terms[$key] );
                break;
            }
        }

        // Now lets put it back at the top of the array.
        if( ! empty ( $term ) ){
            array_unshift( $terms, $term );
        }
    
    }

    return $terms;

}