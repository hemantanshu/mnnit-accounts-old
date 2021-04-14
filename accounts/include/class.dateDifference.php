<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */

class dateDifference{
    private $date1, $date2, $a, $days, $hours, $minutes, $seconds;
    
    public function getDifference($bigger, $smaller) {
        $this->date1 = $bigger;
        $this->date2 = $smaller;

        $this->days = intval((strtotime($this->date1) - strtotime($this->date2)) / 86400);
        $this->a = ((strtotime($this->date1) - strtotime($this->date2))) % 86400;
        $this->hours = intval(($this->a) / 3600);
        $this->a = ($this->a) % 3600;
        $this->minutes = intval(($this->a) / 60);
        $this->a = ($this->a) % 60;
        $this->seconds = $this->a;
    }

    public function getDays(){
        return $this->days;
    }

    public function getHours(){
        return $this->hours;
    }

    public function getMinutes(){
        return $this->minutes;
    }

    public function getSeconds(){
        return $this->seconds;
    }
    
    public function getMonthDifference($sDate, $eDate){
    	$i = 0;
    	$year = substr($sDate, 0, 4);
    	$month = substr($sDate, 4, 2);
    	while (true){
    		$date1 = date('Ym', mktime(0, 0, 0, $month + $i, 22, $year));
    		if ($date1 == $eDate)
    			return $i;
    		++$i;
    	}
    }
}

?>
