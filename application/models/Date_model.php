<?php //Class responsible for date-related queries

class date_model extends CI_Model {

	function __construct() {
        parent::__construct();
        $this->load->database();
    }

    //Returns date as an array(date_parse) by IDZi
    public function date_by_id($idzi) {
    	$query = "SELECT Data FROM Zile WHERE ID = $idzi ;";

    	return date_parse($this->db->query($query)->result_array()[0]['Data']);
    }

    //Returns date+ID as an array by IDZi
    public function id_and_date($idzi) {
        $query = "SELECT * FROM Zile WHERE ID = $idzi ;";
        $result = $this->db->query($query)->result_array();
        $final = ['ID' => intval($result[0]['ID']), 'Data' => $result[0]['Data']];

        return $final;
    }
    
    //Returns the ID of the first day of the month by IDZi
    public function first_day_of_month($idzi) {
    	$data = $this->date_by_id($idzi);
    	$year = $data['year'];
    	$month = $data['month']; 	
    	$query = "SELECT ID FROM Zile WHERE Data = STR_TO_DATE('$year,$month,1','%Y,%m,%d');";

    	return $this->db->query($query)->result_array()[0]['ID'];
    }

    //Returns the Date of the first day of the month by IDzi.Depends on date_by_id()
    public function date_first_day_of_month($idzi) {
    	$year = $this->date_by_id($idzi)['year'];
        $month = $this->date_by_id($idzi)['month'];
    	$query = "SELECT Data FROM Zile WHERE Data = STR_TO_DATE('$year, $month,1' ,'%Y,%m,%d')";

    	return date_parse($this->db->query($query)->result_array()[0]['Data']);
    }

    public function id_by_date($data) {
        $query = "SELECT ID FROM Zile WHERE DATA = STR_TO_DATE('$data' ,'%Y-%m-%d')";

        return $this->db->query($query)->result_array()[0]['ID'];
    }

    public function first_day_db_entry() {
        $query = "SELECT Data FROM Zile ORDER BY DATA LIMIT 1";

        return date_parse($this->db->query($query)->result_array()[0]['Data']);
    }

    //Returns the ID of the last entry in the table
    public function last_day_id() {
    	$query = "SELECT * FROM Zile order by ID DESC LIMIT 1 ;";
    	$result = $this->db->query($query)->result_array();	

		return intval($result[0]['ID']);	
    }

    public function new_day() {
        $query = "SELECT ADDDATE((SELECT Data FROM Zile ORDER BY Data DESC LIMIT 1), INTERVAL 1 DAY) as newdate;";
        $newdate = $this->db->query($query)->result_array()[0]['newdate'];
        $query = "INSERT INTO Zile (Data) VALUES('$newdate');";

        return $this->db->query($query);
    }	

    public function __destruct() {
        $this->db->close();
    }

}