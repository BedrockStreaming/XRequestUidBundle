# XRequestUidBundle

## Why ?

*"The cool thing in microservice is that you can generate 15 http calls in your infrastructure just with on client http call"*  
 
This situation leads to many difficulties to trace calls betweens APIs systems. This bundle provide a solution to generate ids for new requests and configure your guzzle services accordingly to uses those ids in sub http requests made by them.

The bundle will generate and/or transfert those two headers : X-Request-Id and X-Request-Parent-Id.
 
##  How ? 

When a request arrive, if a X-Request-Id exists, it is copied into the X-Request-Parent-Id. If it's dont exist we generate it throught the uniqId_service. 

All your guzzle services are decorated with a proxy who will add the two headers in all the call made. 

At the end both headers are added to the response for debugging purpose.


## Configuration

```yml 
m6_web_x_request_uid:
    request_uid_header_name: X-Request-toto    # optionnal, X-Request-Id by default
    request_parent_uid_header_name: X-Parent   # optionnal, X-Request-Parent-Id by default 
    uniqId_service: myservice                  # optionnal, a service implementing UniqIdInterface 
    services:                                  # list of guzzle services to decorate 
        - test.guzzle1

services:
    test.guzzle1:
        class: 'GuzzleHttp\Client'

```


## Todo 

 * add more tests
 * add more docs

