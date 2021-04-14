<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.pending.php';
class department extends pending {

    public function  __construct() {
        parent::__construct();
    }

    public function getDepartmentIds($flag){        //all the operations are done on the deparment        
        if($flag)
            $query = "SELECT id FROM options WHERE field=\"department\" && status = \"y\" ";
        else
            $query = "SELECT id FROM bakoptions WHERE field=\"department\" && status = \"y\" ";
        
        $query = $this->processQuery($query);

        if(mysql_num_rows($query)){
            $ids = array();
            while($result = mysql_fetch_array($query))
                array_push($ids, $result[0]);

            return $ids;
        }
        return false;
    }

    public function getDepartmentName($id){     //the functoin to get the deparment name for a given department name        
        $variable = $this->getValue("name", "options", "id", $id);

        if($variable == ""){
            $variable = $this->getValue("name", "bakoptions", "id", $id);
        }
         
        return $variable;
    }

    public function setDepartmentName($name){   //the function to set the name of a department
        $counter = $this->getCounter("department");
        if($this->isAdmin()){
            $query = "INSERT INTO options (field, id, name, status) VALUES (\"department\", \"$counter\", \"$name\", \"y\") ";
            if($this->processQuery($query)){                 
                $pendingId = $this->setPendingWork($counter);
                $this->insertProcess($pendingId, "New Department <i>".$name."</i> Created");
                
                return true;
            }
            return false;
        }
        $query = "INSERT INTO bakoptions (field, id, name, status) VALUES (\"department\", \"$counter\", \"$name\", \"y\") ";
        if($this->processQuery($query)){
            $this->setPendingWork($counter);
            return true;
        }
        return false;
    }

    public function getDepartmentEmployeeCount($id){        //the function that gives the total count of the employees working in a given department
        $query = "SELECT employeeid FROM employee WHERE department = \"$id\" ";
        $query = $this->processQuery($query);

        return mysql_num_rows($query);
    }

    public function checkDepartmentName($name){             //the function to chk for a department name if it exists already or is in the pending list
        $query = "SELECT id FROM options WHERE field=\"department\" && name=\"$name\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query))
            return false;

        $query = "SELECT id FROM bakoptions WHERE field=\"department\" && name=\"$name\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query))
            return false;

        
        return true;
    }

    public function deActivateDepartmentName($id){ //@todo this will check first in whole database where the field is used and if there is still persistant which is still under use then it should give a fatal error showing the list of employees whose details has to be edited
        static $query;
    }

    public function getDepartmentPending(){     //function to get the list of pending departments jobs        
        $query = "SELECT id FROM bakoptions WHERE field=\"department\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query)){
            $ids = array();
            while($result = mysql_fetch_array($query))
                array_push($ids, $result[0]);

            return $ids;
        }
        return false;

    }  
    
}
?>
