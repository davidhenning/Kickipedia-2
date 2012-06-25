# Kickipedia 2 RESTful web-service #

## Features ##

- Full JSON support for incoming and outgoing data
- [HTTP digest authentication](http://en.wikipedia.org/wiki/Digest_access_authentication)
- XML output supported 

## Insert an entry ##

### Supported methods

- `PUT /entry`
- `POST /entry/insert`

### Required content type ###

`Content-Type: application/json`

### Request body ###

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
The data attribute is required. It stores the object, that will be inserted into the database. You can use all fields described in the kickipedia2.json configuration file.

### Response body ###

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

If you send malformed JSON or the data attribute is missing, a HTTP 400 and detailed error message will be returned.

## Update an entry ##

### Supported methods

- `PUT /entry/:id`
- `POST /entry/:id/update`

Replace `:id` with the ID of the entry provided in the response of an insert or an entry list.

### Required content type ###

`Content-Type: application/json`

### Request body ###

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
### Response body ###

```json
{
    "status": 200,
    "time": "2012-06-17 16:30:39",
    "request": {
        "method": "PUT",
        "url": "/entry"
    },
    "response": {
        "action": "update",
        "documentId": "4fddea0f9200ce3c11000009",
        "documentUri": "/entry/4fddea0f9200ce3c11000009"
    }
}
```

Except the action in the response attribute, it is all the same an the insert response body.

## Get a single entry ##

### Supported methods

- `GET /entry/:id.:format`

Replace `:id` with the ID of the entry provided in the response of an insert or an entry list. As `:format` HTML, JSON and XML are supported.

### Request body ###

Not necessary.

### Response body ###

```json
{
    "status": 200,
    "time": "2012-06-17 17:11:52",
    "request": {
        "method": "GET",
        "url": "/entry/4fddea0f9200ce3c11000009.json"
    },
    "response": {
        "total": 1,
        "found": 1,
        "documents": [
            {
                "_id": "4fddea0f9200ce3c11000009",
                "createdOn": "2012-06-17 16:30:39",
                "updatedOn": "2012-06-17 16:30:39",
                "type": 1,
                "user": null,
                "name": "REST-Test",
                "reason": "REST-Test",
                "ip": "127.0.0.1",
                "url": null,
                "comment": null
            }
        ]
    }
}
```

## List entries ##

### Supported methods

- `GET /entry/list.:format`

 As `:format` HTML, JSON and XML are supported.

### Possible GET parameters

- `skip`: skip # entries (default: 0)
- `limit`: show only # entries (default: 100)
- `type`: show only entries with given type id
- `term`: search all entries for given term
- `sort`: sort result with given field name ( fields described in the kickipedia2.json configuration file)
- `direction`: asc for ascending sorting, desc for descending sorting

### Request body ###

Not necessary.

### Response body ###

Example URL:

`/entry/list.json?limit=2&sort=createdOn&direction=desc`

```json
{
    "status": 200,
    "time": "2012-06-17 17:18:34",
    "request": {
        "method": "GET",
        "url": "/entry/list.json",
        "params": {
            "limit": 2,
            "sort": "createdOn",
            "direction": "desc"
        }
    },
    "response": {
        "total": 10024,
        "found": 2,
        "documents": [
            {
                "_id": "4fddea0f9200ce3c11000009",
                "createdOn": "2012-06-17 16:30:39",
                "updatedOn": "2012-06-17 16:30:39",
                "type": 1,
                "user": null,
                "name": "REST-Test",
                "reason": "REST-Test",
                "ip": "127.0.0.1",
                "url": null,
                "comment": null
            },
            {
                "_id": "4fdbcad99200ce2809000080",
                "createdOn": "2012-06-16 01:52:57",
                "updatedOn": "2012-06-16 01:52:57",
                "type": 1,
                "user": null,
                "name": "REST-Test",
                "reason": "REST-Test",
                "ip": "127.0.0.1",
                "url": null,
                "comment": null
            }
        ]
    }
}
```