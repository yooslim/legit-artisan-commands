<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Ignored environements
	|--------------------------------------------------------------------------
	|
	| Developers can define environments that don't require authentication in 
	| order to run console commands.
	| Example, in development environment, there is no need to make authentication
	| mandatory for console users.
	|
	*/
	'ignored_envs' => [
		'local',
		'development',
	],

	/*
	|--------------------------------------------------------------------------
	| Token lifetime
	|--------------------------------------------------------------------------
	|
	| When the user performs a successful authentication, a random token is 
	| created and stored in database in order for user to use it later when
	| he runs console commands.
	|
	*/
	'token' => [
		'lifetime' => 600,
		'size' => 16,
	],

	/*
	|--------------------------------------------------------------------------
	| Model relationships
	|--------------------------------------------------------------------------
	|
	| The user model with whom the token model will be associated.
	|
	*/
	'relationships' => [
		'user' => 'App\Models\User',
	]
];
