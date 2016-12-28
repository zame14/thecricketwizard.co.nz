<?php
session_start();
function printcss() { 
?> 
<link rel="stylesheet" type="text/css" href="inc/styles.css" title="default">
<?php
}

function loggedIn($firstname, $lastname, $teamname, $role) {
?>
<form name="logoutfrm" action="logout.php" method="post">
<table cellpadding="0" cellspacing="0" border="0" id="loggedin">
	<tr>
		<td width="150" align="right">Logged in user:</td>
		<td style="padding-left:5px;"><?php  echo $firstname.' '.stripslashes($lastname); ?></td>
	</tr>
	<tr>
		<td align="right">Team:</td>
		<td style="padding-left:5px;"><?php  echo $teamname; ?></td>
	</tr>
	<tr>
		<td colspan="2" align="right" style="padding-top:5px;"><input type="button" value="Logout" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="LogoutUser();"></td>
	</tr>
</table>
</form>
<script language="javascript">
function LogoutUser()
{
	frm = document.logoutfrm;
	frm.submit();
}
</script>
<?php
}
function getUserDetails($userid) {
	$sql="call getUserDetails(".$userid.");";
	$result =dbToArray($sql);
	$user->firstname = $result[1]['firstname'];
	$user->lastname = $result[1]['lastname'];
	$user->teamname = stripslashes($result[1]['teamname']);
	$user->role = $result[1]['role'];
	$user->teamid = $result[1]['teamid'];
	$user->profilesetup = $result[1]['profilesetup'];
	$user->personalsetup = $result[1]['personalsetup'];
	$user->username = $result[1]['username'];
	$user->teampassword = $result[1]['teampassword'];
	$user->userid = $result[1]['userid'];
	$user->uniquekey = $result[1]['uniquekey'];
	$user->email = $result[1]['email'];
	$user->private = $result[1]['private'];
	return $user;
}
function getProfileId($userid){
	$sql="call getProfileId(".$userid.");";
	$result = dbToArray($sql);
	$profile->playerid = $result[1]['playerid'];
	return $profile;
}
function mainmenu(){
?>
<table cellpadding="5" cellspacing="0">
	<tr>
		<td><img src="images/homebutton.gif" onClick="javascript:document.location='home.php';" style="cursor:pointer;"></td>
		<td><img src="images/teambutton.gif" onClick="javascript:document.location='teamroster.php';" style="cursor:pointer;"></td>
		<td><img src="images/fixturesbutton.gif" onClick="javascript:document.location='matches.php';" style="cursor:pointer;"></td>
		<td><img src="images/statsbutton.gif" onClick="javascript:document.location='statshome.php';" style="cursor:pointer;"></td>
	</tr>
</table>
<?php
}

function mainmenu2(){
?>
<table cellpadding="5" cellspacing="0" border="0">
	<tr>
		<td id="whitetext" style="font-size:20px; " align="center"><strong>Main Menu</strong></td>
	</tr>
	<tr>
		<td align="center" style="padding-bottom:20px; "><img src="images/homebutton.gif" onClick="javascript:document.location='home.php';" style="cursor:pointer;"></td>
	</tr>
	<tr>
		<td align="center" style="padding-bottom:20px; "><img src="images/teambutton.gif" onClick="javascript:document.location='teamroster.php';" style="cursor:pointer;"></td>
	</tr>
	<tr>
		<td align="center" style="padding-bottom:20px; "><img src="images/fixturesbutton.gif" onClick="javascript:document.location='matches.php';" style="cursor:pointer;"></td>
	</tr>
	<tr>
		<td align="center" style="padding-bottom:20px; "><img src="images/statsbutton.gif" onClick="javascript:document.location='statshome.php';" style="cursor:pointer;"></td>
	</tr>
</table>
<?php
}

