<?php

//  ob_implicit_flush(true);
//  ob_start(); 

set_time_limit(300);

include_once "script.funcs.php";
include_once "config.php";

$gjt = 0; // let's count the queries ...

echo " Let's Start! \n";
$user_list = array();

//DEBUG - $perioxArray = array("Athens+-location:GA+-location:Georgia+-location:OH+-location:ohio");

foreach ($perioxArray as $perioxh) {
    echo "Current value of location: $perioxh:\n<br />";

    $rwr = runwhenready("search"); // Do we have the resources to make the search request ?
    $thejson = get_json_token("/search/users?q=location:{$perioxh}&page=&per_page=1");
    // DEBUG - DELETE_ME             $gjt++ ;

    $djson = json_dec($thejson);

    if (isset($djson["total_count"])) {  // if desirable answer from api...
        $total_count = $djson["total_count"];
    }
    else { // maybe we reached github's API limits, maybe something else ?
        //  DEBUG - DELETE_ME	 echo get_json_token("/rate_limit") ;
        echo "\n\n\n{$thejson}\n\n -- WAAAAT ? ";
        exit();
    }


    echo "\nTotal users: <strong>{$total_count}</strong>\n- ResourcesLimit: $rwr   \n<br /><br />";

    $p = 1; // reset the number of page to 1

    while ($total_count > 0) { // for as long as results to retrieve left
        // per_page = 100 -> this was the maximum results github's api returns
        $rwr = runwhenready("search"); // Do we have the resources to make the search request ?
        $thejson = get_json_token("/search/users?q=location:{$perioxh}&page={$p}&per_page=100");

        // DEBUG - DELETE_ME            $gjt++ ;
        $djson = json_dec($thejson);

        $p++;  // Next page please ;)
        $total_count = $total_count - 100;
        if ($p == 10) {
            $total_count = 0;
        } // unfortunately github's api can't return more than the first 1000 results :(, so we will exit the loop and continue

        if (isset($djson["items"]) && (is_array($djson["items"]) || $djson["items"] instanceof Traversable)) {
            foreach ($djson["items"] as $item) {
                if (!in_array($item["login"], $user_list)) {  // check if user is allready in array

                    $user_list[] = $item["login"];

                } // END OF IF check if user exists in array
            }  // END OF FOREACH - item
        } // END OF IF check if array

       echo " - Location: {$perioxh} - Page: {$p} - Total_left: {$total_count} - ResourcesLimit: $rwr \n";
    }  // END OF WHILE total_count
}  // END OF FOREACH - perioxh


echo "<hr /><hr />\n\n\n";

echo "USERS: " . count($user_list);
echo "<hr />";

//  DEBUG  echo "Queries: " . $gjt;
echo "<hr />";

$fp = fopen("users.txt", 'w+');
foreach ($user_list as $user) {
    fwrite($fp, "{$user}\n");
}
fclose($fp);


echo "Done get_users..,<hr />";


?>