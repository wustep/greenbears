# Setup

0. Install webhost if necessary. e.g. XAMPP - https://www.apachefriends.org/index.html

1. Copy contents of 'gb-cms-old' to desired directory

2. Create new schema. Edit scripts/functions.php $mysql variables at top to correct database settings.

3. Run 'MYSQL.sql' at that schema.

4. To create a season, run the SQL below with the desired settings or manually add it into `seasons`. 
  Seasons is (id,type,year), Pages is (page,text,editor,edited). 
  Change `seasons` type & year. 0 is cross-country, 1 is track & field. 
  Change `pages` page = 'xcYEAR' or 'tfYEAR', e.g. 'xc2015', 'tf2013', etc.

  INSERT INTO `seasons` VALUES (1,0,2015);
  INSERT INTO `pages` VALUES ('xc2015','',0,0);

5. Go to header.php. Change description & website title. Go to index.php and change $title. 

6. Use /acreate to create an administrator account. Edit acreate.php to desired configuration. 
For example, set $secret to "your_desired_password", then go to acreate?s=your_desired_password and create an administrator account. Then, go back and set $enable = 0. 

7. On line 66 of .htaccess in the main directory, edit the path of 404.html to the correct absolute path.

# Optional

1. Add tracking scripts (google analytics, etc.) to footer.php if desired
2.. Change colors/background if desired! Default colors are at img/colorscheme.png. Background is at img/background.png; it repeats at the bottom of the screen.
3.. Supress PHP errors if desired - https://stackoverflow.com/questions/332178/best-way-to-suppress-php-errors-on-production-servers
4. Add in a favicon - favicon.ico in main directory is default
