<?php
/********************
Includes
********************/
include_once("inc/db_functions.php");
include_once("inc/form_lib.php");
include_once("inc/cricket_lib.php");
printcss();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>The Cricket Wizard</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<style type="text/css">
.pad {
	text-align:right;
}
</style>
<body>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td style="padding-top:5px; padding-bottom:5px; ">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" height="100%" border="0" id="main">
			<tr>
				<td valign="top" style="padding-top:10px; padding-left:10px; "><img src="images/headingloggedout.jpg"></td>
			</tr>
			<tr>
				<td align="center" valign="top"><div class="line"> &nbsp;</div></td>
			</tr>
			<tr>
				<td style="padding-bottom:350px; padding-top:50px;"><?php AboutMe(); ?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
</body>
</html>
<?php
function AboutMe(){ 
?>
<table cellpadding="20" cellspacing="0" border="0" bgcolor="#212121" id="whiteborder" width="60%" align="center">
	<tr>
		<td style="font-size:22px;" align="center"><strong>The Cricket Wizard</strong></td>	
	</tr>
	<tr>
		<td style="font-size:18px; ">
		The Cricket Wizard was designed and developed by Aaron Zame.
		Aaron was educated at Otago University.
		</td>
	</tr>
	<tr>
		<td style="padding-left:50px; padding-right:50px; font-size:18px;">
		<em>"I have combined two passions of mine, cricket and web development to create The Cricket Wizard.
		The idea behind The Cricket Wizard is to provide an online solution for teams/clubs wanting to record
		player details, match results, and player statistics.<br>
		Finally, I would like to thank all my friends and family who have taken time out to trial the system for me."<br>
		<div class="pad">Aaron Zame, Aug 2010.</div></em>		
		</td>
	</tr>
	<tr>
		<td style="font-size:18px; ">
		<strong>Help advertise The Cricket Wizard</strong><br>
		<a href="PDF/The_Cricket_Wizard.pdf" target="_blank">Download</a> the following poster to display at your local clubrooms or school.<sup class="subcls">*</sup><br><br>
		<a href="PDF/The_Cricket_Wizard.pdf" target="_blank"><img src="images/poster.jpg" border="2" style="border-color:#CCCCCC; "></a><br><br>
		<font size="-1"><sup class="subcls">*</sup> You will need <a href="http://www.adobe.com/products/reader/" target="_blank">Adobe Reader</a>.</font>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2"><input type="button" value="Close" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="javascript:self.close();"></td>
	</tr>
</table>
<?php
}
?>
