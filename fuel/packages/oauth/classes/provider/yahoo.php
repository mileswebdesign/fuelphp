<?php
/**
 * OAuth Yahoo Provider
 *
 * Documents for implementing Yahoo OAuth can be found at
 * <http://developer.yahoo.com/oauth/>.
 *
 * [!!] This class does not implement the Yahoo API. It is only an
 * implementation of standard OAuth with Yahoo as the service provider.
 *
 * @package    OAuth
 * @author     Fuel Development Team
 */

namespace OAuth;

class Provider_Yahoo extends Provider {

	public $name = 'yahoo';

	public $uid_key = 'xoauth_yahoo_guid';

	public function url_request_token()
	{
		return 'https://api.login.yahoo.com/oauth/v2/get_request_token';
	}

	public function url_authorize()
	{
		return 'https://api.login.yahoo.com/oauth/v2/request_auth';
	}

	public function url_access_token()
	{
		return 'https://api.login.yahoo.com/oauth/v2/get_token';
	}
	
	public function get_user_info(Consumer $consumer, Token $token)
	{		
		// Create a new GET request with the required parameters
		$request = Request::forge('resource', 'GET', 'http://social.yahooapis.com/v1/user/'.$token->uid.'/profile', array(
			'oauth_consumer_key' => $consumer->key,
			'oauth_token' => $token->access_token,
			'format' => 'json',
			'realm' => 'yahooapis.com',
		));

		// Sign the request using the consumer and token
		$request->sign($this->signature, $consumer, $token);

		$response = json_decode($request->execute());

		$user = $response->profile;
		
		// Create a response from the request
		return array(
			'uid' => $user->guid,
			//'nickname' => $user->username,  // Yahoo doesn't provide nickname
			'name' => $user->nickname,
			'location' => $user->location,
			'image' => $user->image->imageUrl,
			'description' => $user->bio,
			'urls' => array(
			  'Yahoo' => $user->profileUrl,
			),
		);
	}

} // End Provider_Yahoo