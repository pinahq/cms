<?php

namespace Pina\Modules\MailChimp;

use Pina\ModuleInterface;

class Mailchimp
{
	private $apiKey = '';
	private $url = '';

	public function __construct($apiKey)
	{
		if (empty($apiKey) || strpos($apiKey, '-') === false) {
			throw new \Exception('Invalid mailchimp api key');
		}

		$this->apiKey = $apiKey;

		$this->initUrl();
	}

	public function subscribe($listId, $email, $mergeFields)
	{
		$json = json_encode([
			'email_address' => $email,
			'status' => 'subscribed',
			'merge_fields' => [
				'FNAME'  => $mergeFields['firstname'],
				'LNAME' => $mergeFields['lastname'],
				'email' => $email,
				'phone' => $mergeFields['phone']
			]
		]);

		$memberUrl = $this->getMemberUrl($listId, $email);
		return $this->put($memberUrl, $json);
	}

	public function unsubscribe($listId, $email)
	{
		$json = json_encode(['status' => 'unsubscribed']);

		$memberUrl = $this->getMemberUrl($listId, $email);
		return $this->put($memberUrl, $json);
	}

	public function getListMembers($listId, $count, $offset)
	{
		$url = $this->getMembersUrl($listId, $count, $offset);
		return $this->get($url);
	}

	private function initUrl()
	{
		$dc = substr($this->apiKey, strpos($this->apiKey, '-') + 1);
		$this->url = 'https://'. $dc .'.api.mailchimp.com';
	}

	private function getMemberUrl($listId, $email)
	{
		$memberId = md5(strtolower($email));
		return $this->url .'/3.0/lists/'. $listId .'/members/'. $memberId;
	}

	private function getMembersUrl($listId, $count, $offset)
	{
		return $this->url .'/3.0/lists/'. $listId .'/members?count='. $count .'&offset='. $offset;
	}

	private function put($url, $json)
	{
		$ch = curl_init($url);

		$ch = $this->setCurlSettings($ch);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return json_decode($result, true);
	}

	private function get($url)
	{
		$ch = curl_init($url);

		$ch = $this->setCurlSettings($ch);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return json_decode($result, true);
	}

	private function setCurlSettings($ch)
	{
		curl_setopt($ch, CURLOPT_USERPWD, 'user:'. $this->apiKey);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		return $ch;
	}
}
