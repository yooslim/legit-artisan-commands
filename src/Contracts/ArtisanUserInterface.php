<?php

namespace YOoSlim\LegitArtisanCommands\Contracts;

interface ArtisanUserInterface
{
	/**
	 * Returns the user ID (the one used as a primary key)
	 * 
	 * @return int|string
	 */
	public function getUserId(): int|string;
}