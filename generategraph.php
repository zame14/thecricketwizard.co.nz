<?php
error_reporting(E_ERROR);
include_once("inc/db_functions.php");
include_once("inc/form_lib.php");
include_once("graphs/phpgraphlib.php");
include("graphs/phpgraphlib_pie.php");
include_once("inc/cricket_lib.php");
printcss();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>The Cricket Wizard</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php
//make sure we have a player selected
if(!isset($_REQUEST['pid'])||$_REQUEST['pid']==""){
	redirect_rel('statshome.php');
}

$filename = "graphs/image".$_REQUEST['id']."".$_REQUEST['pid'].".png";
$run=1; //this will be set to zero if the sum of data for the graph is not greater than 0.
$title="";
//remove existing graph if it has not been removed already
if(file_exists($filename)){
	unlink($filename);
}

if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="back"){
		//double check that the graph exists for this player
		//if graph does exist, remove it.		
		if(file_exists($filename)){
			unlink($filename);
			redirect_rel("playerstatshome.php?pid=".$_REQUEST['pid']."");
		}	
		else {
			redirect_rel("playerstatshome.php?pid=".$_REQUEST['pid']."");
		}
	}
}

$graph = new PHPGraphLib(900,350,$filename);  
$dataArray=array(); 
/******************************************
RUNS GRAPH
******************************************/
if($_REQUEST['id']=="runs"){
	$data = dbToArray("call runs_chart(".$_REQUEST['pid'].");");
	$i=1;
	if($data){
		foreach($data as $row){
			$count=$i;
			$runs=$row["runs"];
			$dataArray[$count]=$runs;
			$i++;
		}
		$title = "Career Runs Scored by ".$data[1]["firstname"]." ".$data[1]["lastname"];
	}
	else {
		$run=0;
	}
}
/******************************************
WICKETS GRAPH
******************************************/
else if($_REQUEST['id']=="wickets"){
	$data = dbToArray("call wickets_chart(".$_REQUEST['pid'].");");
	$i=1;
	if($data){
		foreach($data as $row){
			$count=$i;
			$runs=$row["wickets"];
			$dataArray[$count]=$runs;
			$i++;
		}
		$title = "Career Wickets Taken by ".$data[1]["firstname"]." ".$data[1]["lastname"];
	}
	else {
		$run=0;
	}
}
/******************************************
DISMISSALS GRAPH
******************************************/
else if($_REQUEST['id']=="dismissals"){
	$graph = new PHPGraphLibPie(700,350,$filename);
	$data = dbToArray("call getStatsOnDismissals(".$_REQUEST['pid'].");");
	if($data){
		foreach($data as $row){
			$mode=$row["dismissal"];
			$count=$row["num"];
			$dataArray[$mode]=$count;
		}
		$title = "Mode of Dismissal Breakdown for ".$data[1]["firstname"]." ".$data[1]["lastname"];
	}
	else {
		$run=0;
	}
}
/******************************************
SCORES GRAPH
******************************************/
else if($_REQUEST['id']=="scores"){
	$graph = new PHPGraphLibPie(700,350,$filename);
	$sqldata = dbToArray("call getScoresBreakdown(".$_REQUEST['pid'].");");
	$u = dbToArray("call getUserDetails(".$_REQUEST['pid'].");");
	if($sqldata[1]['total']>0){	
		$dataArray = array("0-9 runs" => $sqldata[1]["0-9"], "10-19 runs" => $sqldata[1]["10-19"], "20-49 runs" => $sqldata[1]["20-49"], "50-99 runs" => $sqldata[1]["50-99"], "100+ runs" => $sqldata[1]["100+"]);
		$title = "Scores Breakdown for ".$u[1]["firstname"]." ".$u[1]["lastname"];
	}
	else {
		$run=0;
	}
}
/******************************************
SCORES GRAPH
******************************************/
else if($_REQUEST['id']=="wkts"){
	$graph = new PHPGraphLibPie(700,350,$filename);
	$sqldata = dbToArray("call getWicketsBreakdown(".$_REQUEST['pid'].");");
	$u = dbToArray("call getUserDetails(".$_REQUEST['pid'].");");
	if($sqldata){
		$dataArray = array("no wickets" => $sqldata[1]["s1"], "one wicket" => $sqldata[1]["s2"], "two wickets" => $sqldata[1]["s3"], "three wickets" => $sqldata[1]["s4"], "four wickets" => $sqldata[1]["s5"], "five wickets" => $sqldata[1]["s6"], "six or more wickets" => $sqldata[1]["s7"]);
		$title = "Wickets Breakdown for ".$u[1]["firstname"]." ".$u[1]["lastname"];
		if($sqldata[1]['total']==0){
			$run=0;
		}
	}
}
else {
	redirect_rel("playerstatshome.php?pid=".$_REQUEST['pid']."");
}

//create the graph
$graph->addData($dataArray);  
$graph->setTitle($title); 
if($_REQUEST['id']=="runs" || $_REQUEST['id']=="wickets"){ 
	//bar graph
	$graph->setGradient("lime", "green");  
	$graph->setBarOutlineColor("black");  
	$graph->setDataValues(true);  
	$graph->setDataValueColor('maroon');  
}
else {
	//pie graph
	$graph->setLabelTextColor("maroon"); 
}
if($run==1) {
	$graph->createGraph();
}

$imgsrc = "graphs/image".$_REQUEST['id']."".$_REQUEST['pid'];
?>
<form name="thefrm" action="generategraph.php?pid=<?php echo $_REQUEST['pid'] ?>&id=<?php echo $_REQUEST['id'] ?>" method="post">
<input type="hidden" name="action" />
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td style="padding-top:5px; padding-bottom:5px; " valign="top">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" border="0" id="main">
			<tr>
				<td valign="top" style="padding-top:10px; padding-left:10px; "><img src="images/headingloggedout.jpg"></td>
			</tr>
			<tr>
				<td align="center" valign="top"><div class="line"> &nbsp;</div></td>
			</tr>
			<tr>
				<td align="right" style="padding-right:50px; padding-top:20px; "><a href="http://www.ebrueggeman.com/phpgraphlib" target="_blank"><img src="http://www.ebrueggeman.com/phpgraphlib/images/phpgraphlib_150x24_green.png" alt="PHPGraphLib - Click For Official Site" width="150" height="24" align="top" style="margin-right:10px;" border="0" /></a> </td>
			</tr>
			<tr>
				<td style="padding-bottom:50px; padding-top:20px;" align="center" id="whitetext" style="font-size:18px;">
				<?php
				if($run==1){
					//generate graph ?>
					<img src="<?php echo $imgsrc; ?>.png">
				<?php
				}
				else {
					echo "Sorry, no data for this player to be displayed.";
				}
				?>
				</td>
			</tr>
			<tr>
				<td align="center" style="padding-bottom:100px; "><input type="button" value="Back" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="Back();"></td>
			</tr>
			<tr>
				<td align="center" style="padding-bottom:10px; padding-top:5px; " colspan="3"><?php footer(0); ?></td>
			</tr>			
		</table>
		</td>
	</tr>
</table>
</form>
</body>
</html>
<script language="javascript">
function Back() {
	frm = document.thefrm;
	frm.action.value="back";
	frm.submit();
}
</script>