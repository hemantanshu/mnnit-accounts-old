<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.pending.php';

class housing extends pending {

    public function  __construct() {
        parent::__construct();
    }

    public function getHousingIds($flag){        //all the operations are done on the deparment
        if($flag)
            $query = "SELECT id FROM options WHERE field=\"housing\" && status = \"y\"  ";
        else
            $query = "SELECT id FROM bakoptions WHERE field=\"housing\" && status = \"y\" ";

        $query = $this->processQuery($query);

        if(mysql_num_rows($query)){
            $ids = array();
            while($result = mysql_fetch_array($query))
                array_push($ids, $result[0]);

            return $ids;
        }
        return false;
    }

    public function getHousingTypeName($id){     //the functoin to get the deparment name for a given department name
        $variable = $this->getValue("name", "options", "id", $id);

        if($variable == ""){
            $variable = $this->getValue("name", "bakoptions", "id", $id);
        }

        return $variable;
    }

    public function getHouseTypeValue($id){     //the function returns back the value of the housing type
        $variable = $this->getValue("value", "options", "id", $id);

        if($variable == ""){
            $variable = $this->getValue("value", "bakoptions", "id", $id);
        }

        return $variable;
        }

    public function setHousingDetails($name, $value){   //the function to set the name of a department
        $counter = $this->getCounter("housing");
        if($this->isAdmin()){
            $query = "INSERT INTO options (field, id, name, value, status) VALUES (\"housing\", \"$counter\", \"$name\", \"$value\", \"y\") ";
            if($this->processQuery($query)){
                $pendingId = $this->setPendingWork($counter);
                $this->insertProcess($pendingId, "New Housing Type <i>".$name."</i> Created");

                return true;
            }
            return false;
        }
        $query = "INSERT INTO bakoptions (field, id, name, value, status) VALUES (\"housing\", \"$counter\", \"$name\", \"$value\", \"y\") ";
        if($this->processQuery($query)){
            $this->setPendingWork($counter);
            return true;
        }
        return false;
    }

    public function getHousingEmployeeCount($id){        //the function that gives the total count of the employees working in a given department
        $query = "SELECT employeeid FROM employee WHERE housing = \"$id\" ";
        $query = $this->processQuery($query);

        return mysql_num_rows($query);
    }

    public function checkHousingName($name){             //the function to chk for a department name if it exists already or is in the pending list
        $query = "SELECT id FROM options WHERE field=\"housing\" && name=\"$name\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query))
            return false;

        $query = "SELECT id FROM bakoptions WHERE field=\"housing\" && name=\"$name\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query))
            return false;

        return true;
    }

    public function deActivateHousingName($id){ //@todo this will check first in whole database where the field is used and if there is still persistant which is still under use then it should give a fatal error showing the list of employees whose details has to be edited
        static $query;
    }

    public function getHousingPending(){     //function to get the list of pending departments jobs
        $query = "SELECT id FROM bakoptions WHERE field=\"housing\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query)){
            $ids = array();
            while($result = mysql_fetch_array($query))
                array_push($ids, $result[0]);

            return $ids;
        }
        return false;
    }
	
	public function getEmpIDByHousing($housingID)
	{
		$query = "select id from employee where housing = \"".$housingID."\" " ;
		$query = $this->processQuery($query);
        if(mysql_num_rows($query)){
            $ids = array();
            while($result = mysql_fetch_array($query))
                array_push($ids, $result[0]);

            return $ids;
        }
		return false ;
	}

}
?>
