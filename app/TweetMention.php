<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TweetMention extends Model
{
    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'tweet_created_at',
    ];

    public function mentioned()
    {
        return $this->hasOne(TwitterUser::class, 'id', 'mentioned_user_id');
    }
}

