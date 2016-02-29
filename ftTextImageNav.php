<?php
/*
Plugin Name: Text Based Link Navigation for Images
Plugin URI: http://fullthrottledevelopment.com/ft_textimage_nav
Description: This plugin allows you to replace the default previous_image_link and next_image_link functions with custom functions that return text links rather than images. Link text can be modified.
Version: 1.2
Author: Michael Torbert
Author URI: http://semperfiwebdesign.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


if( !class_exists( 'FTTextImageNavLinks' ) ) {
	class FTTextImageNavLinks {

		//This function calls the previous link if previous image exists
		function ft_previous_image_link($linkTitle) {
			$this->ft_adjacent_image_link(true,$linkTitle);
		}
		
		//This function calls the next link if next image exists
		function ft_next_image_link($linkTitle) {
			$this->ft_adjacent_image_link(false,$linkTitle);
		}

		//This function grabs all images associated with post, determines if previous and next images exist, and prints links where needed.
		function ft_adjacent_image_link($prev = true,$linkTitle) {
			global $post;
			$post = get_post($post);
			$attachments = array_values(get_children("post_parent=$post->post_parent&post_type=attachment&post_mime_type=image&orderby=menu_order ASC, ID ASC"));

			foreach ( $attachments as $k => $attachment )
				if ( $attachment->ID == $post->ID )
					break;

			$k = $prev ? $k - 1 : $k + 1;

			if ( isset($attachments[$k]) ) 
				echo $this->ft_get_attachment_link($attachments[$k]->ID, $linkTitle, true);
		}
		
		//This function actually builds the link and returns the value to the original function called by the theme.
		function ft_get_attachment_link($id = 0, $linkTitle, $permalink = false, $icon = false) {
			$id = intval($id);
			$_post = & get_post( $id );
		
			if ( ('attachment' != $_post->post_type) || !$url = wp_get_attachment_url($_post->ID) )
				return __('Missing Attachment');
		
			if ( $permalink )
				$url = get_attachment_link($_post->ID);
		
			$post_title = attribute_escape($_post->post_title);
		
			$link_text = $linkTitle;
		
			return "<a href='$url' class='ft_text_image_link' title='$post_title'>$link_text</a>";		
		}
  	}
}

if( class_exists('FTTextImageNavLinks') ){
	if( !isset($ftTextImageNavLinks) ) {
		$ftTextImageNavLinks = new FTTextImageNavLinks;
	}
}

if( isset($ftTextImageNavLinks) ){
	
	//echos the previous link
	if( !function_exists('ft_previous_image_link') ) {	
		function ft_previous_image_link($text){
			global $ftTextImageNavLinks;
			$ftTextImageNavLinks->ft_previous_image_link($text);
		}
	}

	//echos the next link
	if( !function_exists('ft_next_image_link') ) {	
		function ft_next_image_link($text){
			global $ftTextImageNavLinks;
			$ftTextImageNavLinks->ft_next_image_link($text);
		}
	}
}
