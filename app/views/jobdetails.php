<html >
<head>
	<meta charset="UTF-8">
	<title>  jobs </title>
	
</head>
<body>
	<div class="welcome">
		<h1>You have arrived at jobs details view</h1>
	</div>
	
<?php

function details ($jobId)
{
require_once __DIR__.'/../controllers/DbHandler.php';
//require_once '../controllers/DbHandler.php';
echo "in jobs php";
$jobId=1;
         
            
     
            $response = array();
            $db = new DbHandler();	
            
            // fetch task
            $result = $db->getJobDetails($jobId);

            if ($result != NULL) {
                echo "Result is not null";
                $response["error"] = false;
                $response["jobId"] = $result["jobId"];
                $response["title"] = $result["title"];
                $response["companyname"] = $result["companyname"];
                $response["companylogo"] = $result["companylogo"];
                $response["companywebsite"] = $result["companywebsite"];
                $response["location"] = $result["location"];
                $response["jobcategory"] = $result["jobcategory"];
                $response["major"] = $result["major"];
                $response["datePosted"] = $result["datePosted"];
                $response["deadline"] = $result["deadline"];
                $response["jobDescription"] = $result["jobDescription"];
                $response["applicationURL"] = $result["applicationURL"];
                echoRespnse(200, $response);
                
            } else {
                $response["error"] = true;
                $response["message"] = "The requested resource doesn't exists";
                echoRespnse(404, $response);
            }
       

}
/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
  //  $app = \Slim\Slim::getInstance();
    // Http response code
    //$app->status($status_code);

    // setting response content type to json
    //$app->contentType('application/json');

    echo json_encode($response);
}

//$app->run();
?>	
</body>
</html>