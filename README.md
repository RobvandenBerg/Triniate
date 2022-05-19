# Triniate
The open-source repository for Project Triniate, the MMORPG for the Nintendo 3DS Browser. https://triniate.com

# Setting it up

- Requirements: PHP 8.0 or later
- Will probably not work on PHP 7.4, but try anyways.

To run Triniate in your own environment, clone this repository on a Linux-based or Windows-based server that runs PHP and MySQL or MariaDB.
First, you'll have to set up the SQL database. The schematic is provided in mysql_schematic.sql, so all you have to do is import it.
Next, open db_info.php and update it with the correct credentials to connect to your database. Finally, open github_settings.php and change $tiniate_homepage and $triniate_playpage to the correct URL's on your server.
Now you should be ready to go!

# Pull Requests are welcome!
Of course, you can create your own Triniate spinoffs by forking this repository (please link back to https://triniate.com), but you can also contribute to the main Triniate game by opening a pull request that I will go over :-)
