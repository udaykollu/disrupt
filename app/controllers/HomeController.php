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
	

	public function helperlist($useridd)
	{ 
		require_once __DIR__.'/../controllers/DbHandler.php';
		 $db = new DbHandler();		
            $helperslist=$db->helperlist($useridd);
         	//get helpers list with locations
         	$response["helpers"] = array();
			   while ($task = $helperslist->fetch_assoc())
			   {
                $tmp = array();
				$tmp["userid"] = $task["userid"];
		$tmp["latitude"] = $task["latitude"];
		$tmp["longitude"] = $task["longitude"];
		$tmp["role"] = $task["role"];
		
			  array_push($response["helpers"], $tmp);
            }
$res["code"]="1";
$res["data"]=$response;
			return json_encode($res);

	}

	 public function trackvictim($helperuserid,$victimuserid)
	{ 
		require_once __DIR__.'/../controllers/DbHandler.php';
		$res = array();
		 $db = new DbHandler();			
		 //change role to helper
		 $role="helper";
         $t=$db->rolechange($helperuserid,$role);
         $tt=$db->addtohelp($helperuserid,$victimuserid);// add to help table 
	$res=$this->helperlist($victimuserid);
	return $res;
	/*	 $helperslist=$db->helperlist($victimuserid);
         	//get helpers list with locations
         	$response["helpers"] = array();
			   while ($task = $helperslist->fetch_assoc())
			   {
                $tmp = array();
				$tmp["userid"] = $task["userid"];
		$tmp["latitude"] = $task["latitude"];
		$tmp["longitude"] = $task["longitude"];
		$tmp["role"] = $task["role"];
			  array_push($response["helpers"], $tmp);
            }
$res["code"]="1";
$res["data"]=$response;
 $res["victimdata"]=array();
  //get victim location
         	$result=$db->getlocation($victimuserid);
            $task = $result->fetch_assoc();
            $tmp["userid"] = $victimuserid;
		$tmp["latitude"] = $task["latitude"];
		$tmp["longitude"] = $task["longitude"];
		$res["victimdata"]=$tmp;
         //   array_push($res["victimdata"], $tmp);
        //    $res["victimdata"]=$response["victim"] ;
                return json_encode($res); 
	     //	return json_encode($task);
	     */
return json_encode($res);
   }

public function helped($helperuserid)
	{ 
		require_once __DIR__.'/../controllers/DbHandler.php';
		$res = array();
         
		 $db = new DbHandler();			
		 //change role to user
		 $role="user";
         $t=$db->rolechange($helperuserid,$role);

		 //for future change we change role table
        $tt=$db->helped($helperuserid) ;	
         $res["code"]="1";
         $res["data"]=null;
         return json_encode($res); 
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
	        //set user role to victim
	        $response = array();
	        $role="victim";
            $db = new DbHandler();
            $t=$db->rolechange($userid,$role);
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
		$res = array("code" => "0", "data" => "");
            $response = array();
            $db = new DbHandler();
		      $result = $db->gethelp($userid);
		      $countRows = $result->num_rows;
		      if($countRows==0)
		      {
		      	$res["code"]="0";
		      	return $res;
		      }
      	 $response["helpers"] = array();
			   while ($task = $result->fetch_assoc())
			   {
                $tmp = array();
				$tmp["userid"] = $task["userid"];
		$tmp["latitude"] = $task["latitude"];
		$tmp["longitude"] = $task["longitude"];
		$tmp["role"] = $task["role"];
			  array_push($response["helpers"], $tmp);
            }
            $res["code"]="1";
            $res["data"]=$response;
               return json_encode($res) ;
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
	public function updatelocation($userid,$latitude,$longitude,$activity,$type)
	{
	require_once __DIR__.'/../controllers/DbHandler.php';
	     $db = new DbHandler();
//echo" in update location home ";
	     if($type=="uh")
	     {
		    $result = $db->updatelocation($userid,$latitude,$longitude,$activity);
        	//$result='true';
		 $response=$this->home($userid);
           return $response;
         }
          else
          {$result = $db->updatelocation($userid,$latitude,$longitude,$activity);
          	return json_encode($result);
          }
	
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
	     $algo="sha256";
	     $password = hash($algo, $password, false); 
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
 $response = array("code" => "1", "data" => "");
	        $db = new DbHandler();
            $role="user";
          $t= $db->rolechange($useridd,$role);
           // set user role to user
          $tt=$db->helpreceived($useridd);
			//send gcm notification that help is received
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
return json_encode($response);	
	}
		  
		}



