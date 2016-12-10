<?php
require_once(dirname(__FILE__).'/SGPopup.php');

class SGSubscriptionPopup extends SGPopup
{
	public $content;
	public $subscriptionOptions;
	public $title;

	function __construct()
	{

	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setSubscriptionOptions($options)
	{
		$this->subscriptionOptions = $options;
	}

	public function getSubscriptionOptions()
	{
		return $this->subscriptionOptions;
	}

	public function setSubscriptionTitle($title)
	{
		$this->title = $title;
	}

	public function getSubscriptionTitle()
	{
		return $this->title;
	}

	public static function create($data, $obj = null)
	{
		$obj = new self();

		$title = $data['title'];
		$options = json_decode($data['options'], true);
		$subscriptionOptions = $data['subscriptionOptions'];
		unset($data['subscriptionOptions']);

		$obj->setContent($data['sg_subscription']);
		$obj->setSubscriptionOptions($subscriptionOptions);
		$obj->setSubscriptionTitle($title);

		return parent::create($data, $obj);
	}

	public function save($data = array())
	{

		$editMode = $this->getId()?true:false;

		$res = parent::save($data);
		if ($res===false) return false;

		$content = $this->getContent();
		$options = $this->getSubscriptionOptions();

		global $wpdb;
		if ($editMode) {
			$content = stripslashes($content);
			$sql = $wpdb->prepare("UPDATE ".$wpdb->prefix."sg_subscription_popup SET content=%s, options=%s WHERE id=%d", $content, $options, $this->getId());
			$res = $wpdb->query($sql);
		}
		else {
			$sql = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."sg_subscription_popup (id, content, options) VALUES (%d, %s, %s)", $this->getId(), $content, $options);
			$res = $wpdb->query($sql);
		}
		return $res;
	}

