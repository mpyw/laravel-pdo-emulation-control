# Laravel PDO Emulation Control<br>[![Build Status](https://github.com/mpyw/laravel-pdo-emulation-control/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/mpyw/laravel-pdo-emulation-control/actions) [![Coverage Status](https://coveralls.io/repos/github/mpyw/laravel-pdo-emulation-control/badge.svg?branch=master)](https://coveralls.io/github/mpyw/laravel-pdo-emulation-control?branch=master)

Temporarily enable/disable PDO prepared statement emulation.

## Requirements

| Package | Version                               | Mandatory |
|:--------|:--------------------------------------|:---------:|
| PHP     | <code>^8.2</code>                     |     ✅     |
| Laravel | <code>^11.0 &#124;&#124; ^12.0</code> |     ✅     |
| PHPStan | <code>&gt;=2.0</code>                 |           |

> [!NOTE]
> Older versions have outdated dependency requirements. If you cannot prepare the latest environment, please refer to past releases.

## Installing

```
composer require mpyw/laravel-pdo-emulation-control
```

## Basic Usage

> [!IMPORTANT]
> The default implementation is provided by `ConnectionServiceProvider`, however, **package discovery is not available**.
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

> [!IMPORTANT]
> Note that `DB::getPdo()` `DB::getReadPdo()` are not always called even though these methods directly touch the `PDO` instances.
> **Connections are lazily resolved as possible as they can.**
> `PDO::setAttribute()` is called only after the `PDO` instance has been created and the socket connection to the database has been really established.

## Advanced Usage

> [!TIP]
> You can extend Connection classes with `ControlsEmulation` trait by yourself.

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
