<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Archive extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array('archives_model', 'archive_stations_model', 'archive_events_model'));
        $this->load->helper('form');

    }

    function index()
    {
        $this->load->library(array('pagination'));

        $this->data->conditions = array();

        $this->data->uri = $this->uri->uri_to_assoc(3, array('station', 'order'));
        $this->data->filter = array();
        
        // use session, remove filter from url, move filter to model 
        $this->data->filter = $this->session->userdata('archive_filter');
        $post = $this->input->post();

        //filter resetted
        if(isset($post['reset'])) {
            $this->data->filter = array();
            $this->session->unset_userdata('archive_filter');
        }
        //filter submitted
        if(isset($post['submit'])) {
            $this->data->filter = $post;
            
            //write to session post data
            $this->session->set_userdata(array('archive_filter' => $this->data->filter));
        }

        // =========== PAGINATION ========
        $file_path = APPPATH.'config/config.php';
        require $file_path;
        $config['base_url'] = site_url('archive/index/');
        $config['uri_segment'] = 3;

        $from = $this->uri->segment($config['uri_segment']);

        $this->data->records = $this->archives_model->getArchives($this->data->filter, intval($from), $config['per_page']);

        $this->data->records_count = $config['total_rows'] = $this->db->query('SELECT FOUND_ROWS() as count')->row()->count;

        $this->pagination->initialize($config);

        $this->data->pagination = $this->pagination->create_links();

        $this->data->stations = $this->archive_stations_model->findAll(NULL, '*', 'station_name ASC');
        $this->data->event_list = $this->archive_events_model->autocompleteSelect();
        $this->data->stations_count = $this->archives_model->find_stations_count($this->data->filter);

        $this->template
            ->title('Meteor database | UK Meteor Observation Network')
            ->set_metadata('description', '')
            ->set_metadata('keywords', '')
            ->build($this->view, $this->data);
    }

    function search_name() {
        $this->load->library(array('pagination'));
        
        $this->data->uri = $this->uri->uri_to_assoc(3);
        $this->data->filter = '';
        $this->data->name = '';
        
        if (!empty($this->data->uri['name']))
        {
            $this->data->name = $this->data->uri['name'];
            $this->data->filter = 'name/' . $this->data->uri['name'];
        }
        
        $file_path = APPPATH.'config/config.php';
        require $file_path;
        $config['base_url'] = site_url('archive/search_name/' . $this->data->filter);
        $config['uri_segment'] = 5;
        
        $from = $this->uri->segment($config['uri_segment']);
        
        $this->data->records = $this->archives_model->getArchives(array('name'=>$this->data->name), intval($from), $config['per_page']);
        
        $config['total_rows'] = $this->db->query('SELECT FOUND_ROWS() as count')->row()->count;
        
        $this->pagination->initialize($config);
        $this->data->pagination = $this->pagination->create_links();
        
        $this->template
            ->title('Meteor database | UK Meteor Observation Network')
            ->set_metadata('description', '')
            ->set_metadata('keywords', '')
            ->build($this->view, $this->data);
    }

    function detail($event = NULL, $station = NULL, $archive_id = NULL)
    {
        $record = $this->archives_model->find(array(
            'archive_id' => $archive_id
            ,'event_slug' => $event
            ,'station_slug' => $station
        ));

        if (empty($record)) {
            show_404();
        }

        $this->data->record = $record;

        $this->template
            ->title('Meteor '.date('d F Y | H:i:s', $record->created_at).' | UK Meteor Observation Network')
            ->set_metadata('description', 'Meteor captured by '.$record->station_name .' station on '.date('d F Y H:i:s', $record->created_at).' (BST). See latest meteors with UKMON live!')
            ->set_metadata('keywords', '')
            ->set_metadata('og:image', base_url(ARCHIVES_IMG_URI . $record->image_folder . $record->image), 'property')
            ->build($this->view, $this->data);
    }

    function stats()
    {
       $this->data->records = $this->archives_model->stats($conditions = NULL, $fields = 'event_id', $order = 'event_name ASC');

        $this->template
            ->title('Recorded meteor showers | UK Meteor Observation Network')
            ->set_metadata('description', '')
            ->set_metadata('keywords', '')
            ->build($this->view, $this->data);
    }

	function counts()
	{
		$this->data->records = array();

		$sql = "
			SELECT
				station_id,
				station_name,
				MONTH(FROM_UNIXTIME(created_at)) AS `month`,
				YEAR(FROM_UNIXTIME(created_at)) AS `year`,
				COUNT(*) as cnt
			FROM archives
			LEFT JOIN archive_stations USING (station_id)
			GROUP BY
				station_id,
				MONTH(FROM_UNIXTIME(created_at)),
				YEAR(FROM_UNIXTIME(created_at))
			ORDER BY
				year DESC, station_name ASC
		";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$this->data->records[$row->year][$row->station_name][$row->month] = $row->cnt;
			}
		}


		$this->data->stations = $this->archive_stations_model->generateList($conditions = NULL, $order = 'station_name ASC', $start = 0, $limit = NULL, $key = 'station_id', $value = 'station_name');

		$this->template
			->title('Meteor counts | UK Meteor Observation Network')
			->set_metadata('description', '')
			->set_metadata('keywords', '')
			->build($this->view, $this->data);
	}

}