	protected function setCustomOptions($id)
	{
		global $wpdb;
		$st = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix."sg_subscription_popup WHERE id = %d", $id);
		$arr = $wpdb->get_row($st, ARRAY_A);
		$this->setContent($arr['content']);
		$this->setSubscriptionOptions($arr['options']);
	}

	public function getRemoveOptions()
	{
		return array('onScrolling'=>1);
	}

	protected function getExtraRenderOptions()
	{
		$currentPopupId = $this->getId();
		$options = json_decode($this->getSubscriptionOptions(), true);
		$title = $this->getSubscriptionTitle();
		$textInputsWidth = $options['subs-text-width'];
		$emailPlaceholder = $options['subscription-email'];
		$sgSubsFirstName = $options['subs-first-name'];
		$sgSubsLastName = $options['subs-last-name'];
		$textInputsBgColor = $options['subs-text-input-bgcolor'];
		$submitButtonBgColor = $options['subs-button-bgcolor'];
		$subsButtonColor = $options['subs-button-color'];
		$sgSubsBtnTitle = $options['subs-btn-title'];
		$sgSubsTextBordercolor = $options['subs-text-bordercolor'];
		$sgSubsInputsColor = $options['subs-inputs-color'];
		$sgSubsPlaceholderColor = $options['subs-placeholder-color'];
		$sgSubsTextHeight = $options['subs-text-height'];
		$sgSubsBtnWidth = $options['subs-btn-width'];
		$sgSubsBtnHeight = $options['subs-btn-height'];
		$sgSubsFirstNameStatus = $options['subs-first-name-status'];
		$sgSubsLastNameStatus = $options['subs-last-name-status'];
		$sgSubsValidateMessage = $options['subs-validation-message'];
		$sgSubsBtnProgressTitle = $options['subs-btn-progress-title'];
		$sgSubsSuccessMessage = $options['subs-success-message'];
		$sgSubstextBorderWidth = $options['subs-text-border-width'];
		$subsSontShowAfterSubmitting = $options['subs-dont-show-after-submitting'];

		$subsciption = "<form id='sg-subscribers-data'><div class=\"sg-subs-inputs-wrapper\">";
		$subsciption .= "<input type=\"text\" name='subs-email-name' class=\"js-subs-text-inputs js-subs-email-name\" placeholder=\"$emailPlaceholder\"><br>";
		$subsciption .= "<div class='sg-js-hide js-validate-email'>$sgSubsValidateMessage</div>";
		if($sgSubsFirstNameStatus) {
			$subsciption .= "<input type=\"text\" name='subs-first-name' class=\"js-subs-first-name js-subs-text-inputs\" placeholder=\"$sgSubsFirstName\"><br>";
		}
		if($sgSubsLastNameStatus) {
			$subsciption .= "<input type=\"text\" name='subs-last-name' class=\"js-subs-last-name js-subs-text-inputs\" placeholder=\"$sgSubsLastName\"><br>";
		}
		$subsciption .= "<input type=\"hidden\" name=\"subs-popup-title\" value=\"$title\">";
		$subsciption .= '<input type="submit" value="Submit" class="js-subs-submit-btn">';
		$subsciption .= "</div></form>";
		$subsciption .= "<div class='sg-js-hide sg-subs-success'>".$sgSubsSuccessMessage."</div>";

		$content = $this->getContent();
		$content .= $subsciption;

		$subscriptionParams = array(
			"popupId" => $currentPopupId,
			"ajaxurl" => admin_url( 'admin-ajax.php'),
			"textInputsWidth" => $textInputsWidth,
			"sgSubsBtnWidth" => $sgSubsBtnWidth,
			"sgSubsBtnHeight" => $sgSubsBtnHeight,
			"sgSubsTextHeight" => $sgSubsTextHeight,
			"textInputsBgColor" => $textInputsBgColor,
			"submitButtonBgColor" => $submitButtonBgColor,
			"sgSubsTextBordercolor" => $sgSubsTextBordercolor,
			"sgSubsInputsColor" => $sgSubsInputsColor,
			"subsButtonColor" => $subsButtonColor,
			"sgSubsBtnTitle" => $sgSubsBtnTitle,
			"sgSubsBtnProgressTitle" => $sgSubsBtnProgressTitle,
			"sgSubstextBorderWidth" => $sgSubstextBorderWidth,
			'subsSontShowAfterSubmitting' => $subsSontShowAfterSubmitting
		);

		wp_enqueue_script('sg-subscription', SG_APP_POPUP_URL . '/javascript/sg_subscription.js', array( 'jquery' ));
		wp_localize_script('sg-subscription', 'SgSubscriptionParams', $subscriptionParams);
		wp_enqueue_script('jquery');
		
		$content .= "<style>
			.sg-subs-inputs-wrapper {
				text-align: center;
			}
			.js-subs-submit-btn,
			.js-subs-text-inputs {
				padding: 5px !important;
				box-sizing: border-box;
				font-size: 14px !important;
				border-radius: none !important;
				 box-shadow: none !important;
			}
			.js-subs-submit-btn {
				border:0px !important;
				margin-bottom: 2px;
			}
			.js-subs-text-inputs {
				margin-bottom: 8px;
			}
			.sg-js-hide {
				display: none;
			}
			.sg-subs-success {
				border: 1px solid black;
				color: black;
				background-color: #F0EFEF;
				padding: 5px;
			}
			.js-subs-text-inputs::-webkit-input-placeholder {color:".$sgSubsPlaceholderColor.";}
			.js-subs-text-inputs::-moz-placeholder {color:".$sgSubsPlaceholderColor.";}
			.js-subs-text-inputs:-ms-input-placeholder {color:".$sgSubsPlaceholderColor.";} /* ie */
			.js-subs-text-inputs:-moz-placeholder {color:".$sgSubsPlaceholderColor.";}
		</style>";
		$options = json_decode($this->getsubscriptionOptions(), true);

		$hasShortcode = $this->hasPopupContentShortcode($content);

		if($hasShortcode) {
			$content = $this->improveContent($content);
			$content = base64_encode($content);
			/*Add this part of code right into the page to escape conflicts with shortcodes init functionlity*/
			$currentPopupContent = "<div id=\"sg-popup-content-".$this->getId()."\" style=\"display: none;\">&nbsp;<div id=\"sgpb-all-content-".$this->getId()."\">".$content."</div></div>";
			
			/*Append to body for shortcode break to new line*/
			echo "<script type=\"text/javascript\">
				jQuery(document).ready(function() {
					jQuery('body').append(jQuery('".$currentPopupContent."'));
				});
			</script>";
			$content = ' ';
		}
		else {
			$content = base64_encode($content);
		}
		
		return array('html'=>$content);
	}

	public  function render()
	{
		return parent::render();
	}
}
