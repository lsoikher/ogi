<?php
require_once(dirname(__FILE__).'/SGPopup.php');

class SGCountdownPopup extends SGPopup
{
	public $countdownContent;
	public $countdownOptions;

	function __construct()
	{
		wp_register_script('sg_flipclock_js', SG_APP_POPUP_URL . '/javascript/sg_flipclock.js');
		wp_enqueue_script('sg_flipclock_js');
		wp_register_style('sg_flipclock_css', SG_APP_POPUP_URL . "/style/sg_flipclock.css");
		wp_enqueue_style('sg_flipclock_css');
	}

	public function setCountdownContent($countdownContent)
	{
		$this->countdownContent = $countdownContent;
	}

	public function getCountdownContent()
	{
		return $this->countdownContent;
	}

	public function setCountdownOptions($options)
	{
		$this->countdownOptions = $options;
	}

	public function getCountdownOptions()
	{
		return $this->countdownOptions;
	}

	public static function create($data, $obj = null)
	{
		$obj = new self();
		$options = json_decode($data['options'], true);
		$countdownOptions = $options['countdownOptions'];
		$countdownContent = $data['countdown'];

		$obj->setCountdownContent($countdownContent);
		$obj->setCountdownOptions($countdownOptions);

		return parent::create($data, $obj);
	}

