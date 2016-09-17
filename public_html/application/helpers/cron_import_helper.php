<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;
use mjohnson\utility\TypeConverter;


function get_archive_image_file_name($file)
{
	return substr(strrchr($file, DIRECTORY_SEPARATOR), 1);;
}

function get_archive_image_file_date($file)
{
	$y = substr($file, 1, 4);
	$m = substr($file, 5, 2);
	$d = substr($file, 7, 2);

	return Carbon::create($y, $m, $d);
}

function is_valid_archive_file_name($file)
{
	return preg_match('~(?:M*_).*(?:P\.jpg)~', $file);
}

function is_valid_archive_file_date($file_date, $from = NULL, $to = NULL)
{
	if(empty($from) && empty($to))
	{
		return TRUE;
	}
	elseif(!empty($from) && !empty($to) && $file_date->gte($from) && $file_date->lte($to))
	{
		return TRUE;
	}
	elseif(!empty($from) && empty($to) && $file_date->gte($from))
	{
		return TRUE;
	}
	elseif(empty($from) && !empty($to) && $file_date->lte($to))
	{
		return TRUE;
	}


	return FALSE;
}

function get_archive_image_dir_name($file)
{
	$file_name = get_archive_image_file_name($file);

	$directory_name = str_replace($file_name, '', $file);
	$directory_name = str_replace(ARCHIVES_IMG_PATH, '', $directory_name);

	return $directory_name;
}

function get_archive_image_parent_dir_name($file)
{
	$dir = get_archive_image_dir_name($file);

	return str_replace(strrchr(rtrim($dir, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR), '', $dir);
}

function debug_import($msg = '')
{
	if(!DEBUG_MODE) return;

	echo $msg.'<br/>';
}

function get_station_code($xml)
{
	if (empty($xml['ufo_systems']['ufo_system']['id']))
	{
		return FALSE; // we have few stations in one xml


		//$station = $xml['ufo_systems']['ufo_system'][0]['id'];
	}
	else
	{
		$station = $xml['ufo_systems']['ufo_system']['id'];
	}

	return $station;
}

function get_ufo_objects($xml)
{
	$objects = $xml['ufo_objects']['ufo_object'];

	// ======== TYPE CASTINGS for xml IF WE HAVE ONE OBJECT
	if (isset($objects['y']))
	{
		$objects = array($objects);
	}

	return $objects;
}

function get_object_date($i, $only_ms = FALSE)
{
	$y = $i['y'];
	$mo = str_pad($i['mo'], 2, "0", STR_PAD_LEFT);
	$d = str_pad($i['d'], 2, "0", STR_PAD_LEFT);
	$day = "$y$mo$d";
	$date = "$y-$mo-$d";

	list($sec, $ms) = explode('.', $i['s']);

	if($only_ms) return intval ($ms) ;

	$h = str_pad($i['h'], 2, "0", STR_PAD_LEFT);
	$m = str_pad($i['m'], 2, "0", STR_PAD_LEFT);
	$s = str_pad($sec, 2, "0", STR_PAD_LEFT);

	$time = "$h:$m:$s";

	return Carbon::parse($date.' '.$time);
}


function get_image_file_name($object_date, $station_code)
{
	return 'M' . $object_date->format('Ymd')  . '_' . $object_date->format('His') . '_' . $station_code . 'P.jpg';
}


function get_image_xml_file_name($object_date, $station_code, $folder)
{
	return ARCHIVES_IMG_PATH . $folder . 'M' . $object_date->format('Ymd') . '_' . $object_date->format('His') . '_' . $station_code . 'A.XML';
}

function get_xml_names($folder)
{
	return array_merge(glob(ARCHIVES_IMG_PATH . $folder . 'M*R.xml'), glob(realpath(ARCHIVES_IMG_PATH . $folder . '../') . '/M*R.xml'));
}