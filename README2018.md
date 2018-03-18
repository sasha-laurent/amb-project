# AMB PROJECT


## About the project

This is a 2018 update on the amb-project state.
AMB is now built and running on the Symfony 2.8 version.

This README2018 will give instructions (again) on how to execute and take on the project. It will then give informations about the AMB platform. It will also describe the project's structure (files of the project) as well as describe briefly every bundle.


## SUMMARY

1. Instructions to run the project
2. About the AMB platform
    2.1 Generalities  
    2.2 Architecture  
    2.3 Roles and Functionalities/Accesses  
    2.4 Features  
3. The repository's Structure  
4. Bundles  
    4.1 PresentationBundle  
    4.2 ResourceBundle  
    4.3 QuizBundle  
    4.4 UserBundle  
    4.5 ForumBundle  
    4.6 SearchBundle  
    4.7 ContextualHelpBundle    
5. Deployment and automatic updates  

## 1. Instructions to run the project

1. Download the project from a source (git, zip,...).

2. Install libraries via Composer (install Composer if needed):
	php composer.phar update

3. Generate Data Base ("vmb1") with Doctrine console tools:
	
	First:
	- On PC, in the app/config/parameters.yml file :
	set "database_password" to ""
	- On MAC, in the app/config/parameters.yml file :
	set "database_password" to "root"

	Then:
	- Create the DB using:
	doctrine:database:create 
	- Generate SQL commands to fill in the DB using:
	doctrine:schema:update --dump-sql
	- Execute generated SQL commands using:
	doctrine:schema:update --force

4. By default, topics don't exist. They are added in the data base by the administrators. Since you will need to indicate them when uploading resources, creating matrixes, etc., you first need to create topics by becoming an admin. 
To do so, sign in, then change your ROLE (table "User", column "roles") from s:12:"ROLE_STUDENT" to s:10:"ROLE_ADMIN".

5. To use the Symfony console, you need to set the timezone on the server's php.ini.

6. Finally, to see the website, start your XAMP/MAMP server and visit localhost/amb-project/web/ (change acording to your own path) !  


## 2. About the AMB Platform

### 2.1 Generalities

The AMB (Adaptive Mediation Builder) is a platform with a educative and cultural mediation purpose. Its aim is to give mediators tools to create unlimitedly personalizable presentations to suit their public, the style they want to convey, their intent,...
The platform is made for mediators (professors, museum curators,...) who would like to create content (MOOCs, lectures, museum visits, communications operations, etc...) for a specific public (general public, students, visitors,...).

