<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
////error_reporting(0);

require_once 'class.accountInfo.php';

class reporting extends accounts {

    public $totalEarnings;
    public $totalDeductions;
    public $accountHeadDetails;
	  
    public function  __construct() {
        parent::__construct();

    }

    public function nameAmount($x){
            $x = (int) $x;

            $nwords = array(	"zero", "one", "two", "three", "four", "five", "six", "seven",
                                            "eight", "nine", "ten", "eleven", "twelve", "thirteen",
                                            "fourteen", "fifteen", "sixteen", "seventeen", "eighteen",
                                            "nineteen", "twenty", 30 => "thirty", 40 => "forty",
                                            50 => "fifty", 60 => "sixty", 70 => "seventy", 80 => "eighty",
                                            90 => "ninety" );
            if(!is_numeric($x))
            {
                    $w = '#';
            }else if(fmod($x, 1) != 0)
            {
                    $w = '#';
            }else{
                    if($x < 0)
                    {
                            $w = 'minus ';
                            $x = -$x;
                    }else{
                            $w = '';
                    }
                    if($x < 21)
                    {
                            $w .= $nwords[$x];
                    }else if($x < 100)
                    {
                            $w .= $nwords[10 * floor($x/10)];
                            $r = fmod($x, 10);
                            if($r > 0)
                            {
                                    $w .= '-'. $nwords[$r];
                            }
                    } else if($x < 1000)
                    {
                            $w .= $nwords[floor($x/100)] .' hundred';
                            $r = fmod($x, 100);
                            if($r > 0)
                            {
                                    $w .= ' and '.$this->nameAmount($r);
                            }
                    } else if($x < 1000000)
                    {
                            $w .= $this->nameAmount(floor($x/1000)) .' thousand';
                            $r = fmod($x, 1000);
                            if($r > 0)
                            {
                                    $w .= ' ';
                                    if($r < 100)
                                    {
                                            $w .= 'and ';
                                    }
                                    $w .= $this->nameAmount($r);
                            }
                    } else {
                            $w .= $this->nameAmount(floor($x/1000000)) .' million';
                            $r = fmod($x, 1000000);
                            if($r > 0)
                            {
                                    $w .= ' ';
                                    if($r < 100)
                                    {
                                            $word .= 'and ';
                                    }
                                    $w .= $this->nameAmount($r);
                            }
                    }
            }
            return ucwords($w);
	}
	
	public function isEmployeeSalaryProcessed($employeeId, $month){
		$sqlQuery = "SELECT id FROM salary WHERE employeeid = \"$employeeId\" && month = \"$month\" LIMIT 1";
		$sqlQuery = $this->processQuery($sqlQuery);
		
		if(mysql_num_rows($sqlQuery))
			return true;
		return false;
	}

    public function getProcessedSalaryInformation($employeeId, $endDate, $startDate){        
        if(func_num_args () == 2 || $startDate == ""){ //this is the request for the salary slip calculation
            $variable = $this->getMonthSession($endDate);
            $sDate = $this->getSessionDetails($variable);
            $sDate = explode('-', $sDate[2]);
            $sYear = $sDate[0];
            $sMonth = $sDate[1];
            $sDate = date('Ym', mktime(0, 0, 0, $sMonth - 1, 15, $sYear));           			
        }else{
            $sDate = $startDate;
        }

        $sqlQuery = "SELECT distinct(accounthead) FROM salary WHERE employeeid = \"$employeeId\" && month >= \"$sDate\" && month <= \"$endDate\" ORDER BY accounthead ASC";
        $sqlQuery = $this->processQuery($sqlQuery);

        $accountHead = array();
        while($result = mysql_fetch_array($sqlQuery)){        	
            array_push($accountHead, $result[0]);
        }
        //CALCULATING THE TOTAL OF THE AMOUNTS IN EACH ACCOUNT HEADS. THE FIRST THE ADDITIONS AND THEN THE DEDUCTIONS;
        
        $salary = array();
        
        foreach ($accountHead as $value){
            $sqlQuery = "SELECT sum(amount) FROM salary WHERE type = \"c\" && accounthead = \"$value\" && employeeid = \"$employeeId\" && month >= \"$sDate\" && month <= \"$endDate\" ";
            $sqlQuery = $this->processArray($sqlQuery);
            $salary[$value] = $sqlQuery[0];

            $sqlQuery = "SELECT sum(amount) FROM salary WHERE type = \"d\" && accounthead = \"$value\" && employeeid = \"$employeeId\" && month >= \"$sDate\" && month <= \"$endDate\" ";
            $sqlQuery = $this->processArray($sqlQuery);
            $salary[$value] -= $sqlQuery[0];
        }
		
        //sorting according to the deductions and the earnings so that it can be easily processed in the viewing side
        
        $this->totalDeductions = array();
        $this->totalEarnings = array();
        
        foreach ($salary as $key => $value) {
            if($value >= 0 )
                $this->totalEarnings[$key] = $value;
            else
                $this->totalDeductions[$key] =  0 - $value;
        }
        return;
    }

