<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    const EXPIRATION_TIME = 15;

    protected $fillable = [
      'code',
      'user_id',
      'used'
    ];

    public function __construct(array $attributes = [])
    {
        if (! isset($attributes['code'])){
            $attributes['code'] = $this->generateCode();
        }

        parent::__construct($attributes);
    }

    public function generateCode()
    {
        $code = mt_rand(1000, 9999);

        return $code;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *True if the token is not used nor expired
     *
     * @return bool
     */
    public function isValid()
    {
        return ! $this->isUsed() && ! $this->isExpired();
    }

    /**
     * Is the current token used
     *
     * @return bool
     */
    public function isUsed()
    {
        return $this->used;
    }

    public function isExpired()
    {
        return $this->created_at->diffInMinutes(Carbon::now()) > static::EXPIRATION_TIME;
    }

    public function sendCode()
    {
        if (! $this->user) {
            throw new \Exception("No user attached to this token.");
        }

        if (! $this->code) {
            $this->code = $this->generateCode();
        }
        /**
         *for this code to work, you need to connect the Twilio package
         */
        /*try {
            app('twilio')->messages->create($this->user->getPhoneNumber(),
                ['from' => env('TWILIO_NUMBER'), 'body' => "Your verification code is {$this->code}"]);
        } catch (\Exception $ex) {
            return false; //enable to send SMS
        }*/

        return true;
    }


}
