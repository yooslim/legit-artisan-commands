# Introduction
This package allows you as a developer to restrict who can and cannot run artisan commands, especially in a production environment. For example, a user (admin) should be allowed to run commands only if he has the right role/permission.

# How it works ?
An artisan user will be provided with a token in order to use it as an option when running commands. This token has a configurable size and lifetime. The user can use this token as much as he wants until it expires or is revoked.

In order to get this token, an artisan user must first perform an authentication within the console, if the authentication is successful, the user will be prompted with the token, otherwise, a warning message will be displayed.

The logic of authentication is customizable, the developer can put in place his own validation rules. For exemple, one would want to authorize a user only if he has an "admin" role, an other one would check if he has the right permissions, another one would fetch an active directory or external authentication service, etc.

# How to implement it !
A few steps to put this in place.

### Install the package
`composer require yooslim/legit-artisan-commands`

### Publish the vendor configuration file
`php artisan vendor:publish --provider="YOoSlim\LegitArtisanCommands\Providers\LegitCommandsServiceProvider"`

### Edit configuration file
* **Token lifetime**: The console token lifetime in seconds.
* **Token size**: The number of caracters to be generated (must be less than 255).
* **Environments to be ignored**: No need to waste our time with authentication in local environments, so it possible to ignore a set of environments.
* **User model relationship**: The model name (namespace included) of the user entity.

### Run migrations
`php artisan migrate`

### Add the ArtisanUserInterface to the user model
```
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use YOoSlim\LegitArtisanCommands\Contracts\ArtisanUserInterface;

class User extends Authenticatable implements ArtisanUserInterface
{
    /**
     * Returns the user ID (the one used as a primary key)
     * 
     * @return int|string
     */
    public function getUserId(): int|string
    {
        return $this->id;
    }
}
```
### Customize your authentication logic in AppServiceProvider.php
```
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use YOoSlim\LegitArtisanCommands\Utils\ArtisanAuthenticationHandler;
use YOoSlim\LegitArtisanCommands\Contracts\ArtisanUserInterface;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ArtisanAuthenticationHandler::localUserAuthentication(function(string $username, string $password): ?ArtisanUserInterface {
            $user = User::where('email', $username)->first();

            if ($user && Hash::check($password, $user->password) && $user->hasRole('admin')) return $user;

            return null;
        });
    }
}
```

### Finally, edit your artisan command
There are two main things to edit in your command :
1. Add the **LegitArtisanCommandSignature** trait, it will edit your command signature by appending the --token option part.
2. Wrap your original command inside the **isAuthorized** callback function.
```
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use YOoSlim\LegitArtisanCommands\Utils\Traits\LegitArtisanCommandSignature;
use YOoSlim\LegitArtisanCommands\Facades\LegitArtisanCommand;
use YOoSlim\LegitArtisanCommands\Models\ConsoleToken;

class FilesPurgeCommand extends Command
{
    use LegitArtisanCommandSignature;

    /* ------- */

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        LegitArtisanCommand::authentify($this->option('token'))
            ->isAuthorized(function (?ConsoleToken $token) {
                // The rest of your command
            })->isNotAuthorized(function ($exception) {
                $this->error($exception->getMessage());
            });
    }
```
# How to use it !
1. First, request a token by providing your credentials.

`php artisan console:authentication --username="admin@domain.com"`

This will prompt a random token.

2. Then, whenever you use a protected artisan command, include the --token option.

`php artisan MyCommand:MyAction --token="*"`

# Enjoy :D
Please, let me know if something is ambiguous, incomprehensible or wrong. I would be glad to clarify or fix it.
