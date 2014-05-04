<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Online extends MX_Controller {


	public function index() {
		$this->load->model('online_mapper');
		$this->online_mapper->get_all();
		$this->load->view('online_message');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */