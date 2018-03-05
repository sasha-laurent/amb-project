# AMB PROJECT



## About the project

This is a 2018 update on the amb-project state.
AMB is now built and running on the Symfony 2.8 version.

This README will give instructions (again) on how to execute and take on the project. It will also describe the project's architecture (files of the project) as well as describe briefly every bundle.



## Authors

Names: Sasha Laurent - Clara Lecroisey
Email address: sasha.laurent@imt-atlantique.net - clara.lecroisey@imt-atlantique.net



## Instructions

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

4. By default, topics don't exist. They are added in the data base by the administrators. Since you will need to indicate them when uploading resources, creating matrices, etc., you first need to create topics by becoming an admin. 
To do so, sign in, then change your ROLE (table "User", column "roles") from s:12:"ROLE_STUDENT" to s:10:"ROLE_ADMIN".

5. To use the Symfony console, you need to set the timezone on the server's php.ini.

6. Finally, to see the website, start your XAMP/MAMP server and visit localhost/amb-project/web/ (change acording to your own path) !



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

### ContextualHelpBundle

├── ContextualHelpBundle  
│   ├── Controller  
│   │   ├── DefaultController.php  
│   │   └── HelpController.php  
│   ├── DependencyInjection  
│   │   ├── Configuration.php  
│   │   └── VMBContextualHelpExtension.php  
│   ├── Entity  
│   │   ├── Help.php  
│   │   └── HelpRepository.php  
│   ├── Form  
│   │   └── HelpType.php  
│   ├── Resources  
│   │   ├── config  
│   │   │   ├── routing  
│   │   │   │	└── help.yml  
│   │   │   ├── routing.yml  
│   │   │   └── services.yml  
│   │   └── views  
│   │       └── Help  
│   │       	├── index.html.twig  
│   │       	└── modal.html.twig  
│   └── Tests  
│   

### PresentationBundle

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





