# teambition-api
Teambition OpenAPI library for laravel
## Features

- [x] Basic Client
- [x] Laravel Support
- [ ] Laravel OAuth2 Support
- [ ] Request / Response Define

## Installation

just run the following command in your laravel project
```bash
composer require stackboom/teambition-api
```

put your teambition api key in your [.env](./.env.example) file
```dotenv
TEAMBITION_APP_ID=your app id
TEAMBITION_APP_SECRET=your app secret

# optional
TEAMBITION_API_HOST=https://open.teambition.com
```

## Example

```php
use Stackboom\Teambition\Laravel\TeamBition;

# ...
$user_info = TeamBition::post('/api/oauth/userInfo',[
    'userAccessToken'=>'your user access token',
]);
# ...
$orgId = 'your org id';

$orgInfo = TeamBition::get('/api/org/info',[
    'orgId'=>$orgId,
],[
    'X-Tenant-Id'=>$orgId,
    'X-Tenant-Type'=>'organization',
]);
# ...
$members = TeamBition::getPaged('/api/org/member/list',[
    'orgId'=>$orgId,
],[
    'X-Tenant-Id'=>$orgId,
    'X-Tenant-Type'=>'organization',
]);
# ...

```

## Reference
- [Teambition OpenAPI](https://open.teambition.com)
