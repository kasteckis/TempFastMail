# Self Hosted Temporary Fast Email Box

* https://tempfastmail.com
* Other information soon!

```
services:
  tempfastmail:
    container_name: kasteckis_tempfastmail
    image: kasteckis/temp-fast-mail:latest
    environment:
      CREATE_RECEIVED_EMAIL_API_AUTHORIZATION_KEY: change-this-to-a-random-value # TODO: Create random value here!
      DEFAULT_URI: http://localhost
    ports:
      - "80:80"
    volumes:
      - ./sqlite:/app/sqlite
```
