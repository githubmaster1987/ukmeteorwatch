<?php defined('BASEPATH') OR exit('No direct script access allowed');

use mjohnson\utility\TypeConverter;
use Carbon\Carbon;

/**
  string         meaning
  ------         -------
  @reboot        Run once, at startup
  @yearly        Run once a year, "0 0 1 1 *"
  @annually      (same as @yearly)
  @monthly       Run once a month, "0 0 1 * *"
  @weekly        Run once a week, "0 0 * * 0"
  @daily         Run once a day, "0 0 * * *"
  @midnight      (same as @daily)
  @hourly        Run once an hour, "0 * * * *"

 */

class Cron extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		if (ENVIRONMENT == 'production' && !$this->input->is_cli_request())
		{
			//show_error('Permission denied');
		}
	}


	// curl --silent --compressed http://www.ukmeteorwatch.co.uk/cron/import_archive
	//
	// added ability to debug import
	//
	// http://ukmon.dev/cron/import_archive/mode/debug/from/2013-08-01/to/2014-01-01/station_id/4
	// http://www.ukmeteorwatch.co.uk/cron/import_archive/mode/debug/from/2013-08-01/to/2014-01-01/station_id/4

	function import_archive()
	{
		/* we don't need to be limited by...normal limitations */
		set_time_limit(0);
		ini_set('memory_limit', -1);

		$this->load->helper(array('directory', 'array', 'cron_import'));
		$this->load->model(array('archive_events_model', 'archive_stations_model', 'archives_model', 'archive_logs_model'));

		$exist_stations = $this->archive_stations_model->generateList($conditions = NULL, $order = NULL, $start = 0, $limit = NULL, $key = 'station_id', $value = 'station_code');
		$exist_events = $this->archive_events_model->generateFullList($conditions = NULL, $order = NULL, $start = 0, $limit = NULL, $key = 'event_id', $value = 'event_code');
		//$archive_logs = $this->archive_logs_model->generateList($conditions = NULL, $order = NULL, $start = 0, $limit = NULL, $key = 'import_date', $value = 'import_cnt');

		$filters = $this->uri->uri_to_assoc(3, array('mode', 'from', 'to', 'station_id'));

		define('DEBUG_MODE', ($filters['mode'] == 'debug') ? TRUE : FALSE);
		if(!empty($filters['from'])) $filters['from'] = Carbon::parse($filters['from']);
		if(!empty($filters['to'])) $filters['to'] = Carbon::parse($filters['to']. ' 23:59:59');

		// ================================ GET DATES ===========================
		$archive_dates = array();
		$files = array();
		$file_directories = array();

      $it = new RecursiveDirectoryIterator(ARCHIVES_IMG_PATH);

		foreach (new RecursiveIteratorIterator($it) as $file)
		{		    
			if (is_valid_archive_file_name($file))
			{
				$file_name = get_archive_image_file_name($file);
				$file_date = get_archive_image_file_date($file_name);
				$file_dir = get_archive_image_dir_name($file);

				if(is_valid_archive_file_date($file_date, $filters['from'], $filters['to']))
				{
					if(!isset($file_directories[$file_name]))
					{
						$file_directories[$file_name] = $file_dir;
					}
					else
					{
						debug_import('Duplicate found: '.$file_dir.$file_name);
						continue;
					}

					$files[ $file_date->format('Y-m-d') ] [$file_dir] []  = $file_name;

					if (! in_array($file_date->format('Y-m-d'), $archive_dates))
					{
						$archive_dates[] =  $file_date->format('Y-m-d');
					}
				}
			}
		}

		$not_processed_images = $file_directories;

		if(DEBUG_MODE)
		{
			asort($archive_dates);
		}
		else
		{
			shuffle($archive_dates);	// HACK - shuffle used to process every time other dates in case if script interrupted
		}


		$processed_images = array();
		$ufo_system_errors = array();

		// ============== PROCESS EACH DATE ===================
		foreach ($archive_dates as $archive_date)
		{
			$image_processing_errors = array();

			foreach ($files[$archive_date] as $folder => $images)
			{
				// find XML in *.jpg folder & check for XML in parent folder
				$xmls = get_xml_names($folder);

				if(is_array($xmls) && count($xmls))
				{
					// process all XML files
					foreach ($xmls as $xml_path)
					{				    
						$xml = TypeConverter::xmlToArray(file_get_contents($xml_path), TypeConverter::XML_MERGE);

						$station_code = get_station_code($xml);

						if($station_code === FALSE) // we have few stations in one xml
						{
							/*
							<ufo_systems>
								<ufo_system id="Lockyer2_L2" meteors="220" e-class="0" total="220"/>
								<ufo_system id="Lockyer1_L1" meteors="40" e-class="0" total="40"/>
								<ufo_system id="Lockyer_L1" meteors="3" e-class="0" total="3"/>
							</ufo_systems>
							 */

							$ufo_system_errors[] = '======= <strong>ERROR: ufo_system</strong> should be only one in xml <strong>'. $xml_path . ' but '.count($xml['ufo_systems']['ufo_system']) .'</strong> provided ====== ';
							continue;

						}
                        
                        //Save new station to DB
						if (!$station_id = array_search($station_code, $exist_stations))
						{
							$station_name = trim(str_replace(array('_', '-'), ' ', $station_code), '/');

							$station_id = $this->archive_stations_model->save(array(
								'station_code' => $station_code
								, 'station_name' => $station_name
								, 'station_slug' => $this->create_unique_slug($station_name, 'archive_stations', 'station_slug')
							));

							$exist_stations[$station_id] = $station_code;
						}

						if(!empty($filters['station_id']) && $filters['station_id'] != $station_id) { continue; } // SKIP NOT SELECTED station_id's

						$objects = get_ufo_objects($xml);

						foreach ($objects as $object)
						{
							$object_date = get_object_date($object);

							if ($object_date->format('Y-m-d') != $archive_date) {	continue; }		// SKIP NOT CURRENT DATE FROM XML

							if(array_key_exists(get_image_file_name($object_date, $station_code), $file_directories))
							{
								$image_file_name = get_image_file_name($object_date, $station_code);
								$image_xml_file_name = get_image_xml_file_name($object_date, $station_code, $file_directories[$image_file_name]);
							}
							// we have a problem - file not found - maybe issue with time? Try to substruct 1 second
							elseif(array_key_exists(get_image_file_name($object_date->subSecond(), $station_code), $file_directories))
							{
								$image_file_name = get_image_file_name($object_date, $station_code);
								$image_xml_file_name = get_image_xml_file_name($object_date, $station_code, $file_directories[$image_file_name]);
							}
							else
							{
								$image_processing_errors[] = $folder . get_image_file_name($object_date, $station_code);
								unset($not_processed_images[ get_image_file_name($object_date, $station_code)]);
								continue;
							}

							$event = $object['class'];
							$event_id = array_search($event, $exist_events);

							if(empty($event_id))
							{
								debug_import('Associated with file '. $folder.$image_file_name . ' event <strong>' . $event . '</strong> not found. Skipped');
								unset($not_processed_images[ $image_file_name ]);
								continue;
							}

							if(file_exists($image_xml_file_name))
							{
							    if (!$this->archives_model->find(array('event_id' => $event_id, 'station_id' => $station_id, 'image' => $image_file_name)))
                                {
    								$image_xml = file_get_contents($image_xml_file_name);
    
    								$mag = $h1 = $h2 = $cam = $lens = $rstar = $sec = $av = $vo = $len = '';
    								if (preg_match('/\ mag="(?P<mag>[^"]*)"/im', $image_xml, $matches))
    								{
    									$mag = $matches['mag'];
    								}
    								if (preg_match('/h1="(?P<h1>[^"]*)"/im', $image_xml, $matches))
    								{
    									$h1 = $matches['h1'];
    								}
    								if (preg_match('/h2="(?P<h2>[^"]*)"/im', $image_xml, $matches))
    								{
    									$h2 = $matches['h2'];
    								}
    								if (preg_match('/cam="(?P<cam>[^"]*)"/im', $image_xml, $matches))
    								{
    									$cam = $matches['cam'];
    								}
    								if (preg_match('/lens="(?P<lens>[^"]*)"/im', $image_xml, $matches))
    								{
    									$lens = $matches['lens'];
    								}
    								if (preg_match('/rstar="(?P<rstar>[^"]*)"/im', $image_xml, $matches))
    								{
    									$rstar = $matches['rstar'];
    								}
    								if (preg_match('/sec="(?P<sec>[^"]*)"/im', $image_xml, $matches))
    								{
    									$sec = $matches['sec'];
    								}
    								if (preg_match('/av="(?P<av>[^"]*)"/im', $image_xml, $matches))
    								{
    									$av = $matches['av'];
    								}
    								if (preg_match('/len="(?P<len>[^"]*)"/im', $image_xml, $matches))
    								{
    									$len = $matches['len'];
    								}
    								if (preg_match('/Vo="(?P<Vo>[^"]*)"/im', $image_xml, $matches))
    								{
    									$vo = $matches['Vo'];
    								}
    								// if (preg_match('/maxMag="(?P<maxMag>[^"]*)"/im', $image_xml, $matches))
    								// {
    									// $maxMag = $matches['maxMag'];
    								// }
    
    								unset($image_xml);

									$this->archives_model->save(array(
										'event_id' => $event_id
										, 'station_id' => $station_id
										, 'image' => $image_file_name
										, 'image_folder' => $file_directories[$image_file_name]
										, 'created_at' => $object_date->getTimestamp()
										, 'created_at_ms' => get_object_date($object, TRUE)
										, 'mag' => $mag
										, 'h1'  => $h1
										, 'h2'  => $h2
										, 'cam' => $cam
										, 'lens' => $lens
										, 'rstar' => $rstar
										, 'sec' => $sec
										, 'av'  => $av
										, 'vo'  => $vo
										, 'len' => $len
										//, 'maxMag' => $maxMag
									));
								}

								unset($not_processed_images[$image_file_name]);
								$processed_images[$image_file_name] = $folder;
							}
							else
							{
								debug_import('Xml File doesn\'t exist: '. $image_xml_file_name);
							}
						}
					}
				}
				else
				{
					debug_import('Processing '.$folder.'. No ufoanalyzer report file(s) found: '. realpath(ARCHIVES_IMG_PATH . $folder . '../') . '/M*R.xml');
				}
			}

			if(count($image_processing_errors))
			{
				debug_import(
					'====== Can\'t find images provided in ufoanalyzer report file M*R.xml: ======= <br/>'
					.implode('<br/>', array_unique($image_processing_errors))
					.'<br/>'
				);
			}
		}

		if(count($ufo_system_errors))
		{
			debug_import('<br/>'.implode('<br/>', array_unique($ufo_system_errors)));
		}

		if(empty($filters['station_id']))
		{
			debug_import('<br/>========= <strong>Processed '.count($processed_images). ' from ' . count($file_directories). ' images </strong> =========');

			debug_import('<br/><strong>'.count($not_processed_images). ' files below are missed in ufoanalyzer report file M*R.xml:</strong><br/>');

			foreach ($not_processed_images as $img => $f)
			{
				debug_import($f.$img );
			}
		}
	}

	// curl --silent --compressed http://www.ukmeteorwatch.co.uk/cron/update_archive
	//
	// added ability to debug import

	function update_archive()
	{
		/* I believe it is Oleg's comment:  
         * we don't need to be limited by...normal limitations */
		set_time_limit(0);
		ini_set('memory_limit', -1);

		$this->load->helper(array('directory', 'array', 'cron_import'));
		$this->load->model(array('archive_events_model', 'archive_stations_model', 'archives_model', 'archive_logs_model'));

		$exist_stations = $this->archive_stations_model->generateList($conditions = NULL, $order = NULL, $start = 0, $limit = NULL, $key = 'station_id', $value = 'station_code');
		$exist_events = $this->archive_events_model->generateFullList($conditions = NULL, $order = NULL, $start = 0, $limit = NULL, $key = 'event_id', $value = 'event_code');
	
		$filters = $this->uri->uri_to_assoc(3, array('mode'));

		define('DEBUG_MODE', ($filters['mode'] == 'debug') ? TRUE : FALSE);

		// ================================ GET DATES ===========================
		$archive_dates = array();
		$files = array();
		$file_directories = array();

        $it = new RecursiveDirectoryIterator(ARCHIVES_IMG_PATH);

		foreach (new RecursiveIteratorIterator($it) as $file)
		{		    
			if (is_valid_archive_file_name($file))
			{
				$file_name = get_archive_image_file_name($file);
				$file_date = get_archive_image_file_date($file_name);
				$file_dir = get_archive_image_dir_name($file);

				if(is_valid_archive_file_date($file_date))
				{
					if(!isset($file_directories[$file_name]))
					{
						$file_directories[$file_name] = $file_dir;
					}
					else
					{
						debug_import('Duplicate found: '.$file_dir.$file_name);
						continue;
					}

					$files[ $file_date->format('Y-m-d') ] [$file_dir] []  = $file_name;

					if (! in_array($file_date->format('Y-m-d'), $archive_dates))
					{
						$archive_dates[] =  $file_date->format('Y-m-d');
					}
				}
			}
		}

		$not_processed_images = $file_directories;

		if(DEBUG_MODE)
		{
			asort($archive_dates);
		}
		else
		{
			shuffle($archive_dates);	// HACK - shuffle used to process every time other dates in case if script interrupted
		}


		$processed_images = array();
		$ufo_system_errors = array();

		// ============== PROCESS EACH DATE ===================
		foreach ($archive_dates as $archive_date)
		{
			$image_processing_errors = array();

			foreach ($files[$archive_date] as $folder => $images)
			{
				// find XML in *.jpg folder & check for XML in parent folder
				$xmls = get_xml_names($folder);

				if(is_array($xmls) && count($xmls))
				{
					// process all XML files
					foreach ($xmls as $xml_path)
					{				    
						$xml = TypeConverter::xmlToArray(file_get_contents($xml_path), TypeConverter::XML_MERGE);

						$station_code = get_station_code($xml);

						if($station_code === FALSE) // we have few stations in one xml
						{
							/*
							<ufo_systems>
								<ufo_system id="Lockyer2_L2" meteors="220" e-class="0" total="220"/>
								<ufo_system id="Lockyer1_L1" meteors="40" e-class="0" total="40"/>
								<ufo_system id="Lockyer_L1" meteors="3" e-class="0" total="3"/>
							</ufo_systems>
							 */

							$ufo_system_errors[] = '======= <strong>ERROR: ufo_system</strong> should be only one in xml <strong>'. $xml_path . ' but '.count($xml['ufo_systems']['ufo_system']) .'</strong> provided ====== ';
							continue;

						}
                        
                        //Save new station to DB
						if (!$station_id = array_search($station_code, $exist_stations))
						{
							$station_name = trim(str_replace(array('_', '-'), ' ', $station_code), '/');

							$station_id = $this->archive_stations_model->save(array(
								'station_code' => $station_code
								, 'station_name' => $station_name
								, 'station_slug' => $this->create_unique_slug($station_name, 'archive_stations', 'station_slug')
							));

							$exist_stations[$station_id] = $station_code;
						}

						if(!empty($filters['station_id']) && $filters['station_id'] != $station_id) { continue; } // SKIP NOT SELECTED station_id's

						$objects = get_ufo_objects($xml);

						foreach ($objects as $object)
						{
							$object_date = get_object_date($object);

							if ($object_date->format('Y-m-d') != $archive_date) {	continue; }		// SKIP NOT CURRENT DATE FROM XML

							if(array_key_exists(get_image_file_name($object_date, $station_code), $file_directories))
							{
								$image_file_name = get_image_file_name($object_date, $station_code);
								$image_xml_file_name = get_image_xml_file_name($object_date, $station_code, $file_directories[$image_file_name]);
							}
							// we have a problem - file not found - maybe issue with time? Try to substruct 1 second
							elseif(array_key_exists(get_image_file_name($object_date->subSecond(), $station_code), $file_directories))
							{
								$image_file_name = get_image_file_name($object_date, $station_code);
								$image_xml_file_name = get_image_xml_file_name($object_date, $station_code, $file_directories[$image_file_name]);
							}
							else
							{
								$image_processing_errors[] = $folder . get_image_file_name($object_date, $station_code);
								unset($not_processed_images[ get_image_file_name($object_date, $station_code)]);
								continue;
							}

							$event = $object['class'];
							$event_id = array_search($event, $exist_events);

							if(empty($event_id))
							{
								debug_import('Associated with file '. $folder.$image_file_name . ' event <strong>' . $event . '</strong> not found. Skipped');
								unset($not_processed_images[ $image_file_name ]);
								continue;
							}

							if(file_exists($image_xml_file_name))
							{
							    $meteor = $this->archives_model->find(array('event_id' => $event_id, 'station_id' => $station_id, 'image' => $image_file_name));
                               
                                $image_xml = file_get_contents($image_xml_file_name);
    
                                $mag = $h1 = $h2 = $cam = $lens = $rstar = $sec = $av = $vo = $len = '';
                                if (preg_match('/\ mag="(?P<mag>[^"]*)"/im', $image_xml, $matches))
                                {
                                    $mag = $matches['mag'];
                                }
                                if (preg_match('/h1="(?P<h1>[^"]*)"/im', $image_xml, $matches))
                                {
                                    $h1 = $matches['h1'];
                                }
                                if (preg_match('/h2="(?P<h2>[^"]*)"/im', $image_xml, $matches))
                                {
                                    $h2 = $matches['h2'];
                                }
                                if (preg_match('/cam="(?P<cam>[^"]*)"/im', $image_xml, $matches))
                                {
                                    $cam = $matches['cam'];
                                }
                                if (preg_match('/lens="(?P<lens>[^"]*)"/im', $image_xml, $matches))
                                {
                                    $lens = $matches['lens'];
                                }
                                if (preg_match('/rstar="(?P<rstar>[^"]*)"/im', $image_xml, $matches))
                                {
                                    $rstar = $matches['rstar'];
                                }
                                if (preg_match('/sec="(?P<sec>[^"]*)"/im', $image_xml, $matches))
                                {
                                    $sec = $matches['sec'];
                                }
                                if (preg_match('/av="(?P<av>[^"]*)"/im', $image_xml, $matches))
                                {
                                    $av = $matches['av'];
                                }
                                if (preg_match('/len="(?P<len>[^"]*)"/im', $image_xml, $matches))
                                {
                                    $len = $matches['len'];
                                }
                                if (preg_match('/Vo="(?P<Vo>[^"]*)"/im', $image_xml, $matches))
                                {
                                    $vo = $matches['Vo'];
                                }
                                // if (preg_match('/maxMag="(?P<maxMag>[^"]*)"/im', $image_xml, $matches))
                                // {
                                    // $maxMag = $matches['maxMag'];
                                // }

                                unset($image_xml);
                                
							    if (!$meteor)
                                {
    								/*
									$this->archives_model->save(array(
										'event_id' => $event_id
										, 'station_id' => $station_id
										, 'image' => $image_file_name
										, 'image_folder' => $file_directories[$image_file_name]
										, 'created_at' => $object_date->getTimestamp()
										, 'created_at_ms' => get_object_date($object, TRUE)
										, 'mag' => $mag
										, 'h1'  => $h1
										, 'h2'  => $h2
										, 'cam' => $cam
										, 'lens' => $lens
										, 'rstar' => $rstar
										, 'sec' => $sec
										, 'av'  => $av
										, 'vo'  => $vo
										, 'len' => $len
										//, 'maxMag' => $maxMag
									));*/
								} else {
								    if(empty($meteor->mag)) {								    
    								    $this->archives_model->save(array(
    								          'mag' => $mag
                                            , 'h1'  => $h1
                                            , 'h2'  => $h2
                                            , 'cam' => $cam
                                            , 'lens' => $lens
                                            , 'rstar' => $rstar
                                            , 'sec' => $sec
                                            , 'av'  => $av
                                            , 'vo'  => $vo
                                            , 'len' => $len
                                        ), $meteor->archive_id);
                                    }                                   
								}

								unset($not_processed_images[$image_file_name]);
								$processed_images[$image_file_name] = $folder;
							}
							else
							{
								debug_import('Xml File doesn\'t exist: '. $image_xml_file_name);
							}
						}
					}
				}
				else
				{
					debug_import('Processing '.$folder.'. No ufoanalyzer report file(s) found: '. realpath(ARCHIVES_IMG_PATH . $folder . '../') . '/M*R.xml');
				}
			}

			if(count($image_processing_errors))
			{
				debug_import(
					'====== Can\'t find images provided in ufoanalyzer report file M*R.xml: ======= <br/>'
					.implode('<br/>', array_unique($image_processing_errors))
					.'<br/>'
				);
			}
		}

		if(count($ufo_system_errors))
		{
			debug_import('<br/>'.implode('<br/>', array_unique($ufo_system_errors)));
		}

		if(empty($filters['station_id']))
		{
			debug_import('<br/>========= <strong>Processed '.count($processed_images). ' from ' . count($file_directories). ' images </strong> =========');

			debug_import('<br/><strong>'.count($not_processed_images). ' files below are missed in ufoanalyzer report file M*R.xml:</strong><br/>');

			foreach ($not_processed_images as $img => $f)
			{
				debug_import($f.$img );
			}
		}
	}

	// curl --silent --compressed http://www.ukmeteorwatch.co.uk/cron/import_live
	function import_live()
	{
		$this->load->helper(array('directory'));
		$this->load->model(array('events_model', 'stations_model', 'meteors_model'));

		$exist_events = $this->events_model->generateList($conditions = NULL, $order = NULL, $start = 0, $limit = NULL, $key = 'event_id', $value = 'event_folder');
		$active_events = $this->events_model->generateSingleArray(array('is_event_active' => 1), 'event_id');

		$exist_stations = $this->stations_model->generateList($conditions = NULL, $order = NULL, $start = 0, $limit = NULL, $key = 'station_id', $value = 'station_folder');
		$active_stations = $this->stations_model->generateSingleArray(array('is_station_active' => 1), 'station_id');

		$map = directory_map(METEORS_LIVE_PATH);

		foreach ($map as $event => $stations)
		{
			if (!$event_id = array_search($event, $exist_events))
			{
				$event_name = preg_replace('~\d{4}~', '', $event);
				$event_name = trim(trim(str_replace(array('_', '-'), ' ', $event_name), '/'));

				$event_id = $this->events_model->save(array(
					'event_folder' => $event
					, 'event_name' => ucfirst($event_name)
					, 'event_slug' => $this->create_unique_slug($event_name, 'events', 'event_slug')
				));
			}

			$this->create_dir(METEORS_IMG_PATH . $event_id);

			foreach ($stations as $station => $images)
			{
				if (!$station_id = array_search($station, $exist_stations))
				{
					$station_name = trim(str_replace(array('_', '-'), ' ', $station), '/');
					$station_id = $this->stations_model->save(array(
						'station_folder' => $station
						, 'station_name' => ucwords($station_name)
						, 'station_slug' => $this->create_unique_slug($station_name, 'stations', 'station_slug')
					));

					$exist_stations[$station_id] = $station;
				}

				$this->create_dir(METEORS_IMG_PATH . $event_id . '/' . $station_id);

				if (count($images) && is_array($active_stations) && is_array($active_events) && in_array($station_id, $active_stations) && in_array($event_id, $active_events))
				{
					foreach ($images as $image)
					{
						$file = METEORS_LIVE_PATH . $event . $station . $image;

						if (preg_match('~^ufos\d+\.jpg$~i', $image))
						{
							@unlink($file); // remove thumb
						}
						elseif (preg_match('~^ufo\d+\.jpg$~i', $image))
						{
							$image = md5(uniqid(mt_rand())) . '.' . substr(strrchr($image, '.'), 1);

							if (copy($file, METEORS_IMG_PATH . $event_id . '/' . $station_id . '/' . $image))
							{
								$created_at = filemtime($file);

								if (unlink($file))
								{
									$this->meteors_model->save(array(
										'event_id' => $event_id
										, 'station_id' => $station_id
										, 'image' => $image
										, 'created_at' => $created_at
										, 'status' => 'pending'
									));
								}
								else
								{
									unlink(METEORS_IMG_PATH . $event_id . '/' . $station_id . '/' . $image);
								}
							}
						}
					}
				}
			}
		}
	}

	function create_dir($path = '/')
	{
		// Make sure it has a trailing slash
		$path = rtrim($path, '/') . '/';

		if (!@is_dir($path))
		{
			@mkdir($path);
			@chmod($path, 0755);

			clearstatcache();
		}
	}

	function create_unique_slug($string, $table, $field = 'slug', $key = NULL, $value = NULL)
	{
		$slug = url_title($string);
		$slug = strtolower($slug);

		$i = 0;

		$params = array();
		$params[$field] = $slug;

		if ($key)
		{
			$params["$key !="] = $value;
		}

		while ($this->db->where($params)->get($table)->num_rows())
		{
			if (!preg_match('/-{1}[0-9]+$/', $slug))
			{
				$slug .= '-' . ++$i;
			}
			else
			{
				$slug = preg_replace('/[0-9]+$/', ++$i, $slug);
			}

			$params[$field] = $slug;
		}

		return $slug;
	}

	function import_archive_events()
	{
		$this->load->model(array('archive_events_model'));

		$csv = $this->_csv_to_array(FCPATH . 'docs/materials/ULE_J8.csv');

		$csv[] = array('_code' => 'spo', '_name' => 'spo');

		foreach ($csv as $row)
		{
			$row['event_code'] = trim($row['_code'], '_');
			$row['event_name'] = trim($row['_name'], '_');

			if (empty($row['event_name']))
			{
				$row['event_name'] = $row['event_code'];
			}

			$row['event_slug'] = $this->create_unique_slug(str_replace('.', '', $row['event_name']), 'archive_events', 'event_slug');

			$event = $this->archive_events_model->findBy('event_code', $row['event_code']);

			if (!empty($row['event_code']) && !$event)
			{
				$this->archive_events_model->save($row);
			}
			else
			{
				dump('------Duplicate found------', $row);
			}
		}
	}

	function _csv_to_array($filename = '', $delimiter = ',')
	{
		//ini_set('auto_detect_line_endings',TRUE);

		if (!file_exists($filename) || !is_readable($filename)) return FALSE;

		$header = NULL;
		$data = array();
		if (($handle = fopen($filename, 'r')) !== FALSE) {

			while (($row = fgetcsv($handle, 4096, $delimiter)) !== FALSE) {

				if (!$header) {
					$header = $row;
				}
				else {
					if (count($header) > count($row)) {
						$difference = count($header) - count($row);
						for ($i = 1; $i <= $difference; $i++) {
							$row[count($row) + 1] = $delimiter;
						}
					}
					$data[] = array_combine($header, $row);
				}
			}

			fclose($handle);
		}

		return $data;
	}

	function test_dir()
	{
		$this->load->helper(array('directory', 'array'));

			$it = new RecursiveDirectoryIterator(ARCHIVES_IMG_PATH);

			$images = array();
			$dates = array();

			$pattern = '~(?:M*_).*(?:P\.jpg)~';

			foreach (new RecursiveIteratorIterator($it) as $file)
			{
				if (preg_match($pattern, $file))
				{
					$file_name = substr(strrchr($file, '/'), 1);

					$dates[substr($file_name, 1, 8)] = NULL;

					$directory_name = str_replace($file_name, '', $file);
					$directory_name = str_replace(ARCHIVES_IMG_PATH, '', $directory_name);

					$images[$directory_name][] = $file_name;
				}
			}

			$dates = shuffle_assoc($dates);

			dump($dates);
			dump($images);
	}

}