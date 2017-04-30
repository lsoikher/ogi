<?php
// Add custom Theme Functions here


/* Exclude Category from Shop*/

add_filter( 'get_terms', 'get_subcategory_terms', 10, 3 );

function get_subcategory_terms( $terms, $taxonomies, $args ) {

  $new_terms = array();

  // if a product category and on the shop page
  if ( in_array( 'product_cat', $taxonomies ) && ! is_admin() && is_shop() ) {

    foreach ( $terms as $key => $term ) {

      if ( ! in_array( $term->slug, array( 'deodorant-pack', 'deodorant-sample' , 'deodorants-3' , 'deodorants-4' , 'deodorants-2' , 'deodorants' ) ) ) {
        $new_terms[] = $term;
      }

    }

    $terms = $new_terms;
  }

  return $terms;
}
