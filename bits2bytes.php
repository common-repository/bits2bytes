<?php
/*
Plugin Name: Bits2Bytes
Plugin URI: http://wordpress.org/extend/plugins/bits2bytes
Description: Bits2Bytes is a wordpress simple/yet powerful widget, that allow users convert <a target='_blank' href='https://en.wikipedia.org/wiki/Units_of_information'>computer data units</a> to each other.
Version: 1.1.0
Author: MostafaS
Author URI: https://profiles.wordpress.org/mostafas
License: GPLv3 or later
*/

/*  Copyright 2015-2016  Mostafa Ziasistani

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



//Load Text Domain
function bits2bytes_load_textdomain() {
	load_plugin_textdomain( 'bits2bytes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_filter( 'wp_loaded', 'bits2bytes_load_textdomain' );

if ( !defined( 'B2B_Widget_PATH' ) )
	define( 'B2B_Widget_PATH', plugin_dir_path( __FILE__ ) );
if ( !defined( 'B2B_Widget_BASENAME' ) )
	define( 'B2B_Widget_BASENAME', plugin_basename( __FILE__ ) );
if(function_exists(plugins_url))
	{
		define( 'B2B_PLUGIN_URL', plugins_url() );
	}

// use widgets_init Action hook to execute custom function
add_action( 'widgets_init', 'b2b_register_widgets' );

 //register our widget
function b2b_register_widgets() {
    register_widget( 'b2b_widget' );
}



//b2b_widget class
class b2b_widget extends WP_Widget {

    //process our new widget
    function b2b_widget() {
        $widget_ops = array('classname' => 'b2b_widget', 'description' => __('Allow users convert computer data units to each other','bits2bytes') );
        $this->WP_Widget('b2b_bits2bytes_widget', __('Bits2Bytes Widget','bits2bytes'), $widget_ops);
    }

     //build our widget settings form
    function form($instance) {
        $defaults = array( 'title' => __('Bits2Bytes','bits2bytes') );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = strip_tags($instance['title']);

        ?>
			<p><?php
			/* Translators: Title: Title of widget */
			_e('Title','bits2bytes') ?>: <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>"  type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <?php
    }

    //save our widget settings
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }

    //display our widget
    function widget($args, $instance) {
        extract($args);
        echo $before_widget;
        $title = apply_filters('widget_title', $instance['title'] );
        //$name = empty($instance['name']) ? '&nbsp;' : apply_filters('widget_name', $instance['name']);
        if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		$style_url = B2B_PLUGIN_URL."/bits2bytes/bits2bytes/style.css";
		$script_ajax = B2B_PLUGIN_URL."/bits2bytes/bits2bytes/ajax_request.js";
		$script_serialization = B2B_PLUGIN_URL."/bits2bytes/bits2bytes/serialization.js";
		$script_bits2bytes = B2B_PLUGIN_URL."/bits2bytes/bits2bytes/bits2bytes.js";
		$loader_gif = B2B_PLUGIN_URL."/bits2bytes/bits2bytes/loader.gif";
		wp_enqueue_style( 'style', $style_url );
		wp_enqueue_script( 'ajax_request', $script_ajax);
		wp_enqueue_script( 'serialization', $script_serialization);
		//Start main widget elements
	echo "<div id='main_box' class='container'>";
	echo "<div class='row'>";
	//echo "<div id='title' class='col-md-12'>Bits2Bytes</div>";
	echo "</div>";
	echo "<div class='row'>";
	echo "<div id='form_wrapper' class='col-md-12'>";
	echo "<form name='frm1' id='main_form'>";
	echo "<input type='text' name='amountField' id='amountField' class='field1' placeholder='". /* Translators: form input placeholder */__('Convert','bits2bytes') ."' title='".__('Enter numeric value','bits2bytes')."'/>";
	echo "<select id='unitList' name='unit' class='field2'>";
	echo "<option value='b'>".__('bits','bits2bytes')."</option>";
	echo "<option value='B'>".__('bytes','bits2bytes')."</option>";
	echo "<option value='kb'>".__('kilobits','bits2bytes')."</option>";
	echo "<option value='kB'>".__('kilobytes','bits2bytes')."</option>";
	echo "<option value='mb'>".__('megabits','bits2bytes')."</option>";
	echo "<option value='mB'>".__('megabytes','bits2bytes')."</option>";
	echo "<option value='gb'>".__('gigabits','bits2bytes')."</option>";
	echo "<option value='gB'>".__('gigabytes','bits2bytes')."</option>";
	echo "<option value='tb'>".__('terabits','bits2bytes')."</option>";
	echo "<option value='tB'>".__('terabytes','bits2bytes')."</option>";
	echo "<option value='pb'>".__('petabits','bits2bytes')."</option>";
	echo "<option value='pB'>".__('petabytes','bits2bytes')."</option>";
	echo "</select>";
	echo "<!--<button id='in_submit'>></button>-->";
	echo "<!-- <p id='in_submit' class='go'></p> -->";
	echo "<!--<img src='./btn.png' class='btn' id='in_submit'/>-->";
	echo "<input type='button' id='in_submit' title='".__('Convert','bits2bytes')."'/>";
	echo "</form>";
	echo "<div id='loader'><img src='".$loader_gif."' style='width: 190px; height: 20px;' title='".__('Please wait...','bits2bytes')."'/><small><b>".__('Please wait...','bits2bytes')."</b></small></div>";
	echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
	echo "<div id='results' class='col-md-12'>";

	echo "<table id='resultTable'><tr><td width='10%'>".__('bits','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('bytes','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('kilobits','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('kilobytes','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('megabits','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('megabytes','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('gigabits','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('gigabytes','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('terabits','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('terabytes','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('petabits','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('petabytes','bits2bytes').": </td><td>?</td></tr></table>";
	//echo "<center><small>By <a href='http://wordpress.org/extend/plugins/bits2bytes/'>Bits2Bytes</a></small></center>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
		//End main widget elements

		//Start Bits2Bytes.js File
		?>
<script type="text/javascript">
/*
	@Copyright Bits2Bytes By MostafaS
*/

/**
@deprecated
function checkOfflineOnline() {
    var state = navigator.onLine ? "online" : "offline";
    return state;
}
**/
var BITS_IN_A_BYTE = 8;
var KILO = 1024;
var output = Array(12);
function convert(amt, unit) {
	switch (unit) {
		case 'b':
			output[0] = amt;
			break;
		case 'B':
			output[0] = amt * BITS_IN_A_BYTE;
			break;
		case 'kb':
			output[0] = amt * KILO;
			break;
		case 'kB':
			output[0] = amt * BITS_IN_A_BYTE * KILO;
			break;
		case 'mb':
			output[0] = amt * KILO * KILO ;
			break;
		case 'mB':
			output[0] = amt * KILO * KILO * BITS_IN_A_BYTE;
			break;
		case 'gb':
			output[0] = amt * KILO * KILO * KILO;
			break;
		case 'gB':
			output[0] = amt *KILO * KILO * KILO * BITS_IN_A_BYTE;
			break;
		case 'tb':
			output[0] = amt * KILO * KILO * KILO * KILO;
			break;
		case 'tB':
			output[0] = amt *KILO * KILO * KILO * KILO * BITS_IN_A_BYTE;
			break;
		case 'pb':
			output[0] = amt * KILO * KILO * KILO * KILO * KILO;
			break;
		case 'pB':
			output[0] = amt *KILO * KILO * KILO * KILO * KILO * BITS_IN_A_BYTE;
			break;
		default:
			break;
	}
}
function Loader(status){
  //Hide loader
  document.getElementById("loader").style.display=status;
}
Loader("none");
document.getElementById("in_submit").onclick=function(event){
  //Default form submit
  event.preventDefault();
  Loader("");
//check for number and not empty value
var input_value = document.getElementById("amountField").value;
if(input_value == "" || isNaN(input_value)){
	alert("Please enter a numeric value");
}
else
{
Loader("");
var resultTable = document.getElementById("resultTable");
resultTable.rows[0].cells[1].txtContent = "A";
var form = document.getElementById("main_form");
var results = document.getElementById("results");

  var amountField = document.getElementById("amountField").value;
  var list = document.getElementById('unitList');
  var unit = list.options[list.selectedIndex].value;

//console.log(amountField+" "+unit);
//console.log(list);
//console.log(form);
//console.log(results);

  convert(amountField,unit);
	output[1] = output[0] / BITS_IN_A_BYTE;
	output[2] = output[0] / KILO,
	output[3] = output[2] / BITS_IN_A_BYTE,
	output[4] = output[2] / KILO,
	output[5] = output[4] / BITS_IN_A_BYTE,
	output[6] = output[4] / KILO,
	output[7] = output[6] / BITS_IN_A_BYTE,
	output[8] = output[6] / KILO,
	output[9] = output[8] / BITS_IN_A_BYTE,
	output[10] = output[8] / KILO,
	output[11] = output[10] / BITS_IN_A_BYTE;

//console.log(output);
  //check for delete append error when user calculate new value(delete old error
  //when user calculating new value)
  for(i=0;i<results.childNodes.length;i++){
    if(results.childNodes[i].nodeType == 3){
      var NodeForDel = results.childNodes[i];
      results.removeChild(NodeForDel);
    }
  }

for(j=1;j<=output.length;j++)  {
  //now append table tr values
  for(i=0;i<resultTable.rows.length;i++){
  //console.log(resultTable.rows[i].cells[1].txtContent);
  if(resultTable.rows[i].cells[1].textContent != ""){
  var ChildNodeForDel = resultTable.rows[i].cells[1].childNodes[0];
  resultTable.rows[i].cells[1].removeChild(ChildNodeForDel);
  resultTable.rows[i].cells[1].appendChild(document.createTextNode(output[i]));//
  //resultTable.rows[i].cells[1].innerHTML=( document.createTextNode(response.result.j) ).nodeValue;


  }

  }

}

  //console.log(document.getElementById("resultTable"));
  //console.log(results);


  //console.log(output[0]+" "+output[1]+" "+output[2]+" "+output[3]+" "+output[4]+" "+output[5]+" "+output[6]+" "+output[7]+" "+output[8]+" "+output[9]+" "+output[10]+" "+output[11]);
Loader("none");
		}//check input else
}
//End Bits2Bytes.js Script
</script>
		<?php
		//End Bits2Bytes.js File

		//We add bits2bytes.js file directly...we need translate some text on it(we add it above here)
		//wp_enqueue_script( 'bits2bytes', $script_bits2bytes);
        echo $after_widget;
    }
}
?>
