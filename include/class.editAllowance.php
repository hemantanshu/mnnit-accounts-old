<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author hemantanshu
 */
require_once 'class.allowance.php';

class editAllowance extends allowance {
    //put your code here
    public function __construct(){
        parent::__construct();
    }

    public function isEditable($id){

        if(!$this->getAllowanceTypeName($id))   //checking if there is a department with this id either in pending or in new one
                return false;

        if($this->isWorkInPendingStatus($id)){
                if(!$this->isPendingEditable($id))
                        return false;
        }
        return true;
    }

    public function updateAllowanceNameInfo($id, $name, $head, $update, $roundOff, $contribution, $nature){
        
        $sqlQuery = "SELECT name FROM bakaccounthead WHERE allowanceid = \"$id\" ";
        $sqlQuery = $this->processQuery($sqlQuery);

        if(!mysql_num_rows($sqlQuery)){
            $sqlQuery = "INSERT INTO bakaccounthead (SELECT * FROM accounthead WHERE allowanceid = \"$id\" )";
            $this->processQuery($sqlQuery);
        }
        $sqlQuery = "UPDATE bakaccounthead SET name=\"$name\", accounthead = \"$head\", allowupdate = \"$update\", roundoff = \"$roundOff\", contribution = \"$contribution\", nature = \"$nature\" WHERE allowanceid = \"$id\" ";
        $this->processQuery($sqlQuery);
        
        if($this->isAdmin()){
            $sqlQuery = "DELETE FROM accounthead WHERE allowanceid = \"$id\" ";
            $this->processQuery($sqlQuery);

            $sqlQuery = "INSERT INTO accounthead (SELECT * FROM bakaccounthead WHERE allowanceid = \"$id\" )";
            $this->processQuery($sqlQuery);

            $sqlQuery = "DELETE FROM bakaccounthead WHERE allowanceid = \"$id\" ";
            $this->processQuery($sqlQuery);
            
            $processingId = $this->setPendingWork($id);
            $this->insertProcess($processingId, 'The Account Information Has Been Updated');
        }else{
            $this->setPendingWork($id);
        }
        return;
    }
    
    public function getPendingAllowanceNameInfo($id){
        $details = array();

        $sqlQuery = "SELECT * FROM bakaccounthead WHERE allowanceid = \"$id\" ";
        $sqlQuery = $this->processQuery($sqlQuery);

        if(mysql_num_rows($sqlQuery)){
            $result = mysql_fetch_array($sqlQuery);

            array_push($details, $sqlQuery['name']);
            array_push($details, $sqlQuery['accounthead']);
            array_push($details, $sqlQuery['update']);
            array_push($details, $sqlQuery['status']);
            array_push($details, $sqlQuery['roundoff']);
            array_push($details, $sqlQuery['contribution']);            
            array_push($details, $sqlQuery['nature']);

            
        }else{
            $sqlQuery = "SELECT * FROM bakaccounthead WHERE allowanceid = \"$id\" ";
            $sqlQuery = $this->processQuery($sqlQuery);
            if(mysql_num_rows($sqlQuery)){
                $result = mysql_fetch_array($sqlQuery);

                array_push($details, $sqlQuery['name']);
                array_push($details, $sqlQuery['accounthead']);
                array_push($details, $sqlQuery['update']);
                array_push($details, $sqlQuery['status']);
                array_push($details, $sqlQuery['roundoff']);
                array_push($details, $sqlQuery['contribution']);                        
            }
        }
        return $details;

    }
    public function getPendingAllowanceInfo($id){
        $count = 0;
        $variable = array();

        $sqlQuery = "SELECT * FROM baksubheads WHERE allowanceid = \"$id\" ";
        $sqlQuery = $this->processQuery($sqlQuery);
        if(mysql_num_rows($sqlQuery)){
            while ($result = mysql_fetch_array($sqlQuery)){
                $variable[$count] = array();
                array_push($variable[$count], $result['id']);
                array_push($variable[$count], $result['value']);
                array_push($variable[$count], $result['dependent']);
                array_push($variable[$count], $result['type']);
                array_push($variable[$count], $result['status']);
                ++$count;
            }
        }else{
            $sqlQuery = "SELECT * FROM subheads WHERE allowanceid = \"$id\" && status = \"y\" ";
            $sqlQuery = $this->processQuery($sqlQuery);
            if(mysql_num_rows($sqlQuery)){
                while ($result = mysql_fetch_array($sqlQuery)){
                    $variable[$count] = array();
                    array_push($variable[$count], $result['id']);
                    array_push($variable[$count], $result['value']);
                    array_push($variable[$count], $result['dependent']);
                    array_push($variable[$count], $result['type']);
                    array_push($variable[$count], $result['status']);
                    ++$count;
                }
            }
        }
        return $variable;
    }