	public function getFiscalYearMonth($date){
		$fiscalId = $this->getMonthSession($date);
		$details = $this->getSessionDetails($fiscalId);		
				
		$startdate = explode('-', $details[2]);
		$details = array();
		array_push($details, $startdate[0]);
		array_push($details, $startdate[1]);
		
                
		return $details;
	}
        
	public function getEmployeeId($department, $employeeType, $date){
		$sqlQuery = "SELECT id FROM employee WHERE id IN (SELECT employeeid FROM salaryemployeehead WHERE department = \"$department\" && type = \"$employeeType\" && month = \"$date\") ORDER BY name ASC ";
		$sqlQuery = $this->processQuery($sqlQuery);
		
		$employeeId = array();
		while($result = mysql_fetch_array($sqlQuery)){
			array_push($employeeId, $result[0]);
		}
		if(sizeof($employeeId) > 0)
			return $employeeId;
		return false;
	}
	
	public function getEmployeeReservedBankAccountDetails($employeeId, $date){
    	static $sqlQuery;

        $sqlQuery = "SELECT bank, accountno FROM salaryemployeehead WHERE employeeid = \"$employeeId\" && month = \"$date\" ";
        $sqlQuery = $this->processArray($sqlQuery);

        return $sqlQuery;
    }
    
    public function getProcessedSalaryEmployeeTypes($month){
    	$sqlQuery = "SELECT DISTINCT(type) FROM salaryemployeehead WHERE month = \"$month\" ORDER BY type ASC";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$details = array();
    	while($result = mysql_fetch_array($sqlQuery)){
    		array_push($details, $result[0]);
    	}
    	if(sizeof($details))
    		return $details;
    	return false;
    }
    
    public function getProcessedSalaryEmployeeTypeDepartments($employeeType, $month){
    	$sqlQuery = "SELECT DISTINCT(department) FROM salaryemployeehead WHERE month = \"$month\" && type = \"$employeeType\" ORDER BY department ASC";    	
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$details = array();
    	while($result = mysql_fetch_array($sqlQuery)){
    		array_push($details, $result[0]);
    	}
    	if(sizeof($details))
    		return $details;
    	return false;
    }
    
	public function getCompleteDepartmentId($month){
		if (func_num_args() > 1){
			if (func_get_arg(1) != "")
				$sqlQuery = "SELECT DISTINCT(department) FROM salaryemployeehead WHERE month = \"$month\" && type = \"".func_get_arg(1)."\" ORDER BY department ASC";
			else 
				$sqlQuery = "SELECT DISTINCT(department) FROM salaryemployeehead WHERE month = \"$month\" ORDER BY department ASC";
		}else 
			$sqlQuery = "SELECT DISTINCT(department) FROM salaryemployeehead WHERE month = \"$month\" ORDER BY department ASC";    	
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$details = array();
    	while($result = mysql_fetch_array($sqlQuery)){
    		array_push($details, $result[0]);
    	}
    	if(sizeof($details))
    		return $details;
    	return false;
    }
    
    public function getCompleteAccountHead($month){
    	$sqlQuery = "SELECT DISTINCT(accounthead) FROM salary WHERE month = \"$month\" ORDER BY accounthead ASC ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$details = array();
    	while($result = mysql_fetch_array($sqlQuery)){
    		array_push($details, $result[0]);
    	}
    	if(sizeof($details))
    		return $details;
    	return false;
    }
    
    public function getCumulativeProcessedSalaryData($month, $employeetype, $department){
    	
    	$completeAccountHead = $this->getCompleteAccountHead($month);
    	$details = array();
    	
    	foreach ($completeAccountHead as $accounthead){
    		$query = "SELECT sum(amount) FROM salary WHERE month = \"$month\" && accounthead = \"$accounthead\" && type = \"c\" && employeeid IN (SELECT employeeid FROM salaryemployeehead WHERE month = \"$month\" && type = \"$employeetype\" && department = \"$department\")";    		
    		$query = $this->processArray($query);
    		$amount = (int) $query[0];
    		
    		$query = "SELECT sum(amount) FROM salary WHERE month = \"$month\" && accounthead = \"$accounthead\" && type = \"d\" && employeeid IN (SELECT employeeid FROM salaryemployeehead WHERE month = \"$month\" && type = \"$employeetype\" && department = \"$department\")";    		
    		$query = $this->processArray($query);
    		
    		$amount -= (int) $query[0];
    		$details[$accounthead] = $amount;
    	}
    	return $details;
    }
    
