<?php
/**
 * Plugin Name:     Identify Headings
 * Plugin URI:      https://secret-source.eu/plugins/identify-headings
 * Description:     Add ID attributes to all headings, paragraph, and list elements in the content body
 * Author:          Ted Stresen-Reuter
 * Author URI:      https://secret-source.eu
 * Text Domain:     identify-headings
 * Domain Path:     /languages
 * Version:         1.0.8
 *
 * @package         Identify_Headings
 */

/**
 * This is similar to https://jeroensormani.com/automatically-add-ids-to-your-headings/
 * However, there are problems with the code:
 *  1. Creates duplicate IDs if heading text is duplicated on the page
 *  2. False positive on date-id attributes
 *  3. False positive on atypical attribute definitions such as 'id = "'
 *  4. Creates duplicate class attributes if a class attribute already exists
 */

/**
 * The name of our custom class for displaying the link icon
 */
if ( ! defined( 'IHIHIH_CLASS' ) ) {
	define( 'IHIHIH_CLASS', 'ihihih-linkifier' );
}

/**
 * Adding IDs to heading, paragraph, and list elements in the content
 */
add_filter( 'the_content', 'ihihih_get_identifiable_elements_in_the_content', 10, 1 );

/**
 * Adds IDs and classes to the headings, paragraphs, and list elements in post_content
 *
 * Array structure is as follows:
 *  0: full match
 *  1: element name
 *  2: attributes list (string)
 *  3: value (prepended with a >, which is the closing tag symbol)
 *
 * @param string $input The post_content.
 * @return string post_content with IDs added to the headings, paragraphs, and list elements.
 */
if( ! function_exists( 'ihihih_get_identifiable_elements_in_the_content' ) ) {
	function ihihih_get_identifiable_elements_in_the_content( $input ) {
	   $output  = $input;
	   $pattern = '@<(h[1-6]|p|ul|ol)([^>]*)(>.*?)</\\1>@is';
	   $matches = preg_match_all( $pattern, $input, $p );
	   if ( $matches > 0 ) {
		   $output = ihihih_add_attributes_to_elements( $output, $p );
	   }
	   return $output;
   }
}


/**
 * Adds IDs and classes to all instances of the specified element
 *
 * @param string $input The post content.
 * @param array  $p The "pieces" of preg_match_all(). See get_identifiable_elements_in_the_content for structure.
 *
 * @return string The input with IDs added to all instances of the specified element.
 */
if( ! function_exists( 'ihihih_add_attributes_to_elements' ) ) {
	function ihihih_add_attributes_to_elements( string $input, array $p ) {
	   $output  = $input;
	   $matches = count( $p[0] );
	   for ( $i = 0; $i < $matches; $i++ ) {
		   $elem   = [
			   'match' => $p[0][ $i ],
			   'name'  => $p[1][ $i ],
			   'atts'  => $p[2][ $i ],
			   'value' => $p[3][ $i ],
			   'index' => $i,
		   ];
   
		   $elem_with_id = ihihih_add_id_to_element(
			   $elem
		   );
   
		   $elem_with_id_and_class = ihihih_add_class_to_element(
			   $elem_with_id
		   );
   
		   $output = preg_replace(
			   '/' . preg_quote( $elem['match'], '/' ) . '/is',
			   $elem_with_id_and_class['match'],
			   $output
		   );
	   }
	   return $output;
   }
}

/**
 * Adds an ID attribute to an element if one doesn't already exist
 *
 * @param array $elem A structured array consisting of a 'match', 'element name', 'attributes string', 'value', and 'index'.
 * @return array $output Same as the input only with an ID added to the attribute key if needed.
 */
if( ! function_exists( 'ihihih_add_id_to_element' ) ) {
	function ihihih_add_id_to_element( array $elem ) {
	   $hasid  = (bool) preg_match( '@ id\s*=\s*@is', $elem['atts'] );
	   $output = $elem;
	   if ( false === $hasid ) {
		   $id              = 'id-' . substr( sanitize_title( $elem['value'] ), 0, 50 ) . "-{$elem['index']}";
		   $output['atts']  = ' id="' . $id . '"' . $elem['atts'];
		   $output['match'] = '<' . $elem['name'] . $output['atts'] . $elem['value'] . '</' . $elem['name'] . '>';
	   }
	   return $output;
   }
}

/**
 * Adds a class attribute to an element if one doesn't already exist
 *
 * @param array $elem A structured array consisting of a 'match', 'element name', 'attributes string', 'value', and 'index'.
 * @return array $output Same as the input only with a class added to the attribute key if needed.
 */
if ( ! function_exists( 'ihihih_add_class_to_element' ) ) {
	function ihihih_add_class_to_element( array $elem ) {
	   $output   = $elem;
	   $hasclass = (bool) preg_match( '@ class\s*=\s*(\'|")([^\\1]*?)(\'|")@is', $elem['atts'], $pcs );
   
	   // Remove the class attribute from the attributes string.
	   if ( $hasclass ) {
		   $output['atts'] = str_replace( $pcs[0], '', $elem['atts'] );
	   }
   
	   // Remove our class if it exists so we don't accidentally duplicate it.
	   // This has lower cyclomatic complexity than testing for the class's existence and then acting accordingly.
	   $sanitized_classes = preg_replace( '/\b' . IHIHIH_CLASS . '\b/is', '', $pcs[2] ?? '' );
	   $classes           = preg_split( '/[\s]+/is', $sanitized_classes );
	   $classes[]         = IHIHIH_CLASS;
	   $output['atts']   .= ' class="' . trim( implode( ' ', $classes ) ) . '"';
	   $output['match']   = '<' . $elem['name'] . $output['atts'] . $elem['value'] . '</' . $elem['name'] . '>';
   
	   return $output;
   }
}
