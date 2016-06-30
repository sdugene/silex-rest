# Silex REST
A simple skeleton application for writing RESTful API.

**This project wants to be a starting point to writing scalable and maintainable REST api with Silex PHP micro-framework**

####How do I run it?
After cloning project, from the root folder of the project, run the following command to install the php dependencies.

    
    composer install 
    

Set your database then copy resources/config/prod.php to resouces/config/config.php, update with your database params then run.

   	
    php bin/routesGenerator.php 
   

In production environment, authentification key is required, to generate it run the following command. 

   	
    php bin/keyGenerator.php all
   

Your api is now available at http://localhost/api/v1.

####Run tests
Some tests were written, and all CRUD operations are fully tested :)

From the root folder run the following command to run tests.
    
    vendor/bin/phpunit 


####What you will get
The api will respond to

	GET    ->   http://localhost/api/v1/myTable
	GET    ->   http://localhost/api/v1/myTable/{id}
	POST   ->   http://localhost/api/v1/myTable
	PUT    ->   http://localhost/api/v1/myTable/{id}
	DELETE ->   http://localhost/api/v1/myTable/{id}

Your request should have 'Content-Type: application/json' and 'key: myKey' headers.
Your api is CORS compliant out of the box, so it's capable of cross-domain communication.

Try with curl:
	
	#GET
	curl http://localhost/api/v1/myTable -H 'Content-Type: application/json' -H 'key: myKey' -w "\n"
	curl http://localhost/api/v1/myTable/1 -H 'Content-Type: application/json' -H 'key: myKey' -w "\n"

	#POST (insert)
	curl -X POST http://localhost/api/v1/myTable -d '{"note":"Hello World!"}' -H 'Content-Type: application/json' -H 'key: myKey' -w "\n"

	#PUT (update)
	curl -X PUT http://localhost/api/v1/myTable/1 -d '{"note":"Uhauuuuuuu!"}' -H 'Content-Type: application/json' -H 'key: myKey' -w "\n"

	#DELETE
	curl -X DELETE http://localhost/api/v1/myTable/1 -H 'Content-Type: application/json' -H 'key: myKey' -w "\n"

Under the resources folder you can find a .htaccess file to put the api in production.

####Contributing

Fell free to contribute, fork, pull request, hack. Thanks!

####Author

+	[http://www.siteoffice-cms.fr](http://www.siteoffice-cms.fr)

+	<mailto:sebastien@siteoffice.fr>

## License

see LICENSE file.






