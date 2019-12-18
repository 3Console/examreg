# Examreg

### Architecture Diagram
![Architecture diagram](public/images/readme/architecture.png)

### Pre-requisites

  - PHP >= 7.0.0
  - MySQL
  - Composer
  - Node JS
  - Redis
  - Laravel-echo-server
  - Docker
  - make

### First time setup
  - Init submodules
```sh
$ git submodule update --init
```

  - copy .env file from .env.example and then change database configuration
```sh
$ cp .env.example .env
```
  - install composer modules. You'll need to run this command again if there's any update in the file composer.json
```sh
$ composer install
```
- Generate application key
```sh
$ php artisan key:generate
```
- Init database
```sh
$ php artisan migrate
$ php artisan db:seed
```
- Link storage folder
```sh
$ php artisan storage:link
```
- Generate passport keys
```sh
$ php artisan passport:keys
```
- Install Laravel-echo-server
```sh
$ sudo npm install -g laravel-echo-server
$ cp laravel-echo-server.json.example laravel-echo-server.json
```
### Compiling assets
We use Laravel Mix and webpack to manage and version our assets (javascript, css). Here's how to to do it:
  - Install node modules. You only need to run this command again if there's any update in the file package.json
```sh
$ npm install
```
  - Compile assets
```sh
$ npm run dev
```
  - If you dont want to run the above command every time you update a javscript or css file, you can use the following command to watch and compile automatically
```sh
$ npm run watch
```

### Test it out
- Run redis cache
```sh
$ redis-server
```
- Run Socket.IO server
```sh
$ laravel-echo-server start
```
- Run web server
```sh
$ php artisan serve
```

### Other commands you might find usefull
- Create new table
```sh
$ php artisan make:migration create_companies_table --create=companies
```

### Coding convention

- Indentation: 2 spaces for html/js and 4 spaces for php/css
- No trailing space
- Defining constants inside app/Consts.php and utility functions

- JS: `npm test`
- PHP:
  - `vendor/bin/phpcs --standard=phpcs.xml`
  - `vendor/bin/phpmd app text phpmd.xml`

### Shortcut

```shell
make init-db-full     # Reset databse
make docker-restart   # Restart docker and database
make deploy-dev n=2   # Deploy to test server number 2
make generate-master  # Generate new master data json file (only run this after you modified master data Google sheet)
make update-master    # Update master data
make log              # Tail Laravel log
make test-js          # Check standard *.js
make test-php         # Check standard *.php
make build            # Build assets (*.js, *css)
make watch            # Watch assets (*.js, *css)
make autoload         # Reload *.php
make cache            # Clear cache
make route            # List routes
```

### Using docker
Actually you can ignore all the above steps if you want to use Docker LOL.

- Start the docker containers:
```sh
$ make docker-start
```

- First time install
```sh
$ make docker-init
```

- Compile assets
```sh
$ make watch
```

### How to explicitly specify master database for MySQL queries
We use database replication in our project in which we have 1 master and possibly multiple slave databases. By default, we read from slave and write to master. However, the replication process from master to slave take up to few seconds. Thus, in certain circumstances, you might wanna read from master directly to avoid this delay. Here's how:
- Eloquent
```
User::on('master')->find(1);
```
- Query builder
```
DB::connection('master')->table('users')->find(1);
```

### How to test your API with Postman
In order to request an protected restful API, you need an OAuth 2 access token. Here's how:
- First, create a Password Grant Client
```
$ php artisan passport:client --password
```
- Then you can generate a Personal Access Token for any user by making a Post request like this: https://laravel.com/docs/5.5/passport#requesting-tokens


### How to use signed requests

#### Protect endpoints by authorize request params under HMAC

```php
// Add middleware auth:api and auth.message to the endpoint that need to be proteced under HMAC
Route::group(['middleware' => ['auth:api', 'auth.message']], function () {
    // Protected endpoint definations
});
```

#### Sign request to pass the HMAC authorization

Signed endpoints require an additional parameter, `signature`, to be sent in the query string or request body

1. Request an `access_token` and `secret` using https://laravel.com/docs/5.5/passport#requesting-tokens. This is the example of reponse:

