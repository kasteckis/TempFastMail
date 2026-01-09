# Self Hosted Temporary Fast Email Box

The first self-hosted, disposable fast email inbox that receives and displays messages.

![Temp Fast Mail frontpage photo](https://github.com/kasteckis/tempfastmail/blob/master/docs/readme.png?raw=true)

<b>You can check out working DEMO: [TempFastMail.com](https://tempfastmail.com)</b>


## How to set up with Docker Compose?

1. Create `docker-compose.yml` file with following content. Also adjust environment variables as needed.

```
services:
  tempfastmail:
    container_name: tempfastmail
    image: kasteckis/tempfastmail:latest
    environment:
      CREATE_RECEIVED_EMAIL_API_AUTHORIZATION_KEY: change-this-to-a-random-value # TODO: Create random value here!
      DEFAULT_URI: https://yourdomain.com # TODO: Change to your domain!
    ports:
      - "80:80"
    volumes:
      - ./sqlite:/app/sqlite
```

2. Set up CloudFlare Email Worker (<b>It's free</b>) which would publish received emails to your instance. [You can follow instructions here](https://github.com/kasteckis/tempfastmail/blob/master/docs/CLOUDFLARE_EMAIL_WORKER_SETUP.md).
3. Visit your website and start receiving emails! With email `admin@admin.dev` and password `admin`!

## How it handles privacy?

Users can view their emails for up to 48 hours, because all emails are stored for 48 hours. After that they are automatically deleted from the database. You can also manually delete all emails from the admin interface. This is done to ensure that no sensitive information is stored for longer than necessary. Same approach is used on DEMO.

## How we create production image?
```
$ docker build --target frankenphp_prod -t kasteckis/tempfastmail:latest .
$ docker push kasteckis/tempfastmail:latest
```

## Any issues or questions?

Feel free to open an issue on GitHub - [here](https://github.com/kasteckis/TempFastMail/issues)  
[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/B0B81RZTWV)