    public function getTotalEmployeeCount($date, $employeeType, $department){
    	$sqlQuery = "Select DISTINCT(employeeid) FROM salaryemployeehead WHERE month = \"$date\" && type = \"$employeeType\" && department = \"$department\" ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	return mysql_num_rows($sqlQuery);
    }
    
    public function isSalaryDataAvailiable($month){
    	$sqlQuery = "SELECT * FROM salary WHERE month = \"$month\" LIMIT 1";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	if(mysql_num_rows($sqlQuery))
    		return true;
    	return false;
    }
    
    public function getDistinctSalaryProcessedEmployeeId($sMonth, $eMonth){
    	$sqlQuery = "SELECT DISTINCT(employeeid) FROM salaryemployeehead WHERE month >= \"$sMonth\" && month <= \"$eMonth\" ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	$employeeId = array();
    	while($result = mysql_fetch_array($sqlQuery))
    		array_push($employeeId, $result[0]);
    	if(sizeof($employeeId, 0))
    		return $employeeId;
    	return false;
    }

    public function getSalaryAllowanceInfo($employeeId, $month, $allowanceId, $type){
    	if($allowanceId == "total")
    		return $this->getProcessedSalarySum($employeeId, $month);
    	if($allowanceId == "gross"){
    		$sqlQuery = "SELECT SUM(amount) FROM salary WHERE employeeid = \"$employeeId\" && month = \"$month\" && type = \"c\"";
    		$sqlQuery = $this->processArray($sqlQuery);    		
    		return $sqlQuery[0];    		
    	}
    	if($allowanceId == "deduction"){
    		$sqlQuery = "SELECT SUM(amount) FROM salary WHERE employeeid = \"$employeeId\" && month = \"$month\" && type = \"d\"";
    		$sqlQuery = $this->processArray($sqlQuery);    		
    		return $sqlQuery[0];  
    	}    	
    	
    	if($type){
    		$sqlQuery = "SELECT SUM(amount) FROM salary WHERE allowanceid = \"$allowanceId\" && employeeid = \"$employeeId\" && month = \"$month\" && type = \"c\"";    	
    		$sqlQuery = $this->processArray($sqlQuery);
    		$sum = $sqlQuery[0];
    		
    		$sqlQuery = "SELECT SUM(amount) FROM salary WHERE allowanceid = \"$allowanceId\" && employeeid = \"$employeeId\" && month = \"$month\" && type = \"d\"";
    		$sqlQuery = $this->processArray($sqlQuery);
    		$sum -= $sqlQuery[0];    		
    	}else{
    		$sqlQuery = "SELECT SUM(amount) FROM salary WHERE accounthead = \"$allowanceId\" && employeeid = \"$employeeId\" && month = \"$month\" && type = \"c\"";
    		$sqlQuery = $this->processArray($sqlQuery);
    		$sum = $sqlQuery[0];
    		
    		$sqlQuery = "SELECT SUM(amount) FROM salary WHERE accounthead = \"$allowanceId\" && employeeid = \"$employeeId\" && month = \"$month\" && type = \"d\"";
    		$sqlQuery = $this->processArray($sqlQuery);
    		$sum -= $sqlQuery[0];
    	}
    	
    	return $sum;
    }   
    
    public function getEmployeeSalaryEmolument($employeeId, $sDates, $eDate){
    	if (!$this->isSalaryDataAvailiable($sDates))
    		$sDate = $this->getPreviousMonth($this->getFiscalYearMonth($sDates));
    	else 
    		$sDate = $sDates;
    	
    	$data = array();
    	
    	$sqlQuery = "SELECT sum(amount) FROM salary WHERE employeeid = \"$employeeId\" && month >= \"$sDate\" && month <= \"$eDate\" && type = \"c\"";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	$data[0] = $sqlQuery[0];
    	
    	$sqlQuery = "SELECT sum(amount) FROM salary WHERE employeeid = \"$employeeId\" && month >= \"$sDate\" && month <= \"$eDate\" && type = \"d\"";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	$data[1] = 0 - $sqlQuery[0];
    	
    	return $data;   	
    }
    
