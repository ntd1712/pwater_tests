####################
# Setup
####################

# Download WAMP at https://sourceforge.net/projects/wampserver/files/WampServer 2/Wampserver 2.5/ and install it,
  e.g. D drive
# Copy files bin/*.* to D:/wamp/bin/php/php5.5.12
# Copy files system/*.* to CodeIgniter system folder, e.g. D:/A20P52/www/pwater/system
# To create Bash aliases, copy the file .bash_profile to %userprofile% (e.g. C:/Users/tuan_dung)
# Now, open Git Bash and run the following command: phpunit --version

# Clone Git repository pwater_tests to the webroot directory:
  git clone https://github.com/SpartaTeamDev/pwater_tests
# Go into that folder and run the following command: composer install
# Download Link Shell Extension at http://schinagl.priv.at/nt/hardlinkshellext/linkshellextension.html#download and install it;
  after done, follow the instructions given here http://schinagl.priv.at/nt/hardlinkshellext/linkshellextension.html#usinglinkshellextension
  to create a symbolic link of the tests folder to the project
# To enable PHPUnit support in PHPStorm, read the following manual
  https://www.jetbrains.com/help/phpstorm/2016.2/enabling-phpunit-support.html
# Read more: https://phpunit.de/manual/4.8/en/index.html