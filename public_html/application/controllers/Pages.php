<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->load->model(array('meteors_model'));

        $this->data->records = $this->meteors_model->get_live(array('status' => 'approved'), $fields = '*', 'votes_cnt DESC', 0, 12);

        $this->data->images_per_row = 4;
        $this->data->images_row_class_wrapper = 'col-md-3';
        $this->data->images = $this->load->view('meteors/images', $this->data, TRUE);

        $this->template
            ->title('Meteor Watch! live | UK Meteor Observation Network')
            ->set_metadata('description', 'Meteor watch is largest archive of meteors recorded over United Kingdom')
            ->set_metadata('keywords', '')
            ->build($this->view, $this->data);
    }

    function network()
    {
        $this->template
            ->title('Meteor Camera Network | UK Meteor Observation Network')
            ->set_metadata('description', 'Vast meteor detection camera system across UK')
            ->set_metadata('keywords', '')
            ->build($this->view, $this->data);
    }

}
