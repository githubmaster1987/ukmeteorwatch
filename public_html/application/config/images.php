<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Image Preset Sizes
|--------------------------------------------------------------------------
|
| Specify the preset sizes you want to use in your code. Only these preset
| will be accepted by the controller for security.
|
| Each preset exists of a width and height. If one of the dimensions are
| equal to 0, it will automatically calculate a matching width or height
| to maintain the original ratio.
|
| If both dimensions are specified it will automatically crop the
| resulting image so that it fits those dimensions.
|
*/
$config['image_sizes']['small']  = array(86, 86);
$config['image_sizes']['medium'] = array(133, 132);
$config['image_sizes']['large']  = array(237, 159);
$config['image_sizes']['search']  = array(245, 163);
$config['image_sizes']['wide']   = array(840, 210);
$config['image_sizes']['x_wide']   = array(633, 222);

$config['image_sizes']['square'] = array(134, 134);

$config['image_sizes']['blog_large']  = array(206, 237);
$config['image_sizes']['blog_xlarge']  = array(820, 0);

//$config['image_sizes']['square'] = array(100, 100);
//$config['image_sizes']['long']   = array(280, 600);
//$config['image_sizes']['hero']   = array(940, 320);

