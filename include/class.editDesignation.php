<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */

require_once 'class.designation.php';
class editDesignation extends designation {


    public function  __construct() {
        parent::__construct();
    }

    public function isEditable($id){

        if(!$this->getDesignationTypeName($id, true))   //checking if there is a department with this id either in pending or in new one
                return false;

        if($this->isWorkInPendingStatus($id)){
                if(!$this->isPendingEditable($id))
                        return false;
        }
        return true;
    }    

    public function dropDesignationDependence($did){
        $count = $this->getDesignationDependentDetails($did, true);
        $designationID = $count[0];

        $sqlQuery = "SELECT value FROM rankbenefits WHERE did = \"$did\" ";
        $sqlQuery = $this->processQuery($sqlQuery);
        if(mysql_num_rows($sqlQuery)){
            if($this->isAdmin()){
                $sqlQuery = "UPDATE rankbenefits SET status = \"\" WHERE did = \"$did\" ";
                $this->processQuery($sqlQuery);

                $sqlQuery = "DELETE FROM bakrankbenefits WHERE did = \"$did\" ";
                $this->processQuery($sqlQuery);

                $pendingId = $this->setPendingWork($designationID);
                $this->insertProcess($pendingId, "The Dependence Of Magnitude <i>".$count[2]."</i> Dependent On <i>".$this->getAllowanceTypeName($count[3])."</i> Has Been Successfully Dropped ");
                return true;
                
            }else{

                $sqlQuery = "SELECT value FROM bakrankbenefits WHERE did = \"$did\" ";
                $sqlQuery = $this->processQuery($sqlQuery);

                if(!mysql_num_rows($sqlQuery)){
                    $sqlQuery = "INSERT INTO bakrankbenefits (SELECT * FROM rankbenefits WHERE did = \"$did\")";
                    $this->processQuery($sqlQuery);                    
                }
                $sqlQuery = "UPDATE bakrankbenefits SET status = \"\" WHERE did = \"$did\" ";
                $this->processQuery($sqlQuery);

                $pendingId = $this->setPendingWork($designationID);
                return true;
            }
        }
        return false;
    }



    public function updateDesignationDependenceInfo($did, $value, $dependent){
       
       $dependentDetails = $this->getDesignationDependentDetails($did, true);       
       if($this->isAdmin()){
           $status = false;

           if($dependentDetails[2] != $value){
               $status = true;
               $update = "Value Changed From <i>".$dependentDetails[2]."</i> To <i>".$value."</i> ";
           }

           if($dependentDetails[3] != $dependent){
               $status = true;
               $update = "Allowance Type Changed From <i>".$this->getAllowanceTypeName($dependentDetails[3])."</i> To <i>".$this->getAllowanceTypeName($dependent)."</i> ";
           }

           if($status){
                $sqlQuery = "SELECT value FROM bakrankbenefits WHERE did = \"$did\" ";
                $sqlQuery = $this->processQuery($sqlQuery);

                if(mysql_num_rows($sqlQuery)){
                    $sqlQuery = "UPDATE bakrankbenefits SET value = \"$value\", allowance = \"$dependent\" WHERE did = \"$did\" ";
                    $this->processQuery($sqlQuery);

                    $sqlQuery = "DELETE FROM rankbenefits WHERE did = \"$did\" ";
                    $this->processQuery($sqlQuery);
                    
                    $sqlQuery = "INSERT INTO rankbenefits (SELECT * FROM bakrankbenefits WHERE did = \"$did\") ";
                    $this->processQuery($sqlQuery);

                    $sqlQuery = "DELETE FROM bakrankbenefits WHERE did = \"$did\" ";
                    $this->processQuery($sqlQuery);
                }else{
                    $sqlQuery = "UPDATE rankbenefits SET value = \"$value\", allowance = \"$dependent\" WHERE did = \"$did\" ";
                    $this->processQuery($sqlQuery);
                }
                
                $sqlQuery = "SELECT value FROM bakrankbenefits WHERE did = \"$did\" ";
                $sqlQuery = $this->processQuery($sqlQuery);
                
                if(mysql_num_rows($sqlQuery)){
                    $_SESSION['insertProcess'] .= $update;
                    return true;
                }else{
                    if($dependentDetails[0] == "")
                        $dependentDetails = $this->getDesignationDependentDetails($did, true);
                    
                    $pendingId = $this->setPendingWork($dependentDetails[0]);
                    $this->insertProcess($pendingId, "Dependence Info Changed : ".$_SESSION['insertProcess'].$update);
                    unset ($_SESSION['insertProcess']);
                }
                return true;
           }
           return;
       }else{
           $status = false;
           if($dependentDetails[2] != $value)
               $status = true;

           if($dependentDetails[3] != $dependent)
               $status = true;

           if($dependentDetails[0] == "")
                        $dependentDetails = $this->getDesignationDependentDetails($did, false);
           if($status){
               $sqlQuery = "SELECT value FROM bakrankbenefits WHERE did = \"$did\" ";
               $sqlQuery = $this->processQuery($sqlQuery);
               if(!mysql_num_rows($sqlQuery)){
                   $sqlQuery = "INSERT INTO bakrankbenefits (SELECT * FROM rankbenefits WHERE did = \"$did\") ";
                   $this->processQuery($sqlQuery);
               }
                $sqlQuery = "UPDATE bakrankbenefits SET value = \"$value\", allowance = \"$dependent\" WHERE did = \"$did\" ";
                $this->processQuery($sqlQuery);                

                $sqlQuery = "SELECT value FROM bakrankbenefits WHERE id = \"$dependentDetails[0]\" && did != \"$did\" ";
                $sqlQuery = $this->processQuery($sqlQuery);
                if(!mysql_num_rows($sqlQuery))
                    $this->setPendingWork($dependentDetails[0]);
           }
       }
       
       return false;
    }

