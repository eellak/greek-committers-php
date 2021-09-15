<?php

//  ob_implicit_flush(true);
//  ob_start(); 

set_time_limit(300);

ini_set('date.timezone', 'Europe/Athens');
//execution timer
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
$aaz = 0;
$poshoraa = "";

function poshora($start) {
    $time = microtime();
    $time = explode(' ', $time);
    return round((($time[1] + $time[0]) - $start), 4);
}

include_once "script.funcs.php";
include_once "config.php";

$gjt = 0; // let's count the queries ...


echo " Let's Start! \n";
$user_list = array();


$fpu = fopen("users_extended.txt", 'w+');

$perioxArray = array(
    "Athens+-location:GA+-location:Georgia+-location:OH+-location:ohio",
    "Thessaloniki",
    "Patra",
    "Irakleio",
    "Larissa",
    "Patras",
    "Volos",
    "Heraklion",
    "Rhodes",
    "Rodos",
    "Ioannina",
    "Chania",
    "Chalkis",
    "Chalkida",
    "Agrinio",
    "Katerini",
    "Trikala",
    "Serres",
    "Lamia",
    "Alexandroupoli",
    "Kozani",
    "Kavala",
    "Veria",
    "Athina",
    "Hellas",
    "Ellada",
    "Athens,&nbsp;Greece"
);

$perioxArray = array("Athens+-location:GA+-location:Georgia+-location:OH+-location:ohio");

foreach ($perioxArray as $perioxh) {
    echo "Current value of location: $perioxh:\n<br />";
    output();
    runwhenready("search"); // Do we have the resources to make the search request ?
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


    echo "Total users: <strong>{$djson[total_count]}</strong>\n<br /><br />";

    $p = 1; // reset the number of page to 1

    // DEBUG - DELETE_ME  $zze = 0; // reset location user for extended log

    while ($total_count > 0) { // for as long as results to retrieve left
        // per_page = 100 -> this was the maximum results github's api returns
        $rwr = runwhenready("search"); // Do we have the resources to make the search request ?
        $thejson = get_json_token("/search/users?q=location:{$perioxh}&page={$p}&per_page=100");

        // DEBUG - DELETE_ME       if($p > 10) echo "\n\n\n\n-------------------------------\n{$thejson}\n-------------------------------\n\n\n\n\n" ;
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
                    // the following line - DEBUG DELETE_IT
                    fwrite($fpu, "{$item["login"]},{$perioxh}," . ++$zza . "," . ++$zze . "," . $p . "\n");
                    // DEBUG - DELETE_ME !!    echo "{$zza}: {$item["login"]} \t\t\t\t\t |||" ;
                } // END OF IF check if user exists in array
            }  // END OF FOREACH - item
        } // END OF IF check if array

        $poshoraa .= $aaz++ . ": " . poshora(
                $start
            ) . " - Location: {$perioxh} - Page: {$p} - Total_left: {$total_count} - ResourcesLimit: $rwr \n";


        echo $aaz++ . ": " . poshora(
                $start
            ) . " - Location: {$perioxh} - Page: {$p} - Total_left: {$total_count} - ResourcesLimit: $rwr \n";
    }  // END OF WHILE total_count
}  // END OF FOREACH - perioxh


fclose($fpu);  // close users file


echo "<hr /><hr />\n\n\n";
echo "Excecution time: " . poshora($start) . " seconds";
echo "<hr />";

echo "USERS: " . count($user_list);
echo "<hr />";

echo "Queries: " . $gjt;
echo "<hr />";

output();

$fp = fopen("users.txt", 'w+');
foreach ($user_list as $user) {
    fwrite($fp, "{$user}\n");
}
fclose($fp);


echo "Done ..,<hr />";

echo "Stats: \n<br />\n{$poshoraa}";

output();
?>