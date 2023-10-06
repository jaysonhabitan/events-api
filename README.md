## Event Management API Repository

#### About the Application:
This is a REST API application for managing `events`. You could `CREATE`, `READ`, `UPDATE`, and `DELETE` an `event` data within the application.

---
#### Project prerequisites:
- [PHP 7.4](https://www.php.net/downloads.php)
- [Composer 2](https://getcomposer.org/download/) (PHP package manager)
---
#### 1. Setup local environment:

##### Installing package dependencies
After pulling the repository, go to the project's root directory from the terminal and install the packages, like so:
```sh
composer install
```

##### Setting up the environment

Now we initialize the application by entering the following commands:
```sh
# copy environment variables file template
cp .env.example .env

# generate an application key
php artisan key:generate
```
Next, open your newly created .env file and add or modify the following configurations:
```sh
# Database connection
DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<YOUR_SQLITE_DB_FILE_PATH>
DB_USERNAME=root
DB_PASSWORD=

# app token name for sanctum
APP_TOKEN_NAME="events-api-token"
```
**Notes**
> Example path for `DB_DATABASE` would be: /Users/jaysonhabitan/events-api/api-db.sqlite

Now, make sure you save your .env file and head back to your terminal under your project directory:
```sh
# Creates the database tables and runs the seeders.
php artisan migrate --seed

# Cleaning up and clearing the cache
php artisan optimize:clear
```
We will only use the default local environment hosting of Laravel, run this command in your terminal:
``` 
php artisan serve 
```
After running the command you should see Laravel's development server, and it usually runs on:
``` sh 
http://127.0.0.1:8000
```
Otherwise, use whatever Laravel gave you.

---

### How to use the application:

Assuming that you already have a running development server, we can now use it as a base URL and use it to call available API endpoints within the application.

#### REST API Routes
---
#### 1. Authentication
Before we can call any API endpoints, we must first log in using valid credentials. We may log in using the following endpoint: 

##### `Login`:
Method: `POST`  \
End point:  `/api/v1/login`  \
Headers: 
```
Accept: application/json
Content-Type: application/json
```
Request body:
```sh
{
    "email": "api_user1@test.com",
    "password": "pass1234"
}
```
Response: 
```sh
{
    "user": {
        "name": "John Doe",
        "email": "api_user1@test.com"
    },
    "access_token": "2|O5pm6Hjl7jPCxXEBsATKVxeuFo8XO4lG8u1PASF7"
}
```
#### Available credential to use in the login
```sh
email: api_user1@test.com
password: pass1234
```

#### 2. Using available API endpoints
After logging in you will get an `access_token` that is included in the response, include it in the headers to access protected API routes like so:
```
Authorization: Bearer 2|O5pm6Hjl7jPCxXEBsATKVxeuFo8XO4lG8u1PASF7
```
Before calling any API endpoints, ensure you set the headers correctly. \
HEADERS: 
```
Authorization: Bearer <YOUR_ACCESS_TOKEN>
Accept: application/json
Content-Type: application/json
```
---
## Event Resource API
> ***Notes to take before using the API:*** I assumed that the values of the invitees are user IDs so whenever we update or insert an invitee, make sure that it exists on the DB. In this application, I pre-seeded five(5) users, so user_id 1,2,3,4, and 5 are the only valid value(s) in the `invitees` field unless you create another user by running a UserFactory. This is also the same case for the `frequency` field, so the only valid frequencies are `Once-Off`, `Weekly`, and `Monthly`.

2.1.`GET ALL EVENTS DATA` \
Method: `GET` \
End point:  `/api/v1/events` \
Available query param filters:
* ```from``` - Should be in YYYY-MM-DD HH:MM format (ex. from=2020-01-01 00:00).
* ```to``` - Should be in YYYY-MM-DD HH:MM format (ex. to=2020-01-01 00:00).
* ```invitees``` - Should be a string of user_ids separated by a comma (,) (ex. invitees=1,2,3 ).
* ```duration``` - Should be an integer between 0 to 60 (ex. duration=25 ).

Response: 
```sh
HTTP_STATUS: 200 OK

DATA: {
    "items": [
        {
            "eventName": "test event5",
            "frequency": "Once-Off",
            "startDateTime": "2020-06-01 12:12",
            "endDateTime": null,
            "duration": "0",
            "invitees": [
                "1",
                "2",
                "3"
            ]
        },
        {
            "eventName": "test event5",
            "frequency": "Once-Off",
            "startDateTime": "2020-06-01 12:12",
            "endDateTime": null,
            "duration": "0",
            "invitees": [
                "2",
                "3"
            ]
        }
    ]
}
```

2.2.`GET A SINGLE EVENT DATA` \
Method: `GET` \
End point:  `/api/v1/events/{eventId}` \
Response: 
```sh
HTTP_STATUS: 200 OK

DATA: {
    "data": {
        "eventName": "test event5",
        "frequency": "Once-Off",
        "startDateTime": "2020-06-01 12:12",
        "endDateTime": null,
        "duration": "0",
        "invitees": [
            "1",
            "2",
            "3"
        ]
    }
}
```

2.3.`CREATE AN EVENT` \
Method: `POST` \
End point:  `/api/v1/events` \
Request body:
```sh
{
    "eventName": "test event",
    "frequency": "Weekly",
    "startDateTime": "2020-06-01 12:12",
    "endDateTime": "2020-06-08 12:12",
    "duration": 30,
    "invitees": [1,2,3]
}
```
Response: 
```sh
HTTP_STATUS: 200 OK

DATA: {
    "data": {
        "eventName": "test event",
        "frequency": "Weekly",
        "startDateTime": "2020-06-01 12:12",
        "endDateTime": "2020-06-08 12:12",
        "duration": 30,
        "invitees": [
            "1",
            "2",
            "3"
        ]
    },
    "message": "Event created successfully!"
}
```

2.4.`UPDATE AN EVENT` \
Method: `PUT` \
End point:  `/api/v1/events/{eventId}` \
Request body:
```sh
{
    "eventName": "test update event",
    "frequency": "Weekly",
    "startDateTime": "2020-06-01 12:12",
    "endDateTime": "2020-06-08 12:12",
    "duration": 30,
    "invitees": [1,2,3]
}
```

2.5.`PATCH AN EVENT`\
Method: `PATCH`\
End point:  `/api/v1/events/{eventId}` \
Request body:
```sh
{
    "eventName": "test patch event"
}
```
Response: 
```sh
HTTP_STATUS: 200 OK

DATA: {
    "message": "Event updated successfully."
}
```

2.6.`DELETE AN EVENT` 
Method: `DELETE` \ 
End point:  `/api/v1/events/{eventId}` \
Response: 
```sh
HTTP_STATUS: 200 OK

DATA: {}
```
---
## Unit Test

#### 1. Setup the unit test .env.testing file
> Before we run the unit test, it is recommended to create a separate .env file for the unit test. But `you can skip this part`, just be aware that it will use your .env database as a default and will surely wipe out your data.

Assuming that you are in the application directory, run the following command in the terminal to create a .env.testing for our unit test:
```
# copy environment variables file template
cp .env .env.testing
```
Next, open your newly created .env.testing file and add or modify the following configurations:
```sh
# Database connection
DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<YOUR_SQLITE_TESTING_DB_FILE_PATH>
DB_USERNAME=root
DB_PASSWORD=
```
#### 2. Run the unit test
After setting up the .env.testing file we can now run the following command to run the unit test:

```sh 
php artisan test --env=testing
```

> Make sure you include the `--env=testing`  flag so that it will use the .env.testing configurations, if not it will use your .env configuration and might wipe out your data.
---

## Application models and definitions
* User - The model for users table
* Event - The model for events table
* EventUser - The pivot model and table between event and user model
* Frequency - The model for frequency table.
---
## Application folder architecture
```
├── app 
│   ├── Console
│   ├── Enum - This is where the enumerables are stored.
│   ├── Exceptions
│   ├── Filters - This is where the filter function for GET requests is stored.
│   │   └── V1
│   ├── Http
│   │   ├── Controllers
│   │   │   └── Api  - This is where  the  resource controllers are stored.
│   │   │       └── V1
│   │   ├── Middleware
│   │   ├── Requests - This is where the request validations are stored.
│   │   │   └── V1
│   │   └── Resources - This is where the model's resource format is stored.
│   │       └── V1
│   ├── Models
│   ├── Observers - I added an observer for detaching the many-many relationship for the event model when deleting event data.
│   ├── Providers
│   ├── Services -  This is  where the model's service(s) are stored.
│   │   └── V1
│   └── Tools - This is where we store the API tools.
├── bootstrap
│   └── cache
├── config
├── database
│   ├── factories
│   ├── migrations
│   └── seeders
├── public
├── resources
│   ├── css
│   ├── js
│   ├── lang
│   │   └── en
│   └── views
├── routes
├── storage
│   ├── app
│   │   └── public
│   ├── framework
│   │   ├── cache
│   │   │   └── data
│   │   ├── sessions
│   │   ├── testing
│   │   └── views
│   └── logs
└── tests
    └── Feature
        └── Api - This is where the unit tests are stored.
            └── V1 
```
***Thank you for taking your time to read!*** 🙇🏼
---
