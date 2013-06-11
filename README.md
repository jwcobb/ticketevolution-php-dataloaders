# DataLoaders for caching catalog data from the Ticket Evolution API

## RELEASE INFORMATION
See [CHANGELOG.md](https://github.com/TeamOneTickets/ticket-evolution-dataloaders/blob/master/CHANGELOG.md)


## SYSTEM REQUIREMENTS
The Ticket Evolution PHP Library **requires PHP 5.3.3 or later** and Zend Framework 1.


## INSTALLATION
1. Download and extract to the folder where you want it.

2. Copy `/application/config.sample.php` to `/application/config.php` and edit to fill in your own credentials for each environment.

3. If you do not yet have Composer installed, you can follow these [directions for installing Composer](http://getcomposer.org/doc/00-intro.md#installation-nix).

4. Once you have Composer installed simply `cd` to your directory for this project and execute `composer install`

5. Run the SQL in `/scripts` starting with `create_tables.mysql` and then running any additional scripts in chronological order by the date in the filename.


## LICENSE
The files in this archive are released under the [BSD 3-Clause License](http://opensource.org/licenses/BSD-3-Clause). You can find a copy of this license in [LICENSE.txt](https://github.com/TeamOneTickets/ticket-evolution-dataloaders/blob/master/LICENSE.txt).
