<?php
/**
 * Add "primary" to the list of available query vars.
 *
 * @package PrimaryTerm
 */

namespace PrimaryTerm\QueryVars;

add_filter( 'query_vars', __NAMESPACE__ . '\\query_vars' );

function query_vars( $vars ) {
    $vars[] .= 'primary';
    return $vars;
}