    public function getReservedEmployeeLoanId($employeeId, $sDate, $flag){
    	if($flag == "gpf")
    		$sqlQuery = "SELECT id FROM salaryloanhead WHERE employeeid = \"$employeeId\" && month = \"$sDate\" && flag = \"g\"";
    	else
    		$sqlQuery = "SELECT id FROM salaryloanhead WHERE employeeid = \"$employeeId\" && month = \"$sDate\" && flag = \"l\"";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	$variable = array();
    	while ($result = mysql_fetch_array($sqlQuery))
    		array_push($variable, $result[0]);
    	if(sizeof($variable, 0))
    		return $variable;
    	return false;    	
    }
    
    public function getReservedEmployeeLoanIdDetails($id){
    	$sqlQuery = "SELECT * FROM salaryloanhead WHERE id = \"$id\"";
    	return $this->processArray($sqlQuery);
    }    
    
    public function getCollegeContributionAmount($employeeId, $month){
    	$sqlQuery = "SELECT SUM(amount) FROM collegecontribution WHERE employeeid = \"$employeeId\" && month = \"$month\" ";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	if ($sqlQuery[0] != 0)
    		return $sqlQuery[0];
    	return false;
    }

    public function getEmployeeAccountHeadIds($employeeId, $sDate, $eDate){
        $sqlQuery = "SELECT DISTINCT(accounthead) FROM salary WHERE employeeid = \"$employeeId\" && month >= \"$sDate\" && month <= \"$eDate\" ORDER BY accounthead ASC";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	$employeeId = array();
    	while($result = mysql_fetch_array($sqlQuery))
    		array_push($employeeId, $result[0]);
    	if(sizeof($employeeId, 0))
    		return $employeeId;
    	return false;
    }


    //delete it after all operation is done 
    public function updateSalaryInformation($salaryId, $amount, $type){
        if(func_num_args () > 3){
            $sqlQuery = "DELETE FROM salary WHERE id = \"$salaryId\" ";
            $this->processQuery($sqlQuery);

            return true;
        }else{
            $sqlQuery = "UPDATE salary SET amount = \"".abs($amount)."\", type=\"$type\" WHERE id = \"$salaryId\"";            
            $this->processQuery($sqlQuery);

            return true;
        }
    }

    public function insertNewRow($salaryId, $allowanceId, $accountHead, $amount, $type){
        $details = $this->getSalaryIdDetails($salaryId);
        $counter = $this->getCounter('salary');

        $sqlQuery = "INSERT INTO salary(id, did, employeeid, allowanceid, accounthead, amount, type, month) VALUES (\"$counter\", \"$details[1]\", \"$details[2]\", \"$allowanceId\", \"$accountHead\", \"$amount\", \"$type\", \"$details[7]\")";
        $this->processQuery($sqlQuery);
    }
    
    public function deleteSalaryRow($salaryId){
    	$sqlQuery = "DELETE FROM salary WHERE id = \"$salaryId\" ";
    	$this->processQuery($sqlQuery);
    }
    
    public function updateGpfAmount($employeeId, $month, $type, $amount){
        if($type){
            $sqlQuery = "UPDATE salary SET amount = \"$amount\" WHERE employeeid = \"$employeeId\" && month = \"$month\" && accounthead = \"ACH14\" ";
            $this->processQuery($sqlQuery);
            
            $sqlQuery = "UPDATE gpftotal SET amount = \"$amount\" WHERE employeeid = \"$employeeId\" && month = \"$month\" && flag = \"m\" ";
            $this->processQuery($sqlQuery);
        }else{
            $sqlQuery = "UPDATE salary SET amount = \"$amount\" WHERE employeeid = \"$employeeId\" && month = \"$month\" && accounthead = \"ACH15\" ";
            $this->processQuery($sqlQuery);
            
            $sqlQuery = "UPDATE gpftotal SET amount = \"$amount\" WHERE employeeid = \"$employeeId\" && month = \"$month\" && flag = \"r\" ";
            $this->processQuery($sqlQuery);            
        }
    }
    
