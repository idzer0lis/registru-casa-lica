<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Action extends CI_Controller {

	public function __construct() {
      parent::__construct();
      $this->load->model(['user_model', 'main_model', 'soldinitial_model', 'furnizori_model', 'date_model', 'calcul_model']);
      $this->load->helper('registru_helper'); 
          
    }

    public function index() {
     $this->date_model->new_day();
      
    }

    public function add_record($table, $idzi) {
          if(!$this->session_check($idzi)  ) return;
          $post = $this->input->post();
          unset($post['Furnizor']);
          $furnizor = $this->input->post('Furnizor');
          $tip_furnizor = substr($table, 4);
          $idfurnizor = $this->furnizori_model->furnizor_id($furnizor, $tip_furnizor);        

          if($idfurnizor == 0) $idfurnizor = $this->furnizori_model->new_furnizor($tip_furnizor, $furnizor);  
          $post['IDFurnizor'] = $idfurnizor;

          $this->main_model->new_record($table, $post);

          echo json_encode($this->main_model->get_last_record($table));
          
     }

     public function add_aport($idzi, $suma) {
          $this->main_model->new_record('SumeAport', ['idzi'=> $idzi, 'Suma'=> $suma]);
          echo json_encode($this->main_model->get_last_aport($idzi));
          
     }

     private function edit($table, $id) {
        $idzi = $this->main_model->idzi_by_id($table, $id);
        
        if(is_null($idzi) || $this->session_check($idzi)) {

            $post = $this->input->post();
            unset($post['Furnizor']);

            $furnizor = $this->input->post('Furnizor');
            $tip_furnizor = substr($table, 4);
            $idfurnizor = $this->furnizori_model->furnizor_id($furnizor, $tip_furnizor);


            if($idfurnizor == 0) $idfurnizor = $this->furnizori_model->new_furnizor($tip_furnizor, $furnizor);  
            $post['IDFurnizor'] = $idfurnizor;

            $this->main_model->edit_record($table, $id, $post);
            echo json_encode($this->main_model->get_record_by_id($table, $id)); 
            return true;

        } 
        return false;
    }

    public function edit_record($table, $id) {
      return $this->edit($table, $id);
    }

     private function delete($table, $id) {
        $idzi = $this->main_model->idzi_by_id($table, $id);
        
        if(is_null($idzi) || $this->session_check($idzi)) {

          $this->main_model->delete_record($table, $id); 
          return true;     
        } 
        return false;
     }

     public function delete_record($table, $id) {
        echo $this->delete($table, $id);
     }

     public function edit_sold_initial($idzi, $sum) {
        if(!$this->session_check($idzi)) return;
        $this->soldinitial_model->edit_sold_initial($idzi, $sum);
            
     }

     private function session_check($idzi) {
          return isset($_SESSION['userdata']) || $this->date_model->last_day_id() == $idzi;
    }

    public function loggedin() {
      echo isset($_SESSION['userdata']);
    }
}