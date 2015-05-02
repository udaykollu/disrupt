<html >
<head>
	<meta charset="UTF-8">
	<title>  jobs </title>
	
</head>
<body>
	<div class="welcome">
		<h1>You have arrived at jobs view</h1>
	</div>
	
<?php
require_once __DIR__.'/../controllers/DbHandler.php';

echo "in jobs php";
$jobId;
            $response = array();
            $db = new DbHandler();

            // fetching all user tasks
            $result = $db->getAllJobs();

            $response["error"] = false;
            $response["jobs"] = array();

            // looping through result and preparing tasks array
            while ($task = $result->fetch_assoc()) {
                $tmp = array();
                //$tmp["jobId"] = $task["jobId"];
                $tmp["title"] = $task["title"];
                $tmp["companyname"] = $task["companyname"];
                $tmp["companylogo"] = $task["companylogo"];
                $tmp["location"] = $task["location"];
                /* $tmp["companywebsite"] = $task["companywebsite"];
                
                $tmp["jobcategory"] = $task["jobcategory"];
                $tmp["major"] = $task["major"];

                $tmp["dateposted"] = $task["dateposted"];
                $tmp["deadline"] = $task["deadline"];
                $tmp["jobDescription"] = $task["jobDescription"];
                $tmp["applicationURL"] = $task["applicationURL"]; */

                array_push($response["jobs"], $tmp);
            }

            echoRespnse(200, $response);
      


//$app->run();
?>	
</body>
</html>