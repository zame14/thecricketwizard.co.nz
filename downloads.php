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
<meta name="keywords" content="the cricket wizard,cricket stickers,cricket banner,cricket poster" />
<meta name="description" content="The Cricket Wizard Downloads" />
</head>
<body>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td style="padding-top:5px; padding-bottom:5px; ">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" border="0" id="main">
			<tr>
				<td valign="top" style="padding-top:10px; padding-left:10px; "><img src="images/headingloggedout.jpg"></td>
			</tr>
			<tr>
				<td align="center" valign="top"><div class="line"> &nbsp;</div></td>
			</tr>
			<tr>
				<td style="padding-bottom:350px; padding-top:50px;"><?php Downloads(); ?></td>
			</tr>
			<tr>
				<td align="center" style="padding-bottom:10px; padding-top:5px; " colspan="3"><?php footer(1); ?></td>
			</tr>			
		</table>
		</td>
	</tr>
</table>
</form>
</body>
</html>
<?php
function Downloads(){ 
?>
<table cellpadding="10" cellspacing="0" border="0" bgcolor="#212121" id="whiteborder" width="80%" align="center">
	<tr>
		<td style="font-size:22px;" align="center"><strong>Downloads</strong></td>	
	</tr>
	<tr>
		<td style="font-size:18px; padding-left:20px; ">
		Welcome to The Cricket Wizard downloads page.
		</td>
	</tr>
	<tr>
		<td style="font-size:22px; padding-left:20px; " id="goldtext">Web Banner</td>
	</tr>
	<tr>
		<td style="padding-left:20px; "><img src="images/banner.gif" alt="The Cricket Wizard web banner" border="1" style="border-color:#CCCCCC; "></td>
	</tr>
	<tr>
		<td style="font-size:18px; padding-left:20px; ">
		The Cricket Wizard web banner is available for use on websites and blogs.<br>
		Copy and paste the following HTML code into your website or blog:
		</td>
	</tr>
	<tr>
		<td style="padding-left:20px; ">
		<textarea onClick="this.select();" rows="6" cols="65" wrap="on" readonly><!-- The Cricket Wizard Banner Code - Version 1 --><div align="center"><a href="http://www.thecricketwizard.co.nz" target="_blank"><img src="http://www.thecricketwizard.co.nz/images/banner.gif" alt="The Cricket Wizard - Online Cricket Management System" border="0"></a></div><!-- End Banner Code --></textarea>
		</td>
	</tr>
	<tr>
		<td style="font-size:22px; padding-left:20px; " id="goldtext">Posters<font size="-1" color="#FFFFFF"><sup class="subcls">*</sup></font></td>
	</tr>
	<tr>
		<td style="font-size:18px; padding-left:20px; ">The following posters are available to download and display at your local clubrooms or school.</td>
	</tr>
	<tr>
		<td style="padding-left:20px; ">
		<table cellpadding="0" cellspacing="0" border="0" id="whitetext">
			<tr>
				<td>
				A4<br /><br />
				<a href="PDF/The_Cricket_Wizard.pdf" target="_blank"><img src="images/poster.jpg" border="2" style="border-color:#CCCCCC; " alt="The Cricket Wizard A4 poster"></a>
				</td>
				<td style="padding-left:50px;">
				A3<br /><br />
				<a href="PDF/The_Cricket_Wizard_A3.pdf" target="_blank"><img src="images/poster_a3.jpg" border="2" style="border-color:#CCCCCC; " alt="The Cricket Wizard A3 poster"></a>				
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="font-size:18px; padding-left:20px; ">Click on the poster to start downloading.</td>
	</tr>
	<tr>
		<td style="padding-left:20px; "><font size="-1"><sup class="subcls">*</sup></font>Note: You will need <a href="http://www.adobe.com/products/reader/" target="_blank">Adobe Reader</a>.</td>
	</tr>
	<tr>
		<td style="font-size:22px; padding-left:20px; " id="goldtext">Stickers</td>
	</tr>
	<tr>
		<td style="padding-left:20px; ">
		<table cellpadding="0" cellspacing="0" id="whitetext" style="font-size:18px;" width="100%" border="0">
			<tr>
				<td valign="top" width="50%">
				The following stickers are available to purchase.<br />Stickers can be used as bat stickers or to stick on your teams scorebook.<br /><br />
				Details:<br />
				<ul>
					<li type="disc">Cost (5 stickers): NZD 10.00</li>
					<li type="disc">Cost (10 stickers): NZD 20.00</li>
					<li type="disc">Size: 5cm x 9cm</li>
				</ul>
				</td>
				<td align="center"><img src="stickers/cw_bat_scorebook.jpg" border="1" style="border-color:#CCCCCC; " alt="The Cricket Wizard bat sticker"></td>
			</tr>
			<tr>
				<td style="padding-top:20px; ">Sticker 1<br /><br /><img src="stickers/sticker1.jpg" border="1" style="border-color:#CCCCCC; " alt="The Cricket Wizard bat sticker"></td>
				<td style="padding-top:20px; ">Sticker 2<br /><br /><img src="stickers/sticker2.jpg" border="1" style="border-color:#CCCCCC; " alt="The Cricket Wizard bat sticker"></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="font-size:18px;">For more information on ordering your Cricket Wizard stickers, please email Aaron at <a href="mailto:admin@thecricketwizard.co.nz">admin@thecricketwizard.co.nz</a>.</td>
	</tr>
	<tr>
		<td align="center" colspan="2"><input type="button" value="Close" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="javascript:self.close();"></td>
	</tr>
</table>
<?php
}
?>
