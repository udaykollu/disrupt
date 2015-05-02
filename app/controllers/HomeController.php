<?php

class HomeController extends BaseController 
{


	public function adduser($userid,$username,$password,$phone)
	{ 
		require_once __DIR__.'/../controllers/DbHandler.php';
            
			list($part1, $part2) = explode('@', $userid);
			if($part2!='nyu.edu')
			{
			$response = array("code" => "0", "data" => "");
			return json_encode($response);
			}
            $db = new DbHandler();			
         	$result=$db->insertUser($userid,$username,$password,$phone);
			return json_encode($result);

	}
	
	 public function trackuser($userid,$requesteruserid)
	{ 
		require_once __DIR__.'/../controllers/DbHandler.php';
         $response["friends"] = array();
		 $db = new DbHandler();			
         	$result=$db->getlocation($userid);
                $task = $result->fetch_assoc();
                array_push($response["friends"], $task);
                return Response::json($response); 
		//	return json_encode($task);

	}

 public function forgotpassword($userid)
	{ 
		require_once __DIR__.'/../controllers/DbHandler.php';
         $db = new DbHandler();			
         $result=$db->forgotpassword($userid);
         return json_encode($result);

	}

	public function feedback($userid,$report)
	{ 
		require_once __DIR__.'/../controllers/DbHandler.php';
         $db = new DbHandler();			
         $result=$db->feedback($userid,$report);
         return json_encode($result);

	}


	
	public function askhelp($userid)
	{
	require_once __DIR__.'/../controllers/DbHandler.php';
	        
	        $response = array();
            $db = new DbHandler();
			$userlocation=$db->getlocation($userid);
			
			$locationresponse=array();
			   while ($task = $userlocation->fetch_assoc())
			   {
                $tmp = array();
                $tmp["id"]=$task["ID"];
		$tmp["userid"] = $userid;
		$tmp["latitude"] = $task["latitude"];
		$tmp["longitude"] = $task["longitude"];
		$tmp["helprequested"]=1;
		//phone number not sending for now
			  array_push($locationresponse, $tmp);
            }
			
			
	        $result = $db->gethelp($userid);
			   while ($task = $result->fetch_assoc())
			   {
       
			  array_push($response, $task["phoneid"]);
            }
			
		     	$sendnoti=$db->sendGoogleCloudMessage( $locationresponse, $response );
return json_encode($sendnoti);	
	}
	
	
	
	
	
	public function home($userid)
	{
//	echo "in home";
            $response = array();
            $db = new DbHandler();
		      $result = $db->gethelp($userid);
      	 $response["friends"] = array();
			   while ($task = $result->fetch_assoc())
			   {
                $tmp = array();
				$tmp["userid"] = $task["userid"];
		$tmp["latitude"] = $task["latitude"];
		$tmp["longitude"] = $task["longitude"];
			  array_push($response["friends"], $tmp);
            }
               return Response::json($response); 
	//	 return $result; 
				
	}
	
	 
	public function updategcm($userid,$gcm)
	{
	require_once __DIR__.'/../controllers/DbHandler.php';
	     $db = new DbHandler();
		    $result = $db->updategcm($userid,$gcm);
        //	echo"after db";
		//	$result='true';
		
           return json_encode($result);
	
	}
	




public function resetpassword($userid,$oldpassword,$newpassword)
	{
	require_once __DIR__.'/../controllers/DbHandler.php';
	     $db = new DbHandler();
		 $result = $db->resetpassword($userid,$oldpassword,$newpassword);
    return json_encode($result);
	
	}


public function updatepassword($userid,$password)
	{
	require_once __DIR__.'/../controllers/DbHandler.php';
	     $db = new DbHandler();
		 $result = $db->updatepassword($userid,$password);
    return json_encode($result);
	
	}
	public function updatelocation($userid,$latitude,$longitude)
	{
	require_once __DIR__.'/../controllers/DbHandler.php';
	     $db = new DbHandler();
		    $result = $db->updatelocation($userid,$latitude,$longitude);
        	//$result='true';
           return json_encode($result);
	
	}
	  
     public function verify($userid,$accesscode)
	{
	require_once __DIR__.'/../controllers/DbHandler.php';
	    $db = new DbHandler();
	    $response = array("code" => "0", "data" => "");
        $result = $db->verify($userid);
	    if($result==$accesscode)
		  {
		   $r=$db->updatestatus($userid);
		   $response["code"]="1";
		  }
		else
		   $response["code"]="0";
    return json_encode($response);
	
	}
	
	
	 public function login($userid,$password)
	{
	require_once __DIR__.'/../controllers/DbHandler.php';
	     $db = new DbHandler();
	     $response = array("code" => "0", "data" => "");
  		 $result = $db->login($userid);
		if($result==$password)
		$response["code"]="1";
		else
		$response["code"]="0";
    return json_encode($response);
	}


	  public function verificationcode($userid)
	{
	//echo "in home";
	require_once __DIR__.'/../controllers/DbHandler.php';
	     $db = new DbHandler();
		 $result = $db->verificationcode($userid);
         return json_encode($result);
	
	}




	public function helpreceived($useridd)
	{
	require_once __DIR__.'/../controllers/DbHandler.php';
	        $response = array();
            $db = new DbHandler();
			$userlocation=$db->getlocation($useridd);
			
			$locationresponse=array();
			   while ($task = $userlocation->fetch_assoc())
			   {
                $tmp = array();
		$tmp["userid"] = $useridd;
		$tmp["id"]=$task["ID"];
		$tmp["latitude"] = $task["latitude"];
		$tmp["longitude"] = $task["longitude"];
		$tmp["helprequested"]=0;

		//phone number not sending for now
			  array_push($locationresponse, $tmp);
            }
			
			
	        $result = $db->gethelp($useridd);
			   while ($task = $result->fetch_assoc())
			   {
       
			  array_push($response, $task["phoneid"]);
            }
			
		//all data in $locationresponse and ids in $response	

         	$sendnoti=$db->gcmhelpreceived( $locationresponse, $response );
return json_encode($sendnoti);	
	}
		  
		}



