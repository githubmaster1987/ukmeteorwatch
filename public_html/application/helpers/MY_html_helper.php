<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ------------------------------------------------------------------------

if ( ! function_exists('script_tag'))
{
	/**
	 * Script
	 *
	 * Generates script tag point to a script file with options for async
	 * or defered script loading to speed up DOM rendering.
	 *
	 * @param	mixed	script src or an array of src's
	 * @param	string	MIME type of file(s)
	 * @param	bool	async option
	 * @param	bool	defer option
	 * @return	string
	 */
	function script_tag($src, $type = 'application/javascript', $async = FALSE, $defer = FALSE)
	{
		$CI = &get_instance();
		$script = '<script ';

		if (!is_array($src))
		{
			$src = array($src);
		}

		foreach ($src as $key => $value)
		{
			if (empty($value))
			{
				continue;
			}

			// If not the first script tag, close tag and open new tag
			if ($script != '<script ')
			{
				$script .= '</script>'.PHP_EOL.'<script ';
			}

			// If $value is not FQDN, use the application's base url as root
			if (strpos($value, '://') === FALSE)
			{
				$value = $CI->config->base_url($value);
			}

			$script .= 'src="'.$value.'" type="'.$type.'"';

			// Sets defer tag or async tag
			if ($defer === TRUE)
			{
				$script .= ' defer="defer"';
			}
			elseif ($async === TRUE)
			{
				$script .= ' async="async"';
			}

			$script .= '>';
		}

		return $script."</script>".PHP_EOL;
	}
}

function tag_cloud($tags = array())
{
	$return = array();

	if(is_array($tags))
	{
		shuffle($tags);

		$smallest = 14;
		$largest = 30;
		$unit = 'px';

		foreach ($tags as $key => $tag)
		{
			$counts[$key] = $tag->posts_count;
		}

		$min_count = min($counts);

		$spread = max($counts) - $min_count;

		if ($spread <= 0) $spread = 1;

		$font_spread = $largest - $smallest;

		if ($font_spread < 0) $font_spread = 1;

		$font_step = $font_spread / $spread;

		foreach ($tags as $key => $tag)
		{
			$count = $counts[$key];

			$tag_id = isset($tags[$key]->blog_tag_id) ? $tags[$key]->blog_tag_id : $key;

			$tag_name = $tags[$key]->tag_title;

			$return[] = anchor('blog/tag/' . $tags[$key]->tag_slug, $tag_name, array('class' => "tag-link-$tag_id", 'style' => "font-size: " .
				str_replace(',', '.', ( $smallest + ( ( $count - $min_count ) * $font_step )))
				. "$unit;"));
		}

	}

	return $return;
}
