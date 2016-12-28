<?php

function print_textBox($name,$value,$htmlAttrib='',$error=0,$errorMsg='',$particularError=0,$particularErrorMsg='',$return=0){
	
	$errorClass = "inputError";
	$outputStr  = "<input type='text'";
	$outputStr .= " name='" . $name . "'";
	$outputStr .= " id='" . $name . "-id'";	
	$outputStr .= " value='" . htmlspecialchars($value, ENT_QUOTES) . "'";
	$outputStr .= (($error!=0 && $value=="") || ($particularError!=0))? " class='".$errorClass."'": " ";
	$outputStr .= $htmlAttrib=="" ? "" : " " . $htmlAttrib;
	$outputStr .= ">";
	
	if($particularError != 0){
		if($particularErrorMsg != ''){
			$outputStr .= "<span class=redText>".ucfirst($particularErrorMsg)."</span>";
		}
	} else if ($error != 0 && $value == '') {
		if($errorMsg != ''){
			$outputStr .= "<span class=redText>".ucfirst($errorMsg)."</span>";			
		}
	}
	if(!$return) echo $outputStr; else return $outputStr;
} // end print_textBox
function print_dropDown($name, $default, $arrList, $selected='', $htmlAttrib='', $error=0, $errorMsg='',$particularError=0,$particularErrorMsg='',$class='',$return=0){

	$class = trim($class);
	$tempClass = (($error!=0 && ($selected=='')) || ($particularError!=0))? "inputError": "";
	
	if ($class=='')
		$outputClass = $tempClass;
	else {
		if ($tempClass<> '' ) 
			$outputClass = $class.' '.$tempClass;
		else
			$outputClass = $class;
	}
	
	
	$outputStr = "<select";
	$outputStr .= " id='" . $name."' name='" . $name."'";
	$outputStr .= " class='".$outputClass."'";	
	$outputStr .= $htmlAttrib . ">";
	
	if (isset($default) && $default != ''){
		$outputStr .= "<option value='".$default."'>" . $default . "</option>";
	}
	
	if(count($arrList)>0)
	{
		foreach($arrList as $arr1)
		{
			$text = current($arr1);
			$b = next($arr1);
			$value = '';
			if($b!==false) $value= $b;
				else $value = $text;
			$outputStr .= "<option value='".$value. "'";
			//$outputStr .= ($value==$selected) ? " selected": ":":'';
			$outputStr .= ($value==$selected) ? " selected": '';
			$outputStr .= ">".$text."</option>";
		}
	}		
	$outputStr .= "</select>";
	
	if ($particularError != 0) {
		$outputStr .= "<span class=redText>" . ucfirst($particularErrorMsg) . "</span>";
	} 
	else if ($error != 0 && $selected=="") {
		$outputStr .=  "<span class=redText>" . ucfirst($errorMsg) . "</span>";
	}

	//echo $outputStr;
	if(!$return) echo $outputStr; else return $outputStr;
	
} // end print_dropDown
function print_dob($dd='',$mm='',$yyyy='') {
	echo "<select id='dd' name='dd'><option value=' '> </option>";
	for ($i=1;$i<32;$i++) {
	echo "<option value='$i'";
	if ($dd==$i) echo " selected='selected'";
	echo ">$i</option>";
	}
	
	echo "</select> <select id='mm' name='mm'><option value=' '> </option>";
	
	$month_list=array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
	for ($i=0;$i<12;$i++) {
	echo "<option value=" . ($i+1);
	if ($mm==($i+1)) echo " selected='selected'";
	echo ">" . $month_list[$i] . "</option>";
	}

	echo "</select> <select id='yyyy' name='yyyy'><option value=' '> </option>";
	
	for ($i=(date("Y")-99);$i<(date("Y")+1);$i++) {
	echo "<option value='$i'";
	if ($yyyy==$i) echo " selected='selected'";
	echo ">$i</option>";
	}
	echo "</select>";
}// end print_dob
 function server_url() {
   $proto = "http" .
   ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "s" : "") . "://";
   $server = isset($_SERVER['HTTP_HOST']) ?
   $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
   return $proto . $server;
} // end server_url
function redirect_rel($relativeurl)
{
   $url = server_url().dirname($_SERVER['PHP_SELF'])."".$relativeurl;

   if (!headers_sent()){
       	header("Location: $url");
		exit();
   }else{
       	echo "<meta http-equiv=\"refresh\" content=\"0;url=$url\">\r\n";
		exit();
   }
} // end redirect_rel
function print_radioGroup($name, $arrList, $selected='', $htmlAttrib='', $vertical=true, $error=0, $errorMsg='',$particularError=0,$particularErrorMsg='',$return=false) {
	//print_array_debug('arrList', $arrList);
	$outputStr = "";
	foreach($arrList as $arr1){
		$text = current($arr1);
		//echo_nl('text', $text);
		$b = next($arr1);
		$value = '';
		if($b!==false){
			$value= $b;
		}else{
			$value = $text;
		}//end if
				
		//$outputStr .= ($error!=0 && $selected=="")? "<span class='inputError'>": "<span>";
		$outputStr .= "<table border='0' width='55%' id='whitetext'>";
		$outputStr .= "<tr>";
		$outputStr .= "<td width='90%' style='font-size:18px;'>";
		$outputStr .= "".$text;
		$outputStr .= "</td>";
		$outputStr .= "<td>";
		$outputStr .= "<input ";
		$outputStr .= " type='radio' name='" . $name . "' id='".$name."-id'";
		$outputStr .= " value='".$value."'";
					
		if($selected != ""){
			$outputStr .=  $value==$selected ? " checked ": "";
		}
		$outputStr .= $htmlAttrib . ">";	
		
		//if($vertical==true){
			//$outputStr .= "<br>";
		//}
		$outputStr .= "</input></td></tr></table></span>";
		

	}

	if ($particularError != 0) {
		$outputStr .= "<br>&nbsp;&nbsp;&nbsp;<span class=redText>" . ucfirst($particularErrorMsg) . "</span>";
	} 
	else if ($error != 0 && $selected=="") {
		$outputStr .=  "<br>&nbsp;&nbsp;&nbsp;<span class=redText>" . ucfirst($errorMsg) . "</span>";
	}
	if(!$return) echo $outputStr;
	else return $outputStr;
	
} // end print_radioGroup
function set_param($varname) {
	$default = '';
	if (isset($_REQUEST[$varname])) {
		$param = trim($_REQUEST[$varname]);
	} else {
		return $default;
	}
	return $param;
} // end set_param
function set_session_param($varname) {
	$debug=0;
	$default = '';
	if (isset($_SESSION[$varname])) {
		$param = trim($_SESSION[$varname]);
	} else {
		return $default;
	}
	if($debug==1){
		echo_nl($varname, $param);
	}
	return $param;
} // end set_param
function generate_logonName($firstname, $lastname){
/* algorithm:
1. generate logonname according to fname and lname (no number at the end) removing all spaces inside and outside and processing 			   dashes and apostophies
2. restrict to 12 chars
3. check_logon_name
4. if exists
5. 	generate logonname with the random two-digit number
6. restrict to 12 chars
7. 	go to 3. 
8. else
9.	return logonname	
*/ 

# 1.
	$firstname 		= trim(preg_replace('/[`~!@#\$%\^&*()\-_=+\\|\]}\[{\'\";:\/?.>,<]/', '', $firstname ));
	$lastname 		= trim(preg_replace('/[`~!@#\$%\^&*()\-_=+\\|\]}\[{\'\";:\/?.>,<]/', '', $lastname ));
	//$firstname = str_replace('\'', '', $firstname);
	
	//$firstname = preg_replace('/[`~!@#\$%\^&*()-_=+\\|\]}\[{\'";:\/?.>,<]*/', '', $firstname);
	//echo_nl('firstname', $firstname);
	
	
	//$lastname = str_replace('-', '', $lastname);
	//$lastname = str_replace('\'', '', $lastname);
	//$lastname = preg_replace('/[`~!@#\$%\^&*()-_=+\\|\]}\[{\'";:\/?.>,<]*/', '', $lastname);
	//echo_nl('lastname', $lastname);
	//die();
	
	$firstname = $firstname;
	//echo_nl('firstname', $firstname);
	$lastname = $lastname;
	//echo_nl('lastname', $lastname);
	$finitial = substr($firstname, 0, 1);
	//echo_nl('initial', $finitial); //die();
	
	$lastname =str_replace(' ', '', $lastname);
	$logonname = strtolower($finitial.$lastname);
	//$logonname = sprintf("%-10.15s", $logonname);
	//print "new logon: " . $logonname . "<br>"; //die();
# 2.
	if (strlen($logonname) > 12){ // logonname restricted to 12 chars
		$logonname = substr($logonname, 0, 12);
	}
	//print "new logon: " . $logonname . "<br>"; die();
# 3.
	$exists = !check_logonName($logonname);
	//print_bool('exists', $exists); die();
	while($exists){
		$logonname .= rand(1, 99);
		//print "new logon 1: " . $logonname . "<br>"; 
		
		if (strlen($logonname) > 12){ // logonname restricted to 12 chars
			$logonname = substr($logonname, 0, 10);
			$logonname .= rand(1, 99);
			//print "new logon 2: " . $logonname . "<br>"; 
		}
		
		$exists = !check_logonName($logonname);
		/*
		if($exists){
			print_bool('exists', $exists);
			echo_nl('logonname', $logonname);
		}else{
			echo_nl('logonname', $logonname);
			exit();
		}
		*/
	}	
	//echo_nl('logonname', $logonname); die();
	return $logonname;
} // end generate_logonName


