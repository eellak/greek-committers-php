# greek-committers-php

Script to retrieve GitHub committers based on location and prepare the information for importing into WordPress.

Continuation of the repository:

https://github.com/eellak/greek-commiters

Rewritten from scratch in PHP. Also, a lot of debugging code added.

## Instructions

Copy config_sample.php to config.php and edit the values according to your needs.

## GitHub API -good to know- information:

1. Every search query brings up to 100 results per query, and using pages you can retrieve only the 1000 results in
   total (10 pages). It informs you for the total number of results.

2. The location part is tricky because users can write anything in the location field (ex "Mars").
