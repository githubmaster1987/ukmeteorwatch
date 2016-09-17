<?php (defined('BASEPATH')) or exit ;

	/**
	 * Live update controller
	 * Written for empire-elements
	 *
	 * @author Dale Stevenson
	 * @package ukmeteorwatch
	 */
	
	class Live_images extends Public_Controller
	{
		public function __construct()
		{
			parent::__construct();
	
			$this->load->model(array('meteors_model'));
		}
	
		/**
		 * has_update
		 * return 0 or 1 depending on whether or not we have had a new image
		 *
		 * @param string $since the date string to compare against
		 * @return bool true false
		 */
		public function has_update($type = 'meteors')
		{
			switch ($type)
			{
				case 'meteors':
					$this->data->records = $this->meteors_model->get_live(
						array(
							'status' => 'approved',
							'created_at >' => $this->input->get('latest_date')
						), $fields = '*', 'created_at DESC', 0, 1
					);
					break;
				
				case 'favourites':
					$this->data->records = $this->meteors_model->get_live(
						array(
							'status' => 'approved',
							'votes_cnt >' => 0
						), $fields = '*', 'votes_cnt DESC', 0, 12
					);
					$compare = array_pop($this->data->records);
					if ($compare->votes_cnt > $this->input->get('latest_date'))
					{
						$this->data->records = array(true);
					}
					else
					{
						$this->data->records = array();
					}
					break;
					
				case 'combined':
					extract($_GET);
					$left = $right = array();
					if ( ! empty($latest_left))
					{
						$left = $this->meteors_model->get_latest(
							array(
								'status' => 'approved',
								'station_slug' => $station_left,
								'created_at >' => $latest_left
							), NULL, 'created_at DESC', 0, 1
						);
					}
					if ( ! empty($latest_right))
					{
						$right = $this->meteors_model->get_latest(
							array(
								'status' => 'approved',
								'station_slug' => $station_right,
								'created_at >' => $latest_right
							), NULL, 'created_at DESC', 0, 1
						);
					}
					$this->data->records = array_merge($left, $right);
					break;
				
				default:
					$this->data->records = array();
					break;
			}
				
			if (count($this->data->records) == 0)
			{
				echo 0;
				return;
			}
			echo 1;
			return;
		}
	}
