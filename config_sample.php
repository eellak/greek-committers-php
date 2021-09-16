<?php

   define("TOKEN", "TOKEN_FROM_YOUR_GITHUB_ACOUNT_API") ;
   define("USERDATA", "greek-commiters-3000/") ;
   define("USERPATH", "user/") ;
   define("USERINFO", "info/") ;
   define("USERSFILE", "sum_contr-3000.txt") ;
   define("RESULTSNUMBER", "3100"); // if we want to limit the saved results
   define("API_URL", "https://api.github.com") ;
   define("USERAGENT", "Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:76.0) Gecko/20100101 Firefox/76.0") ; // for curl
   define("TIMEZONE", "Europe/Athens") ;
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
) ;

   date_default_timezone_set(TIMEZONE); // Set proper time zone
   ini_set( 'date.timezone', TIMEZONE );

?>