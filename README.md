#Rest API Server

A simple API REST Server which supports
- HTTP Authentification
- Rate limiting
- X-HTTP-Method-Override

## Send a request

### Date [required]
in "HTTP-date" format as defined by RFC 2616. The date should be exact, the request is valid for a limited time defined with *REQUESTLIFETIME*

### X-Public [required]
Contains the public key

### Authorization [required]
The Authorization request header is required. It contains the signature of the request. The signature is created by using the method, the host, the uri, the public key and the date, *sorted*, with the following format <parameter>=<URL-encoded value>&. Once properly formatted, you create a base64-encoded HMAC_SHA256 signature using your secret key.

### X-HTTP-Method-Override [optional]
To increase accessibility to the API we accept a request header *X-HTTP-Method-Override* with a string value containing one of GET, POST, PATCH or DELETE.

## Rate Limiting
Rate limiting is a user-based limitation. The limitation is defined by the number of requests per window of time. The number of request is defined in *RATELIMIT*. The time window is defined in second in *RATELIMITWINDOW*. 
HTTP headers are returned.
- X-Rate-Limit-Limit: the rate limit ceiling for that given request
- X-Rate-Limit-Remaining: the number of requests left for the 5 minute window
- X-Rate-Limit-Reset: the remaining window before the rate limit resets in UTC epoch seconds
When an application exceeds the rate limit for a given API endpoint, the API will return an HTTP 429 "too many requests".

! The script use Memcache to manage the Rate limiting

## Example
See /test/client.php and /test/server.php

##Licence
Copyright (c) 2014 - Arnaud Jeulin. See the LICENCE.md file.

##Author
[Arnaud Jeulin](https://twitter.com/dauran)
This API server is a part of [Forexticket](http://www.forexticket.com)