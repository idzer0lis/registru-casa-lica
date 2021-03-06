<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

  	public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->model(['user_model', 'main_model', 'soldinitial_model', 'furnizori_model', 'date_model', 'calcul_model']);
        $this->load->helper('registru_helper'); 
            
      }
  	
  	public function index() {
            $this->load->view('login');

  	}

  	public function login_check() {
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
         		//get the posted values and check for login
    			$username = $this->input->post('username');
    			$password = $this->input->post('pwd');
              $is_user = $this->user_model->login_check($username, $password);

              if($is_user) {
              	$this->make_session($username, $password);
              	redirect(site_url('registru/'));
              } else {
                $this->session->set_flashdata('error', 'Invalid Username or Password!');
              	redirect(site_url('login/'));
              }
         }    
  	}

  	private function make_session($username, $password) {
      	$this->session->set_userdata("userdata", "secure($username, $password)");
        /*$this->session->mark_as_temp('userdata', 7200); //7200s,2hours*/
  	}

}
