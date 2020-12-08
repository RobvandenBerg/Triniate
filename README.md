# Triniate
The open-source repository for Project Triniate, the MMORPG for the Nintendo 3DS Browser. https://triniate.com

# Setting it up
To run Triniate in your own environment, clone this repository on a Linux-based server that runs PHP and MySQL or MariaDB.
First, you'll have to set up the SQL database. The schematic is provided in mysql_schematic.sql, so all you have to do is import it.
Next, open db_info.php and update it with the correct credentials to connect to your database. Finally, open github_settings.php and change $tiniate_homepage and $triniate_playpage to the correct URL's on your server.
Now you should be ready to go!

# Pull Requests are welcome!
Of course, you can create your own Triniate spinoffs by forking this repository (please link back to https://triniate.com), but you can also contribute to the main Triniate game by opening a pull request that I will go over :-)
