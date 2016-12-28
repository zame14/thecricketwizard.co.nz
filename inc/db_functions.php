<?php
//session_start();
require_once "conn.php";
require_once "emails.php";

function dbToArray($querystr,$db='cricket_wizard',$field_type='fieldname',$debug=0){
	
	$mysqli_db = dbi_connect($db); //Initialise the mysqli connection
	$query_number=0;// This is to check the number of queries
	//$x=0;
	$array = array();
	if(mysqli_multi_query($mysqli_db, $querystr))
	{
		do {
			if ($result = mysqli_store_result($mysqli_db)) 
			{
				$this_row=0;// Initialise the row number in the array
				$query_number++; //echo $query_number;
				//$array[$query_number][0]['rows']=mysqli_num_rows($result);
				$cols = array();
				$numfields = mysqli_num_fields($result);
				//echo("<BR><BR>$querystr=".$numfields."<BR><BR>");
				while($finfo = mysqli_fetch_field($result))
				{
					$cols[] = $finfo->name;
				}
				while ($row = mysqli_fetch_assoc($result)) 
				{
					
					$this_row++;
					$this_field=0;
					foreach ($cols as $col_name)
					{
						 if($field_type=='fieldname') $array[$query_number][$this_row][$col_name] = $row[$col_name];
						 else $array[$query_number][$this_row][$this_field] = $row[$col_name];
						 $this_field++;
					}
				}
				mysqli_free_result($result); //To free the result set after each usage
			}
			else
			{
				if(mysqli_affected_rows($mysqli_db))
				{
					$query_number++;
					$array[$query_number][0]['rows_affected']=mysqli_affected_rows($mysqli_db);
				}
			}
		} while (mysqli_next_result($mysqli_db));
		mysqli_close($mysqli_db);
		if(count($array)>0)
		{
			if(count($array)==1)foreach($array as $array1) $array =$array1; // This check if there are more result sets returned
			return $array;
		}
	}
	else
	{
		//die("<BR><BR>Error Processing the Query= ".$querystr);		
		emails("error","admin@thecricketwizard.co.nz",$_SESSION["userid"],$querystr,'','','');
		redirect_rel('error.php');
		die();
	}
}

function countdim($array)
{
	$return = 1;
	if(is_array($array))if(is_array(reset($array)))  $return = countdim(reset($array)) + 1;
	return $return;
}




?>
