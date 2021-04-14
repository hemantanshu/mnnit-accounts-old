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
require_once 'class.sqlFunctions.php';
require_once 'class.accountInfo.php';



class testing extends sqlFunction {
    //put your code here

    public function __construct(){
        parent::__construct();

        
    }

    public function updateData(){
        $sqlQuery = "SELECT distinct(employeeid) FROM salary ORDER BY employeeid ASC";
        $sqlQuery = $this->processQuery($sqlQuery);

        while($result = mysql_fetch_array($sqlQuery)){
            $counter = $this->getCounter('remarks');
            $data = ucwords(strtolower("You are requested to please send a copy of salary statement of the month of May to the DR (Accounts) Office"));
            $employeeId = $result[0];

            $query = "INSERT INTO remarks (id, employeeid, date, remarks) VALUES (\"$counter\", \"EMP318\", \"201011\", \"$data\")";
            echo $query."<br />";
            $this->processQuery($query);
            break;
        }
    }
    public function updateIncomeTax($employeeId, $amount){
        $sqlQuery = "UPDATE mastersalary SET amount = \"$amount\" WHERE employeeId = \"$employeeId\" && active = \"y\" && allowanceid = \"ACT22\" ";
        echo $sqlQuery;
        $this->processQuery($sqlQuery);

    }
    public function getIncomeTaxAmount($employeeId){
        $sqlQuery = "SELECT amount FROM mastersalary WHERE employeeid = \"$employeeId\" && allowanceid = \"ACT22\" && active = \"y\"";
        
        $sqlQuery = $this->processArray($sqlQuery);

        return $sqlQuery[0]==''?0:$sqlQuery[0];
    }

    public function updateSubheadNew(){
        $query = "SELECT * FROM subhead_bak ORDER BY name DESC";
        $query = $this->processQuery($query);
        while($result = mysql_fetch_array($query)){
            $allowanceId = $result['id'];
            $name = $result['name'];

            $id = $this->getCounter('accountHeadDependency');

            $sqlQuery = "INSERT INTO accounthead (id, allowanceid, name, accounthead, allowupdate, status) VALUES (\"$id\", \"$allowanceId\", \"$name\", \"\", \"\", \"y\")";
            $this->processQuery($sqlQuery);


            //print_r($result);
            $sqlQuery = "INSERT INTO subheads (id, allowanceid, value, dependent, type, status) VALUES (\"$result[1]\", \"$result[0]\", \"$result[3]\", \"$result[4]\", \"$result[5]\", \"$result[6]\")";
            $this->processQuery($sqlQuery);
        }
    }

    public function roundCompleteAmount(){

        $query = "SELECT * FROM mastersalary ORDER BY id ASC";
        $query = $this->processQuery($query);

        while ($result = mysql_fetch_array($query)) {
            $id = $result['id'];
            $amount = $result['amount'];
            $newamount = $this->roundAmount($amount);

            $sqlQuery = "UPDATE mastersalary SET amount = \"$newamount\" WHERE id = \"$id\" ";
            $this->processQuery($sqlQuery);

            echo $amount."=======>".$newamount."<br />";
        }

    }
    