    public function updateDesignationName($id, $name){
       $designationName = $this->getDesignationTypeName($id, true);
       
       
       if($this->isAdmin()){
           $sqlQuery = "SELECT name FROM designation WHERE id = \"$id\" ";
           $sqlQuery = $this->processQuery($sqlQuery);
           if(mysql_num_rows($sqlQuery))
               $status = true;

           $sqlQuery = "SELECT name FROM bakdesignation WHERE id = \"$id\" ";
           $sqlQuery = $this->processQuery($sqlQuery);

           if(mysql_num_rows($sqlQuery)){
               $sqlQuery = "UPDATE bakdesignation SET name = \"$name\" WHERE id = \"$id\" ";
               $this->processQuery($sqlQuery);

               $sqlQuery = "DELETE FROM designation WHERE id = \"$id\" ";
               $this->processQuery($sqlQuery);

               $sqlQuery = "INSERT INTO designation (SELECT * FROM bakdesignation WHERE id = \"$id\") ";
               $this->processQuery($sqlQuery);

               $sqlQuery = "DELETE FROM bakdesignation WHERE id = \"$id\" ";
               $this->processQuery($sqlQuery);
           }else{
                $sqlQuery = "UPDATE designation SET name = \"$name\" WHERE id = \"$id\" ";
                $this->processQuery($sqlQuery);
           }

           $sqlQuery = "SELECT value FROM bakrankbenefits WHERE id = \"$id\" ";
           $sqlQuery = $this->processQuery($sqlQuery);

           if(mysql_num_rows($sqlQuery)){
               if($status)
                    $_SESSION['insertProcess'] .= "Name Changed From <i>".$this->getDesignationTypeName($id, true)."</i> TO <i>".$name."</i>";
               else
                   $_SESSION['insertProcess'] .= "Name Changed From <i>".$this->getDesignationTypeName($id, true)."</i> TO <i>".$name."</i>";
           }else{
               $pendingId = $this->setPendingWork($id);
               if($status)
                   $this->insertProcess($pendingId, "Name Changed From <i>".$this->getDesignationTypeName($id, true)."</i> TO <i>".$name."</i>");
               else
                    $this->insertProcess($pendingId, "New Designation <i>".$name."</i>Created");
           }

            return true;
       }else{
            $sqlQuery = "SELECT name FROM bakdesignation WHERE id = \"$id\" ";
            $sqlQuery = $this->processQuery($sqlQuery);
            if(!mysql_num_rows($sqlQuery)){
                $sqlQuery = "INSERT INTO bakdesignation (SELECT * FROM designation WHERE id = \"$id\")";
                $this->processQuery($sqlQuery);
            }
            $sqlQuery = "UPDATE bakdesignation SET name = \"$name\" WHERE id = \"$id\" ";
            $this->processQuery($sqlQuery);
            
            $sqlQuery = "SELECT value FROM bakrankbenefits WHERE id = \"$id\" ";
            $sqlQuery = $this->processQuery($sqlQuery);
            if(!mysql_num_rows($sqlQuery)){              
                $this->setPendingWork($id);
            }
            return true;
       }
       return false;
    }

}
?>