The AMB enables its author to creator multiple presentations with different objectives by relying on a key element of the platform: a matrix. The matrix, as an "organised data base", lets the mediator store his/her resources (pictures, videos,...) depending on view points (= different aspects of the content's subject) and levels (= content difficulty), creating a table with rows as view points and columns as levels. Thus, When creating his/her presentation, a mediator will only have to "pick" the relevant resources from the right cells to adjust it to its aimed public and to develop particularly some points.

The AMB was developed in IMT Atlantique's Computer Science department (formerly Telecom Bretagne) and has been the subject of several projects and thesis since then. 


### 2.2 Architecture

The project is based on Symfony2, a "MVC" framework. If you are not an expert of the framework, here are a few indications to keep in mind:

The source code (/src) is separed in bundles. Each bundle, called '<Name>Bundle' is created so that it implements one part/aspect of the application and can be reused independently from other bundles. Ex: A quiz bundle, a forum bundle, etc.
Each bundle has a specific structure:
- Controllers: They constitute the logic of the bundle and make links between the views/templates, the entities, the forms, etc by retrieving data from entities and calling views with those data. They are the coordinators of the bundle.
- Views: (html.twig): They create the user interface of the application. Being twig files (/html), they can easily display data given from the controller files.
- Entities: They are objects that form the Model part of the architecture. They are simple objects with attributes and methods whose attributes are fields in a database table.
- Forms: They fill in entities. They are often used as forms so that the program can receive the user's input data, pass it to entities which automatically generate database queries.
- Routing file: It indicates to the kernel which controller and view template to call according to the url the user is browsing.

The app contains the main routing file, the console which lets you use useful commands to automate some manipulations, the configuration file, the translations files (messages.en.yml and messages.fr.yml).

The vendor contains libraries used in the application/Symfony2.


### 2.3 Roles and Functionalities/Accesses

The platform can be used by several profiles. An user can be:

- Student (public)
- Teacher (mediators)
- Admin (platform administrator)
- Blocked

#### 2.3.1 Student 

The Student is the User by default when signing up.
He has access to the following functions by default:
- Browse resources and presentations shared by other users and available on the platform and sorted by filters and topics.
- Import his/her own resources
- Create his/her own matrixes from his/her own resources or those shared by other users
- Create his/her own presentations from his/her own matrixes or those shared by other users


#### 2.3.2 Teacher 

The Teacher has access to the same pages as the student.
Moreover, they can access several pages: Multiple Indexation, Ontology, Contextual Help.
They can also add a quiz for each resource they add with questions being able to have several formats: multiple choice, numerical answer, text box. 


#### 2.3.3 Admin 

The Admin has access to every page a user and a teacher have. 
Furthermore, he/she can access the "User Management" page where he/she can see the list of all registered users and their roles. He/She then can modify the set role for each user or delete them.


### 2.4 Features

#### 2.4.1 Switching languages

The platform is available in two languages: French and English. There is a dropdown list in the navigation bar to select the language. (In the code, use of filter |trans)

#### 2.4.2 Browsing with filters

By default, when no filters are selected, all public presentations or resources shared by all users are visible.

- Default filter (only on browse/presentation)

- Official filter
All official presentations or resources are visible.

- Personal filter
All personal presentations or resources, public or private (the former ones will appear grey), are visible.

#### 2.4.3 Browsing with topics

While browsing presentations and resources, you can select a topic to see the presentations and resources attached to it.


#### 2.4.4 On presentations and resources

While managing your presentations you can:
- Choose to make them public or private ie share with others or not
- Copy them
- Bookmark them



## 3. The repository's Structure


amb-project  
│  
├── .gitignore  
├── composer.json  
├── composer.lock  
├── contributors.txt  
├── deploy_vmb.sh  
├── LICENSE  
├── README.md  
├── README2018.md  
├── translation_update.bat  
├── /UPGRADE Files (2.2, 2.3, 2.4)  
├── weekly_backup.sh  
│  
├── app  
│   ├── ...  
│   ├── Resources  
│   │   ├── translations  
│   │   │   ├── messages.en.yml  
│   │   │   ├── messages.fr.yml  
├── bin  
│   └── ...  
├── docs  
│   └── ...  
│  
├── src  
│   └── VMB  
│       ├── ContextualHelpBundle  
│       ├── ForumBundle  
│       ├── PresentationBundle  
│       ├── QuizBundle  
│       ├── ResourceBundle  
│       ├── SearchBundle  
│       └── UserBundle  
│  
├── vendor  
│   └── ...  
│  
└── web  
    ├── bundles  
    │   ├── framework  
    │   ├── jmstranslation  
    │   ├── sensiodistribution  
    │   ├── telecomvmb  
    │   └── vmbforum  
    ├── css  
    │   └── ...  
    ├── fonts  
    │   └── ...  
    ├── img  
    │   └── ...  
    ├── js  
    │   └── ...  
    ├── upload  
    │   └── resources  
    │       ├── admin  
    │       └── clara   
    ├── .htaccess  
    ├── app.php  
    ├── app_dev.php  
    ├── apple-touch-icon.png  
    ├── config.php  
    ├── favicon.ico  
    └── robots.txt  



## 4. Bundles

We will describe each bundle from the most to the least important one.
1. PresentationBundle
2. ResourceBundle
3. QuizBundle
4. UserBundle 
5. ForumBundle
6. SearchBundle
7. ContextualHelpBundle


### 4.1 PresentationBundle

#### 4.1.1 Description

This core bundle manages matrixes, presentations, annotations, topics, ontologies,... everything central in the application.


#### 4.1.2 Files

├── ContextualHelpBundle  
│   ├── Controller  
│   │   ├── AnnotationController.php  
│   │   ├── DefaultController.php  
│   │   ├── geany_run_script.bat  
│   │   ├── MatrixController.php  
│   │   ├── OntologyController.php  
│   │   ├── PresentationController.php  
│   │   └── TopicController.php  
│   ├── DependencyInjection  
│   │   ├── Configuration.php  
│   │   └── VMBPresentationExtension.php  
│   ├── Entity  
│   │   ├── Annotation.php  
│   │   ├── AnnotationRepository.php  
│   │   ├── CheckedResource.php  
│   │   ├── CheckedResourceRepository.php  
│   │   ├── Level.php  
│   │   ├── Matrix.php  
│   │   ├── MatrixRepository.php  
│   │   ├── MatrixRow.php  
│   │   ├── MatrixRowRepository.php  
│   │   ├── Ontology.php  
│   │   ├── OntologyRepository.php  
│   │   ├── Pov.php  
│   │   ├── Presentation.php  
│   │   ├── PresentationListener.php  
│   │   ├── PresentationRepository.php  
│   │   ├── Topic.php  
│   │   ├── TopicListener.php  
│   │   ├── TopicRepository.php  
│   │   ├── UsedResource.php  
│   │   └── UsedResourceRepository.php  
│   ├── EventListener  
│   │   └── LocaleListener.php  
│   ├── Form  
│   │   ├── AnnotationType.php  
│   │   ├── LevelType.php  
│   │   ├── MatrixRowType.php  
│   │   ├── MatrixType.php  
│   │   ├── OntologyType.php  
│   │   ├── PovType.php  
│   │   ├── PresentationType.php  
│   │   └── TopicType.php  
│   ├── PreviousUrl  
│   │   └── VMBPreviousUrl.php  
│   ├── Resources  
│   │   ├── config  
│   │   │   ├── routing  
│   │   │   │	├── annotation.yml  
│   │   │   │	├── matrix.yml  
│   │   │   │	├── ontology.yml  
│   │   │   │	├── presentation.yml  
│   │   │   │	└── topic.yml  
│   │   │   ├── routing.yml  
│   │   │   └── services.yml  
│   │   └── views  
│   │       ├── Annotation  
│   │       │	└── edit.html.twig  
│   │       ├── Default  
│   │       │	└── index.html.twig  
│   │       ├── Matrix  
│   │       │	├── edit.html.twig  
│   │       │	├── index.html.twig  
│   │       │	├── modalEdit.html.twig  
│   │       │	├── new.html.twig  
│   │       │	├── official.html.twig  
│   │       │	└── show.html.twig  
│   │       ├── Ontology  
│   │       │	└── ...  
│   │       ├── Presentation  
│   │       │	└── ...  
│   │       └── Topic  
│   │       	└── ...  
│   ├── Tests  
│   │   └── ...  
│   ├── Twig  
│   │   └── ShortenText.php  
│   └── VMBPresentationBundle.php  
│   

### 4.2 ResourceBundle

#### 4.2.1 Description

This bundle manages resources: their creation, modification, deletion,...


### 4.3 QuizBundle  

#### 4.3.1 Description

This bundle implements quizzes that are attached to resources when they are added by a teacher or an administrator. 
It can be 

### 4.4 UserBundle 

#### 4.4.1 Description

This bundle is exclusively for administrator users. It is accessible from the "User Management" tab in the navigation bar if you are logged in as an administrator.
It enables administrators to create users, change their roles or delete them.

### 4.5 ForumBundle

#### 4.5.1 Description

This bundle is the bundle that manages the users' forum.
  

### 4.6 SearchBundle

#### 4.6.1 Description

This bundle controls the search feature which is the feature available for the user to be redirected to the page he/she is looking for. It is available as a search text box on the navigation bar on the top of the page.
This bundle is not fully implemented yet.

  
### 4.7 ContextualHelpBundle

#### 4.7.1 Description

This bundle controls the contextual help which is the help given when the user on the platform clicks on the question mark button at the top of the navigation bar and which gives information about the current page.
This bundle is not fully implemented yet.


## 5. Deployment and automatic updates

Before the deployment, get in touch with the person who maintains the server in order to get the server's name and create your account on the server. /home/{yourAccount}

Then follow these steps:

### 1. Connection:
- Connect to the right network so that you can connect via ssh to the server.
- Connect via ssh to the server:
ssh <yourAccountName>@<serverName>, you will land on the server at /home/{yourAccount}.

### 2. Backup
To make sure not to loose anything, we need to backup the current version. Use the sudo prefix in following commands when required.

- Create your backup directory where you will store the current/old-to-be version of the project. (ex: /var/www-old)

- Go to /var/www, the directory that stores your web project.

- Copy/Move the current/old-to-be amb project (./edu) to another directory (ex: /var/www-old)
command (from the ssh connection): 
mv -r /initial/project/path /your/backup/directory
(something like mv -r ./edu ../www-old)

- Create an empty sql file you will dump your database into and store it for example in your backup directory.
command (from the ssh connection): 
touch <yourEmptyDumpSqlFileName>.sql
chmod 666 <yourEmptyDumpSqlFileName>.sql (give it the write permission)

- Dump your database into the created file
command (from the ssh connection): 
mysqldump -u root -p <databaseName> > /backup/directory/<yourEmptyDumpSqlFile>
* <databaseName> and the password that will be requested can be found in the old project in the app/config/parameters.yml file

### 3. Import your own version
Now that the current/old version is backed up, you can import your own version on the server

- Take all your files of your project except the vendor file (which will be re generated) as well as your composer.phar file and compress the new directory into an .tar.gz archive. 

- Send this archive on the server (from your own machine's terminal, not through your ssh connection)
scp /your/local/path/to/archive.tar.gz <yourAccountName>@<serverName>:/home/<yourAccountName>

- Connect via ssh to your server, then uncompress it in the /var/www directory. Make sure your archive/new project directory is called the same name as the old project's name (here "edu").
command (from the ssh connection):
tar -xvzf your/path/to/archive.tgz /var/www

### 3. Installation
Once your uncompressed project is in /var/www, we will install it! Make sure you use "--env=prod" in all the following commands.
All the following commands are to execute through your ssh connection.

- We will install dependencies and external libraries with the Composer.
php composer install --no-dev --env=prod

- Try the command that should display all the available commands:
php app/console --env=prod

- We will update the Doctrine schema to add some new links. First, observe the newly generated queries by doing:
php app/console doctrine:schema:update --dump-sql --env=prod
Observe if there aren't any queries that could endanger the project by triggering data loss such as "delete from", "drop table", etc...

- If none of the generated queries look risky, you can execute the update by doing:
php app/console doctrine:schema:update --force --env=prod

- Go to the web directory of your old project (ex /var/www-old/edu/web) and copy the "upload" directory to your own project (ex /var/www/edu/web). This directory contains all the links to resources that were uploaded by users.

- Don't forget to empty the cache in the production environment (from /var/www):
php app/console cache:clear --env=prod

- Give permissions to write in the logs and cache directories, as well as in the web/upload one.
chown -R www-data:www-data app/cache/
chown -R www-data:www-data app/logs/
chmod -R 775 app/cache
chmod -R 775 app/logs
chown -R www-data:www-data web/upload/
chmod -R 775 web/upload/


### Or, you can use the deployment script.
What the deployment script does:
1. Install dependencies with Composer
2. Create the database and fill it accordingly
3. Give the rights to the server to write in a few folders (ex: app/logs, app/cache)
4. Clean the cache

## Authors

Clara Lecroisey - clara.lecroisey@imt-atlantique.net  
Sasha Laurent - sasha.laurent@imt-atlantique.net 

