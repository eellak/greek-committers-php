<?php  // 2021_08_18 - 

include_once "config.php" ;
include_once "script.funcs.php" ;

  $ERRORUSER = "" ;

/* The following part: read the users.txt file and get the github-infos of each one, 
 * then save it to a json file 
*/

// $fpu = fopen("users_SAMPLE.txt", "r"); 
 $fpu = fopen("users.txt", "r"); 
       while (($user = fgets($fpu)) !== false) {  // WHILE read file
          $user = str_replace("\n","", $user) ;

      $mktime = microtime() ;
      $thisyear = date('Y-m-d\TH:i:s'.substr((string)$mktime, 1, 4).'\Z');
      $lastyear = date('Y-m-d\TH:i:s'.substr((string)$mktime, 1, 4).'\Z', strtotime("-1 year"));
      $yearbeforelast = date('Y-m-d\TH:i:s'.substr((string)$mktime, 1, 4).'\Z', strtotime("-2 years"));
         // -1 year to NOW
      runwhenready("graphql") ;  echo ", " ;
      $thejson = get_json_post_token("/graphql", $user, $lastyear, $thisyear) ;
      $djson = json_dec($thejson) ;
/* FIX? */   if(isset($djson["data"]["user"]["contributionsCollection"]["contributionCalendar"]["totalContributions"]))
        $usercontribsly = $djson["data"]["user"]["contributionsCollection"]["contributionCalendar"]["totalContributions"] ;
       if($djson == "JSONERROR") $ERRORUSER .= "{$user}, " ;
        // -2 year to -1 year
      runwhenready("graphql") ;  echo "\t" ;
      $thejson = get_json_post_token("/graphql", $user, $yearbeforelast, $lastyear) ;
      $djson = json_dec($thejson) ; 
/* FIX? */   if(isset($djson["data"]["user"]["contributionsCollection"]["contributionCalendar"]["totalContributions"]))
        $usercontribsybl = $djson["data"]["user"]["contributionsCollection"]["contributionCalendar"]["totalContributions"] ;
       if($djson == "JSONERROR") $ERRORUSER .= "{$user}, " ;
       
       // SUM the 2 year contributions
       if(!isset($usercontribsly)) $usercontribsly = 0;
       if(!isset($usercontribsybl)) $usercontribsybl = 0;
      $usercontribstotal = $usercontribsly + $usercontribsybl; 
      
      echo "User: {$user},\t Contribs: LY: {$usercontribsly}, YBL: {$usercontribsybl}, TOTAL: {$usercontribstotal} \n" ;
     
     $auc[] = array("usercontribs" => $usercontribstotal, "user" => $user) ; 
     


    } // END OF WHILE read file
 fclose($fpu);

    // let's short the users array so the most contributions goes up
   $usercontribs  = array_column($auc, 'usercontribs');
   $user = array_column($auc, 'user');
    array_multisort($usercontribs, SORT_DESC, $user, SORT_ASC, $auc) ;

 
 $fp = fopen("sum_contr-3000.txt", 'w+');
       for($i = 0; $i < RESULTSNUMBER; $i++) {
		   if(isset($auc[$i])) fwrite($fp, "{$auc[$i]["usercontribs"]}, {$auc[$i]["user"]}");
             if($i < (RESULTSNUMBER -1)) fwrite($fp, "\n");
	   }
    fclose($fp);
    
    
    
    
    echo "\n\n\n\n\n" ; 
    
             foreach($auc as $line) {
           echo "{$line["usercontribs"]}, {$line["user"]}\n" ;
	   }
    
echo "\nERRORS: {$ERRORS}\n" ;

echo "--------------------\nUSER ERRORS: {$ERRORUSER}\n" ;

echo "\n<br />Done ..,\n" ;

     
?>