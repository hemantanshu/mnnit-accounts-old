<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.pending.php';
require_once 'class.allowance.php';

class designation extends pending {

    public function  __construct() {
        parent::__construct();
    }

    public function getDesignationIds($flag){        //all the operations are done on the deparment
        if($flag)
            $query = "SELECT id FROM designation WHERE status = \"y\" ORDER BY name ASC ";
        else
            $query = "SELECT id FROM bakdesignation ORDER BY name ASC ";            
        $query = $this->processQuery($query);

        $ids = array();
        if(mysql_num_rows($query)){            
            while($result = mysql_fetch_array($query))
                array_push($ids, $result[0]);
        }
        if(!$flag){
            $query = "SELECT id FROM bakrankbenefits ORDER BY id ASC ";
            $query = $this->processQuery($query);
            while ($result = mysql_fetch_array($query)) {
                if(in_array($result[0], $ids))
                        continue;
                array_push($ids, $result[0]);
            }
        }
        return $ids;       
        
    }

    public function getDesignationDependents($id, $flag){
        if($flag)
            $sqlQuery = "SELECT did FROM rankbenefits WHERE status = \"y\" && id = \"$id\" ";
        else
            $sqlQuery = "SELECT did FROM bakrankbenefits WHERE id = \"$id\" ";      

        $sqlQuery = $this->processQuery($sqlQuery);

        $variable = array();

        while($result = mysql_fetch_array($sqlQuery))
            array_push($variable, $result[0]);

        return $variable;
    }

    public function getDesignationDependentDetails($did, $flag){
        $variable = array();

        if($flag)
            $sqlQuery = "SELECT * FROM rankbenefits WHERE did = \"$did\"  && status = \"y\" ";
        else
            $sqlQuery = "SELECT * FROM bakrankbenefits WHERE did = \"$did\" ";

        $sqlQuery = $this->processQuery($sqlQuery);

        while($result = mysql_fetch_array($sqlQuery)){
            array_push($variable, $result['id']);
            array_push($variable, $result['did']);
            array_push($variable, $result['value']);
            array_push($variable, $result['allowance']);
            array_push($variable, $result['status']);
        }
        if(sizeof($variable))
                return $variable;
        return false;
    }


    public function getAllowanceTypeName($id){
        $sqlQuery = "SELECT name FROM accounthead WHERE allowanceid = \"$id\" ORDER BY name DESC";
        $sqlQuery = $this->processArray($sqlQuery);

        return $sqlQuery[0];
    }

    public function getDesignationOptions(){
        $variable = array();

        $sqlQuery = "SELECT DISTINCT(id) FROM subheads ORDER BY name ASC ";
        $sqlQuery = $this->processQuery($sqlQuery);

        while($result = mysql_fetch_array($sqlQuery))
            array_push($variable, $result[0]);

        if(sizeof($variable))
            return $variable;

        return false;
    }
    
    public function getDesignationTypeName($id, $flag){     //the functoin to get the designation Name
        if($flag)
            $sqlQuery = "SELECT name FROM designation WHERE id = \"$id\" ORDER BY name DESC";
        else
            $sqlQuery = "SELECT name FROM bakdesignation WHERE id = \"$id\" ORDER BY name DESC";
        
        $sqlQuery = $this->processArray($sqlQuery);

        if($sqlQuery[0] == "" && $flag){
            $sqlQuery = "SELECT name FROM bakdesignation WHERE id = \"$id\" ORDER BY name DESC";
            $sqlQuery = $this->processArray($sqlQuery);
        }
       
        return $sqlQuery[0];

        
    }

    public function setDesignationName($name){
        $counter = $this->getCounter('designation');
        if($this->isAdmin()){
            $sqlQuery = "INSERT INTO designation (id, name, status) VALUES (\"$counter\", \"$name\", \"y\") ";
            $this->processQuery($sqlQuery);

            $pendingId = $this->setPendingWork($counter);
            $this->insertProcess($pendingId, "New Designation Type <i>".$name."</i> Created");
            return $counter;
        }else{
            $sqlQuery = "INSERT INTO bakdesignation (id, name, status) VALUES (\"$counter\", \"$name\", \"y\") ";
            $this->processQuery($sqlQuery);

            $this->setPendingWork($counter);
            return $counter;
        }
        return false;
    }

    public function setDesignationDependency($id, $value, $dependent){
        $counter = $this->getCounter('designationDependency');
        if($this->isAdmin()){
            $sqlQuery = "INSERT INTO rankbenefits (id, did, value, allowance, status) VALUES (\"$id\", \"$counter\", \"$value\", \"$dependent\", \"y\" ) ";
            $this->processQuery($sqlQuery);

            $pendingId = $this->setPendingWork($id);
            $this->insertProcess($pendingId, "New Designation  Dependence Type <i>".$this->getAllowanceTypeName($dependent)."</i> Created");
            return true;
        }else{
            $sqlQuery = "INSERT INTO bakrankbenefits (id, did, value, allowance, status) VALUES (\"$id\", \"$counter\", \"$value\", \"$dependent\", \"y\" ) ";
            $this->processQuery($sqlQuery);

            $pendingId = $this->setPendingWork($id);
            return true;
        }
        return false;
    }

    public function getDesignationEmployeeCount($id){        //the function that gives the total count of the employees working in a given department
        $query = "SELECT employeeid FROM ranks WHERE designation = \"$id\" && edate != \"0000-00-00\" ";
        $query = $this->processQuery($query);

        return mysql_num_rows($query);
    }

    public function getPendingDependentIds(){
        $sqlQuery = "SELECT did FROM bakrankbenefits ORDER BY id ASC";
        $sqlQuery = $this->processQuery($sqlQuery);

        $variable = array();
        while($result = mysql_fetch_array($sqlQuery))
            array_push($variable, $result[0]);

        if(sizeof($variable))
            return $variable;

        return false;
    }

    public function checkDesignationName($name){             //the function to chk for a department name if it exists already or is in the pending list
        $query = "SELECT id FROM designation WHERE name=\"$name\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query))
            return false;

        $query = "SELECT id FROM bakdesignation WHERE name=\"$name\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query))
            return false;

        return true;
    }

    public function deActivatedesignationType($id){ //@todo this will check first in whole database where the field is used and if there is still persistant which is still under use then it should give a fatal error showing the list of employees whose details has to be edited
        $this->palert("sorry it cannot be deactivated as of now", "./designation.php");
    }

}
?>
