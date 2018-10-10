# README #

This README would normally document whatever steps are necessary to get your application up and running.

# Instructions to run the project on your local server

1. Install libraries via Composer (install Composer if needed):
 `php composer.phar update`

2. Generate Data Base ("vmb1") with Doctrine console tools:

	First:
	- On PC, in the app/config/parameters.yml file :
	set "database_password" to ""
	- On MAC, in the app/config/parameters.yml file :
	set "database_password" to "root"

	Then:
	- Create the DB using:
	`doctrine:database:create`
	- Generate SQL commands to fill in the DB using:
	`doctrine:schema:update --dump-sql`
	- Execute generated SQL commands using:
	`doctrine:schema:update --force`

3. By default, topics don't exist. They are added in the data base by the administrators. Since you will need to indicate them when uploading resources, creating matrixes, etc., you first need to create topics by becoming an admin.
To do so, sign in, then change your ROLE (table "User", column "roles") from s:12:"ROLE_STUDENT" to s:10:"ROLE_ADMIN".

4. To use the Symfony console, you need to set the timezone on the server's php.ini.

5. Finally, to see the website, start your XAMP/MAMP server and visit localhost/amb-project/web/ (change acording to your own path)!  


# Deploying on the school's server

To deploy AMB, you need to connect to the server while being on the school's network in the Computer Science Department. You will also need to ask to create an account on the server.

In the next sections, connect to the server with ssh:
`ssh <your-account-id>@edu3d-vm.enstb.org`

In most of the commands you execute, you will need to have the role of root. (Either add `sudo` in front of the command or upgrade to root with `sudo -i`

## 1. Backup

Before starting the installation of the new version, always make a backup.

1.1 Find the path where the project is running and move it to another directory.
`cp -r /path/to/the/project /the/backup/directory`
It should be inside /var/www and the project should be called `edu`. You can backup /var/www.
Ex: Backup /var/www/ to /var/www-old: `cp -r /var/www /var/www-old`

1.2 Create a dump of the database. To do so, you will need three parameters (database name, username and password) that you can find in the file app/config/parameters.yml.
First, create the file you will backup the database into:
`touch <filename>.sql`
`chmod 666 <filename>.sql`
Then, backup:
`mysqldump -u <username> -p <database name> > /path/to/your/backup/file.sql`

## 2. New version import

2.1 From your laptop, pull the latest version of the directory. You don't need the var and vendor directories since they will be automatically recreated. Compress the directory into a tar file.

2.2 Send the tar file to the server.
`scp /path/to/archive.tar <your-account-id>@edu3d-vm.enstb.org:/path/to/project/root`

2.3 Connect again onto the server then uncompress your tar file in the project root.
Important! Place all your file into the `edu` directory (it's where the server points to).
`tar -xvzf /path/to/archive.tgz /path/to/the/project/root/edu`
Ex (inside /var/www): `tar -xvzf ./archive.tgz ./edu`

2.4 Replace the content of the /web/upload directory by the one from the old directory.
`cp -fR /path/to/old/repo/web/upload/* /path/to/project/root/edu/web/upload`

## 3. Installation

Great! Now, onto installation.

3.1 You will need the Symfony console to execute the following commands. Go to the project root (should be /var/www/edu).
First, verify everything works as expected by doing: `php app/console` It should list all the possible commands and options.

3.2 Install the dependencies:
`php composer.phar install --no-dev` or `composer install --no-dev`

3.3 Update the data scheme with Doctrine:

After taking a look at the changes in the schema:
`php app/console doctrine:schema:update --dump-sql`

If it does look correct, force the update by doing:
`php app/console doctrine:schema:update --force`

3.4 Clean the cache in production.
`php app/console cache:clear --env=prod`

3.5 Give back the right rights to the directories:
```
chown -R www-data:www-data app/cache/
chown -R www-data:www-data app/logs/
chown -R www-data:www-data app/config/parameters.yml
chown -R www-data:www-data app/Resources/translations/
chown -R www-data:www-data web/upload/
```

and

```
chmod -R 775 app/cache
chmod -R 775 app/logs
chmod -R 775 app/config/parameters.yml
chmod -R 775 app/Resources/translations/
chmod -R 775 web/upload/
```

It should be up now!

If it does not work, try cleaning the cache (prod one) or take a look at the logs for more information!
