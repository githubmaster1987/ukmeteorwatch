<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Meteors extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array('meteors_model', 'stations_model'));
    }

    function index()
    {
        $this->load->library(array('pagination'));

        $this->data->conditions = array();

        $this->data->uri = $this->uri->uri_to_assoc(4,  array('status', 'station'));
        $this->data->filter = array();

        if (!empty($this->data->uri['station']))
        {
            $this->data->conditions ['station_slug'] = $this->data->uri['station'];
            $this->data->filter[] = 'station/' . $this->data->uri['station'];
        }

        if (!empty($this->data->uri['status']))
        {
            $this->data->conditions ['status'] = $this->data->uri['status'];
            $this->data->filter[] = 'status/' . $this->data->uri['status'];
        }

        // =========== PAGINATION ========
        
        $file_path = APPPATH.'config/config.php';
        require $file_path;
        $config['base_url'] = site_url(ADMIN_PREFIX . 'meteors/index/' . implode('/', $this->data->filter));
        $config['uri_segment'] = 4 + 2 * count($this->data->filter);


        $from = $this->uri->segment($config['uri_segment']);

        $this->data->records = $this->meteors_model->getPaginated($this->data->conditions , '*', 'created_at DESC', intval($from), $config['per_page']);
        $this->data->records_count = $config['total_rows'] = $this->db->query('SELECT FOUND_ROWS() as count')->row()->count;

        $this->pagination->initialize($config);

        $this->data->pagination = $this->pagination->create_links();

        $this->data->stations = $this->stations_model->findAll(array('is_station_active' => 1), '*', 'station_name ASC');

        $this->template
            ->title('Meteor Watch! live - UKMON')
            ->set_metadata('description', '')
            ->set_metadata('keywords', '')
            ->build($this->view, $this->data);
    }

    function action($meteor_id = NULL, $action = NULL)
    {
        if($record = $this->meteors_model->read($meteor_id))
        {
            if($action == 'delete')
            {
                $this->meteors_model->remove($meteor_id);

                @unlink(METEORS_IMG_PATH . $record->event_id . '/' . $record->station_id . '/' . $record->image);
            }
            elseif($action == 'approve')
            {
                $this->meteors_model->save(array('status' => 'approved'), $meteor_id);
            }
            elseif($action == 'pending')
            {
                $this->meteors_model->save(array('status' => 'pending'), $meteor_id);
            }
        }

        redirect(empty($_SERVER['HTTP_REFERER']) ? ADMIN_PREFIX.$this->router->class : trim($_SERVER['HTTP_REFERER']));
    }

    function delete_pending()
    {
        do
        {
            if ($records = $this->meteors_model->getPaginated(array('status' => 'pending'), '*', 'created_at ASC', 0, 50))
            {

                foreach ($records as $record)
                {
                     unlink(METEORS_IMG_PATH . $record->event_id . '/' . $record->station_id . '/' . $record->image);
                     $this->meteors_model->remove($record->meteor_id);
                }
            }
            else
            {
                redirect(empty($_SERVER['HTTP_REFERER']) ? ADMIN_PREFIX . $this->router->class : trim($_SERVER['HTTP_REFERER']));
            }
        }
        while (TRUE);
    }

}