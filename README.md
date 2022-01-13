# Symfony Project

This project was created using symfony 4 and rabbitmq bundle

## How To Run

* Clone project
* Run consumer in console by executing ```php bin/console rabbitmq:consume messaging``` 

Once this is done, you can load the base url for the app. Everytime you reload the page, a new publish procedure starts. It consumes the provided API and produces the message, which in turn is read by the consumer. The consumed message is saved on a database
