<?php
session_start();
include_once("inc/db_functions.php");
include_once("inc/form_lib.php");
include_once("inc/cricket_lib.php");
include_once("inc/emails.php");
printcss();
//print_r($_REQUEST);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>The Cricket Wizard</title>
<meta name="keywords" content="online cricket management system,cricket management system,cricket wizard,cricket statistics" />
<meta name="description" content="Online Cricket Management System" />
<link rel="stylesheet" href="lightbox2/css/lightbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="lightbox2/js/prototype.js"></script>
<script type="text/javascript" src="lightbox2/js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="lightbox2/js/lightbox.js"></script>

</head>
<style>

a {
	color:#FFFFFF;
	text-decoration:none;
	font-family:Calibri;
}
a:hover {
	text-decoration:underline;
}
.afailed {
	color:#FF0000;
	text-decoration:none;
}
.afailed:hover {
	text-decoration:underline;
}
</style>
<body>
<script type="text/javascript" src="toolstips/wz_tooltip.js"></script>
<?php
$error=0;
if(!isset($_REQUEST['view'])){
	$_REQUEST['view'] = "info";
}
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="login"){
		$login = validateUserLogon($_REQUEST['usernameIn'], $_REQUEST['adminpasswordIn'], session_id());
		if($login->message=='Login successful'){
			$_SESSION['userid'] = $login->userid;
			$_SESSION['sessionid']=session_id();
			redirect_rel('home.php');	
		}
		else {
			login_failed();
		}
	}
	else if($_REQUEST['action']=="registernow"){
		//check form
		$errorobj = check_form_fields();
		$error = $errorobj->general;
		if($error==0){
			//no errors
			//check registration
			$_REQUEST['teamname'] = str_replace("'","",$_REQUEST['teamname']);
			$_REQUEST['grade'] = str_replace("'","",$_REQUEST['grade']);
			if(isset($_REQUEST['province'])&&$_REQUEST['province']<>""){
				$_REQUEST['province'] = str_replace("'","",$_REQUEST['province']);
			}
			$sql = "call checkRegistration('".addslashes($_REQUEST['firstname'])."','".addslashes($_REQUEST['lastname'])."', '".$_REQUEST['email']."',
						'".$_REQUEST['teamname']."','".$_REQUEST['grade']."','".$_REQUEST['province']."');";
			$result = dbToArray($sql);
			if($result[1]['message']=="team registered by user"){
				//Already registered this team, display message.
				already_registered($_REQUEST['firstname'], $result[1]['datecreated'], $_REQUEST['teamname']);
			}	
			else if($result[1]['message']=="team not registered by user" || $result[1]['message']=="team not registered"){
				//User already has team admin account, but is trying to register a different team.
				//Treat this as a new user.
				$teamid=0;
				$userid = 0;
				$roleid=1;
				$logonname = generate_logonName($_REQUEST["firstname"], $_REQUEST["lastname"]);
				$rand = generate_randomNum(4);
				$_REQUEST['teamname'] = str_replace("'","",$_REQUEST['teamname']);
				$team = str_replace(" ","",$_REQUEST['teamname']);				
				$teampassword = strtolower($team).''.$rand;
				$_REQUEST['grade'] = str_replace("'","",$_REQUEST['grade']);
				if(isset($_REQUEST['province'])&&$_REQUEST['province']<>""){
					$_REQUEST['province'] = str_replace("'","",$_REQUEST['province']);
				}
				$newteam = insertTeam($teamid,$_REQUEST['teamname'], $_REQUEST['province'],$_REQUEST['country'],$teampassword);
				$newuser = insertUser($userid,$_REQUEST['firstname'],$_REQUEST['lastname'],$_REQUEST['email'],$_REQUEST['adminpassword'],$roleid,$logonname);			
				$gradeid = 0;
				$newgrade = dbToArray("call insertGrade(".$gradeid.", '".$_REQUEST['grade']."',".$newteam->teamid.");");
				addPlayerToTeam($newuser->userid, $newteam->teamid);
				//send regigstration email
				emails("register", $_REQUEST['email'], $_REQUEST['firstname'], $_REQUEST['teamname'], $logonname, $_REQUEST['adminpassword'],'');
				redirect_rel("newregistration.php?id=".$newuser->userid."&uk=".$newuser->uniquekey."");
			}
			else if($result[1]['message']=="team registered"){
				//New user but team has already been registered
				team_already_registered($_REQUEST['firstname'], $_REQUEST['teamname']);
			}
			else {
				//there was some error.
				registration_error();
			} 
		}
		//else {
		//}
	}
	
}
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px; ">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" border="0" style="background-image:url(images/index_background.jpg); background-repeat:no-repeat;" id="main" height="800">
		<tr height="50">
			<td align="right" style="color:#FFFFFF; padding-right:10px; padding-top:5px; " colspan="2">
			<?php login(); ?>
			</td>
		</tr>
		<tr>
			<td style="padding-top:80px; padding-bottom:30px;" align="center" id="indextext" width="65%">Your Online Cricket Management System</td>
			<td rowspan="2" valign="top" style="padding-right:10px;">
			<?php 
			if (isset($_REQUEST['id'])){
				if($_REQUEST['id']=="register"){
					registerteam($error);
				}
				else {
					options();
				}
			}
			else {
				options(); 
			}		
			?>
			</td>
		</tr>
		<tr>
			<td style="padding-left:10px; padding-bottom:10px; " valign="top"><?php welcome($_REQUEST['view']); ?></td>
		</tr>
		<tr>
			<td align="center" style="padding-bottom:10px; padding-top:5px; " colspan="3"><?php footer(1); ?></td>
		</tr>		
		</table>
		</td>
	</tr>
