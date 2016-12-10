<?php
require_once(dirname(__FILE__).'/SGPopup.php');

class SGSocialPopup extends SGPopup
{
	public $socialContent;
	public $buttons;
	public $socialOptions;
	public $options;

	function __construct() {

	}

	public function renderScripts($themeType = 'classic')
	{
		wp_register_script('jssocials.min', SG_APP_POPUP_URL . '/javascript/jssocials.min.js');
		wp_enqueue_script('jssocials.min');
		wp_register_style('font_awesome', SG_APP_POPUP_URL . "/style/jssocial/font-awesome.min.css");
		wp_enqueue_style('font_awesome');
		wp_register_style('jssocials_main_css', SG_APP_POPUP_URL . "/style/jssocial/jssocials.css");
		wp_enqueue_style('jssocials_main_css');
		wp_register_style('jssocials_theme_'.$themeType, SG_APP_POPUP_URL .'/style/jssocial/jssocials-theme-'.$themeType.'.css');
		wp_enqueue_style('jssocials_theme_'.$themeType);
	}

	public function setSocialContent($socialContent)
	{
		$this->socialContent = $socialContent;
	}

	public function getSocialContent()
	{
		return $this->socialContent;
	}

	public function setButtons($buttons)
	{
		$this->buttons = $buttons;
	}

	public function getButtons()
	{
		return $this->buttons;
	}

	public function setSocialOptions($socialOptions)
	{
		$this->socialOptions = $socialOptions;
	}

	public function getSocialOptions()
	{
		return $this->socialOptions;
	}

	public function setOptions($options)
	{
		$this->options = $options;
	}

	public function getOptions()
	{
		return $this->options;
	}

	public static function create($data, $obj = null)
	{
		$obj = new self();

		$options = json_decode($data['options'],true);
		$socialContent = $data['social'];
		$socialButtons = $options['socialButtons'];
		$socialOptions = $options['socialOptions'];

		$obj->setSocialContent($socialContent);
		$obj->setButtons($socialButtons);
		$obj->setSocialOptions($socialOptions);

		return parent::create($data, $obj);
	}

	public function save($data = array())
	{

		$editMode = $this->getId()?true:false;

		$res = parent::save($data);
		if ($res===false) return false;
		$socialContent = $this->getSocialContent();
		$buttons = $this->getButtons();
		$socialOptions = $this->getSocialOptions();
		global $wpdb;
		if ($editMode) {
			$socialContent = stripslashes($socialContent);
			$sql = $wpdb->prepare("UPDATE ". $wpdb->prefix ."sg_social_popup SET socialContent=%s, buttons=%s,socialOptions=%s WHERE id=%d",$socialContent,$buttons,$socialOptions,$this->getId());
			$res = $wpdb->query($sql);
		}
		else {

			$sql = $wpdb->prepare( "INSERT INTO ". $wpdb->prefix ."sg_social_popup (id, socialContent, buttons, socialOptions) VALUES (%d,%s,%s,%s)",$this->getId(),''.$socialContent.'',''.$buttons.'',''.$socialOptions.'');
			$res = $wpdb->query($sql);
		}
		return $res;
	}

