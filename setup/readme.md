####################
# Setup
####################

# Download WAMP at https://sourceforge.net/projects/wampserver/files/WampServer 2/Wampserver 2.5/ and install it,
  e.g. D drive
# Copy files bin/*.* to D:/wamp/bin/php/php5.5.12
# Copy files system/*.* to CodeIgniter [v2.1.3 only] system folder, e.g. D:/A20P52/www/pwater/system
# To create Bash aliases, copy the file .bash_profile to %userprofile% (e.g. C:/Users/tuan_dung)
# Now, open Git Bash and run the following command: phpunit --version

# Clone Git repository pwater_tests to the webroot directory:
  git clone https://github.com/SpartaTeamDev/pwater_tests
# Go into that folder and run the following command: composer install
# Download Link Shell Extension at http://schinagl.priv.at/nt/hardlinkshellext/linkshellextension.html#download and install it;
  after done, follow the instructions given here http://schinagl.priv.at/nt/hardlinkshellext/linkshellextension.html#usinglinkshellextension
  to create a symbolic link of the pwater_tests folder to the project
  (we may keep the original name or rename it to "tests" as shown in screenshot).

# Read more: https://phpunit.de/manual/4.8/en/index.html

####################
# PHPUnit Support in PhpStorm 2016
####################

# Follow the steps here: https://www.jetbrains.com/help/phpstorm/2016.2/enabling-phpunit-support.html
# Screenshot: phpstorm_settings.png

####################
# PHPUnit Support in Eclipse Luna SR2 (4.4.2) with MakeGood 3.1.1
####################

# Update Eclipse IDE to SR2 (4.4.2) at Help > Check for Updates.
# Install MakeGood from Help > Eclipse Marketplace.
# Follow the steps here: http://maruhgar.blogspot.com/2014/06/setting-up-and-running-php-unit-tests_26.html
# Screenshot: eclipse_settings.png

####################
# PHPUnit Support from command line
####################

# http://maruhgar.blogspot.com/2014/06/setting-up-and-running-php-unit-tests.html