</table>
</form>
<?php
function login(){
?>
<form name="loginfrm" action="index.php" method="post" onSubmit="return LoginUser();">
<input type="hidden" name="action">	
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="right" style="padding-bottom:3px; padding-top:2px;">
		<a href="forgotpassword.php">Forgot your password?</a>
		</td>
	</tr>
	<tr>
		<td id="whitetext">		
		<?php 
		echo"Username:&nbsp;&nbsp;";print_textBox("usernameIn", '', "size='30' maxlength='32'");
		echo '<br>';
		echo '<br>';
		echo"Password:&nbsp;&nbsp;&nbsp;";print_passwordBox("adminpasswordIn", '', "size='30' maxlength='32'");?>
		</td>
		<td bgcolor="#404040" valign="bottom" style="padding-left:5px;">
		<input type="button" value="Login" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="LoginUser();">
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="image" src="images/layout/spacer.gif" width="180" height="1"></td>
	</tr>
</table>
</form>
<script language="javascript">
function LoginUser(){
	frm = document.loginfrm;
	frm.action.value="login";
	frm.submit();
}
</script>
<?php
}

function welcome($view) {
?>
<table id="registration" border="0" width="100%">
	<tr>
		<td style="padding-top:20px; padding-left:10px; padding-right:10px;" colspan="5">
		<?php 
		if($view=="info" || $view==""){
			info(); 
		}
		else if($view=="features"){
			features();
		}
		?>
		</td>
	</tr>
	<tr>
		<td colspan="5" style="padding-left:10px; font-size:18px" id="whitetext">Click on the thumbnails below to view page previews.</td>
	</tr>
	<tr>
		<td align="center" style="padding-bottom:10px; padding-top:10px;"><a href="previews/roster.jpg" rel="lightbox[wizard]"><img src="previews/teamroster_thumb.jpg" style="border-color:#FFFFFF; "></a></td>
		<td align="center" style="padding-bottom:10px; padding-top:10px;"><a href="previews/matches.jpg" rel="lightbox[wizard]"><img src="previews/fixtures_thumb.jpg" style="border-color:#FFFFFF; "></a></td>
		<td align="center" style="padding-bottom:10px; padding-top:10px;"><a href="previews/selectplayers.jpg" rel="lightbox[wizard]"><img src="previews/selectplayers_thumb.jpg" style="border-color:#FFFFFF; "></a></td>
		<td align="center" style="padding-bottom:10px; padding-top:10px;"><a href="previews/stats.jpg" rel="lightbox[wizard]"><img src="previews/stats_thumb.jpg" style="border-color:#FFFFFF; "></a></td>
		<td align="center" style="padding-bottom:10px; padding-top:10px;"><a href="previews/playerstats.jpg" rel="lightbox[wizard]"><img src="previews/playerstats_thumb.jpg" style="border-color:#FFFFFF; "></a></td>
	</tr>
</table>
<?php
} 


