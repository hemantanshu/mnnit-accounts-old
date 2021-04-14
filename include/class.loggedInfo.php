<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
session_start();
error_reporting(0);

require_once 'class.sqlFunctions.php';
require_once 'class.dateDifference.php';

class loggedIn extends sqlFunction {

    public $error;
    protected $datetime;

    public function  __construct() {
        parent::__construct();
        
        date_default_timezone_set('Asia/Calcuta');
        $this->datetime = date('c');
    }  

    public function authenticateEmployeeLogin($username, $password){
    	$query = $this->getValue("password", "employeelogin", "username", $username);        
    	if(!$query){    		
            $this->error .= "Authorisation failed -- No Such Employee Exists";
        }elseif(md5($password) != $query){            

           $this->error = "Error - Password Authentication Failed";
           $this->userLog($username, 'n');

           
               if($this->isLocked($username)){
                   $this->error = $this->error."<br />Your Account Is Locked Please Contact Your System Administrator";

               }elseif($this->getAttempt($username) < $this->getMaxAttempt()){
                    $query = $this->getMaxAttempt('employee') - $this->getAttempt($username);
                    $this->error .= "<br />".$query." Unsuccessful Attempts Left More.";

                    $query = $this->getAttempt($username, 'employee');
                    ++$query;
                    $this->updateAttempt($username, $query, 'employee');
               }elseif($this->getAttempt($username, 'employee') == $this->getMaxAttempt('employee')){
                   $this->error .= "<br />Your Account Has Been Locked. Please Contact Your Administrator To Unlock It";
                   $this->lockOfficer($username);
               }else{
                    $this->error .= "<br />Your Account Is Locked Please Contact Your System Administrator";
               }
           

        }elseif($this->isLocked($username)){
            $this->error .= "Your Account Is Locked Please Contact Your System Administrator";                      
        }else{
            //all the authentication has been passed by the user
            $employeeId = $this->getLoginEmployeeId($username);
            $_SESSION['employeeLogged'] = $employeeId;
            $this->setLoginCookie();
            $this->updateAttempt($username, 0);
            $this->userLog($employeeId, 'y');
            $this->redirect('./employee/');
        }   	
    }
    
    public function getLoginEmployeeId($username){
    	return $flag =  $this->getValue("id", "employeelogin", "username", $username);
    }
    
    public function authenticateUser($username, $password){ //this function authenticates the login process.
        $query = $this->getValue("password", "officer", "username", $username);
        if(!$query){
            $this->authenticateEmployeeLogin($username, $password);
        }elseif(md5($password) != $query){            

            $this->error = "Error - Password Authentication Failed";
           $this->userLog($username, 'n');

           if(!$this->checkAdmin($username)){
               if($this->isLocked($username)){
                   $this->error = $this->error."<br />Your Account Is Locked Please Contact Your System Administrator";

               }elseif($this->getAttempt($username) < $this->getMaxAttempt()){
                    $query = $this->getMaxAttempt() - $this->getAttempt($username);
                    $this->error .= "<br />".$query." Unsuccessful Attempts Left More.";

                    $query = $this->getAttempt($username);
                    ++$query;
                    $this->updateAttempt($username, $query, 'employee');
               }elseif($this->getAttempt($username) == $this->getMaxAttempt()){
                   $this->error .= "<br />Your Account Has Been Locked. Please Contact Your Administrator To Unlock It";
                   $this->lockOfficer($username);
               }else{
                    $this->error .= "<br />Your Account Is Locked Please Contact Your System Administrator";
               }
           }

        }elseif($this->isLocked($username)){
            $this->error .= "Your Account Is Locked Please Contact Your System Administrator";                      
        }else{
            //all the authentication has been passed by the user            
            $this->setLoginCookie();
            $this->updateAttempt($username, 0);
            $this->userLog($username, 'y');
			if($this->checkLoanOfficer($username)){
				$_SESSION['loggedLoanUser'] = $username;
				$this->redirect('./loan/');
			}
			$_SESSION['loggedUser'] = $username;	            
            $this->redirect('./profile/');
        }        
    }

    private function userLog($username, $flag){  //the log of the login process is saved by this function
        $agent = $_SERVER ['HTTP_USER_AGENT'];
        $ip = $_SERVER ['REMOTE_ADDR'];
        if (getenv ( 'HTTP_X_FORWARDED_FOR' ))
                $ip2 = getenv ( 'HTTP_X_FORWARDED_FOR' );
        else
                $ip2 = getenv ( 'REMOTE_ADDR' );

        $query = " INSERT INTO userlog (officer, local_ip, global_ip, date, success, browser) values ( \"$username\" , \"$ip2\" ,\"$ip\",  \"$this->datetime\" , \"$flag\", \"$agent\") " ;
        $this->processQuery($query);
    }

