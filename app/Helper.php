<?php

namespace App;
use Madcoda\Youtube\Facades\Youtube;
use Illuminate\Support\Collection;

class Helper
{
    // https://github.com/madcoda/php-youtube-api/pull/54
	public static function get_all_items_by_youtube_playlist_id($playlistId)  :Collection{
	    $playlistItems = [];

	    $params = [
	        'playlistId' => $playlistId,
	        'part' => 'id, snippet, contentDetails, status',
	    ];

	    do {
	        $raw = Youtube::getPlaylistItemsByPlaylistIdAdvanced($params, true);

	        if ($raw['results'] !== false) {
	            $playlistItems = array_merge($playlistItems, $raw['results']);
	        }

	        $params['pageToken'] = $raw['info']['nextPageToken'] ?? null;
	    } while ($params['pageToken'] !== null);

	    return collect($playlistItems);
	}

}
