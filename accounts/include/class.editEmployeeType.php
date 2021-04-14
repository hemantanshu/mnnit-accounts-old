<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */

require_once 'class.employeeType.php';
class editEmployeeType extends employeeType {


    public function  __construct() {
        parent::__construct();
    }

    public function isEditable($id){

        if(!$this->getEmployeeTypeName($id))   //checking if there is a employeeType with this id either in pending or in new one
                return false;

        if($this->isWorkInPendingStatus($id)){
                if(!$this->isPendingEditable($id))
                        return false;
        }
        return true;
    }

    public function getPendingEmployeeTypeInfo($id){

        $sqlQuery = "SELECT name, status FROM bakoptions WHERE id = \"$id\" ";
        if($sqlQuery = $this->processArray($sqlQuery)){
            $variable = array();

            array_push($variable, $sqlQuery['name']);
            array_push($variable, $sqlQuery['status']);

            return $variable;
        }

        return false;
    }



    public function updateEmployeeTypeName($id, $name){
       $deptName = $this->getEmployeeTypeName($id);

       $sqlQuery = "SELECT status FROM bakoptions WHERE id = \"$id\" ";
       $sqlQuery = $this->processQuery($sqlQuery);

       if(mysql_num_rows($sqlQuery)){    //the option is already in the pending status
           if($this->isAdmin()){
               $pendingId = $this->setPendingWork($id);

               $sqlQuery = "UPDATE bakoptions SET name = \"$name\" WHERE id = \"$id\" && field = \"employeeType\" ";
               $this->processQuery($sqlQuery);


               $sqlQuery = "SELECT name FROM options WHERE id = \"$id\" ";
               $sqlQuery = $this->processQuery($sqlQuery);

               if(mysql_num_rows($sqlQuery)){
                   $sqlQuery = "DELETE FROM options WHERE id = \"$id\" ";
                   $this->processQuery($sqlQuery);

                   $this->insertProcess($pendingId, "EmployeeType Name Changed From <i>".$deptName."</i> to <i>".$name."</i> ");
               }
               else
                    $this->insertProcess($pendingId, "New EmployeeType <i>".$name."</i> Formed ");



               $sqlQuery = "INSERT INTO options (SELECT * FROM bakoptions WHERE id=\"$id\" && field = \"employeeType\" )";
               $this->processQuery($sqlQuery);

               $sqlQuery = "DELETE FROM bakoptions WHERE id = \"$id\" ";
               $this->processQuery($sqlQuery);

                return true;
           }
           elseif($this->isSupretendent()){
                $this->setPendingWork($id);

                $sqlQuery = "UPDATE bakoptions SET name = \"$name\" WHERE id = \"$id\" ";
                $this->processQuery($sqlQuery);

                return true;
           }else{
                $sqlQuery = "UPDATE bakoptions SET name = \"$name\" WHERE id = \"$id\" ";
                $this->processQuery($sqlQuery);

                $this->setPendingWork($id);
                return true;
           }
       }else{                           //the option is not in the pending list

           if($this->isAdmin()){
               $sqlQuery = "UPDATE options SET name = \"$name\" WHERE id = \"$id\" && field = \"employeeType\" ";
               $this->processQuery($sqlQuery);
               $pendingId = $this->setPendingWork($id);

               if($this->insertProcess($pendingId, "EmployeeType Name Changed From <i>".$deptName."</i> to <i>".$name."</i> "))
                return true;

           }elseif ($this->isSupretendent()) {
               $sqlQuery = "INSERT INTO bakoptions (field, id, name, status) VALUES (\"employeeType\", \"$id\", \"$name\", \"y\" )";
               $this->processQuery($sqlQuery);

               $this->setPendingWork($id);

               return true;
           }else{

               $sqlQuery = "INSERT INTO bakoptions (field, id, name, status) VALUES (\"employeeType\", \"$id\", \"$name\", \"y\" )";
               $this->processQuery($sqlQuery);

               $this->setPendingWork($id);
               return true;
           }

        }
    }   

}
?>
