<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.pending.php';
class interest extends pending {

    public function  __construct() {
        parent::__construct();
    }
        
    public function isInterestApplicable($employeeId, $month, $type){
    	//echo $type;
        $tableName = $type == "cpf" ? 'cpftotal' : ($type == 'nps' ? 'npstotal' : 'gpftotal');
        
    	$sqlQuery = "SELECT id FROM $tableName WHERE flag = \"o\" && employeeid = \"$employeeId\" ";   
    //	echo $sqlQuery;	
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	if (mysql_num_rows($sqlQuery))
    		return false;
	if(func_num_args() > 3)
            $sqlQuery = "SELECT id FROM $tableName WHERE flag LIKE \"i".  func_get_arg(3)."\" && employeeid = \"$employeeId\" && month = \"$month\"";   	
        else
            $sqlQuery = "SELECT id FROM $tableName WHERE flag LIKE \"i\" && employeeid = \"$employeeId\" && month = \"$month\"";   	
    	$sqlQuery = $this->processQuery($sqlQuery);
    	if (mysql_num_rows($sqlQuery))
    		return false;
    	
    	return true;
    }
    
    public function isDataAvailiable($employeeId, $sMonth, $eMonth, $type){
    	$tableName = strtolower($type) == "cpf" ? 'cpftotal' : (strtolower($type) == 'nps' ? 'npstotal' : 'gpftotal');
    	$sqlQuery = "SELECT id FROM $tableName WHERE employeeid = \"$employeeId\" && month >= \"$sMonth\" && month <= \"$eMonth\" ";  
    	$sqlQuery = $this->processQuery($sqlQuery);
    	if (mysql_num_rows($sqlQuery))
    		return true;
    	return false;
    }
    
    public function checkInterestPrerequisites($sessionId){
    	$details = $this->getSessionDetails($sessionId);
    	    	
    	if ($details[1] == "")
    		return false;
    	if ($details[3] == "0000-00-00")
    		$this->palert("Please Set The Financial Year To Continue", "./");
    	
    	$variable = array();
    	array_push($variable, $details[1]);
    	$sDate = substr($details[2], 0, 4).substr($details[2], 5, 2);
    	$eDate = substr($details[3], 0, 4).substr($details[3], 5, 2);
    	array_push($variable, $sDate);
    	array_push($variable, $eDate);    	
    	return $variable;
    }
    
    public function getTotalGPFInterest($employeeId, $interestRate, $sessionId, $flag){    	
    	$previousMonth = date('Ym', mktime(0, 0, 0, substr($sessionId[2], 4, 2), 15, substr($sessionId[2], 0, 4)));    	
    	if (!$this->isInterestApplicable($employeeId, $previousMonth))
    		return false;   		   	
    		
    	$i = 0;
    	$sum = 0;   
    	$finalInterest = 0; 	
    	while (true){
    		$month = date('Ym', mktime(0, 0, 0, substr($sessionId[1], 4, 2) +$i , 15, substr($sessionId[1], 0 ,4)));   		                      
            $sqlQuery = "SELECT SUM(amount) FROM gpftotal WHERE month < \"$month\" && employeeid = \"$employeeId\" ";
        	//echo $sqlQuery."<br>";
            $sqlQuery = $this->processArray($sqlQuery);    		
    		$sum = $sqlQuery[0];
    		//echo $sum."<br/>";
    		$interest[$i] = round($sum*$interestRate[$i]*.01/12);
    		//echo $interestRate[$i]."<br/>";
    		$finalInterest += $interest[$i];
    		
    		 if($flag){
    		$counter = $this->getCounter('interestRate');            
    		$sqlQuery = "INSERT INTO interest (id, employeeid, month, rate, type) VALUES (\"$counter\", \"$employeeId\", \"$month\", \"$interestRate[$i]\", \"g\")";
    	//echo $sqlQuery."<br/>";
    		$this->processQuery($sqlQuery);  		
    	}  
    		if ($month == $previousMonth)
                break;
    		++$i;
    		
    	}   
    	
    	//$interest = round($sum * $interestRate * .01 / 12) ;
    if ($flag){
    		$counter = $this->getCounter('gpfTotal');            
    		$sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$finalInterest\", \"$month\", \"i\" )";
    		//echo $sqlQuery."<br/>";
    		$this->processQuery($sqlQuery);
    		}
    	 	
    	
    	return $finalInterest;
    }
    
