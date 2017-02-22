<?php

/*
Plugin Name: PhotoPress - Gallery
Plugin URI: http://www.photopressdev.com
Description: Adds taxonomy query parameters to the gallery shortcode.
Author: Peter Adams
Version: 1.7
Author URI: http://www.photopressdev.com 
*/ 

/**
 * PhotoPress - Gallery
 *
 * Copyright Peter Adams - peter@photopressdev.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

/**
 * The Gallery shortcode.
 *
 * This implements a more flexible version of the core Gallery Shortcode for displaying
 * WordPress images on a post.
 *
 * Adds several addtional filters and a new shortcode attribute for containertag
 *
 * @param 	array 	$attr	Attributes of the shortcode.
 * @return 	string 			HTML content to display gallery.
 */
function photopress_gallery_shortcode( $output, $attr = array() ) {
		
		if ( ! $attr ) {
			
			$attr = array();
		}
		
        $post = get_post();
        static $instance = 0;
        $instance++;
        
        if ( ! empty( $attr['ids'] ) ) {
                // 'ids' is explicitly ordered, unless you specify otherwise.
                if ( empty( $attr['orderby'] ) )
                        $attr['orderby'] = 'post__in';
                $attr['include'] = $attr['ids'];
        }
        // Allow plugins/themes to override the default gallery template.
        //$output = apply_filters('post_gallery', '', $attr);
        if ( $output != '' )
                return $output;
        // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
        if ( isset( $attr['orderby'] ) ) {
                $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
                if ( !$attr['orderby'] )
                        unset( $attr['orderby'] );
        }
        
        $html5 = current_theme_supports( 'html5', 'gallery' );
        $core_attr = shortcode_atts( array(
                'order'      			=> 'ASC',
                'orderby'    			=> 'menu_order ID',
                'id'         			=> $post ? $post->ID : 0,
                'itemtag'    			=> $html5 ? 'figure'     : 'dl',
                'icontag'    			=> $html5 ? 'div'        : 'dt',
                'captiontag' 			=> $html5 ? 'figcaption' : 'dd',
                'columns'    			=> 3,
                'size'       			=> 'thumbnail',
                'include'    			=> '',
                'exclude'    			=> '',
                'link'					=> '',
                'type'					=> '', // new param can be used/filtered to other plugin authors
                'post_type'				=> 'attachment', // new param. can be any valid post_type
                'link_caption_text'		=> false, // new param. Links the caption text to the post's permalink
                'containertag'			=>	'div', // new param
                'container_class'		=> '', // new param
                'caption_source'		=> 'excerpt', // new param. values are: 'excerpt', 'none',
                'gallery_item_class' 	=> 'gallery-item', // new param. makes core class name configurable.
                'gallery_icon_class'	=> 'gallery-icon', // new param. makes core class name configurable.
                'gallery_caption_class'	=> 'wp-caption-text gallery-caption' // new param. makes core class name configurable.
                
        ), $attr, 'gallery');
        
        extract($core_attr);
        
        // This is needed so that downstream filters have access to the
        // combined set of supported core attrs and any others passed in on the shortcode
        $attr = array_merge( $core_attr, $attr );
        
        $id = intval($id);
        
        if ( 'RAND' == $order ) {
                $orderby = 'none';
        }
        // allow plugins to filter the returned attachment list
        $attachments = apply_filters( 'post_gallery_attachments', '', $attr );
        
        // if no attachments are returned by the plugin run the default queries
        if ( empty( $attachments ) ) {
        	
        	if ( !empty($include) ) {
                $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
                $attachments = array();
                foreach ( $_attachments as $key => $val ) {
                        $attachments[$val->ID] = $_attachments[$key];
				}
	        } elseif ( !empty($exclude) ) {
	                $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	        } else {
	                $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	        }
        }
                
        if ( empty( $attachments ) ) {
        	return '';
        }
        
        if ( is_feed() ) {
                $output = "\n";
                foreach ( $attachments as $att_id => $attachment )
                        $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
                return $output;
        }
        
        $containertag = tag_escape($containertag);
        $itemtag = tag_escape($itemtag);
        $captiontag = tag_escape($captiontag);
        $icontag = tag_escape($icontag);
        $valid_tags = wp_kses_allowed_html( 'post' );
        if ( ! isset( $valid_tags[ $containertag ] ) )
                $itemtag = 'div';
        if ( ! isset( $valid_tags[ $itemtag ] ) )
                $itemtag = 'dl';
        if ( ! isset( $valid_tags[ $captiontag ] ) )
                $captiontag = 'dd';
        if ( ! isset( $valid_tags[ $icontag ] ) )
                $icontag = 'dt';
        $columns = intval($columns);
        $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
        $float = is_rtl() ? 'right' : 'left';
        $selector = "gallery-{$instance}";
        $gallery_style = $gallery_div = '';
        
        if ( apply_filters( 'use_default_gallery_style',  ! $html5 ) ) {
                $gallery_style = "
                <style type='text/css'>
                        #{$selector} {
                                margin: auto;
                        }
                        #{$selector} .gallery-item {
                                float: {$float};
                                margin-top: 10px;
                                text-align: center;
                                width: {$itemwidth}%;
                        }
                        #{$selector} img {
                                border: 2px solid #cfcfcf;
                        }
                        #{$selector} .gallery-caption {
                                margin-left: 0;
                        }
                        /* see gallery_shortcode() in wp-includes/media.php */
                </style>\n\t\t";
              
				// allow plugin developers to append or replace the inline styles without
				// worrying about blowing away gallery div.
				$gallery_style = apply_filters( 'post_gallery_styles', $gallery_style, $selector, $attr );
        }
        
        $pre_output = apply_filters( 'post_gallery_pre_output', '', $selector, $attr );
        $size_class = sanitize_html_class( $size );
        $gallery_div = "<{$containertag} id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class} {$container_class}'>";
        $output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $pre_output . $gallery_div );
        
        $i = 0;
        foreach ( $attachments as $id => $attachment ) {
        		
        		// needed becuase icons for non-attachments need to come from featured image thumbnails.
        		if ( $post_type != 'attachment' ) {
        		
	        		$link = '<a href="'. get_permalink($id). '">'.get_the_post_thumbnail($id, $size).'</a>';
	        		
        		} else {
        			
            	    $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);
                }
                
                $gallery_item_properties = '';
                $output .= sprintf(
                	'<%s class="%s" %s>', 
                	$itemtag, 
                	apply_filters( 'gallery_item_class', $gallery_item_class, $attr, $attachment ), 
                	apply_filters( 'gallery_item_properties', $gallery_item_properties, $attr, $attachment )
                );
                
                $gallery_icon_properties = '';
                $icon_tag_html = sprintf(
                	'<%s class="%s" %s>', 
                	$icontag, 
                	apply_filters( 'gallery_icon_class', $gallery_icon_class, $attr, $attachment ), 
                	apply_filters( 'gallery_icon_properties', $gallery_icon_properties, $attr, $attachment )
                );
 
                $output .= "
                        $icon_tag_html
                                $link
                        </{$icontag}>";
                
                if ( $caption_source != 'none' ) {
                
                	$caption = wptexturize( apply_filters( 'post_gallery_caption', trim($attachment->post_excerpt), $attachment, $attr) );        
	                if ( $captiontag && $caption ) {
	                	
	                	$gallery_caption_properties = '';
						$gallery_caption_html = sprintf(
						
							'<%s class="%s" %s>%s</%s>',
							$captiontag, 
							apply_filters( 'gallery_caption_class', $gallery_caption_class, $attr, $attachment ),
							apply_filters( 'gallery_caption_properties', $gallery_caption_properties, $attr, $attachment ),
							$caption,
							$captiontag
						);
	                		
	                    $output .= "
	                                $gallery_caption_html";
	                }
	            }
	            
                $output .= "</{$itemtag}>";
                if ( $columns > 0 && ++$i % $columns == 0 )
                        $output .= '<br style="clear: both" />';
        }
        $output .= "
                        <br style='clear: both;' />
                </$containertag>\n";
                
        $output = apply_filters( 'post_gallery_post_output', $output, $selector, $attr );
        return $output;
}

