<?php

namespace App\Http\Controllers;

use App\Services\TwitterApi;
use App\Tweet;
use App\TweetMention;
use App\TwitterUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function __construct(TwitterApi $twitterApi)
    {
        $this->twitterApi = $twitterApi;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $mentions    = [];
        $twitterUser = [];

        if (session('twitter_user_id')) {
            $id          = session('twitter_user_id');
            $twitterUser = TwitterUser::findOrFail($id);
            $mentions    = TweetMention::groupBy('mentioned_user_id')
                ->select('*', \DB::raw('count(*) as total'))
                ->where('twitter_user_id', $id)
                ->orderBy('total', 'DESC')
                ->get();
        }

        return view('home', compact('mentions', 'twitterUser'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postMentions(Request $request)
    {
        $twitterUser = TwitterUser::firstOrCreate([
            'twitter_handle' => $request->input('twitter_handle'),
        ]);


        //if cache date/time is null of more than an hour, do api update
        if (!$twitterUser->cached_since || Carbon::create()->subHour()->gt(Carbon::parse($twitterUser->cached_since))) {
            $this->handleApiUpdate($twitterUser);
        }

        return redirect()->to('/')->with('twitter_user_id', $twitterUser->id);
    }

    public function getProfile($twitterHandle)
    {
        $twitterUser = TwitterUser::where('twitter_handle', $twitterHandle)->first();

        if (!$twitterUser) {
            abort(404);
        }
        if (empty($twitterUser->profile)) {
            $twitterUser->profile = json_encode($this->twitterApi->getUserByScreenName($twitterHandle));
            $twitterUser->save();
        }
        $profile = json_decode($twitterUser->profile, true);

        return view('profile', compact('profile'));

    }

    /**
     * @param TwitterUser $twitterUser
     * @param TwitterApi $twitterApi
     * @return mixed
     */
    private function handleApiUpdate(TwitterUser $twitterUser)
    {
        //reset the cache
        $twitterUser->cached_since = Carbon::create()->toDateTimeString();
        $twitterUser->save();

        //get the statuses
        $statuses = $this->twitterApi->getUserTimeLine($twitterUser->twitter_handle);

        if (isset($statuses['error'])) {
            return redirect()->back()->withWarning($statuses['error']);
        }

        foreach ($statuses as $status) {
            $tweet = Tweet::firstOrCreate(
                [
                    'tweet_id' => $status['id'],
                ],
                [
                    'twitter_user_id'  => $twitterUser->id,
                    'text'             => $status['text'],
                    'tweet_created_at' => Carbon::parse($status['created_at']),
                ]
            );

            if (!empty($status['entities']['user_mentions'])) {
                foreach ($status['entities']['user_mentions'] as $mention) {
                    $mentioned = TwitterUser::firstOrCreate([
                        'twitter_handle' => $mention['screen_name'],
                    ]);

                    TweetMention::firstOrCreate([
                        'twitter_user_id'   => $twitterUser->id,
                        'mentioned_user_id' => $mentioned->id,
                        'tweet_id'          => $tweet->id,
                    ]);
                }
            }
        }
    }
}