	public function save($data = array())
	{

		$editMode = $this->getId()?true:false;

		$res = parent::save($data);
		if ($res===false) return false;
		$countdownContent = $this->getCountdownContent();
		$countdownOptions = $this->getCountdownOptions();

		global $wpdb;
		if ($editMode) {
			$countdownContent = stripslashes($countdownContent);
			$sql = $wpdb->prepare("UPDATE ".$wpdb->prefix."sg_countdown_popup SET content=%s, options=%s WHERE id=%d",$countdownContent,$countdownOptions,$this->getId());
			$res = $wpdb->query($sql);
		}
		else {
			$sql = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."sg_countdown_popup (id, content, options) VALUES (%d,%s,%s)",$this->getId(),$countdownContent,''.$countdownOptions.'');
			$res = $wpdb->query($sql);
		}
		return $res;
	}

	protected function setCustomOptions($id)
	{
		global $wpdb;
		$st = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."sg_countdown_popup WHERE id = %d",$id);
		$arr = $wpdb->get_row($st,ARRAY_A);
		$this->setCountdownContent($arr['content']);
		$this->setCountdownOptions($arr['options']);
	}

	public static function dateToSeconds($sgDueDate, $sgTimeZone)
	{

		$sgDueDateTime = $sgDueDate;
		$timeDate = new DateTime('now', new DateTimeZone($sgTimeZone));
		$timeNow = strtotime($timeDate->format('Y-m-d H:i:s'));
		$seconds = strtotime($sgDueDateTime)-$timeNow;
		if($seconds < 0) {
			$seconds = 0;
		}
		return $seconds;
	}

	public static function renderScript($seconds, $sgContType, $content, $sgContLanguage, $countdownCustomParams)
	{	
		$autoClose = $countdownCustomParams['countdownAutoclose'];

		$paramas = array(
			"seconds" => $seconds,
			"type" => $sgContType,
			"content" => $content,
			"countLanguage" => $sgContLanguage,
			"countdownAutoclose" => $autoClose
		);
		return $paramas;
	}
	
	public static function renderStyle($countdownNumbersBgColor, $countdownNumbersTextColor)
	{

		return  "<style>
			.flip-clock-wrapper ul li a div div.inn {
				background-color: $countdownNumbersBgColor;
				color: $countdownNumbersTextColor;
			}
			.sg-countdown-wrapper {
				width: 500px;
				height: 130px;
				margin: 0 auto;
			}
			.sg-counts-content {
				disply: inline-block;
			}
			.sg-counts-content > ul.flip {
				width: 40px;
			}

		</style>";
	}

	protected function getExtraRenderOptions()
	{
		$countdownContent = "";
		$countdownCustomParams = array();
		$countdownOptions = json_decode($this->getCountdownOptions(),true);
		$sgFlipClockContent = '<div class="sg-countdown-wrapper" id="sg-clear-coundown">
									<div class="sg-counts-content"></div>
								</div>';
		$sgCountdownPosition = $countdownOptions['pushToBottom'];
		$sgCountdownType = $countdownOptions['sg-countdown-type'];
		if($sgCountdownPosition == 'on') {
			$countdownContent.= $sgFlipClockContent;
		}
		$countdownContent .= $this->getCountdownContent();
		if($sgCountdownPosition == '') {
			$countdownContent.= $sgFlipClockContent;
		}
		$countdownContent.= "<script>

			jQuery('#sgcolorbox').on('sgColorboxOnCompleate',function(e) {
				if($sgCountdownType == 2) {
					jQuery('.sg-counts-content').css({width: '340px',margin: '0 auto'});
				}
				else {
					jQuery('.sg-counts-content').css({width: '461px',margin: '0 auto'});
				}
				jQuery('.sg-countdown-wrapper').css({'text-align': 'center','margin-top': '22px'});
				if(arguments[1] == '') { /* push to bottom param */
					jQuery('.sg-countdown-wrapper').css({'text-align': 'center','position': 'absolute','bottom': '2px','left': '0','right': '0'});
				}
			});
		</script>";
		$content = $this->getCountdownContent();
		$pushToBottom = $countdownOptions['pushToBottom'];
		$countdownNumbersTextColor = $countdownOptions['countdownNumbersTextColor'];
		$countdownNumbersBgColor = $countdownOptions['countdownNumbersBgColor'];
		$sgContType = $countdownOptions['sg-countdown-type'];
		$sgContLanguage = $countdownOptions['counts-language'];
		$closeType = @$countdownOptions['closeType'];
		$sgDueDate = $countdownOptions['sg-due-date'];
		$sgTimeZone = $countdownOptions['sg-time-zone'];
		$countdownAutoclose = @$countdownOptions['countdown-autoclose'];

		/*Compleate in this array for send localized script */
		$countdownCustomParams['countdownAutoclose'] = $countdownAutoclose;

		$seconds = SGCountdownPopup::dateToSeconds($sgDueDate,$sgTimeZone);

		$countdownContent.= SGCountdownPopup::renderStyle($countdownNumbersBgColor,$countdownNumbersTextColor);
		$countdownParams = SGCountdownPopup::renderScript($seconds,$sgContType,$content,$sgContLanguage, $countdownCustomParams);

		wp_register_script('sg_coundtdown_js', SG_APP_POPUP_URL . '/javascript/sg_countdown.js');
		wp_localize_script('sg_coundtdown_js', 'SgCountdownParams', $countdownParams);
		wp_enqueue_script('sg_coundtdown_js');
		$hasShortcode = $this->hasPopupContentShortcode($countdownContent);

		if($hasShortcode) {
			$countdownContent = $this->improveContent($countdownContent);
			$countdownContent = base64_encode($countdownContent);
			/*Add this part of code right into the page to escape conflicts with shortcodes init functionlity*/
			$currentPopupContent = "<div id=\"sg-popup-content-".$this->getId()."\" style=\"display: none;\">&nbsp;<div id=\"sgpb-all-content-".$this->getId()."\">".$countdownContent."</div></div>";
			
			/*Append to body for shortcode break to new line*/
			echo "<script type=\"text/javascript\">
				jQuery(document).ready(function() {
					jQuery('body').append(jQuery('".$currentPopupContent."'));
				});
			</script>";
			$countdownContent = ' ';
		}
		else {
			$countdownContent = base64_encode($countdownContent);
		}

		return array('html'=> $countdownContent);
	}

	public  function render()
	{
		return parent::render();
	}
}
