# create databases
CREATE DATABASE IF NOT EXISTS `symfony_dev`;

# grant rights
GRANT ALL ON symfony_dev.* TO 'symfony'@'%';
