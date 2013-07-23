<?php

	/**
	 * Twitter feed which uses twitteroauth for authentication
	 *
	 * @version	1.0
	 * @author	Andrew Biggart
	 * @link	https://github.com/andrewbiggart/latest-tweets-php-o-auth/
	 *
	 * Notes:
	 * Caching is employed because Twitter only allows their RSS and json feeds to be accesssed 150
	 * times an hour per user client.
	 * --
	 * Dates can be displayed in Twitter style (e.g. "1 hour ago") by setting the
	 * $twitter_style_dates param to true.
	 *
	 * You will also need to register your application with Twitter, to get your keys and tokens.
	 * You can do this here: (https://dev.twitter.com/).
	 *
	 * Don't forget to add your username to the bottom of the script.
	 *
	 * Credits:
	 ***************************************************************************************
	 * Initial script before API v1.0 was retired
	 * http://f6design.com/journal/2010/10/07/display-recent-twitter-tweets-using-php/
	 *
	 * Which includes the following credits
	 * Hashtag/username parsing based on: http://snipplr.com/view/16221/get-twitter-tweets/
	 * Feed caching: http://www.addedbytes.com/articles/caching-output-in-php/
	 * Feed parsing: http://boagworld.com/forum/comments.php?DiscussionID=4639
	 ***************************************************************************************
	 *
	 ***************************************************************************************
	 * Authenticating a User Timeline for Twitter OAuth API V1.1
	 * http://www.webdevdoor.com/php/authenticating-twitter-feed-timeline-oauth/
	 ***************************************************************************************
	 *
	 ***************************************************************************************
	 * Twitteroauth which has been used for the authentication process
	 * https://github.com/abraham/twitteroauth
	 ***************************************************************************************
	 *
	 *
	**/

	// Session start
	session_start();

	// Set timezone. (Modify to match your timezone) If you need help with this, you can find it here. (http://php.net/manual/en/timezones.php)

	// Require TwitterOAuth files. (Downloadable from : https://github.com/abraham/twitteroauth)
	require_once("twitteroauth/twitteroauth/twitteroauth.php");

	// Function to authenticate app with Twitter.
	function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
	  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
	  return $connection;
	}

	// Function to display the latest tweets.
	/** get_latest_tweets
	 *
	 * @param array $params
	 * @return string
	 */
	function get_latest_tweets($params) {

		// Function parameters.
		$defaults = array(
			'twitter_user_id'     => '',
			'tweets_to_display'   => 5,               // Number of tweets you would like to display. (Default : 5)
			'ignore_replies'      => false,           // Ignore replies from the timeline. (Default : false)
			'include_rts'         => false,           // Include retweets. (Default : false)

			'twitter_wrap_open'   => '<ul class="home-tweets-ul">',
			'twitter_wrap_close'  => '</ul>',

			'tweet_wrap_open'     => '<li><p class="home-tweet-tweet">',
			'tweet_wrap_close'    => '</p></li>',

			'meta_wrap_open'      => '<br/><span class="home-tweet-date">',
			'meta_wrap_close'     => '</span>',

			'date_format'         => 'g:i A M jS',    // Date formatting. (http://php.net/manual/en/function.date.php)
			'twitter_style_dates' => true,            // Twitter style days. [about an hour ago] (Default : true)
			'timezone'            => 'America/New_York',

			'cache_file'          => './tweets.txt',  // Change this to the path of your cache file. (Default : ./tweets.txt)
			'cachetime'           => 180,             // Seconds to cache feed (Default : 1 minute).

			// Twitter keys (You'll need to visit https://dev.twitter.com and register to get these.
			'consumerkey'         => "xxxxxxxxxxxxxxxxxxxxxxxxxxx",
			'consumersecret'      => "xxxxxxxxxxxxxxxxxxxxxxxxxxx",
			'accesstoken'         => "xxxxxxxxxxxxxxxxxxxxxxxxxxx",
			'accesstokensecret'   => "xxxxxxxxxxxxxxxxxxxxxxxxxxx"
		);

		$params = array_merge($defaults,$params);

		date_default_timezone_set($params['timezone']);

		// Time that the cache was last updtaed.
		$cache_file_created  = ((file_exists($params['cache_file']))) ? filemtime($params['cache_file']) : 0;

		// A flag so we know if the feed was successfully parsed.
		$tweet_found         = false;

		// Show cached version of tweets, if it's less than $cachetime.
		if (time() - $params['cachetime'] < $cache_file_created) {
	 		$tweet_found = true;
			// Display tweets from the cache.
			readfile($params['cache_file']);
		} else {

		// Cache file not found, or old. Authenticate app.
		$connection = getConnectionWithAccessToken($params['consumerkey'], $params['consumersecret'], $params['accesstoken'], $params['accesstokensecret']);

			if($connection){
				// Get the latest tweets from Twitter
 				$get_tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$params['twitter_user_id']."&count=".$params['tweets_to_display']."&include_rts=".$params['include_rts']);

				// Error check: Make sure there is at least one item.
				if (count($get_tweets)) {

					// Define tweet_count as zero
					$tweet_count = 0;

					// Start output buffering.
					// ob_start();

					// Open the twitter wrapping element.
					$twitter_html = $params['twitter_wrap_open'];

					// Iterate over tweets.
					foreach($get_tweets as $tweet) {

						// If we are not ignoring replies, or tweet is not a reply, process it.
						if ($params['ignore_replies']==false){

							$tweet_found = true;
							$tweet_count++;
 							$tweet_desc = $tweet->text;
							// Add hyperlink html tags to any urls, twitter ids or hashtags in the tweet.
							$tweet_desc = preg_replace('/(https?:\/\/[^\s"<>]+)/','<a href="$1" target="_blank">$1</a>',$tweet_desc);
							$tweet_desc = preg_replace('/(^|[\n\s])@([^\s"\t\n\r<:]*)/is', '$1<a href="http://twitter.com/$2" target="_blank">@$2</a>', $tweet_desc);
							$tweet_desc = preg_replace('/(^|[\n\s])#([^\s"\t\n\r<:]*)/is', '$1<a href="http://twitter.com/search?q=%23$2" target="_blank">#$2</a>', $tweet_desc);

 							// Convert Tweet display time to a UNIX timestamp. Twitter timestamps are in UTC/GMT time.
							$tweet_time = strtotime($tweet->created_at);
 							if ($params['twitter_style_dates']){
								// Current UNIX timestamp.
								$current_time = time();
								$time_diff = abs($current_time - $tweet_time);
								switch ($time_diff)
								{
									case ($time_diff < 60):
										$display_time = $time_diff.' seconds ago';
										break;
									case ($time_diff >= 60 && $time_diff < 3600):
										$min = floor($time_diff/60);
										$display_time = $min.' minutes ago';
										break;
									case ($time_diff >= 3600 && $time_diff < 86400):
										$hour = floor($time_diff/3600);
										$display_time = 'about '.$hour.' hour';
										if ($hour > 1){ $display_time .= 's'; }
										$display_time .= ' ago';
										break;
									default:
										$display_time = date($params['date_format'],$tweet_time);
										break;
								}
 							} else {
 								$display_time = date($params['date_format'],$tweet_time);
 							}

							// Render the tweet.
							$twitter_html .= $params['tweet_wrap_open'].html_entity_decode($tweet_desc).$params['meta_wrap_open'].'<a href="http://twitter.com/'.$params['twitter_user_id'].'">'.$display_time.'</a>'.$params['meta_wrap_close'].$params['tweet_wrap_close'];

						}

						// If we have processed enough tweets, stop.
						if ($tweet_count >= $params['tweets_to_display']){
							break;
						}

					}

					// Close the twitter wrapping element.
					$twitter_html .= $params['twitter_wrap_close'];
					// echo $twitter_html;

					// Generate a new cache file.
					$file = fopen($params['cache_file'], 'w');

					// Save the contents of output buffer to the file, and flush the buffer.
					fwrite($file, $twitter_html);
					fclose($file);
					// ob_end_flush();

					return $twitter_html;

				}

			}

		}

	}

?>