```
{
    "token_type": "Bearer",
    "expires_in": 31536000,
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjU2OTk5Njk4NmZhZWJkNTQzZjNmMjQ5ZjFiY2Q4OTMxMTRjZWIxYmRkOTBmMWM4NThkZjgxNGQzMDBlOGRjMTFlZGRlOGU4M2FkNzAyNWE5In0.eyJhdWQiOiIxIiwianRpIjoiNTY5OTk2OTg2ZmFlYmQ1NDNmM2YyNDlmMWJjZDg5MzExNGNlYjFiZGQ5MGYxYzg1OGRmODE0ZDMwMGU4ZGMxMWVkZGU4ZTgzYWQ3MDI1YTkiLCJpYXQiOjE1MzE0MTIxNTYsIm5iZiI6MTUzMTQxMjE1NiwiZXhwIjoxNTYyOTQ4MTU2LCJzdWIiOiIxMDAwIiwic2NvcGVzIjpbIioiXX0.5CcuM2-7cucN2hjP4eNwUWiPvQwYqaXvRmqw7Pa8RanzER2kFVpbURZcmpg92uf-KHfifcd3abEbvKncEvi-KP84qBr0C2AEOwFXay2DLvMkCQWJ3GbOTaci2a03vtwJgKmut4rgmu732CjgBrVdw8lCOs0V2Dh1Z2lCVjz0du7nkq3RCf-iNHlI3DYPSVie_YGE0iXORz8NTy2YMyCf0UnNuSykdocHflmCStiPo6_uXyVDgD_cHJUtMcJitG8vTV-6bqRLKzv1LgZc--dKE4AaaHACkVF_KtgntWBgpkqxfxDP_07-rtz2R1qfjI8b4KWdaKG35r8u_PtL2S8AcMHo1BdoRbecRQMwi7OF6UgdUzY_NFwj7ZLdzpGCgd4eBumlZ4ujBYeqql1ygep_PcgrPi9lxTI42m8F4y9JGKcIbT9ff-5xlwVVhmWmhtDH4S5hIMJ8itpWwsIxHUdjhepcjhZMDsRXbOLVSRTXbtog-Br9c4xbRIcOLiQH-G_7aEJ3u0E7oawXynq63yIcftpRtgd6EimZLpS-H0X_KGc9nuC4f9siiHvxvoKZ-sxmhrzRNo4A-a40SWr76Lyz_Ze4x1Z9UNXcG39AM9Fs-QxOjesqC97eYvIkHs1c5MTBavAKnlANsyVBo6_jsbVuEw__liJEk3ApeZwtLdy-bGo",
    "refresh_token": "def502000527b1531373c067a740be9dff13e6684e1490803826b7ba7894b7dd964901e6f16602e6a5f61a4b632b92b982c957c5a2823eaaf880d059643bfdb00d0d67d1eb5711aae61e429392e380acf93e43d6fa939aaf0f16c910484186193540d5c4e7185a967432347af35b072e90299749d9b69647cdcd5f0a85fc0e1e983b72267684a699453a895cadae742de52f3092132a9041adb6a8badc7e8c29893074b2ea58e692d4fe1c886fca05eb421c206f639626a8ca7f75b6be7295fe4417a5bcaedc6d8b8e90c5c8828b5a465ab54fba82ce5dc3d914f2b3a62c67c8669d396130369e2b5dcaa3a2be6f69a5aa845e261f8f125a49fc8a0bf66e94f23aac20311c66082bedd84914ffce3ce40912a3442da639c69121830ac118d54b3221674157219ed7bb408074722f908647a12f7678d062c34d9ffb0f3af0364ff5f53c9d89bd43ca58eee2ae96d55f5f4d5dcec3270f5441afd8f2ef6f96ad150cb9511d391e51"
    "secret": "WeoE5N6olt5JRm9oYSdESgV0D1OQJaAbEod1QPVI"
}
```

2. Add `access_token` to request's header as normal: https://laravel.com/docs/5.5/passport#passing-the-access-token

3. Collect all params from query string and request body, then generate the payload string as the format `{METHOD} {path}?{all params in query string format}`. For example:

```
GET api/v1/singed-endpoint?b[0]=1&a=2&c[d]=3
```

4. Use the `secret` from the 1st step and the `payload` above to generate `signature` by the code below in Javascript:

```javascript

import CryptoJS from 'CryptoJS';
CryptoJS.HmacSHA256(payload, secret);
// Ex: CryptoJS.HmacSHA256('GET api/v1/singed-endpoint?b[0]=1&a=2&c[d]=3', 'yRP91wwCmq2KCkUdOZyrGkK5d6V11SAnrCnP9ogS');
// Result: 2ee2595b5f72e2e591ac3dbe95daf65c0d4acb6bb61d79d3316fd54576ec0669

// or

import crypto from 'crypto';
crypto.createHmac('sha256', secret).update(payload).digest('hex')
// Ex: crypto.createHmac('sha256', 'yRP91wwCmq2KCkUdOZyrGkK5d6V11SAnrCnP9ogS').update('GET api/v1/singed-endpoint?b[0]=1&a=2&c[d]=3').digest('hex')
// Result: 2ee2595b5f72e2e591ac3dbe95daf65c0d4acb6bb61d79d3316fd54576ec0669
```

5. Create a query param named `signature` with the value is the result of the 4th step above. For example, the query above will be come:

```
GET api/v1/singed-endpoint?b[0]=1&a=2&c[d]=3&signature=2ee2595b5f72e2e591ac3dbe95daf65c0d4acb6bb61d79d3316fd54576ec0669
```

6. Send that singed request to the server.

