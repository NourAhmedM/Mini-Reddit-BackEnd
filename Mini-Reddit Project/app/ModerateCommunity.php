<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModerateCommunity extends Model
{
    protected $fillable = ['username', 'community_id'];
    public $timestamps = false; //so that doesn't expext time columns

    /**
     * this function checks if a specific user given its username moderates a specific community given its id.
     *
     * @param int    $community_id the id of the community
     * @param string $username     the username of the user
     *
     * @return bool true if the user moderates the community and false if not.
     */
    public static function checkExisting($community_id, $username)
    {
        $result = self::where('community_id', $community_id)->where('username', $username)->exists();

        return $result;
    }

    /**
     * [getModerators description].
     *
     * @param int $community_id [the id of the community]
     *
     * @return array [th moderators of specific community]
     */
    public static function getModerators($community_id)
    {
        $subscribed_communities = DB::select(" select u.username,u.photo_url
                                        from moderate_communities m,users u
                                        where (m.community_id='$community_id')&&(u.username=m.username)");

        return $subscribed_communities;
    }

    /**
     * this function creats a record in the database relation called 'moderate_community' givent the username of the moderator and the community id.
     *
     * @param int    $community_id the id of the community to be moderated by the user
     * @param string $username     the username of the moderator
     *
     * @return bool [ true if the creation succeeded and false if it faild ].
     */
    public static function store($community_id, $username)
    {
        try {
            self::create(['username' => $username, 'community_id' => $community_id]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * [numberOfModerators description].
     *
     * @param int $community_id community_id
     *
     * @return int number of moderators
     */
    public static function numberOfModerators($community_id)
    {
        $result = self::where('community_id', $community_id)->count();

        return $result;
    }

    /**
     * function to remove modirator.
     *
     * @param string $username
     * @param int    $community_id
     *
     * @return bool [true if deleted successfully , false if not].
     */
    public static function remove($username, $community_id)
    {
        $result = self::where('username', $username)->where('community_id', $community_id)->delete();

        return $result;
    }
}
