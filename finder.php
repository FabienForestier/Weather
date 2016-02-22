<?php

global $debug, $distinctValue, $table, $orderField,$selectedState,$database,$place,$whichState,$data;

$debug = true;
$debug = false;

function getArguments($inputArray){//use $_GET as the $inputArray
	global $debug, $table, $distinctValue, $orderField, $selectedState,$database,$place,$whichState,$data;
	if(!empty($inputArray)){
		
		if(isset($inputArray['data'])){
			$data = htmlentities($inputArray['data']);
		}
	
		if(isset($inputArray['database'])){
			$database = htmlentities($inputArray['database']);
		}
		
		if(isset($inputArray['whichstate'])){
			$whichState = htmlentities($inputArray['whichstate']);
		}
	
		if(isset($inputArray['state'])){
			$selectedState = htmlentities($inputArray['state']);
		}
	
		if(isset($inputArray['distinct'])){
			$distinctValue = htmlentities($inputArray['distinct']);
		}
		
		if(isset($inputArray['table'])){
			$table = htmlentities($inputArray['table']);
		}
		if(isset($inputArray['order'])){
			$orderField = htmlentities($inputArray['order']);
		}else{
			$orderField="";
		}
		
		if(isset($inputArray['place'])){
			$place = htmlentities($inputArray['place']);
			$place = str_replace("_"," ",$place);
			//echo "$place";
		}
		
		if($debug) {
			echo "Distinct value is $distinctValue<br>\n";
			echo "Table is $table<br>\n";
			if(strlen($database)==0)
				echo "No database where chosen";
			else
				echo "The database is $database<br>\n";
			if(strlen($orderField)==0)
				echo "No order field was set<br>\n";
			else
				echo"Order set to $orderField<br>\n";
			if(strlen($selectedState)==0)
				echo "No state has been selected yet";
			else
				echo "The selected state is $selectedState";
			if(strlen($place)==0)
				echo "No place has been selected yet";
			else
				echo "The selected place is $place";
		}
	}
}

function makeConnection($user,$password,$host,$datab){
	$connection = mysqli_connect($host,$user,$password,$datab) or die("<p>Error: Could not connect.".mysqli_error($connection)."</p>");
	return $connection;
}

function buildQuery(){
	global $debug, $table, $distinctValue, $orderField, $selectedState,$place,$whichState,$data;
	if(strlen($selectedState)!=0)
	{
		$query = "SELECT DISTINCT place FROM $table WHERE state='$selectedState'";
	}else
	{
		if(strlen($place)!=0)
		{
			if(strlen($data)!=0)
			{
				$query = "SELECT place,state,station,$data FROM $table WHERE place='$place'";
				if(strlen($whichState)!=0)
				{
					 $query= $query." AND state='$whichState'";
				}
			}else{
				$query = "SELECT place,state,station FROM $table WHERE place='$place'";
				if(strlen($whichState)!=0)
				{
					 $query= $query." AND state='$whichState'";
				}
			}
			
		}else
		{
			$query = "SELECT DISTINCT $distinctValue FROM $table";
		}
		
	}
	if($orderField != ''){
		$query = $query." ORDER BY ".$orderField;
		}
	if($debug){
		echo "The query string is $query <br>\n";
	}
	return $query;
}

function doQueryAndDisplay($query,$connection){
	global $debug;
	$result = mysqli_query($connection,$query) or die ("<p>Error: in query".mysqli_error($connection)."</p>");
	//convert the result to an array
	$list = array();
	while($row = mysqli_fetch_assoc($result)){
		$list[]= $row;
	}
	echo json_encode($list);
}


//Main

getArguments($_GET);
$connection = makeConnection("USERNAME",'PASSWORD','noynaert.cs.missouriwestern.edu',$database);
$query = BuildQuery(); 
doQueryAndDisplay($query,$connection);
if($connection)mysqli_close($connection);
?>