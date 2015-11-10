# Setup

0. Install webhost if needed e.g. XAMPP - https://www.apachefriends.org/index.html

1. Copy contents of 'gb-cms-old' to desired directory

2. Create new schema. Edit scripts/functions.php $mysql variables at top to correct database settings.

3. Run 'MYSQL.sql' for that schema. 

4. Create sports. See SPORTS.sql

4. To create a season, run the SQL below with the desired settings or manually add it into `seasons`. 
  Seasons is (id,type,year). 
  Use sport from step 4 
  Date should only include 0-10 characters of letters, numbers, dashes, and single-quotes, and it's up to you how you want to approach this. The year will appear on titles and the season list.
  * If you don't want to use dates, that's fine! Just leave it empty. It's probably best to use years like 2015 OR 2014-15 OR '14-'15

  INSERT INTO `seasons` (`date`, `sport`, `pos`) VALUES ('2015', 1, 0);

5. Go to header.php. Change description & website title. Go to index.php and change $title. 

6. Use /acreate to create an administrator account. Edit acreate.php to desired configuration. 
For example, set $secret to "your_desired_password", then go to acreate?s=your_desired_password and create an administrator account. Then, go back and set $enable = 0. 

7. On line 66 of .htaccess in the main directory, edit the path of 404.html to the correct absolute path.

# Optional

1. Add tracking scripts (google analytics, etc.) to footer.php if desired
2. Change colors/background if desired! Default colors are at img/colorscheme.png. Background is at img/background.png; it repeats at the bottom of the screen.
3. Supress PHP errors if desired - https://stackoverflow.com/questions/332178/best-way-to-suppress-php-errors-on-production-servers
4. Add in a favicon - favicon.ico in main directory is default