    public function insertGPFRow($employeeId, $month, $type, $amount){
        if($type == "i"){
            $sqlQuery = "SELECT did FROM salary WHERE employeeid = \"$employeeId\" && month = \"$month\" LIMIT 1 ";       
            $sqlQuery = $this->processArray($sqlQuery);
            $dependentId = $sqlQuery[0];
            
            $counter = $this->getCounter("salary");
            $sqlQuery = "INSERT INTO salary (id, did, employeeid, allowanceid, accounthead, amount, type, month) VALUES (\"$counter\", \"$dependentId\", \"$employeeId\", \"ACT33\", \"ACH15\", \"$amount\", \"d\", \"$month\" )";
            $this->processQuery($sqlQuery);
            
            $counter = $this->getCounter("gpfTotal");
            $sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"r\")";
            $this->processQuery($sqlQuery);            
        }
        if($type == "m"){
            $sqlQuery = "SELECT did FROM salary WHERE employeeid = \"$employeeId\" && month = \"$month\" LIMIT 1 ";       
            $sqlQuery = $this->processArray($sqlQuery);
            $dependentId = $sqlQuery[0];
            
            $counter = $this->getCounter("salary");
            $sqlQuery = "INSERT INTO salary (id, did, employeeid, allowanceid, accounthead, amount, type, month) VALUES (\"$counter\", \"$dependentId\", \"$employeeId\", \"ACT20\", \"ACH14\", \"$amount\", \"d\", \"$month\" )";
            $this->processQuery($sqlQuery);
            
            $counter = $this->getCounter("gpfTotal");
            $sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"m\")";
            $this->processQuery($sqlQuery);            
        }
        if($type == "f"){        
            $counter = $this->getCounter("gpfTotal");
            $sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"f\")";
            $this->processQuery($sqlQuery);            
        }
        if($type == "n"){        
            $counter = $this->getCounter("gpfTotal");
            $sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"".(0 - $amount)."\", \"$month\", \"n\")";
            $this->processQuery($sqlQuery);            
        }       
    }    
    
    public function deleteSalaryGpfEntry($salaryId){
        $sqlQuery = "SELECT * FROM salary WHERE id = \"$salaryId\" ";       
        $details = $this->processArray($sqlQuery);    
        
        if($details[4] == "ACH14"){
            $sqlQuery = "DELETE FROM gpftotal WHERE employeeid = \"$details[2]\" && month = \"$details[7]\" && flag = \"m\" ";
            $this->processQuery($sqlQuery);
            
            $sqlQuery = "DELETE FROM salary WHERE id = \"$salaryId\" ";
            $this->processQuery($sqlQuery);     
        }
        if($details[4] == "ACH15"){
            $sqlQuery = "DELETE FROM gpftotal WHERE employeeid = \"$details[2]\" && month = \"$details[7]\" && flag = \"r\" ";
            $this->processQuery($sqlQuery);
            
            $sqlQuery = "DELETE FROM salary WHERE id = \"$salaryId\" ";
            $this->processQuery($sqlQuery);     
        }
        if(func_num_args() > 1){
            $sqlQuery = "DELETE FROM gpftotal WHERE id = \"$salaryId\" ";
            $this->processQuery($sqlQuery);
        }
    }
    
    public function getEmployeeGPFAdditionalIds($employeeId, $month){
    	$sqlQuery = "SELECT id FROM gpftotal WHERE employeeid = \"$employeeId\" && month = \"$month\" && flag != \"r\" && flag != \"m\" ";
    	$sqlQuery = $this->processQuery($sqlQuery);    	
    	$variable = array();
    	while($result = mysql_fetch_array($sqlQuery)){
    		array_push($variable, $result[0]);
    	}
    	
    	if (sizeof($variable, 0))
    		return $variable;
    	return false;
    }
    
    public function getFundAmount($employeeId, $fundType, $type, $endDate, $startDate){
        if(func_num_args() == 4 || $startDate == ""){
            $variable = $this->getMonthSession($endDate);
            $sDate = $this->getSessionDetails($variable);
            $sDate = explode('-', $sDate[2]);
            $sYear = $sDate[0];
            $sMonth = $sDate[1];
            $sDate = date('Ym', mktime(0, 0, 0, $sMonth - 1, 15, $sYear));           			
        }else{
            $sDate = $startDate;
        }        
        $tableName = $fundType == 'g' ? 'gpftotal' : ($fundType == 'c' ? 'cpftotal' : 'npstotal');
        if($type == "all")
            $sqlQuery = "SELECT sum(amount) FROM $tableName WHERE employeeid = \"$employeeId\" && month >= \"$sDate\" && month <= \"$endDate\" ";
        else
            $sqlQuery = "SELECT sum(amount) FROM $tableName WHERE employeeid = \"$employeeId\" && month >= \"$sDate\" && month <= \"$endDate\" && flag = \"$type\"";
        
        $sqlQuery = $this->processArray($sqlQuery);
        return $sqlQuery[0];        
    }


}
?>