    public function gpfTotalBalance($employeeId, $month){
    	$sqlQuery = "SELECT sum(amount) FROM gpftotal WHERE employeeid = \"$employeeId\" && month < \"$month\" ";    	
    	$sqlQuery = $this->processArray($sqlQuery);    	
    	return $sqlQuery[0];
    }  

    public function finaliseEmployeeGPFAccount($employeeId, $interestRate, $flag){
    	if (!$this->isInterestApplicable($employeeId, $this->currentMonth))
    		return false;
    	$sqlQuery = "SELECT month FROM gpftotal WHERE flag = \"i\" && employeeid = \"$employeeId\" ORDER BY month DESC LIMIT 1";
    	$sqlQuery = $this->processArray($sqlQuery);
    	$interestMonth = $sqlQuery[0];
    
    	$sum = 0;
    	$i = 0;
    	while (true){
    		$month = date('Ym', mktime(0, 0, 0, substr($interestMonth, 4, 2)+1 +$i, 15, substr($interestMonth, 0 ,4))); 
    	   		
    		$sqlQuery = "SELECT SUM(amount) FROM gpftotal WHERE month < \"$month\" && employeeid = \"$employeeId\" ";    		
    		$sqlQuery = $this->processArray($sqlQuery);    		
    		$sum = $sqlQuery[0];
    		//echo $month."<br/>";
    		//echo $sum."<br/>";
    		$interest[$i] = round($sum*$interestRate[$i]*.01/12);
    		//echo $interest[$i]."<br/>";
    		$finalInterest += $interest[$i];
    		
    		if ($flag){
    			$counter = $this->getCounter('interestRate');            
    		$sqlQuery = "INSERT INTO interest (id, employeeid, month, rate, type) VALUES (\"$counter\", \"$employeeId\", \"$month\", \"$interestRate[$i]\", \"g\")";
    		$this->processQuery($sqlQuery);
    		
    		}
    		++$i;
$curmonth =    date('Ym', mktime(0, 0, 0, substr($this->currentMonth, 4, 2)-1, 15, substr($this->currentMonth, 0 ,4))); 		
    		if ($month >= $curmonth)
    			break;
    	}
    	//$interest = round($sum * $interestRate * .01 / 12) ;
    	if ($flag){
    		$counter = $this->getCounter('gpfTotal');
    		$sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$finalInterest\", \"$month\", \"i\" )";
    		$this->processQuery($sqlQuery);              		
    		
    		
    		$amount = 0 - $this->gpfTotalBalance($employeeId, $this->nextMonth);
    		$counter = $this->getCounter('gpfTotal');
    		$sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"o\" )";
    		$this->processQuery($sqlQuery);                                    		
    		
    	}
    	return $finalInterest;
    	
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////
	public function getTotalCPFInterest($employeeId, $interestRate, $sessionId, $flag, $type){    	
    	$previousMonth = date('Ym', mktime(0, 0, 0, substr($sessionId[2], 4, 2), 15, substr($sessionId[2], 0, 4)));    	
    	if (!$this->isInterestApplicable($employeeId, $previousMonth, 'cpf', $type))
    		return false;   		   	
    		
    	$i = 0;
    	$sum = 0;    	
    	while (true){
    		$month = date('Ym', mktime(0, 0, 0, substr($sessionId[1], 4, 2) +$i , 15, substr($sessionId[1], 0 ,4)));   		                      
                $sqlQuery = "SELECT SUM(amount) FROM cpftotal WHERE month < \"$month\" && employeeid = \"$employeeId\" && flag LIKE \"%".$type."\" ";
        	$sqlQuery = $this->processArray($sqlQuery);    		
    		$sum = $sqlQuery[0];
			$interest[$i] = round($sum*$interestRate[$i]*.01/12);
			$finalInterest +=$interest[$i];
			
			if ($flag){	
    		$counter = $this->getCounter('interestRate');            
                $sqlQuery = "SELECT id FROM interest WHERE employeeid=\"$employeeId\" && month=\"$month\" ";
                $sqlQuery = $this->processQuery($sqlQuery);
                if(!mysql_num_rows($sqlQuery)){
                    $sqlQuery = "INSERT INTO interest (id, employeeid, month, rate, type) VALUES (\"$counter\", \"$employeeId\", \"$month\", \"$interestRate[$i]\", \"c\")";
                    $this->processQuery($sqlQuery);  		                        
                }    		
    	    }    	    	
			
    		if ($month == $previousMonth)
                break;
    		++$i;
    	}   
    	
    	if ($flag){
    		$counter = $this->getCounter('cpfTotal');            
    		$sqlQuery = "INSERT INTO cpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$finalInterest\", \"$month\", \"i".$type."\" )";
    		$this->processQuery($sqlQuery);
    		    }    		
    	 	    	
    	return $finalInterest;
    }
        
    public function cpfTotalBalance($employeeId, $month, $type){
    	$sqlQuery = "SELECT sum(amount) FROM cpftotal WHERE employeeid = \"$employeeId\" && month < \"$month\" && flag LIKE \"%".$type."\" ";    	
    	$sqlQuery = $this->processArray($sqlQuery);    	
    	return $sqlQuery[0];
    }  

    public function finaliseEmployeeCPFAccount($employeeId, $interestRate, $flag){
    	
    	//if (!$this->isInterestApplicable($employeeId, $this->currentMonth,'cpf'))
    		//return false;
    	$sqlQuery = "SELECT month FROM cpftotal WHERE employeeid = \"$employeeId\" && flag = \"i\" ORDER BY month DESC LIMIT 1";
    	echo $sqlQuery;
    	$sqlQuery = $this->processArray($sqlQuery);
    	$interestMonth = $sqlQuery[0];
    	
    	$sum = 0;
    	$i = 0;
    	while (true){
    		$month = date('Ym', mktime(0, 0, 0, substr($interestMonth, 4, 2) +$i +1, 15, substr($interestMonth, 0 ,4)));    		
    		$sqlQuery = "SELECT SUM(amount) FROM cpftotal WHERE month < \"$month\" && employeeid = \"$employeeId\" "; 
    		  		
    		$sqlQuery = $this->processArray($sqlQuery);    		
    		$sum = $sqlQuery[0];
    		
    		$interest[$i] = round($sum*$interestRate[$i]*.01/12);
    		//echo $interest[$i]."<br/>";
    		$finalInterest += $interest[$i];
    		
    		if ($flag){
    			//echo  "yes";
    		$counter = $this->getCounter('interestRate'); 
    		//echo $counter;           
    		$sqlQuery = "INSERT INTO interest (id, employeeid, month, rate, type) VALUES (\"$counter\", \"$employeeId\", \"$month\", \"$interestRate[$i]\", \"c\")";
    		$this->processQuery($sqlQuery);
    		}
    		++$i;    		
    		if ($month >= $this->currentMonth)
    			break;
    	}
    	//$interest = round($sum * $interestRate * .01 / 12) ;
    	if ($flag){
    		$counter = $this->getCounter('cpftotal');
    		$sqlQuery = "INSERT INTO cpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$finalInterest\", \"$month\", \"i\" )";
    		$this->processQuery($sqlQuery);
    			
    		$amount = 0 - $this->cpfTotalBalance($employeeId, $this->nextMonth);
    		$counter = $this->getCounter('cpfTotal');
    		$sqlQuery = "INSERT INTO cpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"o\" )";
    		$this->processQuery($sqlQuery);    		
    	}
    	return $finalInterest;     	
    }
    
        ///////////////////////////////////////////////////////////////////////////////////////////
       public function getTotalNPSInterest($employeeId, $interestRate, $sessionId, $flag, $type){    	
    	$previousMonth = date('Ym', mktime(0, 0, 0, substr($sessionId[2], 4, 2), 15, substr($sessionId[2], 0, 4)));    	
    	if (!$this->isInterestApplicable($employeeId, $previousMonth, 'nps', $type))
    		return false;   		   	
    		
    	$i = 0;
    	$sum = 0;    	
    	while (true){
    		$month = date('Ym', mktime(0, 0, 0, substr($sessionId[1], 4, 2) +$i , 15, substr($sessionId[1], 0 ,4)));   		                      
                $sqlQuery = "SELECT SUM(amount) FROM npstotal WHERE month < \"$month\" && employeeid = \"$employeeId\" && flag LIKE \"%".$type."\" ";
        	$sqlQuery = $this->processArray($sqlQuery);    		
    		$sum = $sqlQuery[0];
			$interest[$i] = round($sum*$interestRate[$i]*.01/12);
			$finalInterest +=$interest[$i];
			if($flag){
			
				$counter = $this->getCounter('interestRate');            
                $sqlQuery = "SELECT id FROM interest WHERE employeeid=\"$employeeId\" && month=\"$month\" ";
                $sqlQuery = $this->processQuery($sqlQuery);
                
                if(!mysql_num_rows($sqlQuery)){
                    $sqlQuery = "INSERT INTO interest (id, employeeid, month, rate, type) VALUES (\"$counter\", \"$employeeId\", \"$month\", \"$interestRate[$i]\", \"n\")";
                   // echo $sqlQuery;
                    $this->processQuery($sqlQuery);  		                        
                }
			}
    		if ($month == $previousMonth)
                break;
    		++$i;
    	}   
    	
    	if ($flag){
    		$counter = $this->getCounter('npsTotal');            
    		$sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$finalInterest\", \"$month\", \"i".$type."\" )";
    		$this->processQuery($sqlQuery);
    		}    	    	
    	return $finalInterest;
    }
    
    
    public function npsTotalBalance($employeeId, $month, $type){
    	$sqlQuery = "SELECT sum(amount) FROM npstotal WHERE employeeid = \"$employeeId\" && month < \"$month\" && flag LIKE \"%".$type."\" ";    	
    	$sqlQuery = $this->processArray($sqlQuery);    	
    	return $sqlQuery[0];
    }  
		
    
    
    public function finaliseEmployeeNPSAccount($employeeId, $interestRate, $flag){
    	if (!$this->isInterestApplicable($employeeId, $this->currentMonth))
    		return false;
    	$sqlQuery = "SELECT month FROM npstotal WHERE employeeid = \"$employeeId\" && flag LIKE \"i%\" ORDER BY month DESC LIMIT 1 ";
    	$sqlQuery = $this->processArray($sqlQuery);
    	$interestMonth = $sqlQuery[0];
    	
    	$sum = 0;
    	$i = 0;
    	while (true){
    		$month = date('Ym', mktime(0, 0, 0, substr($interestMonth, 4, 2) +$i +1, 15, substr($interestMonth, 0 ,4)));    		
    		$sqlQuery = "SELECT SUM(amount) FROM npstotal WHERE month < \"$month\" && employeeid = \"$employeeId\" ";    		
    		$sqlQuery = $this->processArray($sqlQuery);    		
    		$sum = $sqlQuery[0];
    		//echo $month."<br/>";
    		$interest[$i] = round($sum * $interestRate[$i] * .01 / 12) ;
    		$finalInterest += $interest[$i];
    		if ($flag){
    			$counter = $this->getCounter('interestRate');            
    		$sqlQuery = "INSERT INTO interest (id, employeeid, month, rate, type) VALUES (\"$counter\", \"$employeeId\", \"$month\", \"$interestRate[$i]\", \"n\")";
    		$this->processQuery($sqlQuery);
    		
    		}
    	//echo $finalInterest."<br/>";
    		++$i;    		
    		if ($month >= $this->currentMonth)
    			break;
    	}
    	//$interest = round($sum * $interestRate * .01 / 12) ;
    	if ($flag){
    		$counter = $this->getCounter('npsTotal');
    		$sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$finalInterest\", \"$month\", \"i\" )";
    		$this->processQuery($sqlQuery);
    		
    		
    		$amount = 0 - $this->npsTotalBalance($employeeId, $this->nextMonth);
    		$counter = $this->getCounter('npsTotal');
    		$sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"o\" )";
    		$this->processQuery($sqlQuery);    		
    	}
    	return $finalInterest;     	
    }

    public function getFundAmount($employeeId, $type, $month){ //will be taking two more arguments, one the flag and the other not flag
    	$tableName = $type == "gpf" ? "gpftotal" : ($type == "cpf" ? "cpftotal" : npstotal);
    	if(func_num_args() > 3) { //flag argument has been passed and needs to be taken care of
    		if(func_num_args() == 4) //the positive flag content has to be taken care of
    			$sqlQuery = "SELECT SUM(amount) FROM $tableName WHERE employeeid = \"$employeeId\" && month = \"$month\" && flag LIKE \"%".func_get_arg(3)."\" ";
    		if(func_num_args() > 4){ //the negative flag content has to be taken care of
    			$count = func_num_args();
    			for($i=3;	$i<$count;	++$i){
    				if($i != 3)
    					$flag = $flag . "&& ";
    				$flag = $flag."flag != \"".func_get_arg($i)."\" ";
    			}
    			$sqlQuery = "SELECT SUM(amount) FROM $tableName WHERE employeeid = \"$employeeId\" && month = \"$month\" && ($flag) ";
    		}
    	}else 
    		$sqlQuery = "SELECT SUM(amount) FROM $tableName WHERE employeeid = \"$employeeId\" && month < \"$month\"";
    	$sqlQuery = $this->processArray($sqlQuery);
    	return $sqlQuery[0];    	
    }
    
    public function getInterestRate($employeeId, $month, $flag){
    	$sqlQuery = "SELECT rate FROM interest WHERE employeeid = \"$employeeId\" && month = \"$month\" && type = \"$flag\" ";
    	
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery[0];
    }
    
    public function unProcessFundInterest($employeeId, $month, $type){
    	$tableName = $type == "gpf" ? "gpftotal" : ($type == "cpf" ? "cpftotal" : "npstotal");
    	$flagName = $type == "gpf" ? "g" : ($type == "cpf" ? "c" : "n");
    	
    	if(func_num_args() > 3) //final settlement has to be unprocessed
    		$sqlQuery = "DELETE FROM $tableName WHERE month = \"$month\"  && employeeid = \"$employeeId\" && (flag = \"i\" || flag = \"o\")";    		
    	else //normal interest has to be unprocessed    		
    		$sqlQuery = "DELETE FROM $tableName WHERE month = \"$month\" && flag = \"i\" && employeeid = \"$employeeId\" ";
    		
    	$this->processQuery($sqlQuery);
    	
    	//deleting entry from the interest table
    	$sqlQuery  ="DELETE FROM interest WHERE month = \"$month\" && type = \"$flagName\" && employeeid = \"$employeeId\" ";
    	$this->processQuery($sqlQuery);
    	
    	return true;
    }
    
    public function getSettlementDetails($employeeId, $fundType){
        $tableName = $fundType == 'g' ? 'gpftotal' : ($fundType == 'c' ? 'cpftotal' : 'npstotal');
        
        $sqlQuery = "SELECT abs(amount), month FROM $tableName WHERE employeeid = \"$employeeId\" && flag = \"o\" ORDER BY month DESC LIMIT 1";
        $sqlQuery = $this->processArray($sqlQuery);     
        
        return $sqlQuery;
    }
    

    
    public function isLastYearProcessed()
    {
    	$cur = $this->getCurrentMonth();
    	$month = substr($cur,4,2);
    	if($month>3)
    	{
    	$year = substr($cur,0,4);
    	$lmonth = $year."03";
    	}
    	else {
    		$year = substr($cur,0,4) -1;
    		$lmonth = $year."03";
    	}
    	
    	$sqlQuery = "SELECT amount FROM gpftotal WHERE month =  \"$lmonth\" && flag=\"i\"";
    //	echo $sqlQuery;
    	if(mysql_num_rows($this->processQuery($sqlQuery)) != 0)
    	return true;
    	else return false;
    	
    }

     public function isLastYearCPFProcessed()
    {
    	$cur = $this->getCurrentMonth();
    	$month = substr($cur,4,2);
    	if($month>3)
    	{
    	$year = substr($cur,0,4);
    	$lmonth = $year."03";
    	}
    	else {
    		$year = substr($cur,0,4) -1;
    		$lmonth = $year."03";
    	}
    	
    	$sqlQuery = "SELECT amount FROM cpftotal WHERE month =  \"$lmonth\" && flag=\"i\"";
    //	echo $sqlQuery;
    	if(mysql_num_rows($this->processQuery($sqlQuery)) != 0)
    	return true;
    	else return false;
    	
    }
   
     public function isLastYearNPSProcessed()
    {
    	$cur = $this->getCurrentMonth();
    	$month = substr($cur,4,2);
    	if($month>3)
    	{
    	$year = substr($cur,0,4);
    	$lmonth = $year."03";
    	}
    	else {
    		$year = substr($cur,0,4) -1;
    		$lmonth = $year."03";
    	}
    	
    	//echo $lmonth;
    	$sqlQuery = "SELECT amount FROM npstotal WHERE month =  \"$lmonth\" && flag LIKE \"i%\"";
    //	echo $sqlQuery;
    	if(mysql_num_rows($this->processQuery($sqlQuery)) != 0)
    	return true;
    	else return false;
    	
    }
  
    
}
?>