/**
 * Filter callback for doing a taxonomy query for gallery attachments
 *
 * @param		array	$attachments	the array of attachments created by any upstream filters
 * @param		array	$attr			the attributed of the gallery shortcode
 * @return		array					an array of attachment post objects
 */
function photopress_gallery_taxonomy_query( $attachments, $attr ) {
	
	// discard any attachments we might get.
	$attachments = array();
	
	if ( ! $attr ) {
		
		$attr = array();
	}	
	
	extract(  $attr  );
	
	// if there are taxonomy oriented attrs then do an image taxonomy query
	if ( ! empty( $taxonomy ) && ! empty( $term ) && $post_type === 'attachment' ) {
		
		$term = strtolower( $term );
		$taxonomy = strtolower( $taxonomy ); 
		$paged = (get_query_var('page')) ? (int) get_query_var('page') : 1;
	
		if ( ! isset( $numberposts ) ) {
			
			$numberposts = 20;
		}
		
		// setup taxonomy query
		$args = array(
			'tax_query'			=> array(),
			//'showposts' => $num_posts,
			'posts_per_page' 	=> $numberposts,
			'post_type' 		=> 'attachment',
			'paged' 			=> $paged,
			'post_status'		=> 'inherit',
			'offset'			=> ''
		);
		
		$args['tax_query'][] = array(
		
			'taxonomy'	=> $taxonomy,
			'field' 	=> 'slug',
			'terms' 	=> $term
		);
		
		// perform taxonomy query
		$attachment_query = new WP_Query( $args );
		$r_attachments = $attachment_query->get_posts();
	
		if ( $r_attachments ) {
			
			foreach ($r_attachments as $a) {
				
				$attachments[$a->ID] = $a;
			}
		}
	}
	
	// always return something so downstream plugins can operate.
	return $attachments;
}

