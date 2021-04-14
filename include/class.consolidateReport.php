<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
////error_reporting(0);

require_once 'class.reporting.php';

class consolidateReport extends reporting {
      
    public function  __construct() {
        parent::__construct();
    }

    public function getEmployeeTypeDepartmentReportAmount($type, $department, $accountHead, $month){
    	$sqlQuery = "SELECT SUM(amount) FROM salary WHERE month = \"$month\" && accounthead = \"$accountHead\" && type = \"c\" && employeeid IN (SELECT employeeid FROM salaryemployeehead WHERE month = \"$month\" && type = \"$type\" && department = \"$department\")";    	    	
    	$sqlQuery = $this->processArray($sqlQuery);    	
    	$sum = $sqlQuery[0];
    	
    	$sqlQuery = "SELECT SUM(amount) FROM salary WHERE month = \"$month\" && accounthead = \"$accountHead\" && type = \"d\" && employeeid IN (SELECT employeeid FROM salaryemployeehead WHERE month = \"$month\" && type = \"$type\" && department = \"$department\")";    	
    	$sqlQuery = $this->processArray($sqlQuery);    	
    	$sum -= $sqlQuery[0];

    	//$sqlQuery = "SELECT SUM(amount) FROM collegecontribution WHERE month = \"$month\" && accountheadid = \"$accountHead\" && employeeid IN (SELECT employeeid FROM salaryemployeehead WHERE month = \"$month\" && type = \"$type\" && department = \"$department\")";
    	//$sqlQuery = $this->processArray($sqlQuery);
    	//$sum -= $sqlQuery[0];
   	
    	if ($sum == 0)
    		return false;
    	return $sum;
    }
    
	public function getEmployeeTypeReportAmount($type, $accountHead, $month){
    	$sqlQuery = "SELECT SUM(amount) FROM salary WHERE month = \"$month\" && accounthead = \"$accountHead\" && type = \"c\" && employeeid IN (SELECT employeeid FROM salaryemployeehead WHERE month = \"$month\" && type = \"$type\")";    	
    	$sqlQuery = $this->processArray($sqlQuery);    	
    	$sum = $sqlQuery[0];
    	
    	$sqlQuery = "SELECT SUM(amount) FROM salary WHERE month = \"$month\" && accounthead = \"$accountHead\" && type = \"d\" && employeeid IN (SELECT employeeid FROM salaryemployeehead WHERE month = \"$month\" && type = \"$type\")";
    	$sqlQuery = $this->processArray($sqlQuery);
    	$sum -= $sqlQuery[0];

        $sqlQuery = "SELECT SUM(amount) FROM collegecontribution WHERE month = \"$month\" && accountheadid = \"$accountHead\" && employeeid IN (SELECT employeeid FROM salaryemployeehead WHERE month = \"$month\" && type = \"$type\")";
    	$sqlQuery = $this->processArray($sqlQuery);
    	$sum -= $sqlQuery[0];

        
   	
    	if ($sum == 0)
    		return false;
    	return $sum;
    }
    
    public function getDepartmentReportAmount($department, $accountHead, $month){
    	$sqlQuery = "SELECT SUM(amount) FROM salary WHERE month = \"$month\" && accounthead = \"$accountHead\" && type = \"c\" && employeeid IN (SELECT employeeid FROM salaryemployeehead WHERE month = \"$month\" && department = \"$department\")";
    	$sqlQuery = $this->processArray($sqlQuery);    	
    	$sum = $sqlQuery[0];
    	
    	$sqlQuery = "SELECT SUM(amount) FROM salary WHERE month = \"$month\" && accounthead = \"$accountHead\" && type = \"d\" && employeeid IN (SELECT employeeid FROM salaryemployeehead WHERE month = \"$month\" && department = \"$department\" )";
    	$sqlQuery = $this->processArray($sqlQuery);    	
    	$sum -= $sqlQuery[0];

        $sqlQuery = "SELECT SUM(amount) FROM collegecontribution WHERE month = \"$month\" && accountheadid = \"$accountHead\" && employeeid IN (SELECT employeeid FROM salaryemployeehead WHERE month = \"$month\" && department = \"$department\" )";
    	$sqlQuery = $this->processArray($sqlQuery);
    	$sum -= $sqlQuery[0];  	

    	if ($sum == 0)
    		return false;
    	return $sum;
    }
    
	public function getCompleteReportAmount($accountHead, $month){
    	$sqlQuery = "SELECT SUM(amount) FROM salary WHERE month = \"$month\" && accounthead = \"$accountHead\" && type = \"c\"";
    	$sqlQuery = $this->processArray($sqlQuery);    	
    	$sum = $sqlQuery[0];
    	
    	$sqlQuery = "SELECT SUM(amount) FROM salary WHERE month = \"$month\" && accounthead = \"$accountHead\" && type = \"d\"";
    	$sqlQuery = $this->processArray($sqlQuery);
    	$sum -= $sqlQuery[0];

        $sqlQuery = "SELECT SUM(amount) FROM collegecontribution WHERE month = \"$month\" && accountheadid = \"$accountHead\" ";
    	$sqlQuery = $this->processArray($sqlQuery);
    	$sum -= $sqlQuery[0];        

    	if ($sum == 0)
    		return false;
    	return $sum;
    }
    
    

    public function getTotalEmployeeCount($type , $flag, $month){
    	if ($type == "all")
    		$sqlQuery = "SELECT COUNT(id) FROM salaryemployeehead WHERE month = \"$month\" ";
    	else{
    		if ($flag)
	    		$sqlQuery = "SELECT COUNT(id) FROM salaryemployeehead WHERE type = \"$type\" && month = \"$month\" ";
	    	else 
	    		$sqlQuery = "SELECT COUNT(id) FROM salaryemployeehead WHERE department = \"$type\" && month = \"$month\" ";	
    	}    	
    	
    	$sqlQuery = $this->processArray($sqlQuery);
    	if ($sqlQuery[0] == 0)
    		return false;
    	return $sqlQuery[0];    	
    }


    public function getCompleteCollegeContribution($accountHeadId, $month){
    	if (func_num_args() > 2){
            if(func_num_args () > 3){
            	if(func_get_arg(2) == "all")
                	$sqlQuery = "SELECT SUM(amount) FROM collegecontribution WHERE accountheadid = \"$accountHeadId\" && month = \"$month\" && employeeid in (SELECT employeeid FROM salaryemployeehead WHERE department = \"".  func_get_arg(3)."\" && month = \"$month\") ";
                else 
                	$sqlQuery = "SELECT SUM(amount) FROM collegecontribution WHERE accountheadid = \"$accountHeadId\" && month = \"$month\" && employeeid in (SELECT employeeid FROM salaryemployeehead WHERE type = \"".func_get_arg(2)."\" && department = \"".  func_get_arg(3)."\" && month = \"$month\") ";	
            }
            else
    		$sqlQuery = "SELECT SUM(amount) FROM collegecontribution WHERE accountheadid = \"$accountHeadId\" && month = \"$month\" && employeeid in (SELECT employeeid FROM salaryemployeehead WHERE type = \"".func_get_arg(2)."\" && month = \"$month\") ";
    	}else{
    		$sqlQuery = "SELECT SUM(amount) FROM collegecontribution WHERE accountheadid = \"$accountHeadId\" && month = \"$month\" ";
    	}
        $sqlQuery = $this->processArray($sqlQuery);

        return $sqlQuery[0];
    }


    
    
}
?>
