<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */

require_once 'class.sqlFunctions.php';

class personalInfo extends sqlFunction {
    private $personal;

    public function  __construct() {
        parent::__construct();
    }

    public function getEmployeeIds($flag){
        if($flag)
            $sqlQuery = "SELECT id FROM employee ORDER BY name ASC ";
        else
            $sqlQuery = "SELECT id FROM bakemployee ORDER BY name ASC ";

        $sqlQuery = $this->processQuery($sqlQuery);
        $variable = array();
        while($result = mysql_fetch_array($sqlQuery))
            array_push($variable, $result[0]);

        return $variable;
    }

    public function getEmployeeInformation($id, $flag){
        if($flag)
        { $sqlQuery = "SELECT * FROM employee WHERE id = \"$id\" ";
           
        }
        else
            $sqlQuery = "SELECT * FROM bakemployee WHERE id = \"$id\" ";
            
        $this->personal = $this->processArray($sqlQuery);
        if(sizeof($this->personal) && is_array($this->personal))
       	return true;
        return false;
    }
    
    public function getReservedEmployeeInformation($employeeId, $month){
    	
    	$sqlQuery = "SELECT * FROM salaryemployeehead WHERE employeeid = \"$employeeId\" && month = \"$month\" ";
    	$this->personal = $this->processArray($sqlQuery);
    }

    public function getEmployeeId(){
        return $this->personal['id'];
    }

    public function getSalutationId(){
        return $this->personal['salutation'];
    }

    public function getEmployeeCode(){
        return $this->personal['employeeid'];
    }
    
    public function getName(){
        return $this->personal['name'];
    }

    public function getPermanentAddress(){
        return $this->personal['padd'];
    }

    public function getTemporarAddress(){
        return $this->personal['tadd'];
    }

    public function getBloodGroup(){
        return $this->personal['bgrp'];
    }

    public function getDob(){
        return $this->personal['dob'];
    }

    public function getContactNumber(){
        return $this->personal['contact'];
    }    

    public function getDepartment(){
        return $this->personal['department'];
    }
    
    public function getHousingType(){
        return $this->personal['housing'];
    }

    public function getEmployeeType(){
        return $this->personal['type'];
    }
    
    public function getEmployeeIdFromCode($employeeCode){
        $sqlQuery = "SELECT id FROM employee WHERE employeeid = \"$employeeCode\" ";
        $sqlQuery = $this->processArray($sqlQuery);
        
        return $sqlQuery[0];
    }

public function getEmployeeIdsByQuarter($flag){
        if($flag)
            $sqlQuery = "SELECT id FROM employee ORDER BY tadd ASC ";
        else
            $sqlQuery = "SELECT id FROM bakemployee ORDER BY tadd ASC ";

        $sqlQuery = $this->processQuery($sqlQuery);
        $variable = array();
        while($result = mysql_fetch_array($sqlQuery))
            array_push($variable, $result[0]);

        return $variable;
    }

}
?>