function registerteam($error) {

if($error==1){
	$_REQUEST["teamname"] = set_param("teamname");
	$_REQUEST["province"] = set_param("province");
	$_REQUEST["grade"] = set_param("grade");
	$_REQUEST["country"] = set_param("country");
	$_REQUEST["firstname"] = set_param("firstname");
	$_REQUEST["lastname"] = set_param("lastname");
	$_REQUEST["email"] = set_param("email");
	$_REQUEST["adminpassword"] = set_param("adminpassword");
	$teamname = $_REQUEST["teamname"];
	$province = $_REQUEST["province"];
	$country = $_REQUEST["country"];
	$grade = $_REQUEST["grade"];
	$firstname = $_REQUEST["firstname"];
	$lastname = $_REQUEST["lastname"];
	$email = $_REQUEST["email"];
	$adminpassword = $_REQUEST["adminpassword"];
}
else {
	$teamname="";
	$province="";
	$country="";
	$grade="";
	$firstname="";
	$lastname="";
	$email="";
	$adminpassword="";
}

?>
<form name="thefrm" action="index.php" method="post">
<input type="hidden" name="view">
<input type="hidden" name="action">
<table border="0" cellpadding="5" id="registrationfrm" height="650">
	<tr>
		<td>&nbsp;</td>
		<td align="right" rowspan="2" style="background-image:url(images/wizard1.jpg); background-repeat:no-repeat;" height="210">&nbsp;</td>
	</tr>
	<tr>
		<td style="padding-left:15px; color:#FFCC00;" valign="bottom" colspan="2">Register your team:</td>
	</tr>
	<?php
	if($error==1){ ?>
	<tr>
		<td colspan="2" style="padding-left:15px; "><?php validation("Please fill in all required fields."); ?></td>
	</tr>
	<?php } ?>	
	<tr>
		<td style="padding-left:15px;" width="145" valign="top">Team name:<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td>
		<?php 
		$errormsg = "<br>Please enter the name of your cricket team you are wanting to register.";
		print_textBox("teamname", $teamname, "size='30' maxlength='32'",$error);
		?><script language="javascript">document.thefrm.teamname.focus();</script>
		</td>
	</tr>
	<tr>
		<td style="padding-left:15px;">Province:</td>
		<td><?php print_textBox("province", $province, "size='30' maxlength='32'");?></td>
	</tr>
	<tr>
		<td style="padding-left:15px;">Country:</td>
		<td><?php print_textBox("country", $country, "size='30' maxlength='32'");?></td>
	</tr>
	<tr>
		<td style="padding-left:15px;" valign="top">Grade:<font size="+1" color="#C80101";><sup>*</sup></font>&nbsp;&nbsp;
		<img src="images/Question.png" onMouseOver="Tip('Please enter the name of the grade your cricket team competes in.  For example: Premier.', BGCOLOR,'#ffffff', BORDERCOLOR,'#dddddd', DELAY,300, STICKY,false, CLOSEBTN,false, CLICKCLOSE,true, FOLLOWMOUSE, false, PADDING,16, SHADOW,true, SHADOWCOLOR,'#cccccc', SHADOWWIDTH,2, WIDTH,150, FIX, [this,-125,5]);" onMouseOut="UnTip();">
		</td>
		
		<td>
		<?php 
		$errormsg = "<br>Please enter the name of the grade your cricket team competes in.";
		print_textBox("grade", $grade, "size='30' maxlength='32'",$error);?>
		</td>	
	</tr>
	<tr>
		<td colspan="2" style=" padding-top:20px; padding-left:15px; color:#FFCC00;">Your Details:</td>
	</tr>
	<tr>
		<td style="padding-left:15px;" valign="top">First name:<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td>
		<?php 
		$errormsg = "<br>Please enter your first name.";
		print_textBox("firstname", $firstname, "size='30' maxlength='32'",$error);?>
		</td>
	</tr>
	<tr>
		<td style="padding-left:15px;" valign="top">Last name:<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td>
		<?php
		$errormsg = "<br>Please enter your last name."; 
		print_textBox("lastname", $lastname, "size='30' maxlength='32'",$error);?>
		</td>
	</tr>
	<tr>
		<td style="padding-left:15px;" valign="top">Your email:<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td>
		<?php 
		$errormsg = "<br>Please enter your current email address.";
		print_textBox("email", $email, "size='30' maxlength='32'",$error);
		?></td>
	</tr>
	<tr>
		<td style="padding-left:15px;" valign="top">A password:<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td>
		<?php 
		$errormsg = "<br>Please enter a password to use.";
		print_passwordBox("adminpassword", $adminpassword, "size='30' maxlength='32'",$error);
		?></td>
	</tr>
	<tr>
		<td style="padding-top:20px; padding-bottom:10px;" align="center" colspan="2">
		<input type="button" value="Register now!" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="RegisterNow();">
		</td>
	</tr>
</table>
</form>
<script language="javascript">
function RegisterNow(){
	frm = document.thefrm;
	frm.action.value="registernow";
	frm.submit();
}
</script>
<?php
}