    public function updateSalaryTable(){
    	$sqlQuery = "SELECT DISTINCT(allowanceid) FROM salary  ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	while($result = mysql_fetch_array($sqlQuery)){
    		$allowanceId = $result[0];
    		
    		$query = "SELECT accounthead FROM accounthead WHERE allowanceid=\"$allowanceId\" ";
    		$query = $this->processArray($query);
    		
    		$accountHead = $query[0];
    		
    		$query = "UPDATE salary SET accounthead = \"$accountHead\" WHERE allowanceid = \"$allowanceId\" ";
    		$query = $this->processQuery($query);
    	}
    	$month = 201003;
    	require_once 'class.accountHead.php';
    	$class  = new accountHead();
    	$accountHeadIds = $class->getAccountHeadIds(true);   	
    	while($month < 201011){	    	        
	    	foreach ($accountHeadIds as $values){
	    		$id = $this->getCounter('salaryHeadPreserve');
	    		$name = $class->getAccountHeadName($values);    		
	    		$sqlQuery = "INSERT INTO salaryaccounthead (id, accounthead, name, month) VALUES (\"$id\", \"$values\", \"$name\", \"$month\")";
	    		$this->processQuery($sqlQuery);
	    	}
	    	$month++;
    	}
    	
    }
    public function updateEmployeeHeadTable(){
    	$month = 201003;
    	while($month < 201011){
	    	$sqlQuery = "SELECT employee.id, department, type, salary_bankid, salary_accountno FROM employee, bankaccount WHERE employee.id = bankaccount.employeeid ORDER BY employee.name ASC";
	    	$sqlQuery = $this->processQuery($sqlQuery);
	    	
	    	while($result = mysql_fetch_array($sqlQuery)){
	    		$id = $this->getCounter('salaryEmployeeHead');
	    		$query = "INSERT INTO salaryemployeehead (id, employeeid, department, type, bank, accountno, month) VALUES (\"$id\", \"".$result[0]."\", \"".$result['department']."\", \"".$result['type']."\", \"".$result['salary_bankid']."\", \"".$result['salary_accountno']."\", \"$month\")";
	    		$this->processQuery($query);
	    	}	
	    	++$month;
    	}    	
    }
   
    public function inputGPF(){
    	$sqlQuery = "SELECT * FROM gpf ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	while($result = mysql_fetch_array($sqlQuery)){
    		$counter = $this->getCounter('gpfTotal');
    		$comment = 'GPF Balance Brought Forward';
    		$month = $this->currentMonth - 1;
    		
    		$query = "SELECT id FROM employee WHERE employeeid = \"$result[0]\" ";
    		$query = $this->processArray($query);
    		
    		$employeeId = $query[0];
    		
    		$query = "INSERT INTO gpftotal (id, employeeid, amount, month, comment) VALUES (\"$counter\", \"$employeeId\", \"$result[1]\", \"$month\", \"$comment\")";
    		$this->processQuery($query);
    		echo "$result[0]"."---".$employeeId."<br />";
    	}
    }
	public function createLogin(){
		$sqlQuery = "SELECT id, employeeid FROM employee ORDER BY name ASC";
		$sqlQuery = $this->processQuery($sqlQuery);
		while($result = mysql_fetch_array($sqlQuery)){
			$passwd = rand(100000, 1000000);
			$query = "INSERT INTO employees (id, password) VALUES (\"$result[0]\", \"$passwd\")";
			$this->processQuery($query);

			$query = "INSERT INTO employeelogin (id, username, password, attempts, status, active) VALUES (\"$result[0]\", \"$result[1]\", \"".md5($passwd)."\", \"0\", \"\", \"y\" )";
			$this->processQuery($query);
		}
	}

	public function changeMonthOfInstallment(){
		$sqlQuery = "SELECT * FROM loaninstallment WHERE remarks LIKE \"%INSTALLMENT RECOVERY FOR THE MONTH%\" ";
		$sqlQuery = $this->processQuery($sqlQuery);
		while($result = mysql_fetch_array($sqlQuery)){
			$nextMonth = date('Ym', mktime(0, 0, 0, substr($result[3], 4, 2)+1, 24, substr($result[3], 0, 4)));
			
			$query = "UPDATE loaninstallment SET month = \"$nextMonth\" WHERE id = \"$result[0]\" ";
			$this->processQuery($query);
			
			echo $result[0]."----".$result[3]."-----".$nextMonth."<br />";
		}
	}
	
	public function processCPFTotal(){
	       $sqlQuery = "SELECT * FROM salary WHERE accounthead = \"ACH16\" && month > \"201012\" ORDER BY month,employeeid ASC ";
	       $query = $this->processQuery($sqlQuery);

	       while($result = mysql_fetch_array($query)){
	       		$employeeid = $result['employeeid'];
	       		
	       		$sqlQuery = "SELECT amount FROM salary WHERE employeeid=\"".$result['employeeid']."\" && month = \"".$result['month']."\" && allowanceid = \"ACT1\"";
	       		$sqlQuery = $this->processArray($sqlQuery);
	       		$contribution = (int) $sqlQuery[0] * 0.1;	       		
	       		
	       			       		      		
	       		$counter = $this->getCounter('cpfTotal');
	       		$sqlQuery = "INSERT INTO cpftotal (id, employeeid, amount, month, comment, flag) VALUES (\"$counter\", \"$employeeid\", \"".$result['amount']."\", \"".$result['month']."\", \"CPF Credited For The Month\", \"m\")";
	       		$this->processQuery($sqlQuery);
	       		
	       		$counter = $this->getCounter('cpfTotal');
	       		$sqlQuery = "INSERT INTO cpftotal (id, employeeid, amount, month, comment, flag) VALUES (\"$counter\", \"$employeeid\", \"".$contribution."\", \"".$result['month']."\", \"Institute Contribution Amount\", \"c\")";
	       		$this->processQuery($sqlQuery);       		
	       }
	}
	
	public function processGPFTotal(){
		$sqlQuery = "SELECT DISTINCT(employeeid) FROM salary WHERE (accounthead = \"ACH14\" || accounthead = \"ACH15\") ORDER BY employeeid ASC";
		$query = $this->processQuery($sqlQuery);
		
		while ($result = mysql_fetch_array($query)){
                        $employeeId = $result[0];

			$sqlQuery = "SELECT employeeid FROM employee WHERE id =\"".$employeeId."\" ";
			$sqlQuery = $this->processArray($sqlQuery);			

                        $sqlQuery = "SELECT amt FROM gpf WHERE id = \"$sqlQuery[0]\" ";
                        $sqlQuery = $this->processArray($sqlQuery);
                        if($sqlQuery[0] != "" || $sqlQuery[0] != 0){
                            $counter = $this->getCounter('gpfTotal');
                            $sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, comment, flag) VALUES (\"$counter\", \"$employeeId\", \"".$sqlQuery['amt']."\", \"201002\", \"GPF Balance Brought Forward\", \"f\")";
                            $this->processQuery($sqlQuery);
                        }
			
			for ($i = 0; $i < 20; ++$i){
				$month = date('Ym', mktime(0, 0, 0, 3+$i, 15, 2010));
				
				if ($month > 201104)
					break;
					
				$sqlQuery = "SELECT amount FROM salary WHERE month = \"$month\" && accounthead = \"ACH14\" && employeeid = \"$employeeId\" ";
				$sqlQuery = $this->processArray($sqlQuery);
				$amount = $sqlQuery[0];
				if ($amount != "" || $amount != 0){
					$counter = $this->getCounter('gpfTotal');
					$sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, comment, flag) VALUES (\"$counter\", \"$employeeId\", \"".$amount."\", \"$month\", \"GPF Credited For The Month\", \"m\")";
					$this->processQuery($sqlQuery);
				}
				
				$sqlQuery = "SELECT amount FROM salary WHERE month = \"$month\" && accounthead = \"ACH15\" && employeeid = \"$employeeId\" ";
				$sqlQuery = $this->processArray($sqlQuery);
				$amount = $sqlQuery[0];
				if ($amount != "" || $amount != 0){
					$counter = $this->getCounter('gpfTotal');
					$sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, comment, flag) VALUES (\"$counter\", \"$employeeId\", \"".$amount."\", \"$month\", \"GPF Advance Recovery For The Month\", \"i\")";
					$this->processQuery($sqlQuery);
				}			
			}
		}
	}
	
	public function compareGPFTotal(){
		$sqlQuery = "truncate table gpfcompare";
		$this->processQuery($sqlQuery);
		
		$sqlQuery = "SELECT distinct(employeeid) FROM backupgpftotal";
		$query = $this->processQuery($sqlQuery);
		
		while ($result = mysql_fetch_array($query)){
			$employeeId = $result[0];
			
			$sqlQuery = "SELECT sum(amount) FROM backupgpftotal WHERE employeeid = \"$employeeId\" ";
			$sqlQuery = $this->processArray($sqlQuery);
			$originalBalance = $sqlQuery[0];
			
			$sqlQuery = "SELECT sum(amount) FROM gpftotal WHERE employeeid = \"$employeeId\" && month < \"201105\"";
			$sqlQuery = $this->processArray($sqlQuery);
			$newBalance = $sqlQuery[0];
			
			$diff = $newBalance - $originalBalance;
			
			$sqlQuery = "SELECT * FROM employee WHERE id = \"$employeeId\" ";
			$sqlQuery = $this->processArray($sqlQuery);
			
			$sqlQuery = "INSERT INTO gpfcompare (id, employeecode, name, orginal, new, difference) VALUES (\"$employeeId\", \"".$sqlQuery['employeeid']."\", \"".$sqlQuery['name']."\", \"$originalBalance\", \"$newBalance\", \"$diff\")";
			$this->processQuery($sqlQuery);			
		}
		
		$sqlQuery = "delete from gpfcompare where difference=\"0\"  ";		
		$this->processQuery($sqlQuery);
	}
	
	
	
	
	
	public function changeFlagOfInstallment(){
		$sqlQuery = "UPDATE loaninstallment set flag = \"i\" WHERE remarks like \"%RECOVERY%\"";
		$this->processQuery($sqlQuery);
		
		$sqlQuery = "UPDATE loaninstallment set flag = \"n\" WHERE remarks like \"%NEW LOAN%\"";
		$this->processQuery($sqlQuery);
		

		
		$sqlQuery = "UPDATE gpfloaninstallment set flag = \"n\" WHERE comment like \"%New Loan Taken%\"";
		$this->processQuery($sqlQuery);
		
		$sqlQuery = "UPDATE gpfloaninstallment set flag = \"i\" WHERE comment like \"%GPF Advance Recovery For The Month%\"";
		$this->processQuery($sqlQuery);
		
		$sqlQuery = "UPDATE gpfloaninstallment set flag = \"c\" WHERE comment like \"%Refundable Loan Converted Into Non-Refundable%\"";
		$this->processQuery($sqlQuery);
		
		
		
		$sqlQuery = "UPDATE gpftotal set flag = \"i\" WHERE  comment like \"%GPF ADVANCE RECOVERY FOR THE MONTH%\"";
		$this->processQuery($sqlQuery);
		
		$sqlQuery = "UPDATE gpftotal set flag = \"n\" WHERE  comment like \"%GPF Loan Taken%\"";
		$this->processQuery($sqlQuery);
		
		$sqlQuery = "UPDATE gpftotal set flag = \"m\" WHERE  comment like \"%GPF CREDITED FOR THE MONTH%\"";
		$this->processQuery($sqlQuery);
		
	}
	
	public function processNPSData(){
            
                $sqlQuery = "DELETE FROM npstotal where month = \"201107\" ";
                echo $sqlQuery;
                $this->processQuery($sqlQuery);
		$sqlQuery = "SELECT DISTINCT(employeeid) FROM salary WHERE accounthead = \"ACH22\" && month = \"201107\"";
		$query = $this->processQuery($sqlQuery);
		while($result = mysql_fetch_array($query)){                    
                    $employeeId = $result[0];                                        
			echo $employeeId."<br />";
			for($i = 0; $i < 20; ++$i){
				$month = date("Ym", mktime(0, 0, 0, 7, 15, 2010));
				if($month > 201111)
					break;
				$sqlQuery = "SELECT SUM(amount) FROM salary WHERE accounthead = \"ACH22\" && month = \"$month\" && employeeid = \"$employeeId\" ";
				$sqlQuery = $this->processArray($sqlQuery);
				$amount = $sqlQuery[0];
				if($amount == 0)
					continue;		
				
				//normal subscription amount to be entered into the table
				$counter = $this->getCounter("npsTotal");
				$sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"m\")";
				$this->processQuery($sqlQuery);
				
				//Contribution amount to be entered into the table
				$counter = $this->getCounter("npsTotal");
				$sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"c\")";
				$this->processQuery($sqlQuery);
                                
                                break;
			}
		}
	}
        
        public function correctNPSData(){   
            echo "sldkjfsdljf";
            $sqlQuery = "SELECT * FROM nps ORDER BY id ASC";
            $query = $this->processQuery($sqlQuery);
            while($result = mysql_fetch_array($query)){
                $forwardedBalance = $result[3] - $result[1];
                $interest = $result[1];
                $arrear = $result[4];
                
                $sqlQuery = "SELECT id from employee where employeeid = \"".$result[0]."\"";
                $sqlQuery = $this->processArray($sqlQuery);
                $employeeId = $sqlQuery[0];
                
                $counter = $this->getCounter("npsTotal");
                $sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) 
                VALUES (\"$counter\", \"$employeeId\", \"$forwardedBalance\", \"201002\", \"fm\")";
		$this->processQuery($sqlQuery);
                
                $counter = $this->getCounter("npsTotal");
                $sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) 
                VALUES (\"$counter\", \"$employeeId\", \"$forwardedBalance\", \"201002\", \"fc\")";
		$this->processQuery($sqlQuery);
                
                $counter = $this->getCounter("npsTotal");
                $sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) 
                VALUES (\"$counter\", \"$employeeId\", \"$interest\", \"201003\", \"im\")";
		$this->processQuery($sqlQuery);
                
                $counter = $this->getCounter("npsTotal");
                $sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) 
                VALUES (\"$counter\", \"$employeeId\", \"$interest\", \"201003\", \"ic\")";
		$this->processQuery($sqlQuery);
                
                $counter = $this->getCounter("npsTotal");
                $sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) 
                VALUES (\"$counter\", \"$employeeId\", \"$arrear\", \"201008\", \"bm\")";
		$this->processQuery($sqlQuery);
                
                $counter = $this->getCounter("npsTotal");
                $sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) 
                VALUES (\"$counter\", \"$employeeId\", \"$arrear\", \"201008\", \"bc\")";
		$this->processQuery($sqlQuery);
                
                
                for($i = 0; $i < 20; ++$i){
                    $month = date("Ym", mktime(0, 0, 0, 3 + $i, 15, 2010));
                    if($month > 201110)
                            break;
                    $sqlQuery = "SELECT SUM(amount) FROM salary WHERE accounthead = \"ACH22\" && month = \"$month\" && employeeid = \"$employeeId\" ";
                    $sqlQuery = $this->processArray($sqlQuery);
                    $amount = $sqlQuery[0];
                    if($amount == 0)
                            continue;		

                    //normal subscription amount to be entered into the table
                    $counter = $this->getCounter("npsTotal");
                    $sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"m\")";
                    $this->processQuery($sqlQuery);

                    //Contribution amount to be entered into the table
                    $counter = $this->getCounter("npsTotal");
                    $sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"c\")";
                    $this->processQuery($sqlQuery);
                }                
            }            
            $sqlQuery = "DELETE FROM npstotal WHERE employeeid=\"\" || amount=\"\" || amount = \"0\"";
            $this->processQuery($sqlQuery);
        }
        
        public function correctCPFData(){  
            
                $employeeId = "EMP87";
                
                $sqlQuery = "delete from cpftotal where employeeid = \"$employeeId\" ";
                $this->processQuery($sqlQuery);
                
                $sqlQuery = "SELECT * FROM cpf ORDER BY id ASC";
                $query = $this->processQuery($sqlQuery);
                while($result = mysql_fetch_array($query)){
                    $counter = $this->getCounter("cpfTotal");
                    $sqlQuery = "INSERT INTO cpftotal (id, employeeid, amount, month, flag)  VALUES (\"$counter\", \"$employeeId\", \"$result[1]\", \"$result[0]\", \"m\")";
                    $this->processQuery($sqlQuery);
                    
                    $counter = $this->getCounter("cpfTotal");
                    $sqlQuery = "INSERT INTO cpftotal (id, employeeid, amount, month, flag)  VALUES (\"$counter\", \"$employeeId\", \"$result[3]\", \"$result[0]\", \"c\")";
                    $this->processQuery($sqlQuery);
                }             
                for($i = 0; $i < 20; ++$i){
                    $month = date("Ym", mktime(0, 0, 0, 1 + $i, 15, 2011));
                    if($month > 201109)
                            break;
                    
                    $sqlQuery = "SELECT SUM(amount) FROM salary WHERE accounthead = \"ACH16\" && month = \"$month\" && employeeid = \"$employeeId\" ";
                    $sqlQuery = $this->processArray($sqlQuery);
                    $amount = $sqlQuery[0];
                    if($amount == 0)
                        continue;	
                    
                    $counter = $this->getCounter("cpfTotal");
                    $sqlQuery = "INSERT INTO cpftotal (id, employeeid, amount, month, flag)  VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"m\")";
                    $this->processQuery($sqlQuery);       
                    
                    $sqlQuery = "SELECT SUM(amount) FROM salary WHERE accounthead = \"ACH1\" && month = \"$month\" && employeeid = \"$employeeId\" ";
                    $sqlQuery = $this->processArray($sqlQuery);
                    $amount = round($sqlQuery[0] * .1);             
                    
                    $counter = $this->getCounter("cpfTotal");
                    $sqlQuery = "INSERT INTO cpftotal (id, employeeid, amount, month, flag)  VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"c\")";
                    $this->processQuery($sqlQuery);       
                    
                }                
                
                
                $sqlQuery = "update cpftotal set flag='fm' where flag='m' && month='201002' ";
                $this->processQuery($sqlQuery);
                
                $sqlQuery = "update cpftotal set flag='fc' where flag='c' && month='201002' ";
                $this->processQuery($sqlQuery);    
                
                        
            $sqlQuery = "DELETE FROM cpftotal WHERE employeeid=\"\" || amount=\"\" || amount = \"0\"";
            $this->processQuery($sqlQuery);
        }

	
}
?>