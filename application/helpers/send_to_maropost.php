<?php

class Send_to_Maropost {



 public static function request($url_api, $auth_token, $action, $endpoint, $dataArray) {
		$url = $url_api . $endpoint . ".json"; 
	  	$ch = curl_init();

	  	$dataArray['auth_token'] = $auth_token;
	  	$json = json_encode($dataArray);
         
	    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
	    curl_setopt($ch, CURLOPT_URL, $url);

	    switch($action){
	            case "POST":
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	            break;
	        case "GET":
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	            break;
	        case "PUT":
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	            break;
	        case "DELETE":
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	            break;
	        default:
	            break;
	    }
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json','Accept: application/json'));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    $decoded = json_decode($output);
	    return $decoded;
}

}
/*

$postdata = file_get_contents("php://input");
$request  = json_decode($postdata, true);
 
header('Content-type: application/json');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');
 
var_dump($request); 


$results = 0; 
if(!empty($request)):

	// Compute results based on numerical values
	foreach($request as $k=>$val):
		if(is_numeric($val)):
			$results += intval($val);
		endif;
	endforeach;

	$to_post = array();

	// Compute % (58pts = 100%)
	$to_post['custom_field']['cheating_results'] = number_format($results*100/78,0);

	// Main Result
	if($to_post['custom_field']['cheating_results']>45):
		$to_post['custom_field']['partner_cheating'] = 'Yes';
	else:
		$to_post['custom_field']['partner_cheating'] = 'No';
	endif;
	 
	$to_post['last_name']  = filter_var($request['lastName'], FILTER_SANITIZE_STRING);
	$to_post['first_name'] = filter_var($request['firstName'], FILTER_SANITIZE_STRING);

	$to_post['email'] 				= filter_var($request['email'], FILTER_SANITIZE_EMAIL);
	$to_post['custom_field']['sex'] = filter_var($request['gender'], FILTER_SANITIZE_STRING);

	if(!empty($request['dob'])):
		$to_post['custom_field']['birthday'] = date_format(date_create($request['dob']),"Y-m-d");
	endif;

	if(!empty($request['partnerFullName'])):
		$tmp = explode(' ',$request['partnerFullName']);
		$to_post['custom_field']['partner_first_name'] = filter_var($tmp[0], FILTER_SANITIZE_STRING);
		if(!empty($tmp[1])):
			$to_post['custom_field']['partner_last_name'] = filter_var($tmp[1], FILTER_SANITIZE_STRING);
		else:
			$to_post['custom_field']['partner_last_name'] = '?';
		endif;
		unset($tmp);
	endif;

	$to_post['custom_field']['partner_gender'] = filter_var($request['partnerGender'], FILTER_SANITIZE_STRING);
	if(!empty($request['partnerDob'])):
		$to_post['custom_field']['partner_birthday'] = date_format(date_create($request['partnerDob']),"Y-m-d");
	endif;

	if(!empty($request['status'])):
		$to_post['custom_field']['relationship_status'] = filter_var($request['status'], FILTER_SANITIZE_STRING);
	endif;

	if(!empty($request['whish'])):
		$to_post['custom_field']['greatest_wish'] = filter_var($request['whish'], FILTER_SANITIZE_STRING);
	endif;
  
	// Add new contact to Maropost: 
	$newcontact = request($url_api, $auth_token,'POST',$list,$to_post);
	// var_dump($newcontact);
 	
 	echo json_encode($newcontact);

 	// The answer has to have an id (id: 742346448)
 	// otherwiser, an error happened

 	// ex error email
 	// {email: Array(1) 0: "address is marked as spam trap" 
endif;
*/
?>