<?php

class DbHandler extends BaseController{

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

	//not used
	public function sendnotification($ruserid,$huserid)
	{
	 $stmt = $this->conn->prepare("SELECT phone,latitude,longitude  FROM location natural join userdetails where userid=?");
        $stmt->bind_param("s",$userid);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
	//	echo "notification sent to" .$huserid; 
	return $huserid;
	}
    
   public function rolechange($userid, $role)
{
    $stmt = $this->conn->prepare("update userdetails set role=? where userid=?");
        $stmt->bind_param("ss", $role,$userid);
        $stmt->execute();
      //  $tasks = $stmt->get_result();
        $stmt->close();
        return "1";
}

	public function gethelp($userid){
        //echo "in get help userid is".$userid;
	 $stmt = $this->conn->prepare("SELECT latitude,longitude  FROM location  where userid=?");
        $stmt->bind_param("s",$userid);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
		$task = $tasks->fetch_assoc();
		$latitude=$task["latitude"];
		$longitude=$task["longitude"];
		/*	
		$stmt= $this->conn->prepare("select phoneid,userid,latitude, longitude
FROM gcm natural join location
WHERE (
POW( ( 69.1 * ( longitude - $longitude ) * cos( 40.711676 / 57.3 ) ) , 2 ) + POW( ( 69.1 * ( latitude - $latitude ) ) , 2 )
) < ( 1 *0.45 )  and userid!=? and
lastUpdated between date_sub(now(), interval 10 minute) and now() 
order by lastUpdated desc  ;"); 


        $stmt->bind_param('s',$userid);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
		return $tasks;
        */

       // echo "latitude is ".$latitude."longi is".$longitude;
      //  $result = mysqli_query($this->conn, 
     //"CALL nearby( $userid, '-73.9967','40.7298')" or die("Query fail: " . mysqli_error($this->conn)));
	//$tests=$result->fetch_assoc();
    //echo $tests["activity"];
        $stmt = $this->conn->prepare("CALL nearby( ?, ?,?)");
        $stmt->bind_param("sss",$userid,$longitude,$latitude);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
       
        return $tasks;
	}

public function getlocation($userid){
	 $stmt = $this->conn->prepare("SELECT latitude,longitude, ID FROM location natural join
      userdetails where userid=?");
        $stmt->bind_param("s",$userid);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
		return $tasks;                                                                                                                       
	}

public function feedback($userid,$report)
{
     $stmt = $this->conn->prepare("insert into feedback(userid,feedback) values(?,?)");
        $stmt->bind_param("ss",$userid,$report);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;                                                                                                                       
    }
    

public function forgotpassword($userid)
{
    //echo "userid is".$userid;
$response = array();
            
        $stmt = $this->conn->prepare("select status from userdetails where userid=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();   
      // $stmt->store_result();
        $tasks=$stmt->get_result();
      
        $countRows = $tasks->num_rows;
        $stmt->close();



if($countRows==0)
    {
        $response["code"]="0";
        return $response;
    
    }

else
    {  //echo"in not 0";
        $task = $tasks->fetch_assoc();
        $status=$task["status"];
        if($status=="active")
        {
            $temp=$this->verificationcode($userid);
            if($temp["code"]=="0")
            $response["code"]="1";
            else
            $response["code"]="0";
           
        }
       else
           $response["code"]="-1";
    return $response;
    

    }

}

	public function insertUser($userid,$username,$password,$phone) {
	 
	 //check if user exists
	 $response = array("code" => "0", "data" => "");
            
	  $stmt = $this->conn->prepare("select status from userdetails where userid=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $tasks=$stmt->get_result();
      //  $stmt->store_result();
        $countRows = $tasks->num_rows;
        $stmt->close();
        $algo="sha256";
		$password = hash($algo, $password, false); 
	 if($countRows>=1)
	 {
        $task = $tasks->fetch_assoc();
        $status=$task["status"];
        if($status==active)
            $response["code"]="-2";
        else
            $response["code"]="-1";
        return $response;
    
     }
	 else
	 {
	 $role="user";
	    $stmt = $this->conn->prepare("insert into userdetails(userid,username,password,phone,role) values(?,?,?,?,?)");
        $stmt->bind_param("sssss", $userid,$username,$password,$phone,$role);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
		//generate verification code
		//save verification code to db
		//mail verification code to user
		$lat=0.0;
		$lon=0.0;
	    $stmt2 = $this->conn->prepare("insert into location(userid,latitude,longitude) values(?,?,?)");
        $stmt2->bind_param("sss", $userid,$lat,$lon);
        $stmt2->execute();
        $tasks2 = $stmt2->get_result();
        $stmt2->close();
		
		$temp=$this->verificationcode($userid);
		 if($temp["code"]==1)
            $response["code"]="1";
         else
            $response["code"]="0";
    return $response;
        
	}
		
}
	
	public function updatelocation($userid,$latitude,$longitude,$activity)
	{
		 $response = array("code" => "0", "data" => "");
        $stmt = $this->conn->prepare("select * from location where userid=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt->store_result();
        $countRows = $stmt->num_rows;
		$stmt->close();
		
	 if($countRows==0)
	 {$response["code"]="0";
    return $response;
     }
	 else
	 {
	 $stmt = $this->conn->prepare("update location set latitude=?,longitude=?,activity=? where userid=?");
        $stmt->bind_param("ssss", $latitude,$longitude,$activity,$userid);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        $response["code"]="0";
        return $response;
	}
	}

public function updatepassword($userid,$password)
{
    $response = array("code" => "0", "data" => "");
        $stmt = $this->conn->prepare("select password from userdetails where userid=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $tasks = $stmt->get_result();
       // $stmt->store_result();
        $countRows = $tasks->num_rows;
        $stmt->close();
        $algo="sha256";
    $password = hash($algo, $password, false); 
     if($countRows==0)
     { //user does not exist
        $response["code"]="0";
        return $response;
     }
     else
        $stmt = $this->conn->prepare("update userdetails set password=? where userid=?");
           $stmt->bind_param("ss",$password,$userid);
           $stmt->execute();
           $tasks = $stmt->get_result();
           $stmt->close();
           $response["code"]="1";
           return $response;
           


}

    public function resetpassword($userid,$oldpassword,$newpassword)
    {
        $response = array("code" => "0", "data" => "");
        $stmt = $this->conn->prepare("select password from userdetails where userid=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $tasks = $stmt->get_result();
       // $stmt->store_result();
        $countRows = $tasks->num_rows;
       // $tasks = $stmt->get_result();
        $stmt->close();
    $algo="sha256";
    $oldpassword = hash($algo, $oldpassword, false);
    $newpassword = hash($algo, $newpassword, false); 
     if($countRows==0)
     { //user does not exist
        $response["code"]="0";
        return $response;
     }
     else
    {
       $task=$tasks->fetch_assoc();
       $storedpassword=$task["password"];
          if($storedpassword==$oldpassword)
          {
           $stmt = $this->conn->prepare("update userdetails set password=? where userid=?");
           $stmt->bind_param("ss",$newpassword,$userid);
           $stmt->execute();
           $tasks = $stmt->get_result();
           $stmt->close();
           $response["code"]="1";
           return $response;
           }

           else
           {
            //wrong old password
            $response["code"]="-1";
            return $response;
           }
    }
}
	
	
public function verify($userid)
{

	 $stmt = $this->conn->prepare("SELECT code  FROM verify  where userid=?");
        $stmt->bind_param("s",$userid);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
		$task = $tasks->fetch_assoc();
		$key=$task["code"];
		return $key;

}	

public function login($userid)
{
	 $stmt = $this->conn->prepare("SELECT password  FROM userdetails  where userid=?");
        $stmt->bind_param("s",$userid);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
		$task = $tasks->fetch_assoc();
		$password=$task["password"];
		return $password;

}	

public function helperlist($victimuserid)
{
    // $stmt = $this->conn->prepare("SELECT userid,role, latitude, longitude  FROM 
     //   location as l, help as h,userdetails as u
     //   where l.userid=h.helperuserid and h.victimuserid=?
     //   and u.userid=h.helperuserid");
      $stmt=$this->conn->prepare(" select userid,latitude,longitude, role from userdetails natural join location
where userid in(select helperuserid from
 help where victimuserid=?)
union all
select userid,latitude, longitude,role from userdetails natural join location
where userid in(select victimuserid from help
where victimuserid=?)");
        $stmt->bind_param("ss",$victimuserid,$victimuserid);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        
        return $tasks;

}   



public function addtohelp($helperuserid,$victimuserid)
    {
     $stmt = $this->conn->prepare("select * from help where helperuserid=?");
        $stmt->bind_param("s", $helperuserid);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->store_result();
        $countRows = $tasks->num_rows;
       // $tasks = $stmt->get_result();
        $stmt->close();
        
     if($countRows==0)
     { 
     $stmt = $this->conn->prepare("insert into help(helperuserid,victimuserid) values(?,?)");
        $stmt->bind_param("ss",$helperuserid,$victimuserid);
        $stmt->execute();
       // $tasks = $stmt->get_result();
        $stmt->close();
        return "1"; 
     }
     else
    {
       
           $stmt = $this->conn->prepare("update help set victimuserid=? where helperuserid=?");
           $stmt->bind_param("ss",$victimuserid,$helperuserid);
           $stmt->execute();
          // $tasks = $stmt->get_result();
           $stmt->close();
          // $response["code"]="1";
           
            return "1";
           
    }
    }

public function helped($helperuserid)
{
     $stmt = $this->conn->prepare("delete  FROM help  where helperuserid=?");
        $stmt->bind_param("s",$helperuserid);
        $stmt->execute();
       // $tasks = $stmt->get_result();
        $stmt->close();
      //  $task = $tasks->fetch_assoc();
        return "1";

}   


public function helpreceived($victimuserid)
{
     $stmt = $this->conn->prepare("delete  FROM help  where victimuserid=?");
        $stmt->bind_param("s",$victimuserid);
        $stmt->execute();
       // $tasks = $stmt->get_result();
        $stmt->close();
      //  $task = $tasks->fetch_assoc();
        return "1";

}  


	public function updatestatus($userid)
	{
	 $stmt = $this->conn->prepare("update userdetails set status='active' where userid=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
	}
	
	//inserting gcm
public function updategcm($userid,$gcm)
	{
	//echo "in gcm db";
	$response= array();
		  $stmt = $this->conn->prepare("select * from gcm where userid=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt->store_result();
        $countRows = $stmt->num_rows;
		$stmt->close();
		
	 if($countRows>=1)
	 {
        $stmt = $this->conn->prepare("update gcm set phoneid=? where userid=?");
        $stmt->bind_param("ss", $gcm,$userid);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
       $response["code"]="2";
       return $response;
    }
	 else
	 {
	 $stmt = $this->conn->prepare("insert into gcm(userid,phoneid) values(?,?)");
        $stmt->bind_param("ss", $userid,$gcm);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        $response["code"]="1";
        return $response;
        
	}

    $response["code"]="0";
    return $response;
		
	}



 public function verificationcode($userid)
 {
 $response = array("code" => "0", "data" => "");
            
 $stmt = $this->conn->prepare("select * from verify where userid=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt->store_result();
        $countRows = $stmt->num_rows;
        $stmt->close();
        
if($countRows==0)
     {
//new user in verify
       $response["code"]="1";
 $key= rand(1000,9999);
 //echo "key is".$key;
        $stmt = $this->conn->prepare("insert into verify(userid,code) values(?,?)");
        $stmt->bind_param("ss", $userid,$key);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
		//echo"after verifydbcall";
//	echo"in mail";
	$data = 'please use the following verification code to verify your NYU safety application'.$key;
//echo "userid is".$userid;

Mail::send('email.blank', array('msg' => $data), function($message)  use ($userid) {
    $message->to($userid)->subject('NYU safety verification code');
});
//echo "after mail";
		 return $response;


    }//end of new user if

else{
    $response["code"]="0";
 $key= rand(1000,9999);
 //echo "key is".$key;
        $stmt = $this->conn->prepare("update verify set code=? where userid=?");
        $stmt->bind_param("ss", $key,$userid);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        //echo"after verifydbcall";
//  echo"in mail";
    $data = 'please use the following verification code to reset password for your NYU safety application'.$key;
//echo "userid is".$userid;

Mail::send('email.blank', array('msg' => $data), function($message)  use ($userid) {
    $message->to($userid)->subject('NYU safety verification code');
});
//echo "after mail";
//resembles existing user
         return $response;





}



		}
		
public function mailtouser($userid,$verificationcode)
{
//echo"in mail";
	$to      = $userid.'@nyu.edu';
    $subject = 'NYU safety verification code';
    $message = 'please use the following verification code to verify your NYU safety application'.$verificationcode;
    $headers = 'From: udaykollu@gmail.com' . "\r\n" .
    'Reply-To: udaykollu@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
mail($to, $subject, $message, $headers);
//return true;
}

function sendGoogleCloudMessage( $dataa, $ids )
{
    $response=array();
$data= array( "message" => $dataa );

   // print_r($data);
	$apiKey = 'AIzaSyD-1IP1Y_K1K5n4GksYlBzv6EaqVQ-OwTk';
    $url = 'https://android.googleapis.com/gcm/send';

    $post = array(
                    'registration_ids'  => $ids,
                    'data'              => $data,
                    );

    //------------------------------
    // Set CURL request headers
    // (Authentication and type)
    //------------------------------

    $headers = array( 
                        'Authorization: key=' . $apiKey,
                        'Content-Type: application/json'
                    );
    //------------------------------
    // Initialize curl handle
    //------------------------------

    $ch = curl_init();
//disable local host sll certification verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //------------------------------
    // Set URL to GCM endpoint
    //------------------------------

    curl_setopt( $ch, CURLOPT_URL, $url );

    //------------------------------
    // Set request method to POST
    //------------------------------

    curl_setopt( $ch, CURLOPT_POST, true );

    //------------------------------
    // Set our custom headers
    //------------------------------

    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

    //------------------------------
    // Get the response back as 
    // string instead of printing it
    //------------------------------

    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    //------------------------------
    // Set post data as JSON
    //------------------------------

    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $post ) );

    //------------------------------
    // Actually send the push!
    //------------------------------

    $result = curl_exec( $ch );

    //------------------------------
    // Error? Display it!
    //------------------------------

    if ( curl_errno( $ch ) )
    {
       // echo 'GCM error: ' . curl_error( $ch );
        $response["code"]="0";
		return $response;
    }

    //------------------------------
    // Close curl handle
    //------------------------------

    curl_close( $ch );

    //------------------------------
    // Debug GCM response
    //------------------------------
$response["code"]="1";
return $response;

   // echo $result;
}



function gcmhelpreceived( $dataa, $ids )
{
   // echo "in helpreceived gcm";
$data= array( "message" => $dataa );

   // print_r($data);
    $apiKey = 'AIzaSyD-1IP1Y_K1K5n4GksYlBzv6EaqVQ-OwTk';
    $url = 'https://android.googleapis.com/gcm/send';

    $post = array(
                    'registration_ids'  => $ids,
                    'data'              => $data,
                    );

    //------------------------------
    // Set CURL request headers
    // (Authentication and type)
    //------------------------------

    $headers = array( 
                        'Authorization: key=' . $apiKey,
                        'Content-Type: application/json'
                    );
    //------------------------------
    // Initialize curl handle
    //------------------------------

    $ch = curl_init();
//disable local host sll certification verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //------------------------------
    // Set URL to GCM endpoint
    //------------------------------

    curl_setopt( $ch, CURLOPT_URL, $url );

    //------------------------------
    // Set request method to POST
    //------------------------------

    curl_setopt( $ch, CURLOPT_POST, true );

    //------------------------------
    // Set our custom headers
    //------------------------------

    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

    //------------------------------
    // Get the response back as 
    // string instead of printing it
    //------------------------------

    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    //------------------------------
    // Set post data as JSON
    //------------------------------

    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $post ) );

    //------------------------------
    // Actually send the push!
    //------------------------------

    $result = curl_exec( $ch );

    //------------------------------
    // Error? Display it!
    //------------------------------

    if ( curl_errno( $ch ) )
    {
        echo 'GCM error: ' . curl_error( $ch );
        return "0";
    }

    //------------------------------
    // Close curl handle
    //------------------------------

    curl_close( $ch );

    //------------------------------
    // Debug GCM response
    //------------------------------

return "1";

   // echo $result;
}



}

?>
