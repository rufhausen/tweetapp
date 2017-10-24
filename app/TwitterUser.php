<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwitterUser extends Model
{
    protected $fillable = [
        'twitter_handle',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'cached_since',
    ];

    public function mentions()
    {
        return $this->hasManyThrough(Tweet::class, TweetMention::class, 'mentioned_user_id', 'id', 'id', 'tweet_id');
    }

    public function tweets()
    {
        return $this->hasMany(Tweet::class, 'twitter_user_id');
    }

    public function user_mentions()
    {
        return $this->hasMany(TweetMention::class, 'twitter_user_id');
    }

}
