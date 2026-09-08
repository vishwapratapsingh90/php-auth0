# PHP Auth0 Sample

This is a small stateful Auth0 web application based on the
[Auth0 PHP SDK](https://github.com/auth0/auth0-PHP). It supports two explicit
login choices: an Active Directory or enterprise connection and a non-AD
connection such as a database connection.

## Setup

### Create a free Auth0 account

1. Open the [Auth0 signup page](https://auth0.com/signup).
2. Create an account and choose the free trial or free-plan option when
   prompted.
3. Create a development tenant. The tenant domain created during signup is
   used as `AUTH0_DOMAIN`.

After the tenant is ready, create a **Regular Web Application** under
**Applications → Applications** and configure:

- Allowed Callback URLs: `http://localhost:3000/callback`
- Allowed Logout URLs: `http://localhost:3000/`
- Token Endpoint Authentication Method: `POST`

## Environment values

Find the application credentials under **Applications → Applications → your
application → Settings**:

- `AUTH0_DOMAIN`: the application **Domain**.
- `AUTH0_CLIENT_ID`: the application **Client ID**.
- `AUTH0_CLIENT_SECRET`: the application **Client Secret**.
- `AUTH0_BASE_URL`: `http://localhost:3000` for local development.
- `AUTH0_COOKIE_SECRET`: generate a local secret with:

  ```sh
  openssl rand -hex 32
  ```

Find the connection names under **Authentication → Connections**:

- `AUTH0_AD_CONNECTION`: the **Name** of the enabled Active Directory or
  enterprise connection.
- `AUTH0_NON_AD_CONNECTION`: the **Name** of an enabled database, social, or
  other non-AD connection.

Enable both connections for the application under the application's
**Connections** tab. Do not commit `.env`, share client secrets, or expose
cookie secrets. Rotate credentials immediately if they are accidentally
disclosed.

Copy `.env.example` to `.env` and set the values from the application:

```sh
cp .env.example .env
openssl rand -hex 32
```

`APP_ENV` identifies the running environment. Set `APP_DEBUG=true` locally to
display authentication exception messages while developing. Keep
`APP_DEBUG=false` outside local development so internal details are not shown
to users; failures are still written to the PHP application log.

Set `AUTH0_AD_CONNECTION` to the name of your Active Directory connection and
`AUTH0_NON_AD_CONNECTION` to the name of your database, social, or other
non-AD connection. Auth0 must have both connections enabled for this
application.

## Run with Docker Compose

```sh
docker compose up -d
```

Open <http://localhost:3000>. The application exchanges the authorization
code, stores the Auth0 session in an encrypted cookie, displays the authenticated
profile, and supports logout.

## Install dependencies manually

```sh
composer require symfony/http-client nyholm/psr7
composer require auth0/auth0-php
```