function mainmenu_match($teamid, $phppage){
if(isset($_REQUEST['onExit'])){
	if($_REQUEST['onExit']=="true"){
		unsetSessions();
		//dbToArray("call deleteSelectedPlayers(".$teamid.")");
		if($_REQUEST['goto']=="home"){
			redirect_rel('home.php');
			unsetSessions();
		} 
		else if($_REQUEST['goto']=="roster"){
			redirect_rel('teamroster.php');
			unsetSessions();
		} 
		else if($_REQUEST['goto']=="match"){
			redirect_rel('matches.php');
			unsetSessions();
		} 
		else if($_REQUEST['goto']=="stats"){
			redirect_rel('statshome.php');
			unsetSessions();
		}
	}
}
?>
<form name="matchmenu" action="<?php echo $phppage; ?>" method="post">
<input type="hidden" name="onExit">
<input type="hidden" name="goto">
<table cellpadding="5" cellspacing="0">
	<tr>
		<td id="whitetext" style="font-size:20px; " align="center"><strong>Main Menu</strong></td>
	</tr>
	<tr>
		<td align="center" style="padding-bottom:20px; "><img src="images/homebutton.gif" onClick="Go('home');" style="cursor:pointer;"></td>
	</tr>
	<tr>
		<td align="center" style="padding-bottom:20px; "><img src="images/teambutton.gif" onClick="Go('roster');" style="cursor:pointer;"></td>
	</tr>
	<tr>
		<td align="center" style="padding-bottom:20px; "><img src="images/fixturesbutton.gif" onClick="Go('match');" style="cursor:pointer;"></td>
	</tr>
	<tr>
		<td align="center" style="padding-bottom:20px; "><img src="images/statsbutton.gif" onClick="Go('stats');" style="cursor:pointer;"></td>
	</tr>
</table>
</form>
<script language="javascript">
function Go(val){
	ans = confirm("Are you sure you want to exit? Match details have not been completed.");
	if (ans == true) {
		frm = document.matchmenu;
		frm.onExit.value = true;
		frm.goto.value = val;
		frm.submit();
	}
}
</script>
<?php
}
function steps($page){
?>
<table border="0" id="steps">
	<tr>
		<td colspan="3">Progress...</td>
	</tr>
	<tr height="50">
	<?php
	if($page > 0){ ?>
		<td width="180" style="text-decoration:line-through;">Select competition</td>	
		<?php } else if ($page==0){?>
		<td width="180" bgcolor="#00CC00" style="padding-left:5px; "><strong>Select competition</strong></td>			
		<?php } else { ?>
		<td>Select the competition</td>		
		<?php }	?>	
	</tr>
	<tr height="50">
	<?php
	if($page > 1){ ?>	
		<td style="text-decoration:line-through;">Select grade</td>
		<?php } else if ($page==1){ ?>
		<td bgcolor="#00CC00" style="padding-left:5px; "><strong>Select grade</strong></td>		
		<?php } else { ?>
		<td>Select grade</td>		
		<?php }	?>	
	</tr>
	<tr height="50">
	<?php
	if($page > 2){ ?>	
		<td style="text-decoration:line-through;">Enter match details</td>
		<?php } else if ($page==2){ ?>
		<td bgcolor="#00CC00" style="padding-left:5px; "><strong>Enter match details</strong></td>		
		<?php } else { ?>
		<td>Enter match details</td>		
		<?php }	?>	
	</tr>
	<tr height="50">
	<?php
	if($page > 3){ ?>	
		<td style="text-decoration:line-through;">Team selection</td>
		<?php } else if ($page==3){ ?>
		<td bgcolor="#00CC00" style="padding-left:5px; "><strong>Team selection</strong></td>		
		<?php } else { ?>
		<td>Team selection</td>		
		<?php }	?>	
	</tr>
	<tr height="50">
	<?php
	if($page > 4){ ?>	
		<td style="text-decoration:line-through;">Batting performances</td>
		<?php } else if ($page==4){ ?>
		<td bgcolor="#00CC00" style="padding-left:5px; "><strong>Batting performances</strong></td>		
		<?php } else { ?>
		<td>Batting performances</td>		
		<?php }	?>	
	</tr>
	<tr height="50">
	<?php
	if($page > 5){ ?>	
		<td style="text-decoration:line-through;">Batting partnerships</td>
		<?php } else if ($page==5){ ?>
		<td bgcolor="#00CC00" style="padding-left:5px; "><strong>Batting partnerships</strong></td>		
		<?php } else { ?>
		<td>Batting partnerships</td>		
		<?php }	?>	
	</tr>
	<tr height="50">
	<?php
	if($page > 6){ ?>	
		<td style="text-decoration:line-through;">Bowling performances</td>
		<?php } else if ($page==6){ ?>
		<td bgcolor="#00CC00" style="padding-left:5px; "><strong>Bowling performances</strong></td>		
		<?php } else { ?>
		<td>Bowling performances</td>		
		<?php }	?>	
	</tr>
	<tr height="50">
	<?php
	if($page > 7){ ?>	
		<td style="text-decoration:line-through;">Fielding performances</td>
		<?php } else if ($page==7){ ?>
		<td bgcolor="#00CC00" style="padding-left:5px; "><strong>Fielding performances</strong></td>		
		<?php } else { ?>
		<td>Fielding performances</td>		
		<?php }	?>	
	</tr>
</table>
<?php
}
function unsetSessions(){
	if(isset($_SESSION['m']['teamid'])&&$_SESSION['m']['teamid']<>""){
		dbToArray("call deleteSelectedPlayers(".$_SESSION['m']['teamid'].")");
	}
	unset($_SESSION['m']);
}

