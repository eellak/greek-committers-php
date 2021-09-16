<?php


//  CURL PART !!!!  
function get_json_token($url) {
//  echo "\nASKING FOR: ".API_URL.$url"\n" ;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, API_URL . $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_USERAGENT, USERAGENT);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: token " . TOKEN));  // ".TOKEN
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3);

// DEBUGING cURL communication
    /*
          curl_setopt($curl, CURLOPT_VERBOSE, true);
           $out = fopen('php://output', 'w+');
          curl_setopt($curl, CURLOPT_STDERR, $out);
    */

    $content = curl_exec($curl);
// DEBUGING    echo $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return $content;
}

/* function to get the json from graphql API, with parameters of API endpoint, user, from dat, and to date  */
function get_json_post_token($url, $user, $from, $to) {
//  echo "\nASKING FOR: ".API_URL.$url"\n" ;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, API_URL . $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_USERAGENT,USERAGENT);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: token " . TOKEN));  // ".TOKEN
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,
        '{"query":"query {\n  user(login: \"' . $user . '\") {\n    name\n contributionsCollection(from: \"' . $from . '\", to: \"' . $to . '\") {\n      contributionCalendar {\n colors\n        totalContributions\n    }\n    }\n  }\n}"}'
    );

    $content = curl_exec($curl);

// DEBUGING cURL communication
    /*
          curl_setopt($curl, CURLOPT_VERBOSE, true);
           $out = fopen('php://output', 'w+');
          curl_setopt($curl, CURLOPT_STDERR, $out);

       $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
       echo " CCOONNTTEENNTT: {$content} - HTTP_STATUS - {$http_status}" ;
    */

    curl_close($curl);
    return $content;
}


/* Function to decode AND handle json decoding / error messages */
function json_dec($thejson) {
    global $ERRORS;
    $djson = json_decode($thejson, JSON_THROW_ON_ERROR);
    $jsonerror = json_last_error_msg();
    if ($jsonerror != "No error") {
        echo "JSON ERROR: {$jsonerror} \nJSON: \n {$thejson} \n\nDJSON: \n " . print_r($djson) . " \n\n";
        $ERRORS .= "JSON ERROR: {$jsonerror} \nJSON: \n {$thejson} \n\n";
        return "JSONERROR";
    } else {
        return $djson;
    }
}


/* Function to check if there is remaining usage for the given resource's api */
function runwhenready($resource) {
    $z = 0;
    $time = 3;
    global $limitgraphql;
    global $limitrate;
    global $limitsearch;
    
    if ($resource == "graphql") {
        $time = 60;

         if(isset($limitgraphql) && $limitgraphql-- > 1) { 
			 return $resource."-".$limitgraphql;
		 }

	 } // END OF IF resource IS graphql
     
         if($resource == "search" && isset($limitsearch) && $limitsearch-- > 1) { 
			 return $resource."-".$limitsearch;
	     } // END OF IF resource IS search
	     
    if ($resource == "rate") {
		if($limitrate-- < 1) {
          $time = 60; // rate limit resets after 1 hour, so asking every 1 min is ok
          $djson["rate"]["remaining"] = 0;
          while ($djson["rate"]["remaining"] < 1) {
              $thejson = get_json_token("/rate_limit");
              $djson = json_dec($thejson);
              if ($djson["rate"]["remaining"] === 0) { sleep($time); }
            $z++;
            $limitrate = $djson["rate"]["remaining"] ;
        }  // END OF WHILE
	   } // end of if low limit rate check
        return $resource."-".$limitrate;
    }
    else { // ELSE
        $djson["resources"][$resource]["remaining"] = 0;  // avoid notices
        while ($djson["resources"][$resource]["remaining"] < 1) {
            $thejson = get_json_token("/rate_limit");
            $djson = json_dec($thejson);
            if ($djson["resources"][$resource]["remaining"] === 0) { sleep($time); }
            $z++;
            if($resource =="search") $limitsearch = $djson["resources"][$resource]["remaining"] ;
            if($resource =="graphql") $limitgraphql = $djson["resources"][$resource]["remaining"] ;
        }  // END OF WHILE
         return $resource."-". $djson["resources"][$resource]["remaining"];
    } // END OF ELSE

}

/* function to create directory in specific path and make some checks first / exit if can't create dir */
   function makethedir($dirname,$inpath) {
	   if(!is_dir($inpath.$dirname)) {  // CHECK IF directory exists
	if(!mkdir($inpath.$dirname,0777)) { 
		   return "Error creating {$inpath}{$dirname}\n" ; 
		   exit() ; 
		} else { return "Directory: {$inpath}{$dirname} created\n" ; }
	} else { return "Directory exists, let's continue\n" ; }
}	
	

?>