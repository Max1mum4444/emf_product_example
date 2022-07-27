# EMF PRODUCTS EXAMPLE
This app is only an example of api and data getting from that api 2 in 1. It's not correct way to use it within real app
It should be divided in 2 apps to use it as separate microservices
## Logger
TODO configure logger - I am currently using LoggerInterface and logging exception in controller but it would be good to write it somewhere

## Normalizer - ProductNormalizer
TODO make processing data from api and hydrate Entities
in new app to process data coming from api via normalizer in its own repository where would be client calling

## General TODOs
add price unit to DB


## Elastic with mysql sync via logstash
https://www.elastic.co/blog/how-to-keep-elasticsearch-synchronized-with-a-relational-database-using-logstash

https://towardsdatascience.com/how-to-synchronize-elasticsearch-with-mysql-ed32fc57b339

## URL Examples
http://emf-products.localhost/

http://emf-products.localhost/products

http://emf-products.localhost/products/1

http://emf-products.localhost/products?search=nechaj

## RUN and install
```console
docker-compose up -d --build
docker exec -it php81-container /bin/bash
composer install 
yarn install
yarn build
```
