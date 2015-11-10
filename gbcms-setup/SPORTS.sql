/*
- INSERT SPORTS SQL -
Example INSERT statement for Track & Field and Cross Country. Edit fields if desired.

'name' of the current season will appear on the front-page if no default title is set
'name' will also appear in seasons
'short' will be used in the URL for past season pages. Keep it short!

* DELETE the first two lines if you already have sports in the table!
*/
TRUNCATE `sports`;
ALTER TABLE `sports` AUTO_INCREMENT=1;

INSERT INTO `sports` (`name`, `short`) VALUES ('Cross Country', 'TF');
INSERT INTO `sports` (`name`, `short`) VALUES ('Track & Field', 'XC');
