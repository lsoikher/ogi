<?php
require_once(dirname(__FILE__).'/SGPopup.php');

class SGVideoPopup extends SGPopup {
	private $url;
	private $realUrl;
	private $videoOptions;

	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function setRealUrl($url)
	{
		$this->realUrl = $url;
	}

	public function getRealUrl()
	{
		return $this->realUrl;
	}

	public function setVideoOptions($options)
	{
		$this->videoOptions = $options;
	}

	public function getVideoOptions()
	{
		return $this->videoOptions;
	}

	public function integrateVideo($data)
	{
		$videoUrl = '';
		$options = json_decode($this->getVideoOptions(), true);
		$sgDefaultAutoplay = '';

		$parsed = parse_url($data);
		$videoHost = @$parsed['host'];
		parse_str(@$parsed['query'], $output);
		$videoParam = @$output['v'];

		$array1 = explode('?', @$output['v']);
		$isAutoplay = in_array("autoplay=1", $array1);
		if(!$isAutoplay && $options['video-autoplay'] == 'on') {
			$sgDefaultAutoplay = "?autoplay=1";
		}

		if ($videoHost == "www.youtube.com" ||  $videoHost == 'youtube.com' || $videoHost == 'www.youtube-nocookie.com' || $videoHost == 'youtu.be') {

			if($videoParam) {
				$videoUrl = 'https://www.youtube.com/embed/'.$videoParam;
			}
			else {
				$videoUrlArray = explode("/", $data);
				$videoUrl = 'https://www.youtube.com/embed/'.$videoUrlArray[count($videoUrlArray)-1];
			}
		}
		else if ($videoHost == 'vimeo.com' || $videoHost == 'player.vimeo.com') {
			$videoUrlArray = explode("/", $data);
			$videoUrl = 'https://player.vimeo.com/video/'.$videoUrlArray[count($videoUrlArray)-1];
		}
		else if ($videoHost == 'screen.yahoo.com') {
			$videoUrlArray = explode("/", $data);
			$sgYahooId = $videoUrlArray[count($videoUrlArray)-1];
			$sgYahooRegExp = '/\?format=embed$/';
			preg_match($sgYahooRegExp, $sgYahooId, $matches);
			$videoUrl = 'https://screen.yahoo.com/'.$sgYahooId;
			if ($matches) {
				$videoUrl = 'https://screen.yahoo.com/'.$sgYahooId;
			}
			else {
				$videoUrl = 'https://screen.yahoo.com/'.$sgYahooId."?format=embed";
			}
		}
		preg_match("/www.dailymotion.com/", $data, $getdaliyHost);
		if ($videoHost == 'www.dailymotion.com') {
			$videoUrlArray = explode("/", $data);
			$sgDailymotionId = $videoUrlArray[count($videoUrlArray)-1];
			$idPosition = strpos($sgDailymotionId, '_');
			$sgDailymotionEmbedId = substr($sgDailymotionId, 0, $idPosition);
			$videoUrl = '//www.dailymotion.com/embed/video/'.$sgDailymotionEmbedId;
		}
		else if (@$getdaliyHost[0]  == "www.dailymotion.com") {
			$sleshPos = strpos($data, "/");
			if ($sleshPos == 0) {
				$videoUrl = $data;
			}
			else {
				$videoUrl = '//'.$data;
			}
		}
		return $videoUrl.$sgDefaultAutoplay;
	}

	public static function create($data, $obj = null)
	{
		$obj = new self();
		$options = json_decode($data['options'], true);
		$videoOptions = $options['videoOptions'];

		$obj->setRealUrl($data['video']);
		$obj->setVideoOptions($videoOptions);
		$videoUrl = $obj->integrateVideo($data['video']);

		$obj->setUrl($videoUrl);


		parent::create($data, $obj);
	}

	public function save($data = array())
	{

		$editMode = $this->getId()?true:false;

		$res = parent::save($data);
		if ($res===false) {
			return false;
		}
		global $wpdb;

		$videoOptions = $this->getVideoOptions();

		if ($editMode) {
			$videoUrl = $this->integrateVideo($this->getUrl());

			$sql = $wpdb->prepare("UPDATE ". $wpdb->prefix ."sg_video_popup SET url=%s, real_url=%s, options=%s WHERE id=%d", $videoUrl, $this->getRealUrl(), $videoOptions, $this->getId());
			$res = $wpdb->query($sql);
		}
		else {

			$sql = $wpdb->prepare( "INSERT INTO ". $wpdb->prefix ."sg_video_popup (id, url, real_url, options) VALUES (%d, %s, %s, %s)", $this->getId(), $this->getUrl(), $this->getRealUrl(), $videoOptions);
			$res = $wpdb->query($sql);
		}
		return $res;
	}

	protected function setCustomOptions($id)
	{
		global $wpdb;
		$st = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."sg_video_popup WHERE id = %d", $id);
		$arr = $wpdb->get_row($st, ARRAY_A);

		$this->setUrl($arr['url']);
		$this->setRealUrl($arr['real_url']);
		$this->setVideoOptions($arr['options']);
	}

	protected function getExtraRenderOptions()
	{
		$vidoOtions = $this->getVideoOptions();
		$videoUrl = $this->getUrl();

		$videoOptions = json_decode($vidoOtions, true);

		return array('video'=>$videoUrl);
	}

	public  function render()
	{
		return parent::render();
	}
}