# AMB PROJECT



## About the project

This is a 2018 update on the amb-project state.
AMB is now built and running on the Symfony 2.8 version.

This README2018 will give instructions (again) on how to execute and take on the project. It will also describe the project's architecture (files of the project) as well as describe briefly every bundle.



## Authors

Names: Sasha Laurent - Clara Lecroisey
Email address: sasha.laurent@imt-atlantique.net - clara.lecroisey@imt-atlantique.net



## Instructions to run the project

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


## Adaptative Media Builder

### Roles and Functionalities/Accesses

- Student
- Teacher
- Admin
- Blocked

#### Student 

The Student is the User by default when signing up.
He has access to the following functions by default:
- Browse resources and presentations shared by other users and available on the platform and sorted by filters and topics.
- Import his/her own resources
- Create his/her own matrixes from his/her own resources or those shared by other users
- Create his/her own presentations from his/her own matrixes or those shared by other users


#### Teacher 

The Teacher has access to the same pages as the student.
Moreover, they have access to 


#### Admin 

The Admin has access to



### Features

#### Switching languages

The platform is available in two languages: French and English. There is a dropdown list in the navigation bar to select the language. (In the code, use of filter |trans)

#### Browsing with filters

By default, when no filters are selected, all public presentations or resources shared by all users are visible.

- Default filter (only on browse/presentation)

- Official filter
All official presentations or resources are visible.

- Personal filter
All personal presentations or resources, public or private (the former ones will appear grey), are visible.

#### Browsing with topics

While browsing presentations and resources, you can select a topic to see the presentations and resources attached to it.


#### On presentations and resources

While managing your presentations you can:
- Choose to make them public or private ie share with others or not
- Copy them
- Bookmark them



## Repository's Architecture


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
│   └── ...  
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



## Bundles

We will describe each bundle from the most to the least important one.
1. PresentationBundle
2. ResourceBundle
3. UserBundle 
4. QuizBundle
5. ForumBundle
6. SearchBundle
7. ContextualHelpBundle


### 1. PresentationBundle

#### Description

This core bundle manages matrixes, presentations, annotations, topics, ontologies,... everything central in the application.


#### Files

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

### 2. ResourceBundle

#### Description

This core bundle manages matrixes, presentations, annotations, topics, ontologies,... everything central in the application.


### 3. UserBundle 

#### Description


### 4. QuizBundle

#### Description

### 5. ForumBundle

#### Description


### 6. SearchBundle

#### Description

This bundle controls the contextual help which is the help given when the user on the platform clicks on the question mark button at the top of the navigation bar.
This bundle is not fully implemented yet.



### 7. ContextualHelpBundle

#### Description

This bundle controls the contextual help which is the help given when the user on the platform clicks on the question mark button at the top of the navigation bar.
This bundle is not fully implemented yet.


