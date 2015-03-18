<?php
/*
Description: Class to extend WP_Widget with Factfish.com Widget
Version: 1.0
Author: Bernhard Kux
Author URI: http://www.kux.de/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

class factfishcom_widget_plugin extends WP_Widget {

  /* constructor */
  function factfishcom_widget_plugin() {
    parent::WP_Widget(false, $name = __('Factfish.com Widget', 'factfishcom_widget_plugin'),
      array( 'description' => __( 'Free Content from Factfish.com for your Wordpress-Website' , 'factfishcom_widget_plugin'))
    );
  }

  /* widget form creation */
  function form($instance) {

    $languageArr = array(
      "en" => "english",
      "de" => "deutsch"
    );

    // Check values
    $title_default = "Facts of a random selected country:";
    $extratext_default = "Provided by <a href=\"http://www.factfish.com\" target=\"_blank\">www.factfish.com</a>";
    $textarea_default = "<style>
#boxwrapper {  display:table;  }
#boxcontent {   display:table-row; }
#boxcontent>div {  display:table-cell }
#boxleft { background:white; width:120px; }
#boxright { background:white; }
#countryname { font-size: 30px; }
</style>
<div id=\"boxwrapper\">
  <div id=\"boxcontent\">
    <div id=\"boxleft\">
      <a href=\"{country_link}\" target=\"_blank\"><img src=\"http://www.factfish.com/images/maps/{flag}\" alt=\"{country}\" title=\"{country}\"></a>
    </div>
    <div id=\"boxright\">
      <b id=\"countryname\"><a href=\"{country_link}\" target=\"_blank\">{country}</a></b><ul>{subloop-array:data:10}<li><a href=\"{data_set_link}\" target=\"_blank\">{data_set_name}</a>
      <br>{value}</li>{/subloop-array:data}</ul>
    </div>
  </div>
</div>
";
    $json_urlgettimeout =  5; # 5 sec
    $json_basenode = '';
    $json_numberofitems = '';

    if( $instance) {
      $title = esc_attr($instance['title']);
      $textarea = esc_textarea($instance['textarea']);
      #$json_url =  esc_textarea($instance['json_url']);
      #$json_urlgettimeout = esc_textarea($instance['json_urlgettimeout']);
      #$json_basenode =   esc_textarea($instance['json_basenode']);
      #$json_numberofitems =  esc_textarea($instance['json_numberofitems']);
      $extratext =  esc_textarea($instance['extratext']);
      $cacheflag =  esc_textarea($instance['cacheflag']);
      $cachetime =  esc_textarea($instance['cachetime']);
      $language =  $instance['language'];
    } else {
      $title = $title_default;
      $textarea = $textarea_default;
      $extratext = $extratext_default;
      $cacheflag = '';
      $cachetime = '';
      $language = "en";
    }

    if ($language=="") {
      $language = "en";
    }
    $json_url = "http://www.factfish.com/api/json/get_random_facts.php";
  ?>
  <input id="<?php echo $this->get_field_id('json_url'); ?>" name="<?php echo $this->get_field_name('json_url'); ?>" type="hidden" value="<?php echo $json_url; ?>" />
  <input id="<?php echo $this->get_field_id('json_numberofitems'); ?>" name="<?php echo $this->get_field_name('json_numberofitems'); ?>" type="hidden" value="<?php echo $json_numberofitems; ?>" />
  <input id="<?php echo $this->get_field_id('json_basenode'); ?>" name="<?php echo $this->get_field_name('json_basenode'); ?>" type="hidden" value="<?php echo $json_basenode; ?>" />
  <input id="<?php echo $this->get_field_id('json_urlgettimeout'); ?>" name="<?php echo $this->get_field_name('json_urlgettimeout'); ?>" type="hidden" value="<?php echo $json_urlgettimeout; ?>" />

  <p>
  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'factfish_widget_plugin'); ?> (optional):</label>
  <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
  </p>

  <p>
  <label for="<?php echo $this->get_field_id('language'); ?>"><?php _e('Language of JSON-data', 'factfish_widget_plugin'); ?> (default: english):</label>
  <br>
  <?php
    foreach($languageArr as $lanCode => $lanName) {
      echo "<input class=\"widefat\" id=\"".$this->get_field_id('language')."\" name=\"".$this->get_field_name('language')."\" value=\"".$lanCode."\" type=\"radio\"";
      if ($lanCode==$language) { echo " checked "; }
      echo ">".$lanName."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    }
  ?>
  </p>

  <p>
  <label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Template', 'factfish_widget_plugin'); ?> (mandatory):</label>
  <textarea class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
  </p>

  <p>
  <label for="<?php echo $this->get_field_id('extratext'); ?>"><?php _e('Extratext after data', 'factfish_widget_plugin'); ?> (optional):</label>
  <textarea class="widefat" id="<?php echo $this->get_field_id('extratext'); ?>" name="<?php echo $this->get_field_name('extratext'); ?>"><?php echo $extratext; ?></textarea>

  <p>
  <label for="<?php echo $this->get_field_id('cacheflag'); ?>"><?php _e('Cache for Factfish.com API', 'jci_widget_cacheflag'); ?> (default: caching off):</label>
  <br>Check for switching caching on: <input class="widefat" id="<?php echo $this->get_field_id('cacheflag'); ?>" name="<?php echo $this->get_field_name('cacheflag'); ?>" type="checkbox" value="cacheon"
  <?php if ($cacheflag=="cacheon") { echo " checked "; } ?>" />
  <br>Cachetime in seconds: <input class="widefat" id="<?php echo $this->get_field_id('cachetime'); ?>" name="<?php echo $this->get_field_name('cachetime'); ?>" type="text" value="<?php echo $cachetime; ?>" />
  </p>
  <?php
  }

