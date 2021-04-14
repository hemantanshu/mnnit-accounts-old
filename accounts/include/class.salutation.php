<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.pending.php';
class salutation extends pending {

    public function  __construct() {
        parent::__construct();
    }

    public function getSalutationIds($flag){        //all the operations are done on the deparment        
        if($flag)
            $query = "SELECT id FROM options WHERE field=\"salutation\" && status = \"y\" ";
        else
            $query = "SELECT id FROM bakoptions WHERE field=\"salutation\" && status = \"y\" ";

        $query = $this->processQuery($query);

        if(mysql_num_rows($query)){
            $ids = array();
            while($result = mysql_fetch_array($query))
                array_push($ids, $result[0]);

            return $ids;
        }
        return false;
    }

    public function getSalutationName($id){     //the functoin to get the deparment name for a given salutation name
        $variable = $this->getValue("name", "options", "id", $id);

        if($variable == ""){
            $variable = $this->getValue("name", "bakoptions", "id", $id);
        }

        return $variable;
    }

    public function setSalutationName($name){   //the function to set the name of a salutation
        $counter = $this->getCounter("salutation");
        if($this->isAdmin()){
            $query = "INSERT INTO options (field, id, name, status) VALUES (\"salutation\", \"$counter\", \"$name\", \"y\") ";
            if($this->processQuery($query)){
                $pendingId = $this->setPendingWork($counter);
                $this->insertProcess($pendingId, "New Salutation <i>".$name."</i> Created");

                return true;
            }
            return false;
        }
        $query = "INSERT INTO bakoptions (field, id, name, status) VALUES (\"salutation\", \"$counter\", \"$name\", \"y\") ";
        if($this->processQuery($query)){
            $this->setPendingWork($counter);
            return true;
        }
        return false;
    }

    public function getSalutationEmployeeCount($id){        //the function that gives the total count of the employees working in a given salutation

        $query = "SELECT employeeid FROM employee WHERE salutation = \"$id\" ";
        $query = $this->processQuery($query);

        return mysql_num_rows($query);
    }

    public function checkSalutationName($name){             //the function to chk for a salutation name if it exists already or is in the pending list
        $query = "SELECT id FROM options WHERE field=\"salutation\" && name=\"$name\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query))
            return false;

        $query = "SELECT id FROM bakoptions WHERE field=\"salutation\" && name=\"$name\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query))
            return false;


        return true;
    }

    public function deActivateSalutationName($id){ //@todo this will check first in whole database where the field is used and if there is still persistant which is still under use then it should give a fatal error showing the list of employees whose details has to be edited
        static $query;
    }

    public function getSalutationPending(){     //function to get the list of pending salutations jobs
        $query = "SELECT id FROM bakoptions WHERE field=\"salutation\" ";
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
