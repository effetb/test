# create databases
CREATE DATABASE IF NOT EXISTS `symfony_test`;
CREATE DATABASE IF NOT EXISTS `symfony_prod`;
CREATE DATABASE IF NOT EXISTS `symfony_dev`;

# grant rights
GRANT ALL ON symfony_test.* TO 'symfony'@'%';
GRANT ALL ON symfony_prod.* TO 'symfony'@'%';
GRANT ALL ON symfony_dev.* TO 'symfony'@'%';
