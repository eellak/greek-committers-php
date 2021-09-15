<?php
// 2021_08_17 -

include_once "config.php";
include_once "script.funcs.php";


/* Check if dirs / subdirs exist, if not create them, if errors exit() */
if (!is_dir(USERPATH)) {  // CHECK IF directory exists
    if (!mkdir(USERPATH, 0777)) {
        echo "Error creating " . USERPATH . "\n";
        exit();
    }
    else {
        echo "Directory: " . USERPATH . " created\n";
    }
}
else {
    echo "Directory exists, let's continue\n";
}
if (!is_dir(PATHINFO)) {
    if (!mkdir(PATHINFO, 0777)) {
        echo "Error creating " . PATHINFO . "\n";
        exit();
    }
    else {
        echo "Subirectory: " . PATHINFO . " created\n";
    }
}
else {
    echo "Subdirectory exists too, let's continue\n";
}


/* The following part: read the users.txt file and get the github-infos of each one, 
 * then save it to a json file 
*/

$fpu = fopen("users.txt", "r");
while (($user = fgets($fpu)) !== false) {  // WHILE read file
    $user = str_replace("\n", "", $user);
    runwhenready("rate");
    file_put_contents(PATHINFO . "/{$user}.json", get_json_token("/users/$user"));
} // END OF WHILE read file
fclose($fpu);


echo "\n<br />Done ..,\n";


?>