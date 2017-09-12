<?php

namespace App;

use Carbon\Carbon;
use Kitano\Aktiv8me\ActivatesUsers;
use Illuminate\Database\Eloquent\Model;

/**
 * App\RegistrationToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Query\Builder|\App\RegistrationToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RegistrationToken whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RegistrationToken whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RegistrationToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RegistrationToken whereUserId($value)
 * @mixin \Eloquent
 */
class RegistrationToken extends Model
{
    use ActivatesUsers;

    /** @var array */
    protected $fillable = ['user_id', 'token'];

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'token';
    }

    /**
     * Relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    /**
     * Creates a token for a given user
     *
     * @param \App\User $user
     *
     * @return static|\App\User
     */
    public static function createFor(User $user)
    {
        return static::create([
            'user_id' => $user->id,
            'token' => ActivatesUsers::generateToken(),
        ]);
    }

    /**
     * Deletes all tokens for a given user
     *
     * @param int $uid  User ID
     *
     * @return bool|null
     * @throws \Exception
     */
    public static function deleteCode($uid)
    {
        return static::where('user_id', $uid)->delete();
    }

    /**
     * Finds tokens for a given user
     *
     * @param $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function findCodes($userId)
    {
        return static::where('user_id', $userId)->get();
    }

    /**
     * Finds a specific Token
     *
     * @param $code
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function findToken($code)
    {
        return static::where('token', $code)->first();
    }

    /**
     * @param $email
     *
     * @return \App\RegistrationToken|\App\User
     */
    public static function makeToken($email)
    {
        return static::createFor(\App\User::findByEmail($email));
    }

    /**
     * Updates a token for a given user
     *
     * @param \App\User $user
     *
     * @return \App\RegistrationToken|null|static
     */
    public static function updateFor(User $user)
    {
        $user_token = static::where('user_id', $user->id)->first();
        $user_token->token = ActivatesUsers::generateToken();
        $user_token->save();

        return $user_token;
    }
}
