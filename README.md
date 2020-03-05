# Laravel PDO Emulation Control<br>[![Build Status](https://travis-ci.com/mpyw/laravel-pdo-emulation-control.svg?branch=master)](https://travis-ci.com/mpyw/laravel-pdo-emulation-control) [![Code Coverage](https://scrutinizer-ci.com/g/mpyw/laravel-pdo-emulation-control/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mpyw/laravel-pdo-emulation-control/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mpyw/laravel-pdo-emulation-control/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mpyw/laravel-pdo-emulation-control/?branch=master)

Temporarily enable/disable PDO prepared statement emulation.

## Requirements

- PHP: ^7.1
- Laravel: ^5.8 || ^6.0 || ^7.0

## Basic Usage

The default implementation is provided by `ConnectionServiceProvider`, however, **package discovery is not available**.
Be careful that you MUST register it in **`config/app.php`** by yourself.

```php
<?php

return [

    /* ... */

    'providers' => [
        /* ... */

        Mpyw\LaravelPdoEmulationControl\ConnectionServiceProvider::class,

        /* ... */
    ],

];
```

```php
<?php

use Illuminate\Support\Facades\DB;

// Temporarily enable PDO prepared statement emulation.
DB::emulated(function () {
    // Your code goes here
});

// Temporarily disable PDO prepared statement emulation.
// (Only if you've already configured your connection by options [PDO::ATTR_EMULATE_PREPARES => true])
DB::native(function () {
    // Your code goes here    
});
```

Note that `DB::getPdo()` `DB::getReadPdo()` are not always called even though these methods directly touch the `PDO` instances.
**Connections are lazily resolved as possible as they can.**
`PDO::setAttribute()` is called only after the `PDO` instance has been created and the socket connection to the database has been really established.

## Advanced Usage

You can extend Connection classes with `ControlsEmulation` trait by yourself.

```php
<?php

namespace App\Providers;

use App\Database\MySqlConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Connection::resolverFor('mysql', function (...$parameters) {
            return new MySqlConnection(...$parameters);
        });
    }
}
```

```php
<?php

namespace App\Database;

use Illuminate\Database\Connection as BaseMySqlConnection;
use Mpyw\LaravelPdoEmulationControl\ControlsEmulation;

class MySqlConnection extends BaseMySqlConnection
{
    use ControlsEmulation;
}
```
