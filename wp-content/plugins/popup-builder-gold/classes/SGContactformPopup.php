<?php
require_once(dirname(__FILE__).'/SGPopup.php');

class SGContactformPopup extends SGPopup
{
	public $content;
	public $params;

	function __construct()
	{
		wp_enqueue_script('sg_contactForm', SG_APP_POPUP_URL . '/javascript/sg_contactForm.js', array( 'jquery' ));
		//wp_localize_script('sg_contactForm', 'contact_frontend_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));
		wp_enqueue_script('jquery');
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function steParams($params)
	{
		$this->params = $params;
	}

	public function getParams()
	{
		return $this->params;
	}

	public static function create($data, $obj = null)
	{
		$obj = new self();

		$options = json_decode($data['options'], true);
		$obj->steParams($data['contactFormOptions']);
		unset($data['contactFormOptions']);

		$obj->setContent($data['sg_contactForm']);

		return parent::create($data, $obj);
	}

	public function save($data = array())
	{

		$editMode = $this->getId()?true:false;

		$res = parent::save($data);
		if ($res===false) return false;

		$content = $this->getContent();
		$params = $this->getParams();

		global $wpdb;
		if ($editMode) {
			$content = stripslashes($content);
			$sql = $wpdb->prepare("UPDATE ".$wpdb->prefix."sg_contact_form_popup SET content=%s, options=%s WHERE id=%d", $content, $params, $this->getId());
			$res = $wpdb->query($sql);
		}
		else {
			$sql = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."sg_contact_form_popup (id, content, options) VALUES (%d, %s, %s)", $this->getId(), $content, $params);
			$res = $wpdb->query($sql);
		}
		return $res;
	}

	protected function setCustomOptions($id)
	{
		global $wpdb;
		$st = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix."sg_contact_form_popup WHERE id = %d", $id);
		$arr = $wpdb->get_row($st, ARRAY_A);