    public function dropAllowanceDependence($did){

        $allowanceID = $this->getAllowanceDependentDetails($did, true);

        $sqlQuery = "DELETE FROM baksubhead WHERE id = \"$did\" ";
        $this->processQuery($sqlQuery);

        if($this->isAdmin()){
            $sqlQuery = "DELETE FROM subhead WHERE id = \"$did\" ";
            $this->processQuery($sqlQuery);


            $pendingId = $this->setPendingWork($allowanceID[1]);
            $this->insertProcess($pendingId, "The Dependece of ".$this->getAllowanceTypeName($allowanceID[1])."Has Been Dropped");
        }else{
            $sqlQuery = "INSERT INTO baksubhead (SELECT * FROM subhead WHERE id = \"$did\")";
            $this->processQuery($sqlQuery);

            $sqlQuery = "UPDATE baksubhead SET status = \"\" WHERE id = \"$did\" ";
            $this->processQuery($sqlQuery);
        }
    }
    
    public function updateAllowanceInfo($did, $value, $dependent, $type){
       $status = false;
       $details = $this->getAllowanceDependentDetails($did, true);
       if($details){ //there is already some entry in the main table
           if($details[2] != $value){
               $status = true;
               $update .= "The Value Of Account Type Dependent".$this->getAllowanceTypeName($dependent)." From $details[2] to $value";
           }
           if($details[3] != $dependent){
               $status = true;
               $update .= "The Account Type Dependent Changed From".$this->getAllowanceTypeName($details[3])." to ".$this->getAllowanceTypeName($dependent)."";
           }
           if($details[4] != $type){
               $status = true;
               $update .= "The Type Of Account Type Dependent".$this->getAllowanceTypeName($dependent)." From ".$details[4] == 'c' ? 'Credit' : 'Debit'." to ".$details[4] == 'c' ? 'Credit' : 'Debit'."";
           }
       }  else {
           $details = $this->getAllowanceDependentDetails($did, false);
       }
       $sqlQuery = "SELECT value FROM baksubheads WHERE id = \"$did\" ";
       $sqlQuery = $this->processQuery($sqlQuery);

       if(!mysql_num_rows($sqlQuery)){//there is a entry in the pending state
           $sqlQuery = "INSERT INTO baksubheads (SELECT * FROM subheads WHERE id = \"$did\")";
           $this->processQuery($sqlQuery);
       }

       $sqlQuery = "UPDATE baksubheads SET value = \"$value\", dependent = \"$dependent\", type = \"$type\" WHERE id = \"$did\" ";
       $this->processQuery($sqlQuery);

       if($this->isAdmin()){
           $sqlQuery = "DELETE FROM subheads WHERE id = \"$did\" ";
           $this->processQuery($sqlQuery);

           $sqlQuery = "INSERT INTO subheads (SELECT * FROM baksubheads WHERE id = \"$did\")";
           $this->processQuery($sqlQuery);

           $sqlQuery = "DELETE FROM baksubheads WHERE id = \"$did\" ";
           $this->processQuery($sqlQuery);

           $processingId = $this->setPendingWork($details[1]);
           if($status)
               $this->insertProcess ($processingId, $update);
       }else{
           $this->setPendingWork($details[1]);
       }
       return;
    }

    public function getAllowancePendingDependentCount($id){

        $sqlQuery = "SELECT value FROM baksubheads WHERE id = \"$id\" ";
        $sqlQuery = $this->processQuery($sqlQuery);

        return mysql_num_rows($sqlQuery);
    }
}
?>
