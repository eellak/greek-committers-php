<?php

// CREATE USER DIR 
function create_dir($user) {
    $userdir = PATHINFO . $user;

    if (!file_exists($userdir) && !is_dir($userdir)) {
        mkdir($userdir, 0777);
        return "The directory $userdir was successfully created.";
    }
    else {
        return "The directory $userdir exists.";
    }
} // END OF FUNC CREATE_DIR


//  CURL PART !!!!  

function get_json($url) {
    $base = "https://api.github.com";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $base . $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(
        $curl,
        CURLOPT_USERAGENT,
        "Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:76.0) Gecko/20100101 Firefox/76.0"
    );
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
    $content = curl_exec($curl);
    // DEBUGING echo $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return $content;
}


function get_json_token($url) {
    $base = "https://api.github.com";

//  echo "\nASKING FOR: {$base}.{$url}\n" ;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $base . $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(
        $curl,
        CURLOPT_USERAGENT,
        "Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:76.0) Gecko/20100101 Firefox/76.0"
    );
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


function get_json_post_token($url, $user, $from, $to) {
    $base = "https://api.github.com";

//  echo "\nASKING FOR: {$base}.{$url}\n" ;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $base . $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(
        $curl,
        CURLOPT_USERAGENT,
        "Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:76.0) Gecko/20100101 Firefox/76.0"
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: token " . TOKEN));  // ".TOKEN
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt(
        $curl,
        CURLOPT_POSTFIELDS,
        '{"query":"query {\n  user(login: \"' . $user . '\") {\n    name\n contributionsCollection(from: \"' . $from . '\", to: \"' . $to . '\") {\n      contributionCalendar {\n colors\n        totalContributions\n    }\n    }\n  }\n}"}'
    );

    $content = curl_exec($curl);

// DEBUGING cURL communication
    /*
          curl_setopt($curl, CURLOPT_VERBOSE, true);
           $out = fopen('php://output', 'w+');
          curl_setopt($curl, CURLOPT_STDERR, $out);

       $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
       echo " AAAAAAAAAAAAAAAAAA: {$content} - BBB - {$http_status}" ;
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
    }
    else {
        return $djson;
    }
}


/* Function to check if there is remaining usage for the given resource's api */
function runwhenready($resource) {
    $z = 0;
    $time = 3;
    if ($resource == "graphql") {
        $time = 60;
    }
    if ($resource == "rate") {
        $time = 60; // rate limit resets after 1 hour, so asking every 1 min is ok
        $djson["rate"]["remaining"] = 0;
        while ($djson["rate"]["remaining"] < 1) {
            $thejson = get_json_token("/rate_limit");
            $djson = json_dec($thejson);
            if ($djson["rate"]["remaining"] === 0) {
                sleep($time);
            }
            $z++;
        }  // END OF WHILE
        echo "Resources Remaining: " . $djson["rate"]["remaining"];
    }
    else { // ELSE

        $djson["resources"][$resource]["remaining"] = 0;  // avoid notices
        while ($djson["resources"][$resource]["remaining"] < 1) {
            $thejson = get_json_token("/rate_limit");
            $djson = json_dec($thejson);
            if ($djson["resources"][$resource]["remaining"] === 0) {
                sleep($time);
            }
            $z++;
        }  // END OF WHILE
        echo "Resources Remaining: " . $djson["resources"][$resource]["remaining"];
    } // END OF ELSE

    return ($z * 3);
}


function output() {
    /*
        // echo $str;
        ob_end_flush();
        ob_flush();
        flush();
        ob_start();
    */
}


?>