		$this->setContent($arr['content']);
		$this->steParams($arr['options']);
	}

	public function getRemoveOptions()
	{
		return array();
	}

	protected function getExtraRenderOptions()
	{
		$content = "<div class='contact-content-wrapper'>";
		$content .= $this->getContent();
		$content .= "</div>";
		$options = json_decode($this->getParams(), true);
		$formElements = '';

		$namePlaceholder = $options['contact-name'];
		$subjectPlaceholder = $options['contact-subject'];
		$emailPlaceholder = $options['contact-email'];
		$messagePlaceholder = $options['contact-message'];
		$sgContactInputsWidth = $options['contact-inputs-width'];
		$sgContactBtnWidth = $options['contact-btn-width'];
		$sgContactInputsHeight = $options['contact-inputs-height'];
		$sgContactBtnHeight = $options['contact-btn-height'];
		$sgContactBtnProgressTitle = $options['contact-btn-progress-title'];
		$sgContactPlaceholderColor = $options['contact-placeholder-color'];
		$sgContactButtonColor = $options['contact-button-color'];
		$sgContactButtonBgcolor = $options['contact-button-bgcolor'];
		$sgContactTextInputBgcolor = $options['contact-text-input-bgcolor'];
		$sgContactInputsBorderWidth = $options['contact-inputs-border-width'];
		$sgContactInputsColor = $options['contact-inputs-color'];
		$sgContactTextBordercolor = $options['contact-text-bordercolor'];
		$sgContactValidationMessage = $options['contact-validation-message'];
		$sgContactAreaWidth = $options['contact-area-width'];
		$sgContactAreaHeight = $options['contact-area-height'];
		$sgContactResize = $options['sg-contact-resize'];
		$sgSubsValidateMessage = $options['contact-validation-message'];
		$contactSuccessMessage = $options['contact-success-message'];
		$sgContactValidateEmail= $options['contact-validate-email'];
		$sgContactReceiveMail = $options['contact-receive-email'];
		$sgContactFailMessage = $options['contact-fail-message'];
		$sgContactNameStatus = $options['contact-name-status'];
		$sgContactSubjectStatus = $options['contact-subject-status'];
		$sgContactSubjectRequired = $options['contact-subject-required'];
		$sgContactNameRequired = $options['contact-name-required'];
		$showFormToTop = $options['show-form-to-top'];
		$submitButtonTitle = $options['contact-btn-title'];

		$contactParams = array(
			'inputsWidth' => $sgContactInputsWidth,
			'buttnsWidth' => $sgContactBtnWidth,
			'inputsHeight' => $sgContactInputsHeight,
			'buttonHeight' => $sgContactBtnHeight,
			'procesingTitle' => $sgContactBtnProgressTitle,
			'placeholderColor' => $sgContactPlaceholderColor,
			'btnTextColor' => $sgContactButtonColor,
			'btnBackgroundColor' => $sgContactButtonBgcolor,
			'inputsBackgroundColor' => $sgContactTextInputBgcolor,
			'inputsColor' => $sgContactInputsColor,
			'contactInputsBorderWidth' => $sgContactInputsBorderWidth,
			'ajaxurl' => admin_url( 'admin-ajax.php'),
			'contactAreaWidth' => $sgContactAreaWidth,
			'contactAreaHeight' => $sgContactAreaHeight,
			'contactResize' => $sgContactResize,
			'inputsBorderColor' => $sgContactTextBordercolor,
			'validateMessage' => $sgContactValidationMessage,
			'receiveEmail' => $sgContactReceiveMail,
			'sgContactNameStatus' => $sgContactNameStatus,
			'sgContactSubjectStatus' => $sgContactSubjectStatus,
			'sgContactNameRequired' => $sgContactNameRequired,
			'sgContactSubjectRequired' => $sgContactSubjectRequired
		);
	
		$formElements .= '<div id="sg-contact-faild" class="sg-js-hide">'.$sgContactFailMessage.'</div>';
		$formElements .= '<form id="sg-contact-data"><div class="sg-contact-inputs-wrapper">';

		if($sgContactNameStatus) {
			$formElements .= '<input type="text" name="contact-name" class="sg-contact-required js-contact-text-inputs js-contact-name" value="" placeholder="'.$namePlaceholder.'">';
		}
		if($sgContactSubjectStatus) {
			$formElements .= '<input type="text" name="contact-subject" class=" sg-contact-required js-contact-text-inputs js-contact-subject" value="" placeholder="'.$subjectPlaceholder.'">';
		}

		$formElements .= '<input type="text" name="contact-email" class="sg-contact-required js-contact-text-inputs js-contact-email" value="" placeholder="'.$emailPlaceholder.'">';
		$formElements .= '<div class="sg-js-hide js-validate-email">'.$sgContactValidateEmail.'</div>';			
		$formElements .= '<textarea name="content-message" placeholder="'.$messagePlaceholder.'" class="sg-contact-required js-contact-message js-contact-text-area"></textarea>';			
		$formElements .= '<input type="submit" value="'.$submitButtonTitle.'" class="js-contact-submit-btn">';	
		$formElements .= '</div></form>';		
		$formElements .= '<div id="sg-contact-success" class="sg-js-hide">'.$contactSuccessMessage.'</div>';
		
		/* if not checked Form must be show to bottom text */
		if($showFormToTop == '') {
			$content = $content.$formElements;
		}
		else {
			$content = $formElements.$content;
		}
		$content .= "<style>
			.sg-contact-inputs-wrapper {
				text-align: center;
			}
			.js-contact-text-inputs {
				margin: 3px auto !important;
			}
			.js-contact-submit-btn {
				border: none !important;
			}
			.js-contact-submit-btn,
			.js-contact-text-inputs {
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
			.js-contact-text-inputs {
				margin-bottom: 8px;
			}
			.sg-js-hide {
				display: none;
			}
			#sg-contact-faild {
				border: 1px solid black;
				color: red;
				text-align: center;
				background-color: #F0EFEF;
				padding: 5px;
				width: $sgContactInputsWidth;
				margin: 5px auto;
			}
			#sg-contact-success {
				border: 1px solid black;
				color: black;
				background-color: #F0EFEF;
				padding: 5px;
			}
			.js-contact-text-area {
				padding: 0px !important;
				text-indent: 3px;
			}
			.contact-content-wrapper {
			    margin: 0;
			    height: auto;
			    display: table;
			}
			.js-validate-email,
			.js-requierd-style {
				'margin': '0px auto 5px auto',
				'font-size': '12px',
				'color': 'red'
			}
			.sg-contact-required {
				display: block;
				margin: 3px auto 3px auto;
			}
			.js-contact-submit-btn {
				margin-bottom: 8px;
				line-height: 0px !important;
			}
			.js-requierd-style {
				margin: 0px auto 5px auto;
				font-size: 12px;
				color: red;
				display: block;
			}
			.js-contact-text-inputs::-webkit-input-placeholder {color:".$sgContactPlaceholderColor.";}
			.js-contact-text-inputs::-moz-placeholder {color:".$sgContactPlaceholderColor.";}
			.js-contact-text-inputs:-ms-input-placeholder {color:".$sgContactPlaceholderColor.";} /* ie */
			.js-contact-text-inputs:-moz-placeholder {color:".$sgContactPlaceholderColor.";}
		</style>";
		wp_enqueue_script('sg_contactForm', SG_APP_POPUP_URL . '/javascript/sg_contactForm.js', array( 'jquery' ));
		wp_localize_script('sg_contactForm', 'contactFrontend', $contactParams);

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

		return array('html'=> $content);
	}

	public  function render()
	{
		return parent::render();
	}
}
