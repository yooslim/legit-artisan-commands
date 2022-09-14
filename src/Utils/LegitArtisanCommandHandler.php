<?php

namespace YOoSlim\LegitArtisanCommands\Utils;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use YOoSlim\LegitArtisanCommands\Models\ConsoleToken;
use YOoSlim\LegitArtisanCommands\Exceptions\TokenHasExpiredException;

class LegitArtisanCommandHandler
{
	private ?ConsoleToken $token = null;
	
	private ?Exception $exception = null;

	private bool $forceCommand = false;

	public function __construct()
	{
		$this->forceCommand = in_array(config('app.env'), config('legit-commands.ignored_envs', []));
	}

	/**
	 * Authentify user with token
	 * 
	 * @param  ?string  $token
	 * 
	 * @return LegitArtisanCommandHandler
	 */
	public function authentify(?string $token): LegitArtisanCommandHandler
	{
		if (!$this->forceCommand) {
			try {
				$this->token = ConsoleToken::with('user')->where('value', $token)->firstOrFail();
	
				if ($this->tokenHasExpired($this->token)) {
					$this->exception = new TokenHasExpiredException(trans('legit-artisan-commands::messages.token_has_expired'));
				}
			} catch (ModelNotFoundException $e) {
				$this->exception = new Exception(trans('legit-artisan-commands::messages.token_not_found'));
			}
		}

		return $this;
	}

	/**
	 * Checks if token is legit by verifying the expiration date
	 * 
	 * @return bool
	 */
	public function tokenIsLegit(): bool
	{
		return $this->forceCommand || ($this->token && !$this->tokenHasExpired($this->token));
	}

	/**
	 * Checks if token has expired
	 * 
	 * @return bool
	 */
	private function tokenHasExpired(ConsoleToken $token): bool
	{
		return now()->isAfter($token->expires_at);
	}

	/**
	 * Executes a callback function if token is legit
	 * 
	 * @param  callable  $callback
	 * 
	 * @return LegitArtisanCommandHandler
	 */
	public function isAuthorized(callable $callback): LegitArtisanCommandHandler
	{
		if ($this->tokenIsLegit()) $callback($this->token);

		return $this;
	}

	/**
	 * Executes a callback function if token is illegit
	 * 
	 * @param  callable  $callback
	 * 
	 * @return LegitArtisanCommandHandler
	 */
	public function isNotAuthorized(callable $callback): LegitArtisanCommandHandler
	{
		if (!$this->tokenIsLegit()) $callback($this->exception);

		return $this;
	}
}