/**
 * Filter callback for doing query to use an alternate Post Type (other than attachments)
 *
 * This is typically used in the cases where the user wants to assemble a 
 * gallery of images using the Features Images of a specific set of Posts.
 *
 * @param		array	$attachments	the array of attachments created by any upstream filters
 * @param		array	$attr			the attributed of the gallery shortcode
 * @return		array					an array of attachment post objects
 */
function photopress_gallery_posts_query( $attachments, $attr ) {
	
	if ( ! $attr ) {
		
		$attr = array();
	}
	
	extract( $attr  );
	
	$args = array();
	
	if ( $post_type != 'attachment' ) {
	
		// discard any attachments we might get.
		$attachments = array();	
		
		if ( ! empty( $include ) ) {
			
			$paged = (get_query_var('page')) ? (int) get_query_var('page') : 1;
			
			// setup taxonomy query
			$args = array(
				
				//'showposts' => $num_posts,
				'tax_query'			=>	array(),
				'posts_per_page' 	=> $numberposts,
				'post_type' 		=> $post_type,
				'paged' 			=> $paged,
				'post_status'		=> 'published',
				'offset'			=> '',
				'include'			=> $include
			);
			
			if ( ! empty( $taxonomy ) && ! empty( $term ) ) {
				$args['tax_query'][] = array(
				
					'taxonomy'	=> $taxonomy,
					'field' 	=> 'slug',
					'terms' 	=> $term
				);
			}	
		}
		
		// if there is no includes attr then pull a list of child posts based
		// the Post ID
		if ( empty( $include ) && ! empty( $id ) ) {
			
			$args = array(
				'post_parent' 		=> $id, 
				'posts_per_page' 	=> -1,
				'post_type' 		=> $post_type, 
				'paged' 			=> $paged,
				'offset'			=> '',
				'post_status' 		=> 'published'
			);
		}
	}
	
	if ( $args ) {
		
		$r_attachments = get_posts($args);
		
		if ( $r_attachments ) {
			
			foreach ($r_attachments as $a) {
				
				$attachments[$a->ID] = $a;
			}
		}
	}
	
	// always return something so downstream filters can operate.
	return $attachments;
}

/**
 * Filter call back for operating on caption text source
 *
 * @param 	string	$caption	the caption text
 * @param	object	$post		the Post object
 * @param	array	$attr		the attributes of the gallery shortcode
 * @return	string				the caption text
 */
function photopress_gallery_title_caption( $caption, $post, $attr ) {
	
	
	if ( ! $attr ) {
		
		$attr = array();
	}
	
	// merge in default values into the gallery attr array in case they are not present
	extract( $attr );

	if ($caption_source === 'title' && $post ) {
		
		$caption = $post->post_title;
	}
	
	if ( $link_caption_text ) {
		
		if ( $post_type === 'attachment' ) {
			$caption = wp_get_attachment_link( $post->ID, '' , false, false, $caption ); 
		} else {
					
			$caption = sprintf('<a href="%s">%s</a>', get_permalink( $post->ID ), $caption );
		}
	}
	
	return $caption;
}

// register Gallery filter method.
add_filter( 'post_gallery', 'photopress_gallery_shortcode', 99, 3 );
// provides support for taxonomy queries in gallery shortcode
add_filter( 'post_gallery_attachments', 'photopress_gallery_taxonomy_query', 99, 2 );
// provides support for post queries in gallery shortcode
add_filter( 'post_gallery_attachments', 'photopress_gallery_posts_query', 99, 2 );
// provides support for using titles for captions
add_filter( 'post_gallery_caption', 'photopress_gallery_title_caption', 99, 3 );

?>