/**
 * check_logonName($firstname, $lastname)
 * checks if the logonname exists in the DB
*/
function check_logonName($logonname){

	$sqlstr = "call checkLogonNameExists('" . $logonname ."')";
	$arr = array();
	$arr = dbToArray($sqlstr);
	//print_array_debug('arr', $arr, true);//die();
	$exists = $arr[1]['exist'];
	//print_bool('exists', $exists); die();
	
	if($exists){
		return false;
	}else{
		return true;
	}
	
} // end check_logonName

function generate_randomKey($length) 
{
  $possible = "23456789".
              "abcdefghijkmnopqrstuvwxyz".
              "ABCDEFGHJKMNPQRSTUVWXYZ";
  $numericPossible ="23456789";
  $i=1;
  $str = "";
  
  $numeric = rand(1,$length);
  while(strlen($str) < $length) 
  {
	  if($i==$numeric)  $str .= substr($numericPossible, (rand() % strlen($numericPossible)),1);
	  else   $str .= substr($possible, (rand() % strlen($possible)),1);
	$i++;
  }
  return($str);
} // end generate_randomKey
function print_textArea($name, $text, $htmlAttrib, $error=0){

	$outputStr = "<textarea name='" . $name . "' id='" . $name ."'";
	//$outputStr .= $error==1 ? "class='spinputred'": "class='spinput'";
	$outputStr .= ($error==1 && $text=="")? "class='inputError'": "class=''";
	$outputStr .= $htmlAttrib ;
	$outputStr .= " value='" . htmlspecialchars($text, ENT_QUOTES) . "'>";
	//$outputStr .= " value=" . $text .">";
	$outputStr .= $text;
	$outputStr .= "</textarea>";
	$outputStr .= ($error == 1 && $text=="") ? "<br><span class=redText>Please enter the required text.</span>":"";
	echo $outputStr;
	
}
function print_passwordBox($name, $text, $htmlAttrib, $error=0, $errormessage=''){
	//echo_nl('text in print_textBox', $text);
	//echo_nl('error in print_textBox', $error);
	
	$outputStr = "<input type='password' ";
	
	$outputStr .= ($error!=0 && $name=='lastname' && strlen($text) < 2)? "class='inputError'": (($error!=0 && $text=="")? "class='inputError'": "class=''");
	
	$outputStr .= " name='" . $name . "' ";
	$outputStr .= $htmlAttrib;
	$outputStr .= " value='" . htmlspecialchars($text, ENT_QUOTES) . "'>";
	$outputStr .= "</input>";
	if($errormessage != ''){
		//$outputStr .= ($error != 0 && $text=="") ? "<br><span class=datared>".$errormessage."</span>":"";
		$outputStr .= ($error!=0 && $name=='lastname' && strlen($text) < 2)? "<span class='redText'>".$errormessage."</span>":(($error!=0 && $text=="")? "<span class='redText'>".$errormessage."</span>":"");
	}else{
		//$outputStr .= ($error!=0 && $name=='lastname' && strlen($text) < 2)? "<span class='redText'>Please enter the required text</span>":(($error!=0 && $text=="")? "<span class='redText'>Please enter the required text</span>":"");
	}
	echo $outputStr;
	
} // end print_passwordBox
function dbDateDisplay($date,$dateFormat="d M Y",$defaultError="")
{
	if($dateFormat=="")$dateFormat="d M Y";
	if(isDBDate($date)) eval('$date =date("'.$dateFormat.'",strtotime($date));');
	else $date=$defaultError;
	return $date;
}
function isDBDate($date)
{
	if($date!="" && !is_null($date) && str_replace(array(0,'-',' ',':'),'',$date)!="") $date =true;
	else $date=false;
	return $date;
}

function validate_usersession(){
	if(!isset($_SESSION['sessionid'])){
		redirect_rel('index.php');
	}
}

function validate_email($address) {
//    return (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.
//                  '@'.
//                  '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
//                  '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',
//                  $address));

	return (eregi("^[a-zA-Z0-9]+[_a-zA-Z0-9-]*(\.[_a-z0-9-]+)*@[a-z?G0-9]+(-[a-z?G0-9]+)*(\.[a-z?G0-9-]+)*(\.[a-z]{2,4})$", $address));

} // end validate_email
function redirect_abs($absoluteurl)
{
   $url = $absoluteurl;
   if (!headers_sent()){
       	header("Location: $url");
		exit();
   }else{
       	echo "<meta http-equiv=\"refresh\" content=\"0;url=$url\">\r\n";
		//exit();
   }
} // end redirect_abs
?>