function insertTeam($teamid,$teamname,$province,$country,$teampassword){
	$sql="call insertUpdateTeam(".$teamid.",'".addslashes($teamname)."','".addslashes($province)."','".$country."','".$teampassword."');";
	$result =dbToArray($sql);
	$newteam->teamid = $result[1]['teamid'];
	return $newteam;
}
function insertUser($userid,$firstname,$lastname,$email,$password,$roleid,$logonname) {
	$sql="call insertUpdateUser(".$userid.",'".addslashes($firstname)."','".addslashes($lastname)."','".$email."','".$password."',".$roleid.",'".$logonname."','');";
	$result =dbToArray($sql);
	$newuser->userid = $result[1]['userid'];
	$newuser->uniquekey = $result[1]['uniquekey'];
	return $newuser;
}
function addPlayerToTeam($playerid, $teamid){
	$sql="call insertPlayerTeam(".$playerid.",".$teamid.");";
	$result =dbToArray($sql);
}
function validateUserLogon($logonname, $password, $sessionid)
{
	$sql = "call validateUserLogon('".addslashes($logonname)."','".$password."','".$sessionid."');";
	$result = dbToArray($sql);
	//print_r($result);
	$login->message = $result[1]['message'];
	if($login->message == 'Login successful')
	{
		$login->userid = $result[1]['userid'];
	}
	return $login;
}

function generate_randomNum($length) {
  $numericPossible ="23456789";
  $i=1;
  $str = "";  
  $numeric = rand(1,$length);
  while(strlen($str) < $length) {
	  $str .= substr($numericPossible, (rand() % strlen($numericPossible)),1);
	$i++;
  }
  return($str);
} // end generate_randomKey

function login_failed(){
?>
<table cellpadding="5" cellspacing="0" border="0" id="loginfailed" bgcolor="#CCCCCC">
	<tr>
		<td style="padding-left:120px; padding-right:100px;">
		You have entered an invalid Cricket Wizard username or password.  Please check that the correct details are entered.
		If you are trying to login with a Team Administrator account, click <a class="afailed" href="forgotpassword.php">here</a> to request your login details.
		If you are trying to login with a Player account, please contact your Team Administrator for your login details.
		</td>
	</tr>
</table>
<?php
}

function already_registered($firstname, $dateregistered, $teamname){
?>
<table cellpadding="5" cellspacing="0" border="0" id="loginfailed" bgcolor="#CCCCCC">
	<tr>
		<td style="padding-left:120px; padding-right:100px;">
		<?php
		echo "Hi ".$firstname.",<br>
		You have already registered the ".$teamname." cricket team, on ".$dateregistered.".  Please use the login details that were
		emailed to you to login to the system.<br>
		If you have forgotten your login details, click <a class='afailed' href='forgotpassword.php'>here</a>.";
		?>
		</td>
	</tr>
</table>
<?php
}

function team_already_registered($firstname, $teamname){
?>
<table cellpadding="5" cellspacing="0" border="0" id="loginfailed" bgcolor="#CCCCCC">
	<tr>
		<td style="padding-left:120px; padding-right:100px;">
		<?php
		echo "Hi ".$firstname.",<br>
		The ".$teamname." cricket team has already been registered.<br>
		Once your Team Administrator has added you to the ".$teamname." team roster, he/she will
		provide you with a players login account which will allow you to login to the system."; 
		?>
		</td>
	</tr>
</table>
<?php
}

function registration_error(){
?>
<table cellpadding="5" cellspacing="0" border="0" id="loginfailed">
	<tr>
		<td style="padding-left:60px; padding-right:60px;">
		There was an error with your registration.  Please click <a class='afailed' href='index.php'>here</a> to try again.
		</td>
	</tr>
</table>
<?php
}

function info(){ ?>
<table cellpadding="0" cellspacing="0" border="0" id="whitetext">
	<tr>
		<td style="font-size:17px;">
		The Cricket Wizard is an easy to use website that allows you to manage your cricket team online.<br><br>
		Register your team today and receive a Team Administrator login account.  This account allows you
		to manage your team - add players to your team roster; add matches your team plays; and enter the scorecard data from that match.<br><br>
		Every player added to your team roster will receive a unique login. They can update their profile, enter their goals
		for the upcoming season, view detailed batting, bowling, and fielding statistics.<br><br>
		These statistics are automatically produced by the system when the match scorecard data is entered. It can be restricted to
		one season, one competition, one opposition, or a combination of all seasons, all competitions, and all opposition.<br><br>
		The Cricket Wizard has many more features making it a must for all cricket teams.<br><br>
		To view the complete list of features, click <a href="index.php?view=features">here</a>.<br><br>
		</td>
	</tr>
</table>
<script language="javascript">
function Toggle_View(id){
	frm = document.thefrm;
	frm.view.value=id;
	frm.submit();
}
</script>
<?php
}

