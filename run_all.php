<?php

/*
    File to run all scripts in one, usefull for crontab automation.
*/



echo "Starting...\n" ;

include_once 'get_users.php' ;

echo "Got users, continuing to users infos..\n" ;

include_once 'get_users_infos.php' ;

echo "Got users infos, continuing to users contributions..\n" ;

include_once 'get_users_contribs.php' ;

echo "Got user contributions\n" ;

echo "Done..," ;




?>