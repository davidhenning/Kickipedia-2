# Kickipedia 2 RESTful web-service #

## Features ##

- Full JSON support for incoming and outgoing data
- [HTTP digest authentication](http://en.wikipedia.org/wiki/Digest_access_authentication)
- XML output supported 

## Insert an entry ##

Supported methods:

- `PUT /entry`
- `POST /entry/new`

Required content type:

`Content-Type: application/json`

Request body:

```json
{
	"data": {
		"type": 1,
		"name": "REST-Test",
		"reason": "REST-Test",
		"ip": "127.0.0.1"	
	}
}
```

Response body:

```json
{
    "status": 200,
    "time": "2012-06-17 16:30:39",
    "request": {
        "method": "PUT",
        "url": "/entry"
    },
    "response": {
        "action": "create",
        "documentId": "4fddea0f9200ce3c11000009",
        "documentUri": "/entry/4fddea0f9200ce3c11000009"
    }
}
```