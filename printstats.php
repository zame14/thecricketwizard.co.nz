<?php
/********************
Includes
********************/
include_once("inc/db_functions.php");
include_once("inc/form_lib.php");
include_once("inc/cricket_lib.php");
session_start();
validate_usersession();
//printcss();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>The Cricket Wizard</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<style type="text/css">
#stats {
	border:1px solid #000000;
	background-color:#FFFFFF;
	color:#000000;
	font-family: Calibri, Tahoma, sans-serif;	
		
}
#stats td {
	border:1px solid #000000;
}
</style>
<body onLoad="javascript:window.print();">
<?php
$opponenttext = "all opposition";
$comptext = "all competitions";
$seasontext = "all seasons";
$criteria = "WHERE m.teamid=".$_REQUEST["teamid"]."";
$cols = 14;
$showExtra = false;
if($_REQUEST["teamid"] == 638 || $_REQUEST['teamid'] == 776) {
	$cols = 16;
	$showExtra = true;
}
//work out criteria
//check if opponent has been selected
if((isset($_REQUEST['opponent']))&&$_REQUEST['opponent']<>""){
	$opponent = '"'.str_replace("_"," ",$_REQUEST['opponent']).'"';
	$opponenttext = $_REQUEST['opponent'];
	//check if comp has been selected
	if((isset($_REQUEST['compid']))&&$_REQUEST['compid']<>""){
		//check if season has been selected
		$comptext = $_REQUEST['compid'];
		if((isset($_REQUEST['season']))&&$_REQUEST['season']<>""){
			//all three have been selected so set criteria			
			$season = '"'.str_replace("_"," ",$_REQUEST['season']).'"';
			$seasontext = $_REQUEST['season']." season";
			$criteria = "WHERE m.teamid=".$_REQUEST["teamid"]." AND opponent=".$opponent." AND m.compid=".$_REQUEST['compid']." AND season=".$season."";
		}
		else {
			//season not selected, but opponent and comp have been selected
			$criteria = "WHERE m.teamid=".$_REQUEST["teamid"]." AND opponent=".$opponent." AND m.compid=".$_REQUEST['compid']."";
		}
	}
	else if((isset($_REQUEST['season']))&&$_REQUEST['season']<>""){
		//comp not select but opponent and season have been
			$season = '"'.str_replace("_"," ",$_REQUEST['season']).'"';
			$criteria = "WHERE m.teamid=".$_REQUEST["teamid"]." AND opponent=".$opponent." AND season=".$season."";			
	}
	else {
		//just opponent has been selected
		$criteria = "WHERE m.teamid=".$_REQUEST["teamid"]." AND opponent=".$opponent."";	
	}
}
//Opponent not selected but check comp and season
else if(isset($_REQUEST['compid'])&&$_REQUEST['compid']<>""){
	$comptext = $_REQUEST['compid'];
	//season and comp have been selected
	if(isset($_REQUEST['season'])&&$_REQUEST['season']<>""){
		$season = '"'.str_replace("_"," ",$_REQUEST['season']).'"';
		$seasontext = $_REQUEST['season']." season";
		$criteria = "WHERE m.teamid=".$_REQUEST["teamid"]." AND m.compid=".$_REQUEST['compid']." AND season=".$season."";
	}
	else {
		//just comp been selected
		$criteria = "WHERE m.teamid=".$_REQUEST["teamid"]." AND m.compid=".$_REQUEST['compid']."";
	}		
}
//just season has been selected
else if(isset($_REQUEST['season'])&&$_REQUEST['season']<>""){
		$season = '"'.str_replace("_"," ",$_REQUEST['season']).'"';
		$seasontext = $_REQUEST['season']." season";
		$criteria = "WHERE m.teamid=".$_REQUEST["teamid"]." AND season=".$season."";
}		
if($comptext<>"all competitions"){ 
	$rs = dbToArray("call getComps('where compid=".$comptext."')");
	$comptext = $rs[1]['competition'];
}
?>
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="right" style="font-family: Calibri, Tahoma, sans-serif; font-size:14px; color:#999999;">Printed on: <?php echo date('d-m-Y'); ?></td>
	</tr>
	<tr>
		<td style="padding-left:20px; padding-top:20px; font-family:Broadway; font-size:40px;">The Cricket Wizard</td>
	</tr>
	<tr>
		<td style="padding-left:20px; padding-top:20px; padding-bottom:10px; font-family:Calibri; font-size:22px;">
		<?php
		if($_REQUEST['id']=="batting"){
			$stats = dbToArray("call getStatsTeam('".$criteria."','group by p.playerid','order by sum(runs) desc','');");
			echo "Batting & Fielding Statistics for ".$stats[1]['teamname'];
		}
		else {
			$stats = dbToArray("call getStatsTeam('".$criteria."','group by p.playerid','order by sum(wickets) desc','');");
			echo "Bowling Statistics for ".$stats[1]['teamname'];
		} 
		?>
		</td>
	</tr>
	<tr>
		<td style="padding-left:15px; ">
		<table cellpadding="5" cellspacing="0" border="0" id="stats">
			<tr>
				<td colspan="<?php echo $cols; ?>" style="font-size:18px;"><strong><?php echo "vs ".$opponenttext.", ".$comptext.", ".$seasontext; ?></strong></td>
			</tr>
			<?php
			if($_REQUEST["id"]=="batting"){ 
			?>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>M</td>
				<td>I</td>
				<td>NO</td>
				<td>Runs</td>
				<?php
				if($showExtra) { ?>
				<td>BF</td>
				<td>SR</td>
				<?php } ?>
				<td>Ave</td>
				<td>HS</td>
				<td>100</td>
				<td>50</td>
				<td>4s</td>
				<td>6s</td>
				<td>Ct</td>
				<td>St</td>
			</tr>
			<?php
			for($i=1;$i<=count($stats);$i++){	
				if($stats[$i]['innings']>0){ 
					if($i % 2) { $color="#CCCCCC"; } else { $color="#FFFFFF"; }
				?>
				<tr>
					<td bgcolor="<?php echo $color; ?>"><?php echo $i; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo stripslashes($stats[$i]['player']); ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['matches']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['innings']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['notouts']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['runs']; ?></td>
					<?php
					if($showExtra) { ?>
					<td bgcolor="<?php echo $color; ?>"><?php echo ($stats[$i]['ballsfaced']<>"-") ? $stats[$i]['ballsfaced'] : "&nbsp;"; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo ($stats[$i]['ballsfaced']<>"-") ? round(($stats[$i]['runs']/$stats[$i]['ballsfaced'])*100,2) : "&nbsp;"; ?></td></td>
					<? } ?>
					<td bgcolor="<?php echo $color; ?>">					
					<?php 
					if($stats[$i]['innings']==$stats[$i]['notouts']){
						echo"-";
					}
					else {
						echo round($stats[$i]['bataverage'],2); 
					}
					?>
					</td>
					</td>
					<?php
					$hs = dbToArray("call highestIndividualScore('".$criteria." AND p.playerid = ".$stats[$i]['playerid']."','order by runs desc, did desc');");
					if($hs[1]['did']==7){
						$highestscore = $hs[1]['runs']."*";
					}
					else {
						$highestscore = $hs[1]['runs'];
					}
					?>
					<td bgcolor="<?php echo $color; ?>"><?php echo $highestscore; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['100s']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['50s']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['fours']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['sixes']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['catches']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['stumpings']; ?></td>
				</tr>
			<?php }
			} 
		}
		else { ?>				
		<tr>
			<td colspan="2">&nbsp;</td>
			<td>M</td>
			<td>Balls</td>
			<td>Mdns</td>
			<td>Runs</td>
			<td>Wkts</td>
			<td>Ave</td>
			<td>SR</td>
			<td>Econ</td>
			<td>5</td>
			<td>10</td>
		</tr>
			<?php
			for($i=1;$i<=count($stats);$i++){	
				if($stats[$i]['deliveries']>0){ 
					if($i % 2) { $color="#CCCCCC"; } else { $color="#FFFFFF"; }
				?>
				<tr>
					<td bgcolor="<?php echo $color; ?>"><?php echo $i; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo stripslashes($stats[$i]['player']); ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['matches']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['deliveries']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['maidens']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['runsconceded']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['wickets']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php if($stats[$i]['deliveries']>0&&$stats[$i]['wickets']==0){echo"-";}else{echo round($stats[$i]['bowlaverage'],2);} ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php if($stats[$i]['deliveries']>0&&$stats[$i]['wickets']==0){echo"-";}else{echo round($stats[$i]['strikerate'],2);} ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo round($stats[$i]['rpo'],2); ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['fivewickets']; ?></td>
					<td bgcolor="<?php echo $color; ?>"><?php echo $stats[$i]['tenwickets']; ?></td>
				</tr>
			<?php }
			} 
		} ?>
		</table>
		</td>
	</tr>
	<tr>
		<td style="font-family: Calibri, Tahoma, sans-serif; font-size:14px; color:#999999; padding-left:20px; padding-top:20px;">http://www.thecricketwizard.co.nz</td>
	</tr>
</table>
</body>
</html>
