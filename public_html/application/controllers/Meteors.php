<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Meteors extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array('meteors_model'));
        $this->load->library(array('pagination'));
    }

    function index()
    {
     //   $file_path = APPPATH.'config/config.php';
       // require_once $file_path;
        // =========== PAGINATION ========
        $file_path = APPPATH.'config/config.php';
        require $file_path;

        $config['base_url'] = site_url('meteors');
        $config['uri_segment'] = 2;

        $pagination_config = config_item('pagination');
        if(!empty($pagination_config)) {
            $config = array_merge($config, (array)$pagination_config);
        }

        //print_r($config);

        $from = $this->uri->segment($config['uri_segment']);

        $this->data->records = $this->meteors_model->get_live(array('status' => 'approved'), $fields = '*', 'created_at DESC', intval($from), $config['per_page']);
        $this->data->records_count = $config['total_rows'] = $this->db->query('SELECT FOUND_ROWS() as count')->row()->count;

        // pull the latest date for the live image updates
        $this->data->latest_date = $this->data->records[0]->created_at;

        $this->pagination->initialize($config);

        $this->data->pagination = $this->pagination->create_links();

        $this->data->images_per_row = 3;
        $this->data->images_row_class_wrapper = 'col-md-4';
        $this->data->images = $this->load->view($this->router->class.'/images', $this->data, TRUE);

        $this->template
            ->title('Latest meteor images | UK Meteor Observation Network')
            ->set_metadata('description', 'Bringing you live stream of images from our meteor detection cameras')
            ->set_metadata('keywords', 'meteor, fireball, live camera, cctv, meteorwatch')
            ->build($this->view, $this->data);
    }

    function favourites()
    {
        // =========== PAGINATION ========
        
        $file_path = APPPATH.'config/config.php';
        require $file_path;
        $config['base_url'] = site_url('meteors/favourites');
        $config['uri_segment'] = 2;
        
        $from = $this->uri->segment($config['uri_segment']);

        $this->data->records = $this->meteors_model->get_live(array('status' => 'approved', 'votes_cnt >' => 0), $fields = '*', 'votes_cnt DESC', intval($from), $config['per_page']);
        $this->data->records_count = $config['total_rows'] = $this->db->query('SELECT FOUND_ROWS() as count')->row()->count;

        // pull the minimum votes for live images updates
        $this->data->latest_date = $this->data->records[0]->votes_cnt;
        foreach ($this->data->records as $r)
        {
            if ($r->votes_cnt < $this->data->latest_date)
            {
                $this->data->latest_date = $r->votes_cnt;
            }
        }

        $this->pagination->initialize($config);

        $this->data->pagination = $this->pagination->create_links();

        $this->data->images_per_row = 4;
        $this->data->images_row_class_wrapper = 'col-md-3';
        $this->data->images = $this->load->view($this->router->class.'/images', $this->data, TRUE);

        $this->template
            ->title('Favourite meteors | UK Meteor Observation Network')
            ->set_metadata('description', 'Best rated meteors from our live events')
            ->set_metadata('keywords', '')
            ->build($this->view, $this->data);
    }

    function combined()
    {
        $this->load->model(array('stations_model'));

        $left = $right = FALSE;

        $this->data->conds = $conds = $this->uri->uri_to_assoc(3,  array('left', 'right'));

        if (!empty($conds['left']))
        {
            $left = $this->meteors_model->get_latest(array('status' => 'approved', 'station_slug' => $conds['left']), NULL, 'created_at DESC', 0, 20);
            // data for live images
            if (count($left) > 0)
            {
                $this->data->latest_left = $left[0]->created_at;
                $this->data->station_left = $conds['left'];
            }
        }
        if (!empty($conds['right']))
        {
            $right = $this->meteors_model->get_latest(array('status' => 'approved', 'station_slug' => $conds['right']), NULL, 'created_at DESC', 0, 20);
            // data for live images
            if (count($right) > 0)
            {
                $this->data->latest_right = $right[0]->created_at;
                $this->data->station_right = $conds['right'];
            }
        }

        $this->data->images_per_row = 2;
        $this->data->images_row_class_wrapper = 'col-md-6';

        $this->data->records = $left;

        $this->data->left_images = $this->load->view($this->router->class.'/images', $this->data, TRUE);

        $this->data->records = $right;
        $this->data->right_images = $this->load->view($this->router->class.'/images', $this->data, TRUE);

        $this->data->records = FALSE;
        $this->data->stations = $this->stations_model->findAll(array('is_station_active' => 1), '*', 'station_name ASC');

        $this->template
            ->title('Combined view | UK Meteor Observation Network')
            ->set_metadata('description', 'Watch meteors from two stations at once')
            ->set_metadata('keywords', '')
            ->build($this->view, $this->data);
    }

    function detail($event = NULL, $station = NULL, $meteor_id = NULL)
    {
        $record = $this->meteors_model->find(array(
            'meteor_id' => $meteor_id
            ,'event_slug' => $event
            ,'station_slug' => $station
        ));

        if (empty($record)) {
            show_404();
        }
        /*
        $this->data->records = $this->meteors_model->get_live(array(
            'status' => 'approved',
            'votes_cnt >' => 0
        ), $fields = '*', 'votes_cnt DESC', intval($from), $config['per_page']);
        */

        $this->data->previous_record = $this->meteors_model->get_live(array(
            'status' => 'approved',
            'created_at >' => $record->created_at
        ), $fields = 'meteor_id', 'created_at ASC', 0, 1);

        $this->data->next_record = $this->meteors_model->get_live(array(
            'status' => 'approved',
            'created_at <' => $record->created_at
        ), $fields = 'meteor_id', 'created_at DESC', 0, 1);

        $this->data->record = $record;
        $this->template
            ->title('Meteor '.date('d F Y | H:i:s', $record->created_at).' | Meteor Watch! live')
            ->set_metadata('description', 'Meteor captured by '.$record->station_name .' station on '.date('d F Y H:i:s', $record->created_at).'. See latest meteors with UKMON live!')
            ->set_metadata('keywords', '')
            ->set_metadata('og:image', base_url(METEORS_IMG_URI . $record->event_id . '/' . $record->station_id . '/' . $record->image), 'property')
            ->build($this->view, $this->data);
    }
}
