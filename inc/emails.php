<?php
include_once("db_functions.php");
include_once("form_lib.php");
include_once("cricket_lib.php");
printcss();

function emails($action,$to,$firstname,$teamname,$username,$password,$player){
$tmp = '';
	switch ($action) {
		case "register":
		$subject = "Welcome to The Cricket Wizard";
		//$subject = "Registration test";

		$tmp.="<table cellpadding=3 cellspacing=0 border=0 width=670 style=font-weight:500;color:#000000;font-family:Calibri;font-size:16px;>";
		$tmp.="<tr><td bgcolor=#404040><img src=".HOSTNAME."/images/headingloggedout.jpg></td></tr>";
		$tmp.="<tr><td>";
		$tmp.="Welcome ".$firstname.",<br><br>Thank you for registering the ".$teamname." cricket team with The Cricket Wizard.<br><br>Here are your Team Administrator login details:<br><br>";
		$tmp.="<strong>Username:</strong>&nbsp;&nbsp;".$username."<br><strong>Password:</strong>&nbsp;&nbsp;".$password."<br><br>";
		$tmp.="As the Team Administrator you are responsible for:<br><br>";
		$tmp.="<b>Adding players to your team roster</b><br>Each player you add to your team roster receives a unique login account.  The login account is emailed to you and you can pass these onto each player.";
		$tmp.="<br><br><b>Updating profiles</b><br>You will have access to update the profiles of the players in your team roster.  Each player will be able to update their own profile, but no one else's.";
		$tmp.="<br><br><b>Adding fixtures</b><br>Each match that your team plays needs to be entered into the system.  The players who played in that match are selected from your team roster.";
		$tmp.="<br><br><b>Adding players performances</b><br>Players batting, bowling and fielding performances need to be entered into the system for each match. Players statistics are automatically calculated from the performances that you enter.";
		$tmp.="<br><br><b>Adding trophies to your team's cabinet</b><br>Everytime your team wins a competition you can add this to your team's trophy cabinet for everyone to see.";
		$tmp.="<br><br><b>Resetting passwords</b><br>A player will be able to change their password once they have logged into the system.  If they forget their new password, you can reset it back to the default team password.";
		$tmp.="<br><br><b>Reassigning the  Team Administrator role</b><br>If you are unable to continue as the Team Administrator for this cricket team, you are able to assign a different user to be the Administrator.  You will still be able to log onto the system but only with a player account.";
		$tmp.="<br><br>Thats it. Team and player statistics will be available to view once you have added your first match.<br><br>Regards<br>The Cricket Wizard</td></tr></table>";

		$message = '
		<html>
		<head>
		  <title>'.$subject.'</title>
		</head>
		<body>'.$tmp.'
		</body>
		</html>
		';	
	break;
	
	case "addplayers":
		$count = count($player);
		if($count==1){
			$subject = "New player added to your team roster";
		}
		else {
			$subject = "New players added to your team roster";
		}		
		$tmp.="<table cellpadding=3 cellspacing=0 border=0 width=670 style=font-weight:500;color:#000000;font-family:Calibri;font-size:16px;>";		
		$tmp.="<tr><td bgcolor=#404040><img src=".HOSTNAME."/images/headingloggedout.jpg></td></tr>";
		$tmp.="<tr><td style=padding-left:10px;>";
		$tmp.="Hi ".$firstname.",<br><br>";
		if($count==1){
			$tmp.=$count." new player has been added to your team roster.<br>Their details are:<br><br>"; 
		}
		else {
			$tmp.=$count." new players have been added to your team roster.<br>Their details are:<br><br>";
		}
		$tmp.="</td></tr><tr><td>";
		$tmp.="<table cellpadding=5 border=1 width=600 style=color:#000000;font-family:Calibri;font-size:18px;>";
		$tmp.="<tr><td><b>Name</b></td><td><b>Username</b></td><td><b>Password</b></td></tr>";
		for($i=1;$i<=$count;$i++){			
			$tmp.="<tr><td>".$player[$i]['player']."</td><td>".$player[$i]['username']."</td><td>".$password."</td></tr>";
		}
		$tmp.="</table></td></tr>";
		$tmp.="<tr><td style=padding-left:10px;><br>Please pass these login details to each player.<br><br>Regards<br>The Cricket Wizard.</td></tr></table>";

		$message = '
		<html>
		<head>
		  <title>'.$subject.'</title>
		</head>
		<body>'.$tmp.'
		</body>
		</html>
		';	
	break;		
		case "forgotten":
		//$subject = "Welcome to the Cricket Wizard";
		$subject = "Your Cricket Wizard login details";

		$tmp.="<table cellpadding=3 cellspacing=0 border=0 width=670 style=font-weight:500;color:#000000;font-family:Calibri;font-size:16px;>";
		$tmp.="<tr><td bgcolor=#404040><img src=".HOSTNAME."/images/headingloggedout.jpg></td></tr>";
		$tmp.="<tr><td>";
		$tmp.="Hi ".$firstname.",<br><br>Your new Cricket Wizard login details are:<br><br>";
		$tmp.="<strong>Username:</strong>&nbsp;&nbsp;".$username."<br><strong>Password:</strong>&nbsp;&nbsp;".$password."<br><br>";
		$tmp.="To change this password, go to your profile and use the Change your password screen.";
		$tmp.="<br><br>Regards<br>The Cricket Wizard</td></tr></table>";
		$message = '
		<html>
		<head>
		  <title>'.$subject.'</title>
		</head>
		<body>'.$tmp.'
		</body>
		</html>
		';	
	break;
	case "error":
			//$subject = "Welcome to the Cricket Wizard";
			$subject = "An error has been reported";
	
			$tmp.="<table cellpadding=3 cellspacing=0 border=0 width=670 style=font-weight:500;color:#000000;font-family:Calibri;font-size:16px;>";
			$tmp.="<tr><td>";
			$tmp.="Hi Aaron,<br><br>The following Stored Procedure failed on ".date('d-M-Y')."<br><br>";
			$tmp.= $teamname."<br><br>";
			$tmp.="User ID: ".$firstname;
			$tmp.="<br><br>Regards<br>The Cricket Wizard</td></tr></table>";
			$message = '
			<html>
			<head>
			  <title>'.$subject.'</title>
			</head>
			<body>'.$tmp.'
			</body>
			</html>
			';	
		break;
		case "newteam":
			$to = "admin@thecricketwizard.co.nz";
			$subject = "New Team Registered";
	
			$tmp.="<table cellpadding=3 cellspacing=0 border=0 width=670 style=font-weight:500;color:#000000;font-family:Calibri;font-size:16px;>";
			$tmp.="<tr><td>";
			$tmp.="Hi Aaron,<br><br>A new team has registered with The Cricket Wizard on ".date('d-M-Y')."<br><br>";
			$tmp.= "Team name: ".$teamname.".";
			$tmp.="<br><br>Regards<br>The Cricket Wizard</td></tr></table>";
			$message = '
			<html>
			<head>
			  <title>'.$subject.'</title>
			</head>
			<body>'.$tmp.'
			</body>
			</html>
			';	
		break;
	default:
		$subject='';
		$tmp='';
		$message = '
		<html>
		<head>
		  <title>'.$subject.'</title>
		</head>
		<body>'.$tmp.'
		</body>
		</html>';
	break;
	}
	/*
	else if($action=="addplayers"){
		$to = $disce_email;
		//$subject = "Welcome to the Cricket Wizard";
		$subject = "Registration test";
	
	}
	else if($action=="newmatch"){
		$to = $disce_email;
		//$subject = "Welcome to the Cricket Wizard";
		$subject = "Registration test";
	
	}
	else if($action=="newadmin"){
		$to = $disce_email;
		//$subject = "Welcome to the Cricket Wizard";
		$subject = "Registration test";
	
	}
	*/
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		//$headers .= 'To: '.$to . "\r\n";
		$headers .= 'From: admin@thecricketwizard.co.nz' . "\r\n";
		//$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
		//$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";
		
		// Mail it
		mail($to, $subject, $message, $headers);
}
?>