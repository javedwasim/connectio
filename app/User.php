<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read bool $verified
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\RegistrationToken[] $codes
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 *
 * @method static \Illuminate\Database\Query\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRememberToken($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * Aditional attributes not persisted on the table
     *
     * @var array
     */
    protected $appends = [
        'verified'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Relationship
     *
     * Function name should be 'tokens', but 'codes' was kept
     * to reuse some code I already had.
     * You are welcome to refactor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function codes()
    {
        return $this->hasMany(\App\RegistrationToken::class)->orderBy('created_at', 'DESC');
    }

    /**
     * @param $email
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static|\App\User
     */
    public static function findByEmail($email)
    {
        return static::whereEmail($email)->first();
    }

    /**
     * @param $email
     *
     * @return \App\RegistrationToken[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public static function getTokens($email)
    {
        return static::findByEmail($email)->codes;
    }

    /**
     * @return bool
     */
    public function getVerifiedAttribute()
    {
        return (bool) ! $this->codes->count();
    }
}