// update widget
function update($new_instance, $old_instance) {
  $instance = $old_instance;
  // Fields
  $instance['title'] = strip_tags($new_instance['title']);
  $instance['extratext'] = $new_instance['extratext'];
  $instance['textarea'] = $new_instance['textarea'];
  $instance['json_url'] = strip_tags($new_instance['json_url']);
  $instance['json_urlgettimeout'] = strip_tags($new_instance['json_urlgettimeout']);
  $instance['json_basenode'] = strip_tags($new_instance['json_basenode']);
  $instance['json_numberofitems'] = strip_tags($new_instance['json_numberofitems']);
  $instance['cachetime'] = strip_tags($new_instance['cachetime']);
  $instance['cacheflag'] = strip_tags($new_instance['cacheflag']);
  $instance['language'] = strip_tags($new_instance['language']);
  return $instance;
}

// display widget
function widget($args, $instance) {
  extract( $args );
  // these are the widget options
  $title = apply_filters('widget_title', $instance['title']);
  $textarea = $instance['textarea'];
  $extratext = $instance['extratext'];
  $json_urlgettimeout = $instance['json_urlgettimeout'];
  $json_basenode = $instance['json_basenode'];
  $json_numberofitemsIn = $instance['json_numberofitems'];
  $language = $instance['language'];
  $json_url = $instance['json_url']."?lang=".$language;
  $json_randomselect = FALSE;
  if (is_numeric($json_numberofitemsIn)) {
    $json_numberofitems = $json_numberofitemsIn;
  } else {
    $json_numberofitemsArr = explode(" ", trim($json_numberofitemsIn));
    if (is_numeric(trim($json_numberofitemsArr[0]))) {
      $json_numberofitems = trim($json_numberofitemsArr[0]);
    } else {
      $json_numberofitems = 1;
    }
    if (trim($json_numberofitemsArr[1])=="random") {
      $json_randomselect = TRUE;
    }
  }
  $cachetime = $instance['cachetime'];
  $cacheflag = $instance['cacheflag'];
  $FactfishContentImporterWidget = new FactfishContentImporterWidget($json_url, $json_urlgettimeout, $json_basenode, $json_numberofitems, $json_randomselect, $textarea, $cacheflag, $cachetime);

  echo $before_widget;
  // Display the widget
  echo '<div class="widget-text wp_widget_plugin_box">';
  // Check if title is set
  if ( $title!="" ) {
    echo $before_title . $title . $after_title;
  }
  echo $FactfishContentImporterWidget->getOutputHTML();
  echo $extratext;
  echo '</div>';
  echo $after_widget;
}

}



