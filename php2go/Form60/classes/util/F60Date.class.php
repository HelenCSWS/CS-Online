<?php

/**
 * Perform the necessary imports
 */
import('php2go.datetime.Date');


class F60Date extends Date 
{
	
    function localDateTime() 
    {
        $Conf =& Conf::getInstance();
        $dateFormat = $Conf->getConfig('LOCAL_DATETIME_FORMAT');
        if ($dateFormat) {
                return date($dateFormat);
        } else {
                return date("d/m/Y H:i:s");
        }
    }
    
    function sqlDateTime() 
    {
        return date("Y-m-d H:i:s");
    }
    
    function sqlDate() 
    {
        return date("Y-m-d");
    }

    function initDate()
    {
        return date("d/m/Y");
    }
    
    
    function getDisplayDate($dateText)
	{
	 	
		//delivery date
		$delivery_date =split("-",$dateText);
		
		$month_name = $delivery_date[0]; 
		
	
		$month_number = "";   

		for($i=1;$i<=12;$i++){   
		    if(strtolower(date("M", mktime(0, 0, 0, $i, 1, 0))) == strtolower($month_name)){   
		        $month_number = $i;   
		        break;   
		    }   
		}  
//		echo $dateText;
		$month_number = str_pad($month_number,2,"0",STR_PAD_LEFT );
		$month_day = str_pad($delivery_date[1],2,"0",STR_PAD_LEFT );
		
		//YYYY-mm-dd
		return "2010-$month_number-$month_day";
	}
    
    function getNextWeekDate($next_weekday, $isSqlDate=false)
	 {
		$c_year = date("Y");
		$c_month = date("m");
		$c_date = date("d");
		$c_week_day = date("w");
		
		if($c_week_day<=$next_weekday)
			$left_day = $next_weekday - $c_week_day;
		else
			$left_day = 7-( $c_week_day-$next_weekday);
			
		if($isSqlDate)
			$next_week_date= date("Y-m-d H:i:s",mktime(0,0,0,$c_month,$c_date+$left_day,$c_year));
		else
			$next_week_date= date("m/d/Y",mktime(0,0,0,$c_month,$c_date+$left_day,$c_year));
		
	//	print date("Y-m-d H:i:s",mktime(0,0,0,$c_month,$c_date+$left_day,$c_year));
		return $next_week_date;
	
	}
	
	function getLastDay4Month($month = '', $year = '') 
	 {
		   if (empty($month)) {
		      $month = date('m');
		   }
		   if (empty($year)) {
		      $year = date('Y');
		   }
		   $result = strtotime("{$year}-{$month}-01");
		   $result = strtotime('-1 second', strtotime('+1 month', $result));
		   return date('d', $result);
		}
		
    //change  d/m/y format to Y-m-d for mysql format
    function getSqlDate($mydate) //  m/d/y
    {
        $sql_date ="";
        if (strlen($mydate)!=0)
        {
            list($month, $day, $year) = split('[/]', $mydate);
            $sql_date = $year."-".$month."-".$day;
        }
        return $sql_date;

    }

    function get2goDate($mydate) //  m/d/y
    {
        $sql_date = "";
       if (strlen($mydate)!=0 and $mydate!=NULL )
        {
            list($year,$month,$day ) = split('[-]', $mydate);
            $sql_date = $month."/".$day."/".$year;
        }
        return $sql_date;

    }
    
 
    
    function sql2USDate($mydate, $showTime=True)
    {
        ereg("^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})([[:space:]]([0-9]{1,2}):([0-9]{1,2}):?([0-9]{1,2})?)?$", $mydate, $regs);
        $retVal = "$regs[2]/$regs[3]/$regs[1]";
        if ($showTime) 
            $retVal .= " $regs[5]:$regs[6]:$regs[7]";
        return $retVal;
    }
    
    function sql2PHPDate($mydate, $showTime=True) // $exp_date = "2006-01-16";
    {
        ereg("^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})([[:space:]]([0-9]{1,2}):([0-9]{1,2}):?([0-9]{1,2})?)?$", $mydate, $regs);
        //$retVal = "$regs[2]/$regs[3]/$regs[1]";//01/10/2011timehere 
        $retVal = "$regs[1]-$regs[2]-$regs[3]";
        if ($showTime) 
            $retVal .= " $regs[5]:$regs[6]:$regs[7]";
        return $retVal;
    }
    
    function getMonthTxt($month_num)
    {
        $dateInfo  = getDate(mktime(0, 0, 0, $month_num ,1, date("Y")));

        return $dateInfo["month"];
    }
    
      function ucwords1($s)
	{
		$exceptions_upper = array("ab", "bc", "se", "sw", "ne", "nw", "rca");
		$exceptions_lower = array("a", "at", "is", "of", "in", "are", "s", "the", "and", "or");
		$exceptions_lower_th = array("th", "st", "nd");
        $s = strtolower($s);
        $middle = 0;
        $middle_sep = 0;
        $st = 0;
        $letter_first = -1;
        $a = array();
        for($i = 0; $i < strlen($s); $i++)
        {
			$v = ord($s{$i});
			if($v >= 97 && $v <= 122) {	//letters
				if($middle == 0) {	//start of a new word
				    if($i > $st) {
						array_push($a, substr($s, $st, $i-$st));	//save last separator
						$st = $i;
					}
				    $middle = 1;
				}
                $middle_sep = 0;
				if($letter_first == -1)
				    $letter_first = 1;
			} else {
				if($middle_sep == 0) {	//start of new seperator
				    if($i > $st) {
						array_push($a, substr($s, $st, $i-$st));	//save last word
						$st = $i;
					}
					$middle_sep = 1;
				}
				$middle = 0;
				if($letter_first == -1)
				    $letter_first = 0;
			}
		}
	    if($i > $st) {
			array_push($a, substr($s, $st, $i-$st));	//save the last segement
		}
		if($letter_first == -1)
		    return '';
        for($i = 1-$letter_first; $i < count($a); $i+=2) {
			if(in_array($a[$i], $exceptions_lower)) {
				if($i == 0)   //first word
					$a[$i] = ucfirst($a[$i]);
			} else if(in_array($a[$i], $exceptions_lower_th)) {
				if($i != 0) {
					if($a[$i-1] == ' ')
						$a[$i] = ucfirst($a[$i]);
				}
				else
					$a[$i] = ucfirst($a[$i]);
			} else if(in_array($a[$i], $exceptions_upper)) {
				$a[$i] = strtoupper($a[$i]);
			} else {
				$a[$i] = ucfirst($a[$i]);
			}
		}
		return join('', $a);
	}
}

?>