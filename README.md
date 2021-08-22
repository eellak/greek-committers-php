# greek-commiters-php
Script to retreive github commiters based on location and prepare the information for wordpress import

Continuation of the repository:

https://github.com/eellak/greek-commiters

Writen in php and updated on the way how it works. Also a lot of debuging code added.

Rename che config_sampe.php to config.php and edit the values accoring to your needs.


Github API -good to know- information:

Every search query brings up to 100 results per query, and using pages you can retrieve only the 1000 results in total (10 pages). It informs you for the total number of results.

The location part is tricky because users can write the location field with anything (ex "Mars).

