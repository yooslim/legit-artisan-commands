<?php

namespace YOoSlim\LegitArtisanCommands\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ConsoleToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'value',
		'expires_at',
    ];

	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Auto fill the value attribute
     *
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function (ConsoleToken $model) {
            $model->value = Str::random(config('legit-artisan-commands.token.size', 8));
            $model->expires_at = now()->addSeconds(config('legit-artisan-commands.token.lifetime', 60));
        });
    }

	/**
	 * Create relationship with users table
	 */
	public function user(): BelongsTo
    {
        return $this->belongsTo(config('legit-artisan-commands.relationships.user'), 'user_id');
    }

	/**
     * Define setter and getter for value.
     *
     * @return Attribute
     */
    public function value(): Attribute
	{
        return Attribute::make(
            get: fn (string $value) => $value,
			set: function (string $value) {
				if (empty($value)) {
					throw new InvalidArgumentException();
				}

				return $value;
			},
        );
    }
}
