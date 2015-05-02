<html >
<head>
	<meta charset="UTF-8">
	<title>  users </title>
	
</head>
<body>
	<div class="welcome">
		<h1>You have arrived at users  view</h1>
	</div>
	
<?php

//function details ($jobId)
//{
require_once __DIR__.'/../controllers/DbHandler.php';
echo "in jobs php";
$personName="uday";
$personPhotoUrl="purl";
$personGooglePlusProfile="gurl";
$email="myeamil";
         
            
     
            $response = array();
            $db = new DbHandler();	
            
            // fetch task
			$result=$db->insertUser($personName,$personPhotoUrl,$personGooglePlusProfile,$email);
 //}          
?>	
</body>
</html>