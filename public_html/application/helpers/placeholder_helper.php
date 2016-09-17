<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*

https://github.com/edmundask/codeigniter-placeholder

Usage

placeholder(width, height, text, bgcolor, color); - placeholder images
lipsum(paragraphs, length, extra_options) - placeholder text

The helper uses placehold.it and loripsum.net services.

Examples

Image placeholders

    300px square: placeholder(300);.
    200x100px rectangular: placeholder(200, 100);
    200x100px rectangular with custom text: placeholder(200, 100, 'Howdy');
    100px black square with white text: placeholder(100, 100, 'Howdy', '000000', 'FFFFFF');

Note that colors are represented as HEX values (when passing arguments to the function, don't add the pound # symbol).

You can also pass in an array:

placeholder(array('width' => 100, 'height' => 100, 'text' => 'Howdy', 'background' => '000000', 'foreground' => 'FFFFFF'));

Text placeholders

    2 medium length paragraphs: lipsum()
    3 short paragraphs: lipsum(3, 'short')
    5 long paragraphs with decorations (bold, italic text) and links: lipsum(3, 'long', array('decorate', 'link'))

Here is a full list of extra options that you can put in the array:

    decorate - Add bold, italic and marked text
    link - Add links
    ul - Add unordered lists
    ol - Add numbered lists
    dl - Add description lists
    bq - Add blockquotes
    code - Add code samples
    headers - Add headers
    allcaps - Use ALL CAPS
    prude - Prude version

 */


/**
 * Generates a placeholder image
 *
 * @access	public
 * @param 	mixed  	width as integer or array of params
 * @param 	integer	height
 * @param 	string 	text
 * @param 	string 	background color
 * @param 	string 	foreground color
 * @return	string 	HTML
 */
if(!function_exists('placeholder'))
{
	function placeholder($width, $height = NULL, $text = NULL, $background = NULL, $foreground = NULL)
	{
		$params = array();

		if(is_array($width))
		{
			$params = $width;
		}
		else
		{
			$params['width']     	= $width;
			$params['height']    	= $height;
			$params['text']      	= $text;
			$params['background']	= $background;
			$params['foreground']	= $foreground;
		}

		$params['height']    	= (empty($params['height'])) ? $params['width'] : $params['height'];
		$params['text']      	= (empty($params['text'])) ? $params['width'] . ' x '. $params['height'] : $params['text'];
		$params['background']	= (empty($params['background'])) ? 'CCCCCC' : $params['height'];
		$params['foreground']	= (empty($params['foreground'])) ? '969696' : $params['foreground'];

		return '<img src="http://placehold.it/'. $params['width'] . 'x'. $params['height'] . '/' . $params['background'] . '/' . $params['foreground'] . '&text='. $params['text'] . '" alt="Placeholder">';
	}
}

/**
 * Generates lorem ipsum (dummy) text paragraphs
 *
 * @access	public
 * @param 	integer	number of paragraphs to generate
 * @param 	string 	length of each paragraph
 * @param 	array  	extra options
 * @return	string 	HTML
 */
if(!function_exists('lipsum'))
{
	function lipsum($paragraphs = 2, $length = 'medium', $flags = array())
	{
		$contents = file_get_contents('http://loripsum.net/api/' . $paragraphs . '/' . $length . join('/', $flags));

		return $contents;
	}
}

/**
 * Get either a placehold.it URL or complete image tag of specified size & color.
 *
 * @param int 		$width
 * @param int 		$height
 * @param boolean 	$img True to return a complete IMG tag False for just the URL
 * @param string 	$fgcolor 
 * @param string 	$bgcolor
 * @return 			String containing either just a URL or a complete image tag
 */
if ( ! function_exists('placeholder2'))
{ 
	function placeholder2( $width = 320, $height = 240, $img = true, $fgcolor = '969696', $bgcolor = 'cccccc' )
	{
		$color = '/' . $bgcolor . '/' . $fgcolor;
		$url = 'http://placehold.it/' . $width . 'x' . $height . $color;
		if ( $img )
		{
			$url = '<img src="' . $url . '" alt="' . $width . 'x' . $height .' placeholder" />';
		}
		return $url;
	}
} 

/**
 * Get either a placekitten.com URL or complete image tag of specified size & color.
 *
 * @param int 		$width
 * @param int 		$height
 * @param boolean 	$img True to return a complete IMG tag False for just the URL
 * @return 			String containing either just a URL or a complete image tag
 */
if ( ! function_exists('placekitten'))
{ 
	function placekitten( $width = 320, $height = 240, $img = true )
	{
		$url = 'http://placekitten.com/' . $width . '/' . $height;
		if ( $img )
		{
			$url = '<img src="' . $url . '" alt="' . $width . 'x' . $height .' placekitten" />';
		}
		return $url;
	}
} 

/* End of file placeholder_helper.php */