function banner($colour, $text){
?>
<table width="100%" border="1" cellpadding="0" cellspacing="0" bgcolor="<?php echo $colour; ?>" id="banner">
	<tr>
		<td valign="middle" align="right" colspan="2"><?php echo $text; ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td rowspan="2" valign="top">
		<table bgcolor="#333333" width="100%">
			<tr>
				<td>here</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<?php
}

function validation($errormsg){ ?>
<table border="0" cellpadding="5" cellspacing="0" id="validation">
	<tr>
		<td><?php echo $errormsg; ?></td>
	</tr>
</table>
<?php
}

function footer($val=0, $index=0){
?>
<table cellpadding="5" cellspacing="0" border="0" id="whitetext">
	<tr>
		<?php
		if($index==1){?>
		<td align="center" style="font-size:12px;"><a href="http://www.thecricketwizard.co.nz" style="color:#F7C200; ">Home</a></td>
		<td align="center" style="font-size:12px;">|</td> 
		<td align="center" style="font-size:12px;"><a href="mailto:admin@thecricketwizard.co.nz" style="color:#F7C200; ">Contact</a></td>
		<td align="center" style="font-size:12px;">|</td>
		<td align="center" style="font-size:12px;"><a href="downloads.php" target="_blank" style="color:#F7C200; ">Downloads</a></td>
		<td align="center" style="font-size:12px;">|</td>
		<td align="center" style="font-size:12px; color:#F7C200;"><a href="http://www.axialis.com/free/icons" target="_blank" style="color:#F7C200; ">Icons</a> by <a href="http://www.axialis.com" target="_blank" style="color:#F7C200; ">Axialis Team</a></td>		
	</tr>
	<tr>
		<td align="center" style="font-size:12px;" colspan="9" style="color:#CCCCCC; ">&copy;&nbsp;Copyright 2012 The Cricket Wizard</td>
	</tr>
	<tr>
	<?php }
	else { 
		if($val==1){
			echo '<td align="center" style="font-size:12px;"><a href="http://www.thecricketwizard.co.nz" style="color:#FFFFFF; ">Home</a></td>';
		}
		else {
			echo '<td align="center" style="font-size:12px;"><a href="home.php" style="color:#FFFFFF; ">Home</a></td>';
		}
		?>		
		<td align="center" style="font-size:12px;">|</td> 
		<td align="center" style="font-size:12px;"><a href="mailto:admin@thecricketwizard.co.nz" style="color:#FFFFFF; ">Contact</a></td>
		<td align="center" style="font-size:12px;">|</td>
		<td align="center" style="font-size:12px;"><a href="downloads.php" target="_blank" style="color:#FFFFFF; ">Downloads</a></td>
		<td align="center" style="font-size:12px;">|</td>
		<td align="center" style="font-size:12px; color:#FFFFFF;"><a href="http://www.axialis.com/free/icons" target="_blank" style="color:#FFFFFF; ">Icons</a> by <a href="http://www.axialis.com" target="_blank" style="color:#FFFFFF; ">Axialis Team</a></td>	
	</tr>
	<tr>
		<td align="center" style="font-size:12px;" colspan="9" style="color:#FFFFFF; ">&copy;&nbsp;Copyright 2012 The Cricket Wizard</td>
	</tr>
		<?php
		} ?>		
</table>
<?php
}

