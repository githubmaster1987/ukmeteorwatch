<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* Code Igniter
*
* An open source application development framework for PHP 4.3.2 or newer
*
* @package		CodeIgniter
* @author		Rick Ellis
* @copyright	Copyright (c) 2006, pMachine, Inc.
* @license		http://www.codeignitor.com/user_guide/license.html
* @link			http://www.codeigniter.com
* @since        Version 1.0
* @filesource
*/

// ------------------------------------------------------------------------

/**
* Code Igniter Asset Helpers
*
* @package		CodeIgniter
* @subpackage	Helpers
* @category		Helpers
* @author       Philip Sturgeon < email@philsturgeon.co.uk >
*/

// ------------------------------------------------------------------------

function get_asset_instance()
{
	$ci =& get_instance();
	$ci->load->library('asset');
	return $ci->asset;
}

function css($asset_name, $module_name = NULL, $attributes = array())
{
	return get_asset_instance()->css($asset_name, $module_name, $attributes);
}

function theme_css($asset, $attributes = array())
{
	return css($asset, '_theme_', $attributes);
}

function css_url($asset_name, $module_name = NULL)
{
	return get_asset_instance()->css_url($asset_name, $module_name);
}

function css_path($asset_name, $module_name = NULL)
{
	return get_asset_instance()->css_path($asset_name, $module_name);
}

// ------------------------------------------------------------------------


function image($asset_name, $module_name = NULL, $attributes = array())
{
	return get_asset_instance()->image($asset_name, $module_name, $attributes);
}

function theme_image($asset, $attributes = array())
{
	return image($asset, '_theme_', $attributes);
}

function image_url($asset_name, $module_name = NULL)
{
	return get_asset_instance()->image_url($asset_name, $module_name);
}

function image_path($asset_name, $module_name = NULL)
{
	return get_asset_instance()->image_path($asset_name, $module_name);
}

// ------------------------------------------------------------------------


function js($asset_name, $module_name = NULL)
{
	return get_asset_instance()->js($asset_name, $module_name);
}

function theme_js($asset, $attributes = array())
{
	return js($asset, '_theme_', $attributes);
}

function js_url($asset_name, $module_name = NULL)
{
	return get_asset_instance()->js_url($asset_name, $module_name);
}

function js_path($asset_name, $module_name = NULL)
{
	return get_asset_instance()->js_path($asset_name, $module_name);
}

/**
  * Partial Helper
  *
  * Loads the partial
  *
  * @access		public
  * @param		mixed    file name to load
  */


	function file_partial($file = '', $ext = 'php', $view_data=array())
	{
		$CI =& get_instance();
		$data =& $CI->load->_ci_cached_vars;
		
		$path = $data['template_views'].'partials/'.$file;
	
		echo $CI->load->_ci_load(array(
			'_ci_path' => $data['template_views'].'partials/'.$file.'.'.$ext,
			'_ci_vars' => $view_data,			
			'_ci_return' => TRUE
		));
	}
	
/**
  * Template Partial
  *
  * Display a partial set by the template
  *
  * @access		public
  * @param		mixed    partial to display
  */


	function template_partial($name = '')
	{
		$CI =& get_instance();
		$data =& $CI->load->_ci_cached_vars;

		echo isset($data['template']['partials'][$name]) ? $data['template']['partials'][$name] : '';
	}
	

function theme_thumb($image, $theme)
{
	return '<img src="/image.php/'.$image.'?width=168&height=99&color=FFFFFF&image=/themes/frontend/'.$theme.'/'.$image.'" alt="" />';
}

function thumb($full_source_path, $w=180, $h=144, $cropratio = NULL)
{
	$x = explode('/', $full_source_path);
	$image = end($x);
	
	return '<img src="/image.php/'.$image.'?width='.$w.'&height='.$h.(!empty($cropratio) ? '&cropratio='.$cropratio : '').'&image='.$full_source_path.'" alt="" />';
}

function thumb_url($full_source_path, $w=180, $h=144, $cropratio = NULL)
{
	$x = explode('/', $full_source_path);
	$image = end($x);
	
	return '/image.php/'.$image.'?width='.$w.'&height='.$h.(!empty($cropratio) ? '&cropratio='.$cropratio : '').'&image='.$full_source_path;
}

function theme_image_path($asset_name, $theme)
{
	return '/' . config_item('theme_asset_dir') . 'frontend/' . $theme . '/' . config_item('asset_img_dir') . '/' . $asset_name;	
}