	protected function setCustomOptions($id)
	{
		global $wpdb;
		$st = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."sg_social_popup WHERE id = %d",$id);
		$arr = $wpdb->get_row($st,ARRAY_A);
		$this->setSocialContent($arr['socialContent']);
		$this->setButtons($arr['buttons']);
		$this->setSocialOptions($arr['socialOptions']);
	}

	protected function getExtraRenderOptions()
	{
		$plain = 'classic';
		$options = json_decode($this->getOptions(), true);
		$socialButtons = json_decode($this->getButtons(), true);
		$socialOptions = json_decode($this->getSocialOptions(), true);
		$plain = $socialOptions['sgSocialTheme'];
		$this->renderScripts($plain);
		$socialContent = $this->getSocialContent();
		
		$fbShareLabel = $socialOptions['fbShareLabel'];
		$sgTwitte = @$socialOptions['sgTwitte'];
		$sgMailSubject = $socialOptions['sgMailSubject'];
		$sgMailLable = $socialOptions['sgMailLable'];
		$sgGoogLelabel = $socialOptions['googLelabel'];
		$sgLindkinLabel = $socialOptions['lindkinLabel'];
		$sgSocialButtonsSize = $socialOptions['sgSocialButtonsSize'];
		$sgSocialShareCount = $socialOptions['sgSocialShareCount'];
		$sgPinterestLabel = $socialOptions['pinterestLabel'];
		$sgSocialLabel = $socialOptions['sgSocialLabel'];
		$sgShareUrl = $socialOptions['sgShareUrl'];
		$shareUrlType = $socialOptions['shareUrlType'];
		$sgTwitte = @$socialOptions['sgTwitte'];
		$sgRoundButton = $socialOptions['sgRoundButton'];
		$fbStatus = $socialButtons['sgFbStatus'];
		$sgEmailStatus = $socialButtons['sgEmailStatus'];
		$sgTwitterStatus = $socialButtons['sgTwitterStatus'];
		$sgTwitterLabel = $socialOptions['twitterLabel'];
		$sgLinkedinStatus = $socialButtons['sgLinkedinStatus'];
		$sgGoogleStatus = $socialButtons['sgGoogleStatus'];
		$sgPinterestStatus = $socialButtons['sgPinterestStatus'];
		$pushToBottom = $options['pushToBottom'];
		$socialContent .= '<div id="share-btns-container" class="js-sg-push-on-bottom"  style="font-size: '.$sgSocialButtonsSize.'px"></div>';
		if ($sgSocialShareCount === true) {
			$sgSocialShareCount = true;
		}
		if ($sgSocialShareCount === false) {
			$sgSocialShareCount = false;
		}
		if ($sgSocialShareCount === 'inside') {
			$sgSocialShareCount = 'inside';
		}
		if ($shareUrlType == 'activeUrl') {
			$sgShareUrl = '';
		}
		$socialContent.= "<style type=\"text/css\">
					.sg-push-to-bottom {
						position: absolute !important;
						bottom: 2px !important;
						left: 0 !important;
						right: 0 !important
					}";
		if($sgRoundButton == 'on') {
			$socialContent.= ".jssocials-share-link { border-radius: 50%; }";
		}
		$socialContent.= "#share-btns-container {text-align: center}";
		$socialContent.= "</style>";

		$socialParams = array(
			"sgEmailStatus" => $sgEmailStatus,
			"sgMailLable" => $sgMailLable,
			"sgEmailStatus" => $sgEmailStatus,
			"sgTwitterStatus" => $sgTwitterStatus,
			"sgTwitterLabel" => $sgTwitterLabel,
			"fbStatus" => $fbStatus,
			"fbShareLabel" => $fbShareLabel,
			"sgGoogleStatus" => $sgGoogleStatus,
			"sgGoogLelabel" => $sgGoogLelabel,
			"sgLinkedinStatus" => $sgLinkedinStatus,
			"sgLindkinLabel" => $sgLindkinLabel,
			"sgPinterestStatus" => $sgPinterestStatus,
			"sgPinterestLabel" => $sgPinterestLabel,
			"sgSocialLabel" => $sgSocialLabel,
			"sgSocialShareCount" => $sgSocialShareCount,
			"sgShareUrl" => $sgShareUrl,
			"pushToBottom" => $pushToBottom

		);

		wp_register_script('sg_social_front', SG_APP_POPUP_URL . '/javascript/sg_social_front.js');
		wp_localize_script('sg_social_front', 'SgSocialParams', $socialParams);
		wp_enqueue_script('sg_social_front');

		//sg_social_front.js

		$socialContent.= "<script type=\"text/javascript\">
		
		</script>";
		$hasShortcode = $this->hasPopupContentShortcode($socialContent);
		
		if($hasShortcode) {

			$socialContent = $this->improveContent($socialContent);
			$socialContent = base64_encode($socialContent);
			/*Add this part of code right into the page to escape conflicts with shortcodes init functionlity*/
			$currentPopupContent = "<div id=\"sg-popup-content-".$this->getId()."\" style=\"display: none;\">&nbsp;<div id=\"sgpb-all-content-".$this->getId()."\">".$socialContent."</div></div>";
			
			/*Append to body for shortcode break to new line*/
			echo "<script type=\"text/javascript\">
				jQuery(document).ready(function() {
					jQuery('body').append(jQuery('".$currentPopupContent."'));
				});
			</script>";
			$socialContent = ' ';
		}
		else {
			$socialContent = base64_encode($socialContent);
		}
		return array('html'=>$socialContent);
	}

	public  function render() {
		return parent::render();
	}
}
