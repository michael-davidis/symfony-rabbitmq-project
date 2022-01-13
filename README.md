# Symfony Project

This project was created using the MVC architectural pattern, using Symfony 4.

Its purpose and structure is to consume data from an API, produce them to a RabbitMQ exchange, consume the result queue and register them to a database using Doctrine.

## Execution

* Firstly, you need to clone the repo and run it in your prefered server. Laragon was used for this project.

* Then, in order to run this project you need to run ```php bin/console rabbitmq:consume messaging``` in your console. This forces the program to start listening for incoming messages that might come throught he result queue.

* Once this is done, you can load your page. Everytime you reload the page, a new publish procedure starts. It consumes the provided API and produces the message.
