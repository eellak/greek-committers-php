<?php
// 2021_08_17 -

include_once "config.php";
include_once "script.funcs.php";

/* Check if dirs / subdirs exist, if not create them, if errors exit() */

    echo makethedir(USERDATA, "") ;
    echo makethedir(USERPATH, USERDATA) ;
    echo makethedir(USERINFO, USERDATA.USERPATH) ;

/* The following part: read the users.txt file and get the github-infos of each one, 
 * then save it to a json file 
*/

$fpu = fopen("users.txt", "r");
while (($user = fgets($fpu)) !== false) {  // WHILE read file
    $user = str_replace("\n", "", $user);
    $rwr = runwhenready("rate");
    file_put_contents(PATHINFO . "/{$user}.json", get_json_token("/users/$user"));
    echo "User: {$user} - ResourcesLimit: {$rwr} \n";
} // END OF WHILE read file
fclose($fpu);


echo "\n<br />Done users_infos..,\n";

?>