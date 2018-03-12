# AMB PROJECT


## About the project

This is a 2018 update on the amb-project state.
AMB is now built and running on the Symfony 2.8 version.

This README2018 will give instructions (again) on how to execute and take on the project. It will then give informations about AMB,  It will also describe the project's structure (files of the project) as well as describe briefly every bundle.


## SUMMARY

1. Instructions to run the project
2. About the AMB platform
    2.1 Generalities
    2.2 Roles and Functionalities/Accesses
    2.3 Features
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



### 2.2 Roles and Functionalities/Accesses

The platform can be used by several profiles. An user can be:

- Student (public)
- Teacher (mediators)
- Admin (platform administrator)
- Blocked

#### 2.2.1 Student 

The Student is the User by default when signing up.
He has access to the following functions by default:
- Browse resources and presentations shared by other users and available on the platform and sorted by filters and topics.
- Import his/her own resources
- Create his/her own matrixes from his/her own resources or those shared by other users
- Create his/her own presentations from his/her own matrixes or those shared by other users


#### 2.2.2 Teacher 

The Teacher has access to the same pages as the student.
Moreover, they can access several pages: Multiple Indexation, Ontology, Contextual Help.
They can also add a quiz for each resource they add with questions being able to have several formats: multiple choice, numerical answer, text box. 


#### 2.2.3 Admin 

The Admin has access to every page a user and a teacher have. 
Furthermore, he/she can access the "User Management" page where he/she can see the list of all registered users and their roles. He/She then can modify the set role for each user or delete them.


### 2.3 Features

#### 2.3.1 Switching languages

The platform is available in two languages: French and English. There is a dropdown list in the navigation bar to select the language. (In the code, use of filter |trans)

#### 2.3.2 Browsing with filters

By default, when no filters are selected, all public presentations or resources shared by all users are visible.

- Default filter (only on browse/presentation)

- Official filter
All official presentations or resources are visible.

- Personal filter
All personal presentations or resources, public or private (the former ones will appear grey), are visible.

#### 2.3.3 Browsing with topics

While browsing presentations and resources, you can select a topic to see the presentations and resources attached to it.


#### 2.3.4 On presentations and resources

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

This core bundle manages matrixes, presentations, annotations, topics, ontologies,... everything central in the application.


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

These are the steps to follow before deploying this Symfony2 project:
1. Modify the web/config.php file so that the "if" condition looping on the localhost ip address is removed.
2. Make sure the server is running on:
- a PHP version >= 5.3.3
- the extention SQLite 3 is active
- the extention JSON is active
- the extention Ctype is active
- the timezone parameter is set in the server's php.ini

To deploy the project, use the deployment script.
What the deployment script does:
1. Clean the cache
2. Install dependencies with Composer
3. Create the database and fill it accordingly
4. Give the rights to the server to write in a few folders (ex: app/logs, app/cache)
5. Regenerate translations


## Authors

Names: Sasha Laurent - Clara Lecroisey
Email address: sasha.laurent@imt-atlantique.net - clara.lecroisey@imt-atlantique.net

