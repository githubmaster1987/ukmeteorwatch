<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Site settings
|--------------------------------------------------------------------------
|
| Global site settings
|
*/
$config['site_name'] 	= '';
$config['site_email'] 	= ''; // Global site email

/*
|--------------------------------------------------------------------------
| Default Meta tags
|--------------------------------------------------------------------------
|
| Set the default meta tags site wide
|
*/

$config['default_title'] 		= 'Meteor Watch! live | UK Meteor Observation Network';
$config['default_description'] 	= 'Bringing meteors across united kingdom to a single website';
$config['default_keywords'] 	= '';


/*
|--------------------------------------------------------------------------
| Errors delimiters
|--------------------------------------------------------------------------
|
| Define the error delimiters
|
*/

$config['err_open'] = '<p class="error">';
$config['err_close'] = '</p>';

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
|
*/
$config['pagination']['full_tag_open'] = '<div class="row"><div class="col-md-12"><nav><ul class="pagination">';
$config['pagination']['full_tag_close'] = '</ul></nav></div></div>';
$config['pagination']['num_links']  =      10;
$config['pagination']['per_page']   =      12;
$config['pagination']['cur_tag_open'] =    '<li class="active"><a href="javascript:void(null);">';
$config['pagination']['cur_tag_close'] =   '</a></li>';
$config['pagination']['num_tag_open'] =   '<li>';
$config['pagination']['num_tag_close'] =   '</li>';

$config['pagination']['first_tag_open'] =  '<li>';
$config['pagination']['first_tag_close'] = '</li>';
$config['pagination']['last_tag_open'] =   '<li>';
$config['pagination']['last_tag_close'] =  '</li>';
$config['pagination']['next_tag_open'] =   '<li>';
$config['pagination']['next_tag_close'] =  '</li>';
$config['pagination']['prev_tag_open'] =   '<li>';
$config['pagination']['prev_tag_close'] =  '</li>';
$config['pagination']['next_link'] = 'Next';
$config['pagination']['prev_link'] = 'Prev';


$config['admin_pagination']['full_tag_open'] = '<div class="row"><div class="col-md-12"><nav><ul class="pagination">';
$config['admin_pagination']['full_tag_close'] = '</ul></nav></div></div>';
$config['admin_pagination']['num_links']  =      10;
$config['admin_pagination']['per_page']   =      12;
$config['admin_pagination']['cur_tag_open'] =    '<li class="active"><a href="javascript:void(null);">';
$config['admin_pagination']['cur_tag_close'] =   '</a></li>';
$config['admin_pagination']['num_tag_open'] =   '<li>';
$config['admin_pagination']['num_tag_close'] =   '</li>';

$config['admin_pagination']['first_tag_open'] =  '<li>';
$config['admin_pagination']['first_tag_close'] = '</li>';
$config['admin_pagination']['last_tag_open'] =   '<li>';
$config['admin_pagination']['last_tag_close'] =  '</li>';
$config['admin_pagination']['next_tag_open'] =   '<li>';
$config['admin_pagination']['next_tag_close'] =  '</li>';
$config['admin_pagination']['prev_tag_open'] =   '<li>';
$config['admin_pagination']['prev_tag_close'] =  '</li>';
$config['admin_pagination']['next_link'] = 'Next';
$config['admin_pagination']['prev_link'] = 'Prev';


/*
|--------------------------------------------------------------------------
| Constants
|--------------------------------------------------------------------------
|
*/
define('METEORS_LIVE_PATH', FCPATH.'live/');

define('METEORS_IMG_URI', 'img/meteors/');
define('METEORS_IMG_PATH', FCPATH.METEORS_IMG_URI);

define('ARCHIVES_IMG_URI', 'data'.DIRECTORY_SEPARATOR);
define('ARCHIVES_IMG_PATH', FCPATH.ARCHIVES_IMG_URI);



/* End of file site_settings.php */
/* Location: ./application/config/site_settings.php */
