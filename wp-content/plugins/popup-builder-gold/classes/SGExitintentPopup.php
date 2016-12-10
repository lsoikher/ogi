<?php
require_once(dirname(__FILE__).'/SGPopup.php');

class SGExitintentPopup extends SGPopup {
	public $content;
	public $exitIntentOptions;

	function __construct()
	{
		wp_register_script('sg_exit_intent_js', SG_APP_POPUP_URL . '/javascript/sg_exit_intent.js');
		wp_enqueue_script('sg_exit_intent_js');
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setExitIntentOptions($options)
	{
		$this->exitIntentOptions = $options;
	}

	public function getExitIntentOptions()
	{
		return $this->exitIntentOptions;
	}

	public static function create($data, $obj = null)
	{
		$obj = new self();

		$options = json_decode($data['options'], true);
		$exitIntentOptions = $options['exitIntentOptions'];

		$obj->setContent($data['exitIntent']);
		$obj->setExitIntentOptions($exitIntentOptions);

		return parent::create($data, $obj);
	}

	public function save($data = array())
	{

		$editMode = $this->getId()?true:false;

		$res = parent::save($data);
		if ($res===false) return false;

		$content = $this->getContent();
		$options = $this->getExitIntentOptions();

		global $wpdb;
		if ($editMode) {
			$content = stripslashes($content);
			$sql = $wpdb->prepare("UPDATE ".$wpdb->prefix."sg_exit_intent_popup SET content=%s, options=%s WHERE id=%d", $content, $options, $this->getId());
			$res = $wpdb->query($sql);
		}
		else {
			$sql = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."sg_exit_intent_popup (id, content, options) VALUES (%d, %s, %s)", $this->getId(), $content, $options);
			$res = $wpdb->query($sql);
		}
		return $res;
	}

	protected function setCustomOptions($id)
	{
		global $wpdb;
		$st = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix."sg_exit_intent_popup WHERE id = %d", $id);
		$arr = $wpdb->get_row($st, ARRAY_A);
		$this->setContent($arr['content']);
		$this->setExitIntentOptions($arr['options']);
	}

	public function getRemoveOptions()
	{
		return array('onScrolling'=>1,'showOnlyOnce'=>1);
	}

	public function getExitIntentInitScript($id)
	{
		return "<script>
			sgAddEvent(window, 'load', function() {
				sgExitIntentObj = new SGExitIntnetPopup();
				sgExitIntentObj.init($id);
			});</script>";
	}

	protected function getExtraRenderOptions()
	{
		$content = $this->getContent();
		
		$options = json_decode($this->getExitIntentOptions(), true);
		$type = $options['exit-intent-type'];

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
