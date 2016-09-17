<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {

    function __construct() 
	{
		parent::__construct();
		
		$this->load->driver('session');
		$this->load->library('access');
		
	}

	function index()
	{
		$this->access->logout();
		redirect('/');
	}

}

/* End of file logout.php */
/* Location: ./application/controllers/logout.php */