function features(){
$f = dbToArray("call getFeatures('','','');");
$count = count($f);
?>
<table cellpadding="0" cellspacing="0" border="0" id="whitetext" width="100%">
	<tr>
		<td style="padding-bottom:10px;" colspan="2"><strong>List of features</strong></td>
	</tr>
	<tr>
		<td>
		<table id="whitetext">
		<?php
		for($i=1;$i<=10;$i++){?>
		<tr>
			<td>
			<ul>
				<li type="disc"><?php echo $f[$i]['feature']; ?></li>
			</ul>
			</td>
		</tr>
		<?php
		}
		?>
		</table>
		</td>
		<td valign="top">
		<table id="whitetext">
		<?php
		for($i=11;$i<=20;$i++){?>
		<tr>
			<td>
			<ul>
				<li type="disc"><?php echo $f[$i]['feature']; ?></li>
			</ul>
			</td>
		</tr>
		<?php
		}
		?>
		</table>	
		</td>
	</tr>	
	<tr>
		<td style="padding-left:20px; "><a href="index.php"><?php echo "<<< Back"; ?></a></td>
	</tr>
</table>
<?php
}

function check_form_fields(){
	//$error = 0;
	$error->general = 0;
	if($_REQUEST['teamname']=="") $error->general = 1;
	if($_REQUEST['grade']=="") $error->general = 1;
	if($_REQUEST['firstname']=="") $error->general = 1;
	if($_REQUEST['lastname']=="") $error->general = 1;
	if($_REQUEST['email']=="") $error->general = 1;
	if($_REQUEST['adminpassword']=="") $error->general = 1;
	
	return $error;
}
function options(){
?>
<table cellpadding="0" cellspacing="0" border="0" id="whitetext">
	<tr>
		<td rowspan="2" valign="top" style="padding-right:5px; padding-left:5px; padding-top:160px; "><img src="images/Exclamation.png"></td>
		<td width="90%" style="font-size:20px; padding-bottom:5px; padding-top:160px;" valign="top" id="goldtext">Register Your Team</td>
	</tr>
	<tr>
		<td style="font-size:18px; padding-right:5px; padding-bottom:30px;" valign="top">
		Click <a href="index.php?id=register">here</a> to register your cricket team with The Cricket Wizard and receive a Team Administrator login account.
		</td>
	</tr>
	<tr>
		<td rowspan="2" valign="top" style="padding-right:5px; padding-left:5px; "><img src="images/Target.png"></td>
		<td width="90%" style="font-size:20px; padding-bottom:5px; color:#CCCCCC;" valign="top">Enter Site in Guest Mode</td>
	</tr>
	<tr>
		<td style="font-size:18px; padding-right:5px; padding-bottom:30px;" valign="top">
		Click <a href="home_guestmode.php">here</a> to enter the site in Guest mode to view batting, bowling, and fielding statistics.  No username or password is required. 
		</td>
	</tr>
	<tr>
		<td rowspan="2" valign="top" style="padding-right:5px; padding-left:5px; "><img src="images/Go In.png"></td>
		<td width="90%" style="font-size:20px; padding-bottom:5px;" valign="top" id="goldtext">Login to The Cricket Wizard</td>
	</tr>
	<tr>
		<td style="font-size:18px; padding-right:5px; padding-bottom:30px;" valign="top">
		If you have a Team Administrator or a player login account, login above.
		</td>
	</tr>
	<tr>
		<td rowspan="2" valign="top" style="padding-right:5px; padding-left:5px; "><img src="images/Fbook.png"></td>
		<td width="90%" style="font-size:20px; padding-bottom:5px; color:#CCCCCC;" valign="top">Join us on Facebook</td>
	</tr>
	<tr>
		<td style="font-size:18px; padding-right:5px; padding-bottom:10px;" valign="top">
		Click <a href="http://www.facebook.com/pages/The-Cricket-Wizard/152689471426986" target="_blank">here</a> to join The Cricket Wizard Facebook page.
		</td>
	</tr>
</table>	
<?php
}
?>
</body>
</html>