class FactfishContentImporterWidget {

    /* shortcode-params */
    private $numberofdisplayeditems = -1; # -1: show all
		private $feedUrl;
    private $json_urlgettimeout = 5;  #5 sec
    private $basenode = "";

    /* plugin settings */
    private $cacheEnable = FALSE;

    /* internal */
		private $cacheFile = "";
		private $jsondata;
		private $feedData  = "";
 		private $cacheFolder;
    private $cachetime = 0;
    private $cacheflag = "";
    private $datastructure = "";
    private $triggerUnique = NULL;
    private $outputHTML = "";
    private $json_randomselect = FALSE;
    private $cacheWritesuccess = "";
    private $cacheExpireTime = 0;

		public function __construct($json_url, $json_urlgettimeout, $json_basenode, $json_numberofitems, $json_randomselect, $textarea, $cacheflag, $cachetime){
      $this->feedUrl = $json_url;
      $this->json_urlgettimeout = $json_urlgettimeout;
      $this->basenode = $json_basenode;
      if (is_numeric($json_numberofitems) && $json_numberofitems>=0) {
        $this->numberofdisplayeditems = $json_numberofitems;
      }
      $this->datastructure = $textarea;
      $this->json_randomselect = $json_randomselect;
      if (is_numeric($cachetime) && $cachetime>0) {
        $this->cachetime = $cachetime;
      }
      $this->cacheflag = $cacheflag;
      $this->cacheExpireTime = time() - $this->cachetime; # 60 sec cachtime ;#string time, [int now])strtotime(date('Y-m-d H:i:s'  , strtotime(" -".$cacheTime." " . $format )));

      $this->buildWidgetHTML();
		}


    /* shortcodeExecute: read shortcode-params and check cache */
		public function getOutputHTML(){
      return $this->outputHTML;
    }

   /* shortcodeExecute: read shortcode-params and check cache */
		private function buildWidgetHTML(){

      /* caching or not? */
      if ($this->cacheflag == "cacheon") {
        $this->cacheEnable = TRUE; # cache on
      } else {
        $this->cacheEnable = FALSE; # cache off
      }
      $this->cacheFolder = WP_CONTENT_DIR.'/cache/jsoncontentimporter/';
      if (
          (!class_exists('FileLoadWithCache'))
          || (!class_exists('JSONdecode')))
        {
        require_once plugin_dir_path( __FILE__ ) . 'class-fileload-cache.php';
      }

      $checkCacheFolderObj = new CheckCacheFolder($this->cacheFolder);


      # cachefolder ok: set cachefile
  		$this->cacheFile = $this->cacheFolder.urlencode($this->feedUrl);  # cache json-feed

      $fileLoadWithCacheObj = new FileLoadWithCache($this->feedUrl, $this->json_urlgettimeout, $this->cacheEnable, $this->cacheFile, $this->cacheExpireTime);
      $fileLoadWithCacheObj->retrieveJsonData();
      $this->feedData = $fileLoadWithCacheObj->getFeeddata();
#			$this->retrieveJsonData();
			# build json-array
      $jsonDecodeObj = new JSONdecode($this->feedData);
      $this->jsondata = $jsonDecodeObj->getJsondata();


      $this->datastructure = preg_replace("/\n/", "", $this->datastructure);

      if(!class_exists('JsonContentParser')){     # the class might be already invoked
        require_once plugin_dir_path( __FILE__ ) . '/class-json-parser.php';
       }
      $JsonContentParser = new JsonContentParser($this->jsondata, $this->datastructure, $this->basenode, $this->numberofdisplayeditems, "", "");
			$this->outputHTML = $JsonContentParser->retrieveDataAndBuildAllHtmlItems();
		}

}
?>