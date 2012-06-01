<?php
/*
 * A class that provides methods to fetch the comment count(s) from the back-end. Its
 * intended use is to provide devs methods to "pre-fetch" the comment count and
 * build the page before it's rendered to the end user.
 * 
 * Please note that these calls are blocking and will delay rending of the page. If
 * that's a concern, please use the standard means of using the Javascript include.
 */
include_once("Http.php");

class Livefyre_CommentCount
{
	const MAX_FETCH_ATTEMPTS = 5;
	const DEFAULT_RETRY_DELAY = 500;
	const JS_PATH = "/api/v1.1/public/comments/ncomments/";
	
	private $network;
	private $siteId;
	
	private $fetchAttempts;
	
	/*
	 * Constructor
	 * 
	 * @param $network: Your network name as a string. E.g. example-network.fyre.co
	 */
	public function __construct($network, $siteId)
	{
		$this->network = $network;
		$this->siteId = $siteId;
		$this->fetchAttempts = 0;	
	}
	
	/*
	 * Get the comment count for one article.
	 * 
	 * @param id: The id of the article that you want the comment count for as a string.
	 * 
	 * @return The number of comments for an article as an integer.
	 */
	public function getCountForOne($id)
	{
		$ret = $this->getCommentCount(array($id));
		return $ret[$id]["total"];	
	}
	
	/*
	 * Get the comment count for many articles.
	 *
	 * @param ids: An array of string article ids to get the comment counts for.
	 * 
	 * @return A associative array where the keys are article ids and the values are the number of comments for that associated article.
	 * Keys are strings and values are integers.
	 */
	public function getCountForMany($ids)
	{
		$counts = $this->getCommentCount($ids);
		$ret = array();
		foreach ($ids as $id)
		{
			$ret[$id] = $counts[$id]["total"];
		}
		return $ret;
	}
	
	/*
	 * Make the request to get comment counts.
	 * 
	 * @param ids: A string array of article ids
	 * @param hash(optional): The calculated hash to be appended to the URL
	 * 
	 * @return An associative array of data received.
	 */
	private function getCommentCount($ids, $hash = "")
	{
		$this->fetchAttempts += 1;
		$http = new Livefyre_http();
		if ($hash === "")
		{
			$hash = $this->buildArticleHash($ids);
		}
		$url = $this->getEndpoint($hash);
		$resp = $http->request($url);
		$respObj = json_decode($resp["body"], true);
		
		if ($respObj["status"] === "ok")
		{
			$this->fetchAttempts = 0;
			return $respObj["data"][$this->siteId];
		}
		else if (($respObj["code"] == 503 || $respObj["code"] == 202) && $this->fetchAttempts < self::MAX_FETCH_ATTEMPTS)
		{
			sleep(($respObj["data"]["wait"] || DEFAULT_RETRY_DELAY)/1000);
			return $this->getCommentCount($ids, $hash);
		}	
	}
	 
	/*
	 * Build the required hash for the request.
	 * 
	 * @param ids: An array of article id(s) to include in the hash.
	 * 
	 * @return A base64 encoded string.
	 */
	private function buildArticleHash($ids)
	{
		$str = $this->siteId . ":";
		$len = count($ids);
		
		for($i = 0; $i < $len; $i++)
		{
			$str .= $ids[$i];
			if ($i + 1 < $len)
			{
				$str .= ",";
			} 
		}
		return base64_encode($str);
	}
	
	/*
	 * Build the endpoint for the request.
	 *
	 * @param hash: The hash created from the buildArticleHash function.
	 * 
	 * @return The url for the endpoint as a string.
	 */
	private function getEndpoint($hash)
	{
		return "http://bootstrap." . $this->network . self::JS_PATH . $hash . ".json";
	}
}
?>