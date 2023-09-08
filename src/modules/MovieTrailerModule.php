<?php

namespace cinema\modules;

use cinema\utilities\Exception;
use cinema\utilities\Fetch;

/**
 * This class will fetch Youtube data and doesn't need to connect to the Cinema database
 */
class MovieTrailerModule {
  private $youtubeAPIKey;
  private const BASE_URL = 'https://www.googleapis.com/youtube/v3';

  public function __construct($apiKey) {
    $this->youtubeAPIKey = $apiKey;
  }

  /**
   * This function is related to Youtube API, which the client code does not need to know about
   */
  private function listYoutubeVideos(string $videoId) {
    if (!$videoId) {
      Exception::handleException(new \Exception('No video found', 400));
    }

    return Fetch::get(self::BASE_URL . "/videos?part=player&id=$videoId&key=$this->youtubeAPIKey");
  }

  public function search(string $keyword) {
    if (!isset($this->youtubeAPIKey) || !$this->youtubeAPIKey) {
      Exception::handleException(new \Exception('No Youtube API key is provided', 404));
    }
    if (!$keyword) {
      Exception::handleException(new \Exception('No keyword is provided', 404));
    }
    $result = Fetch::get(self::BASE_URL . "/search?part=snippet&maxResults=25&q=$keyword&key=$this->youtubeAPIKey");

    if (!$result || (is_array($result) && count($result) === 0)) {
      Exception::handleException(new \Exception('No result found', 400));
    }
    // Pick the most matched item - index 0
    $videoId = $result['items'][0]['id']['videoId'];
    return $this->listYoutubeVideos($videoId);
  }
}
