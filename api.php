<?php

include("db.php");
$method = $_SERVER['REQUEST_METHOD'];
mysqli_set_charset($conn,"utf8");

function registerPushNotifications($conn, $user, $device_id) {
    $query = mysqli_query($conn, "SELECT * FROM usuarios WHERE deviceid='".$device_id ."' and username='".$user ."' ");
	if (mysqli_num_rows($query) > 0){	
	    return array("success" => 1);
	} else {	    
	    $sql = "DELETE FROM usuarios where username='". $user ."'; INSERT into registros_push (deviceid, username) values ('". $device_id . "','". $user . "')"; 
	    if (!mysqli_multi_query($conn, $sql)) {
	    	return array("success" => 0, "Falla Multiquery");
	    }
	}
	return array("success" => 1);
}

function sendPushNotifications($conn,$username,$nombre, $apiKey, $appsecret, $message) {
	//$idsSelect = "select username from usuarios where idUsuario in (select idAsociado from relacionemergencia where idUsuario=(select idUsuario from usuarios where username='".$username."'))";   
    $sql = "select * from usuarios WHERE username='".$username."'"; 

	$result = mysqli_query($conn, $sql);

	$device_ids = array();
	if (mysqli_num_rows($result) > 0) {
	    while($row = mysqli_fetch_assoc($result)) {
	    	array_push($device_ids, $row["deviceid"]);
	    }
	} else {
	    echo "0 results";
	}	
    $device_ids_j = json_encode($device_ids);
    
    if (count($device_ids) > 0) {

		$data_string ='{"message": { "alert": "Le informamos que '. $message .'" }, "target" : {"deviceIds" :' . $device_ids_j . ' } }';
			
		$ch = curl_init('https://mobile.ng.bluemix.net/imfpush/v1/apps/' . $apiKey .'/messages');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		    'Content-Type: application/json',                                                                                
		    'Content-Length: ' . strlen($data_string),
		    'Accept: application/json',
		    'appSecret:'. $appsecret, 
		    'Accept-Language: en-US'      )                                                          
		);               
	
		$text1 = curl_exec($ch);
		if (FALSE === $text1) {
	       print curl_error($ch);
	       print curl_errno($ch);
	   }
	   var_dump( $text1);
	   curl_close($ch);	
	}	 
}

switch ($method) {
  case 'POST':
	  if (isset($_POST["procedure"])) {
	  	$procedure = $_POST["procedure"];	  	
	  	switch($procedure){
	  		case '1':
	  			$user = $_POST["username"];
	  			$device_id = $_POST["deviceid"];
	  			registerPushNotifications($conn,$user,$device_id);
	  			break;
	  		case '2':
				$apikey = $_POST["apikey"];
				$appsecret = $_POST["appsecret"];
				$message = $_POST["message"];
				$username = $_POST["username"];
				$nombre = $_POST["nombre"];
    			sendPushNotifications($conn,$username,$nombre, $apikey, $appsecret, $message);
    			break;
	  	}
	  }
    break;
  case 'PUT':
  	echo "Method not allowed";
    break;
  case 'GET':  	
  	echo "Method not allowed";
  	break;
  case 'DELETE':
  	echo "Method not allowed";
    break;
}

?>