function register_guest(){ ?>
<table cellpadding="0" cellspacing="0" border="0" id="loggedin">
	<tr>
		<td width="160" align="right">You are not logged in.</td>
	</tr>
	<tr>
		<td align="right" style="padding-top:10px; "><input type="button" value="Login" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="javascript:document.location='index.php'"></td>
	</tr>
</table>
<?php
}

function guest_menu(){ ?>
<table cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding-left:5px; padding-right:5px; padding-top:20px; "><input type="button" value="Register Now" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold; height:50px;" onClick="javascript:document.location='index.php?id=register'"></td>
	</tr>
	<tr>
		<td style="padding-top:10px; " align="center"><?php ad("125x125",0,1); ?></td>
	</tr>
</table>
<?php
}	
function unstrip_array($array){    
	foreach($array as &$val){       
		if(is_array($val)){            
			$val = unstrip_array($val);        
		}else{            
			$val = stripslashes($val);        
		}    
	} 
	return $array; 
}

function ad($size,$single,$promo=0)
{
	//$size = size of ad
	//$single = if single = 1, display just one ad and randomly display different ads.
				//if single = 0, loop thru and display all ads.
	//$promo = if promo = 1 display 'Your Ad Here' if there are no ads to display.
	global $dbname2;
	$x = "Your Ad Here";
	$ad = dbToArray("call getSponsors('WHERE p.size=\"".$size."\" AND sponsor<>\"".$x."\"','');",$dbname2); 
	
	if($ad[1]['id']<>"")
	{
		?>
		<table cellpadding="5" cellspacing="0" border="0">
			<?php
			if($single==0)
			{
				for($i=1;$i<=count($ad);$i++)
				{ 
				?>
				<tr>
					<td><?php echo "<a href='sponsorship/hits.php?id=".$ad[$i]['id']."' target='_blank'><img src=".IMGFOLDER."/".$ad[$i]['logo']." style=border-color:#CCCCCC; alt=".$ad[$i]['sponsor']."></img></a>"; ?></td>
				</tr>
				<?php
				}
			}
			else
			{
			$i = rand(1,count($ad)); 
			?>
				<tr>
					<td><?php echo "<a href='sponsorship/hits.php?id=".$ad[$i]['id']."' target='_blank'><img src=".IMGFOLDER."/".$ad[$i]['logo']." style=border-color:#CCCCCC; alt=".$ad[$i]['sponsor']."></img></a>"; ?></td>
				</tr>
			<?php			
			}
			?>
		</table>
		<?php
	}
	else 
	{
		if($promo==1)
		{
			// Show "Your Ad Here" banner
			$ad2 = dbToArray("call getSponsors('WHERE p.size=\"".$size."\" AND sponsor=\"".$x."\"','');",$dbname2); 
			?>
			<table cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td><?php echo "<a href='sponsorship/hits.php?id=".$ad2[1]['id']."' target='_blank'><img src=".IMGFOLDER."/".$ad2[1]['logo']." style='border-color:#CCCCCC; border-style:dashed;' alt='Your Ad Here'></img></a>"; ?></td>
				</tr>
			</table>
			<?php		
		}
	}
}


function hits($id)
{
///
}
?>