<?php

namespace YOoSlim\LegitArtisanCommands\Utils;

use YOoSlim\LegitArtisanCommands\Contracts\ArtisanUserInterface;
use Illuminate\Support\Facades\Hash;

class ArtisanAuthenticationHandler
{
	private static $callback = null;

	/**
	 * User authentication logic
	 * 
	 * Developer is supposed to override this method in order to apply his own 
	 * application authentication & authorization logic.
	 * 
	 * Must return the user (or some class that implements ArtisanUserInterface) if authenticated, 
	 * otherwise, return null
	 * 
	 * @param  string  $username
	 * @param  string  $password
	 * 
	 * @return ?ArtisanUserInterface
	 */
	public static function authenticateUser(string $username, string $password): ?ArtisanUserInterface
	{
		if (!is_callable(static::$callback)) {
			$user = app(config('legit-commands.relationships.user'))::where('email', $username)->first();

			if ($user && Hash::check($password, $user->password)) return $user;
	
			return null;
		} else {
			return call_user_func(static::$callback, $username, $password);
		}
	}

	/**
	 * Allows developer to define his own authentication logic by calling this method
	 * in AppServiceProvider and define a callback function that returns :
	 * 
	 * Case of success: ArtisanUserInterface based object
	 * Case of failure: null
	 * 
	 * @return void
	 */
	public static function localUserAuthentication(?callable $callback): void
	{
		static::$callback = $callback;
	}
}