    public function checkLogged(){          //this function chks the status of the logged user whether he is logged or not
        
        if(isset ($_SESSION['loggedUser'])){
            if($this->checkCookie()){
                if(!$this->checkSeizure()){;
                    $this->setLoginCookie();
                    $loggedUser = $_SESSION['loggedUser'];
                    return $loggedUser;
                }                
            }
        }
        return false;
    }
    
	public function checkEmployeeLogged(){          //this function chks the status of the logged user whether he is logged or not        
        if(isset ($_SESSION['employeeLogged'])){
            if($this->checkCookie()){
                if(!$this->checkSeizure()){;
                    $this->setLoginCookie();
                    $loggedUser = $_SESSION['employeeLogged'];
                    return $loggedUser;
                }                
            }
        }
        return false;
    }
    
	public function checkLoanOfficerLogged(){          //this function chks the status of the logged user whether he is logged or not        
        if(isset ($_SESSION['loggedLoanUser'])){
            if($this->checkCookie()){
                if(!$this->checkSeizure()){;
                    $this->setLoginCookie();
                    $loggedUser = $_SESSION['loggedLoanUser'];
                    return $loggedUser;
                }                
            }
        }
        return false;
    }
  

    private function checkCookie(){        //this one chks the cookie information of the browser of the user. whether the cookie is active or not
        if(isset($_COOKIE['username'])){
            
            $dateDifference = new dateDifference;
            $dateDifference->getDifference($this->datetime, $_COOKIE['username']);
            if($dateDifference->getSeconds() > $this->getCookieTimer()){
                return false;
            }
            return true;
        }
        return false;
    }

    protected function checkSeizure(){
        $flag =  $this->getValue("value", "global", "field", "seizure");
        if($flag != y)
            return false;
        return true;
    }
    
    public function getMaxAttempt($type){     //this functoin gets the total attempts defined in the database
        if(func_num_args() == 0)
        	return $this->getValue("value", "global", "field", "login_attempt");
        elseif($type == 'employee')
        	return $this->getValue("value", "global", "field", "employeeLoginAttempt");
        else 	
        	return 0;       	
    }

    private  function getAttempt($username, $type){    //this function gets the total consecutive invalid attempts made by the user
        if(func_num_args() > 1){
        	if($type == 'employee')
        		return $this->getValue("attempts", "officer", "username", $username);
        }
    	return $this->getValue("attempts", "officer", "username", $username);
    }

    private function updateAttempt($username, $attempt, $type){        //this function upadates the invalid login attempts of the user in the database
		if(func_num_args() > 2){
			if($type == 'employee')
				$query = "UPDATE employeelogin SET attempts = \"$attempt\" WHERE username = \"$username\" ";
		}else 
			$query = "UPDATE officer SET attempts = \"$attempt\" WHERE username = \"$username\" ";
        $this->processQuery($query);
    }

    private function checkAdmin($username){         //this function is used to chk if the given username is the admin        

        $flag = $this->getValue("type", "officer", "username", $username);

        if($flag == 'a')
            return true;
        return false;
    }

    private function checkLoanOfficer($username){         //this function is used to chk if the given username is the admin        

        $flag = $this->getValue("type", "officer", "username", $username);

        if($flag == 'l')
            return true;
        return false;
    }
    
    protected function isLocked($username){       //this function chks whether the given person is locked or not due to any reason

        $flag = $this->getValue("status", "officer", "username", $username);

        if($flag == 'n')
            return true;
       	elseif ($flag== ''){
       		$flag = $this->getValue("status", "employeelogin", "username", $username);
       		if($flag == 'n')
       			return true;
       	}
        return false;
    }

    protected  function lockOfficer($username, $type){      //this function is used to lock the officer. the officer will not be able to log in if he/she is locked
        if(func_num_args() > 1){
        	if($type == 'employee')
        		$query = "UPDATE employeelogin SET status = \"n\" WHERE username = \"$username\" ";
        	else 
        		$query = "UPDATE officer SET status = \"n\" WHERE username = \"$username\" ";
        }else
        	$query = "UPDATE officer SET status = \"n\" WHERE username = \"$username\" ";
        
        $this->processQuery($query);
    }

    private function setLoginCookie(){              //this function sets the login cookie       
        $expiry = time() + $this->getCookieTimer();
        setcookie ("username", $this->datetime, $expiry);
    }

    public function getCookieTimer(){            //this function chks the cookie time of the user in his browser
        return $this->getValue("value", "global", "field", "logged_timer");

    }

    public function redirect($url){             //this function is used to redirect the user to any url 
    	echo "<script type=\"text/javascript\">
                    window.location= \"$url\"
            </script>";
        echo "Please Wait For Some Time. If The Time Exceeds For More Than 15 Seconds Then The javascript Of Your Browser is Disabled. So Please Enable it to use the software";
        exit(0);
    }

    public function palert($message, $url){     //this function is used to show a alert box with the message defined within
        echo "<script>
          alert( \"$message\" );
          </script>";
        $this->redirect($url);        
    }    
}
?>
