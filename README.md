# greek-committers-php

Script to retrieve GitHub committers based on location and prepare the information for importing into WordPress.

Continuation of the repository:

https://github.com/eellak/greek-commiters

Rewritten from scratch in PHP. Also, a lot of debugging code added.

## GitHub API -good to know- information:

1. Every search query brings up to 100 results per query, and using pages you can retrieve only the 1000 results in
   total (10 pages). It informs you for the total number of results.

2. The location part is tricky because users can write anything in the location field (ex "Mars").

## Requirements

- minimum PHP version: 7.3
- PHP running in command line (mostly because of long execution time produced timeout)
- write permissions (create and delete files and directories) in the running directory.

# Instructions

## Configuration

Rename the file config_sample.php to config.php . Edit it to meet your needs:

- **TOKEN**: add your github token for API calls
- **USERDATA, USERPATH, USERINFO, PATHINFO**: the (path) directory in which the json files with information for users will be stored. Directories will be automaticaly if they do not exist (check directory permissions)
- **RESULTSNUMBER**: limit the saved results. It keeps the profiles with most contributions first
- **API_URL**: the API url of github
- **USERAGENT**: in some cases the user agent in curl calls needs to be defined
- **TIMEZONE**: when we get the range of last year and the year before last. If it isn't defined in php configuration a notice/ warning might appear
- **$perioxArray**: the array of the areas we are intrested in



## How to run

The code is optimized to run from command line (at first I tried from web browser but because of the time it takes to run (in total more than 1 hour) I was geting timeouts and I couldn't see right away the output).

1. **php -f get_users.php**: This must be the first script you run. It finds the users from the areas in the array $perioxArray (maximum 1000 per area). using the syntax ex "Athens+-location:GA" you can exclude some results.
2. **php -f get_users_infos.php**: It creates the directories needed (if they don't exist) and saves there the json files with the information of each user.
3. **php -f get_users_contribs.php**: This script uses the graphql API of github to find how many contributions each user made the last year and the the year before last (we wanted last two years). API limits the range in maximum 1 year for queries, thats why we made two calls.

