<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Table extends CI_Controller {

	public function __construct() {

          parent::__construct();
          $this->load->library('session');
          $this->load->helper('form');
          $this->load->helper('url');
          $this->load->helper('html');
          $this->load->database();
          $this->load->library('form_validation');
          //load the model classes and helpers
          $this->load->model(['user_model', 'main_model', 'soldinitial_model', 'furnizori_model', 'date_model', 'calcul_model']);
          $this->load->helper('registru_helper'); 
 
     }

     public function index() {

          $idzi = $this->date_model->last_day_id() ;
 
          
          $data['server_data'] = json_encode($this->get_records($idzi)); 
          $data['server_total'] = json_encode($this->get_total($idzi)); 

          $this->load->view('index', $data);

     }

     public function get_total_json($idzi) {
          echo json_encode($this->get_total($idzi));
     }
     private function get_total($idzi) {
         return ['total_chelt'=>$this->calcul_model->get_amount_by_day('SumeCheltuieli', $idzi), 
                    'total_tva9'=> $this->calcul_model->get_amount_by_day('SumeMarfaTVA9', $idzi),
                    'total_tva24'=> $this->calcul_model->get_amount_by_day('SumeMarfaTVA24', $idzi), 
                    'total_aport'=> $this->calcul_model->get_amount_by_day('SumeAport', $idzi)];
     }

     public function get_furnizori_json() {
          echo  json_encode($this->get_furnizori());
     }

     public function get_records_json($data) {
          $idzi = $this->date_model->id_by_date($data);
          echo json_encode($this->get_records($idzi));
     }

     public function get_records($idzi) {

          $chelt = $this->main_model->get_records('SumeCheltuieli', $idzi);
          $marfa9 = $this->main_model->get_records('SumeMarfaTVA9', $idzi);
          $marfa24 = $this->main_model->get_records('SumeMarfaTVA24', $idzi);
          $aport = $this->main_model->get_records('SumeAport', $idzi);

          $soldinitial = $this->soldinitial_model->get_sold_initial($this->date_model->id_first_day_by_id($idzi));

          $zi = $this->date_model->get_id_date_by_id($idzi);

          $first_date = $this->parsed_date_to_string($this->date_model->first_day_ever()) ;

     
          $furnizori = $this->get_furnizori();

          $calcule = ['chelt' => floatval($this->calcul_model->cumul('SumeCheltuieli', $idzi)),
                      'tva9' => floatval($this->calcul_model->cumul('SumeMarfaTVA9', $idzi)), 
                      'tva24' => floatval($this->calcul_model->cumul('SumeMarfaTVA24', $idzi)), 
                      'aport' => floatval($this->calcul_model->cumul('SumeAport', $idzi)), 
                      'soldinitial' => floatval($soldinitial) ];
          
          return ['Cheltuieli' => $chelt, 'MarfaTVA9' => $marfa9, 'MarfaTVA24' => $marfa24, 'Aport' => $aport, 'zi' => $zi, 'first_date' => $first_date,
                    'furnizori' => $furnizori, 'cumuli' => $calcule] ;
     }

     function get_furnizori() {
          $furnizori_chelt = $this->furnizori_model->get_furnizori('Cheltuieli');
          $furnizori_marfa9 = $this->furnizori_model->get_furnizori('MarfaTVA9');
          $furnizori_marfa24 = $this->furnizori_model->get_furnizori('MarfaTVA24');

          return ['Cheltuieli' => $furnizori_chelt, 'MarfaTVA9' => $furnizori_marfa9, 'MarfaTVA24' => $furnizori_marfa24];
     }


     public function new_day() {
          $this->date_model->new_day();
          echo json_encode(['new_last_day' => $this->parsed_date_to_string( $this->date_model->get_date_by_id($this->date_model->last_day_id() ) ) ] );      
     }

     //$date = result of call to date_parse
     private function parsed_date_to_string($date) {
          return  join('-', [$date['year'], $date['month'], $date['day'] ] );
     }

}