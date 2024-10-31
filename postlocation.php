<?php
/*
Plugin Name: PostLocation
Plugin URI: http://dev.wp-plugins.org/wiki/PostLocation#
Description: Attaches address information to posts.  <br />Licensed under the <a href="http://www.opensource.org/licenses/gpl-license.php">GNU General Public License</a>, Copyright 2005 Will Read.
Version: 0.2
Author: Will Read (will@read.name)
Author URI: http://www.will.read.name/
WordPress Version Required: 1.5
Credits: Based off of Owen Winkler's Geo Plug-in http://www.asymptomatic.net
*/

/*
PostLocation - Attaches address information to posts.
Copyright (c) 2005 Will Read

This program is free software; you can redistribute it
and/or modify it under the terms of the GNU General Public
License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any
later version.

This program is distributed in the hope that it will be
useful, but WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
PURPOSE. See the GNU General Public License for more
details.

You should have received a copy of the GNU General Public
License along with this program; if not, write to the Free
Software Foundation, Inc., 59 Temple Place, Suite 330,
Boston, MA 02111-1307 USA
*/

/*

INSTRUCTIONS:

Drop this file into your WordPress plugins directory, and then
Activate it on the Plugins tab of the WordPress admin console.

See http://dev.wp-plugins.org/wiki/PostLocation for more
detailed installation instructions and usage information.

*/


class PostLocation
/**
 * Provide a namespace for our plugin
 */
{
	function options_page_postlocation()
	/**
	* Short Description for the Function, one line, end w/ period.
	*
	* Long description for the Function.
	*
	* @return	mixed	Description
	*/
	{
		if(isset($_REQUEST['deleteid']))
		{
			$postlocations = get_settings('post_locations');
			unset($postlocations[$_REQUEST['deleteid']]);
			update_option('post_locations', $postlocations);
		}
		if(isset($_POST['Options']))
		{
			$use_post_positions = $_POST['use_post_positions'] == 1 ? 1 : 0;
			$use_default_postlocation = $_POST['use_default_postlocation'] == 1 ? 1 : 0;
			update_option('use_post_positions', $use_post_positions);
			update_option('use_default_postlocation', $use_default_postlocation);
			update_option('default_postlocation_location', $_POST['default_postlocation_location']);
			update_option('default_postlocation_street', $_POST['default_postlocation_street']);
			update_option('default_postlocation_city', $_POST['default_postlocation_city']);
			update_option('default_postlocation_state', $_POST['default_postlocation_state']);
			update_option('default_postlocation_zip', $_POST['default_postlocation_zip']);
			update_option('default_postlocation_country', $_POST['default_postlocation_country']);
			echo '<div class="updated"><p><strong>' . __('Options updated.', 'PostLocation') . '</strong></p></div>';
	
		}
		if(isset($_POST['Submit']) || isset($_POST['Add']))
		{
	
			$postlocations = get_settings('post_locations');
			foreach($postlocations as $name => $address)
			{
				$postlocations[$name] = "{$_POST['location'][$name]},{$_POST['street'][$name]},{$_POST['city'][$name]},{$_POST['state'][$name]},{$_POST['zip'][$name]},{$_POST['country'][$name]}";
			}
			if(isset($_POST['new_location']) && ($_POST['new_location'] != ''))
			{
				$postlocations[$_POST['new_location']] = "{$_POST['new_location']},{$_POST['new_street']},{$_POST['new_city']},{$_POST['new_state']},{$_POST['new_zip']},{$_POST['new_country']}";
			}
			update_option('post_locations', $postlocations);
	
			echo '<div class="updated"><p><strong>' . __('Locations updated.', 'PostLocation') . '</strong></p></div>';
		}
		else
		{
			PostLocation::add_options();
		}
	
		$use_post_positions = get_settings('use_post_positions');
		$use_default_postlocation = get_settings('use_default_postlocation');
        $default_postlocation_location = get_settings('default_postlocation_location');
		$default_postlocation_street = get_settings('default_postlocation_street');
		$default_postlocation_city = get_settings('default_postlocation_city');
		$default_postlocation_state = get_settings('default_postlocation_state');
		$default_postlocation_zip = get_settings('default_postlocation_zip');
		$default_postlocation_country = get_settings('default_postlocation_country');
	
		$ck_use_post_positions = $use_post_positions == 1 ? ' checked="checked"' : '';
		$ck_use_default_postlocation[intval($use_default_postlocation)] = ' checked="checked"';
	
		$postlocations = get_settings('post_locations');
		if(!is_array($postlocations)) $postlocations = array();
	
	
		//Option Page Presentation
		echo '
			<div class="wrap">
			<h2>' . __('Post Location Manager', 'PostLocation') . '</h2>
			<form method="post">
				<table width="100%" cellspacing="2" cellpadding="5" class="editform">
					<tr valign="top">
						<th width="33%" scope="row">' . __('Post Tracking Features', 'PostLocation') . ':</th>
						<td><input type="checkbox" name="use_post_positions" id="use_post_positions" ' . $ck_use_post_positions . ' value="1" /> Enable</td>
					</tr>
					<tr valign="top">
						<th width="33%" scope="row">' . __('When no location is specified', 'PostLocation') . ':</th>
						<td>
						<label for="use_default_postlocation0"><input type="radio" name="use_default_postlocation" id="use_default_postlocation0" ' . $ck_use_default_postlocation[0] . ' value="0" /> ' . __('Do nothing.', 'PostLocation') . '</label><br />
						<label for="use_default_postlocation1"><input type="radio" name="use_default_postlocation" id="use_default_postlocation1" ' . $ck_use_default_postlocation[1] . ' value="1" /> ' . __('Use these:', 'PostLocation') . '</label>
							<table>
								<tbody>
									<tr>
										<td><label for="default_postlocation_location">' . __('Location Name', 'PostLocation') . ':</label></td>
										<td><input type="text" class="code" name="default_postlocation_location" size="20" value="' . $default_postlocation_location . '" /></td>
									</tr><tr>
										<td valign="top"><label for="default_postlocation_street">' . __('Address', 'PostLocation') . ':</label></td>
										<td>
											<p class="code">
											<input type="text" class="code" name="default_postlocation_street" size="20" value="' . $default_postlocation_street . '" /><br />
											<input type="text" class="code" name="default_postlocation_city" size="14" value="' . $default_postlocation_city . '" />, <input type="text" size="2" class="code" name="default_postlocation_state" value="' . $default_postlocation_state . '" /><br />
											<input type="text" class="code" name="default_postlocation_zip" size="7" value="' . $default_postlocation_zip . '" />
											</p>
										</td>
									</tr><tr>
										<td><label for="default_postlocation_country">' . __('Country', 'PostLocation') . ':</label></td>
										<td><input type="text" class="code" name="default_postlocation_country" size="2" maxlength="2" value="' . $default_postlocation_country . '" /></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</table>
				<div class="submit"><input type="submit" name="Options" value="' . __('Update Options', 'PostLocation') . ' »" /></div>
			</form>
			</div>
		';
	
		echo '
			<div class="wrap">
			<h2>' . __('Preset Locations', 'PostLocation') . '</h2>
			<form method="post">
			<table width="100%" cellpadding="3" cellspacing="3">
				<thead>
			  <tr>
			    <th scope="col">'.__('Location Name', 'PostLocation').'</th>
			    <th scope="col">'.__('Street', 'PostLocation').'</th>
				<th scope="col">'.__('City', 'PostLocation').'</th>
				<th scope="col">'.__('State', 'PostLocation').'</th>
				<th scope="col">'.__('Zip', 'PostLocation').'</th>
			    <th scope="col">'.__('Country', 'PostLocation').'</th>
			    <th scope="col">'.__('Action', 'PostLocation').'</th>
			  </tr>
			  </thead>
			  <tbody>
		';
	
		foreach($postlocations as $name => $address)
		{
			list($location, $street, $city, $state, $zip, $country) = split(',', $address);
			$alternate = $alternate == ''? ' class="alternate"' : '';
			echo "<tr{$alternate}>";
			echo "<td><input type=\"text\" class=\"code\" name=\"location[{$name}]\" value=\"{$location}\" /></td>";
			echo "<td><input type=\"text\" class=\"code\" name=\"street[{$name}]\" value=\"{$street}\" /></td>";
			echo "<td><input type=\"text\" class=\"code\" name=\"city[{$name}]\" value=\"{$city}\" /></td>";
			echo "<td><input type=\"text\" class=\"code\" name=\"state[{$name}]\" size=\"2\" value=\"{$state}\" /></td>";
			echo "<td><input type=\"text\" class=\"code\" name=\"zip[{$name}]\" size=\"7\" value=\"{$zip}\" /></td>";
			echo "<td><input type=\"text\" class=\"code\" name=\"country[{$name}]\" size=\"2\" maxlength=\"2\" value=\"{$country}\" /></td>";
			echo "<td><a href=\"" . add_query_arg('deleteid', $name) . "\"
				onClick=\"return confirm('" . __("Are you sure you want to delete this location entry?", 'PostLocation') . "');\"
				>Delete</a>
				·
				" . get_map_url('GoogleMaps', 'Map ') . "		</td>
			";
			echo "</tr>";
		}
	
		echo '<tr valign="bottom">
		<td><strong>'.__('New Location', 'PostLocation').':</strong><br/>
		<input type="text" class="code" name="new_location" value="" /></td>
		<td><input type="text" class="code" name="new_street" value="" /></td>
		<td><input type="text" class="code" name="new_city" value="" /></td>
		<td><input type="text" class="code" name="new_state" size="2" maxlength="2" value="" /></td>
		<td><input type="text" class="code" name="new_zip" size="7" value="" /></td>
		<td><input type="text" class="code" name="new_country" size="2" maxlength="2" value="US" /></td>
		<td> </td>
		</tr>
		</tbody>
		</table>';
	
	
	?>
	
	<div class="submit"><input type="submit" name="Submit" value="<?php _e("Add/Update Locations", 'PostLocation'); ?> »" /></div>
	</form>
	
	</div>
	<?
	}	//end options_page
	
	
	function add_options()
	/**
	* Short Description for the Function, one line, end w/ period.
	*
	* Long description for the Function.
	*
	*/
	{
		add_option('use_post_positions', 0);
		add_option('use_default_postlocation', 0);
		add_option('default_postlocation_location', 0);
        add_option('default_postlocation_street', 0);
		add_option('default_postlocation_city', 0);
		add_option('default_postlocation_state', 0);
		add_option('default_postlocation_zip', 0);
		add_option('default_postlocation_country', 0);
		add_option('post_locations', array());
	}

	function edit_form_advanced($not_used)
	/**
	* Short Description for the Function, one line, end w/ period.
	*
	* Long description for the Function.
	*
	* @param	$not_used	Description
	*/
	{
		global $postdata;

		$postlocations = get_settings('post_locations');

		echo '<fieldset><legend>' . __('Location', 'PostLocation') . '</legend>';

		list($location, $street, $city, $state, $zip, $country) = split(',', get_post_meta($postdata->ID, '_post_location', true));
        echo '<label for="post_location">' . __('Location Name:', 'PostLocation') . ' <input size="10" type="text" value="' . $location.'" name="post_location" id="post_location" /></label>   ';
		echo '<label for="post_street">' . __('Street:', 'PostLocation') . ' <input size="10" type="text" value="' . $street .'" name="post_street" id="post_street" /></label>   ';
		echo '<label for="post_city">' . __('City:', 'PostLocation') . ' <input size="10" type="text" value="' . $city .'" name="post_city" id="post_city" /></label>   ';
		echo '<label for="post_state">' . __('State:', 'PostLocation') . ' <input size="10" type="text" value="' . $state .'" name="post_state" id="post_state" /></label>   ';
		echo '<label for="post_zip">' . __('Zip:', 'PostLocation') . ' <input size="10" type="text" value="' . $zip .'" name="post_zip" id="post_zip" /></label>   ';
		echo '<label for="post_country">' . __('Country:', 'PostLocation') . ' <input size="10" type="text" value="' . $country .'" name="post_country" id="post_country" /></label>   ';

		echo '<label for="post_select">' . __('Preset:', 'PostLocation') . ' <select id="post_select" onchange="post_chooselocation(this);"><option value="">--choose one--</option>';
		foreach($postlocations as $postlocation => $addr)
		{
			echo "<option value=\"$addr\"";
			if($addr == "{$location},{$street},{$city},{$state},{$zip},{$country}") echo ' selected="selected"';
			echo ">{$postlocation}</option>\n";
		}
		echo '</select></label>';

		echo '</fieldset>';
	}

	function admin_head($not_used)
	/**
	* Short Description for the Function, one line, end w/ period.
	*
	* Long description for the Function.
	*
	* @param	$not_used	Description
	*/
	{
		if(strstr($_SERVER['REQUEST_URI'], 'post.php'))
		{
			echo '
<script type="text/javascript">
//<![CDATA[
function post_chooselocation(sel)
{
	var coord = sel.options[sel.selectedIndex].value;
	var ll = coord.split(",");
	var inps = document.getElementsByTagName("input");
	for(z=0;z<inps.length;z++)
	{
                if(inps[z].getAttribute("name") == "post_location") inps[z].setAttribute("value", ll[0]);
		if(inps[z].getAttribute("name") == "post_street") inps[z].setAttribute("value", ll[1]);
		if(inps[z].getAttribute("name") == "post_city") inps[z].setAttribute("value", ll[2]);
		if(inps[z].getAttribute("name") == "post_state") inps[z].setAttribute("value", ll[3]);
		if(inps[z].getAttribute("name") == "post_zip") inps[z].setAttribute("value", ll[4]);
		if(inps[z].getAttribute("name") == "post_country") inps[z].setAttribute("value", ll[5]);

	}
	sel.selectedIndex = 0;
}
//]]>
</script>';
		}
	}

	function update_post($id)
	/**
	* Short Description for the Function, one line, end w/ period.
	*
	* Long description for the Function.
	*
	* @param	$id	Description
	*/
	{
		delete_post_meta($id, '_post_location');
		add_post_meta($id, '_post_location', $_POST['post_location'] . ',' . $_POST['post_street'] . ',' . $_POST['post_city'] . ',' . $_POST['post_state'] . ',' . $_POST['post_zip'] . ',' . $_POST['post_country']);
	}

	function wp_head($not_used)
	/**
	* Short Description for the Function, one line, end w/ period.
	*
	* Long description for the Function.
	*
	* @param	$not_used	Description
	*/
	{
		global $wp_query;

		if(!get_settings('use_post_positions')) return;

		list($location, $street, $city, $state, $zip, $country) = split(',', get_post_meta($wp_query->post->ID, '_post_location', true));
		if(is_single() && ($location != '') && ($street != '') && ($city != '') && ($state != '') && ($zip != '') && ($country != ''))
		{
			$title = convert_chars(strip_tags(get_bloginfo("name")))." - ".$wp_query->post->post_title;
		}
		else if(get_settings('use_default_postlocation'))
		{
			// send the default here
			$title = convert_chars(strip_tags(get_bloginfo("name")));
            $location = get_settings('default_postlocation_location');
			$street = get_settings('default_postlocation_street');
			$city = get_settings('default_postlocation_city');
			$state = get_settings('default_postlocation_state');
			$zip = get_settings('default_postlocation_zip');
			$country = get_settings('default_postlocation_country');
		}
		else
		{
			return;
		}
		echo "<meta name=\"Address\" content=\"{$location}: {$street}, {$city} {$state} {$zip}, {$country}\" />\n";
	}

	function logErrors($msg)
	/**
	* Short Description for the Function, one line, end w/ period.
	*
	* Long description for the Function.
	*
	* @return	bool	Always true
	* @param	$msg	Description
	*/
	{
		$fp = fopen("../asy.log","a+");
		fwrite($fp, "\n\n".date("Y-m-d H:i:s - ").$msg);
		fclose($fp);
		return true;
	}

	function admin_menu($not_used)
	/**
	* Short Description for the Function, one line, end w/ period.
	*
	* Long description for the Function.
	*
	* @param	$not_used	Description
	*/
	{
			add_options_page(__('Post Location Manager', 'PostLocation'), __('Location Info', 'PostLocation'), 5, 'postlocation.php');
	}

}	// End class PostLocation

// Note the array as the second parameter for calling static methods on our nice, safe object namespace
// For instance, array('PostLocation', 'edit_form_advanced') calls PostLocation::edit_form_advanced()
add_action('edit_form_advanced', array('PostLocation', 'edit_form_advanced'));
add_action('admin_head', array('PostLocation', 'admin_head'));
add_action('wp_head', array('PostLocation', 'wp_head'));
add_action('edit_post', array('PostLocation', 'update_post'));
add_action('edit_post', array('PostLocation', 'update_post'));
add_action('publish_post', array('PostLocation', 'update_post'));
add_action('admin_menu', array('PostLocation', 'admin_menu'));
add_action('options_page_postlocation', array('PostLocation', 'options_page_postlocation'));

function get_Location()
{
	global $post;
	get_settings('post_locations');
	list($location, $street, $city, $state, $zip, $country) = split(',', get_post_meta($post->ID, '_post_location', true));
	if ($location != '') {
		return trim($location);
	} else if(get_settings('use_default_postlocation')) {
		return trim(get_settings('default_postlocation_location'));
	}

	return NULL;
}

function get_Street()
{
	global $post;

	list($location, $street, $city, $state, $zip, $country) = split(',', get_post_meta($post->ID, '_post_location', true));
	if ($street != '') {
		return trim($street);
	} else if(get_settings('use_default_postlocation')) {
		return trim(get_settings('default_postlocation_street'));
	}

	return '';
}
function get_City()
{
	global $post;

	list($location, $street, $city, $state, $zip, $country) = split(',', get_post_meta($post->ID, '_post_location', true));
	if ($city != '') {
		return trim($city);
	} else if(get_settings('use_default_postlocation')) {
		return trim(get_settings('default_postlocation_city'));
	}

	return '';
}
function get_State()
{
	global $post;

	list($location, $street, $city, $state, $zip, $country) = split(',', get_post_meta($post->ID, '_post_location', true));
	if ($state != '') {
		return trim($state);
	} else if(get_settings('use_default_postlocation')) {
		return trim(get_settings('default_postlocation_state'));
	}

	return '';
}
function get_Zip()
{
	global $post;

	list($location, $street, $city, $state, $zip, $country) = split(',', get_post_meta($post->ID, '_post_location', true));
	if ($zip != '') {
		return trim($zip);
	} else if(get_settings('use_default_postlocation')) {
		return trim(get_settings('default_postlocation_zip'));
	}

	return '';
}
function get_Country()
{
	global $post;

	list($location, $street, $city, $state, $zip, $country) = split(',', get_post_meta($post->ID, '_post_location', true));
	if ($country != '') {
		return trim($country);
	} else if(get_settings('use_default_postlocation')) {
		return trim(get_settings('default_postlocation_country'));
	}

	return '';
}

function the_Location()
{
	if(get_settings('use_post_positions')) {
		echo get_Location();
	}
}
function the_Street()
{
	if(get_settings('use_post_positions')) {
		echo get_Street();
	}
}
function the_City()
{
	if(get_settings('use_post_positions')) {
		echo get_City();
	}
}function the_State()
{
	if(get_settings('use_post_positions')) {
		echo get_State();
	}
}
function the_Zip()
{
	if(get_settings('use_post_positions')) {
		echo get_Zip();
	}
}

function the_Country()
{
	if(get_settings('use_post_positions')) {
		echo get_Country();
	}
}

function postLocation_PopUpScript()
/**
* Short Description for the Function, one line, end w/ period.
*
* Long description for the Function.
*
* @return	string	Description
*/
{
	echo "
		<script type='text/javascript'>
		function formHandler(form) {
			var URL = form.site.options[form.site.selectedIndex].value;
			if(URL != \".\") {
				popup = window.open(URL,\"MenuPopup\");
			}
		}
		</script>";
}

function map_urls($street = '', $city = '', $state = '', $zip = '', $country = '')
/**
* Short Description for the Function, one line, end w/ period.
*
* Long description for the Function.
*
* @return	array	Description
*/
{
	$street = ($street == ''? get_Street() : $street);
	$city = ($city == ''? get_City() : $city);
	$state = ($state == ''? get_State() : $state);
	$zip = ($zip == ''? get_Zip() : $zip);
	$country = (substr(($country == ''? get_Country() : $country),0,2));
	$ary = array (
		'MapQuest' => array("http://www.mapquest.com/maps/map.adp?address={$street}&city={$city}&state={$state}&zipcode={$zip}&country={$country}&cid=lfmaplink", __('MapQuest', 'PostLocation')),
		'GoogleMaps' => array("http://maps.google.com/maps?q={$street}%2C{$city}%20{$state}%20{$zip}%20{$country}", __('GoogleMap', 'PostLocation')),
		'YahooMaps' => array("http://us.rd.yahoo.com/maps/us/insert/Tmap/extmap/*-http://maps.yahoo.com/maps_result?addr={$street}&csz={$city}%2C+{$state}+{$zip}&country={$country}", __('YahooMaps', 'PostLocation'))
	);

	return $ary;
}

function postLocation_UrlPopNav()
/**
* Short Description for the Function, one line, end w/ period.
*
* Long description for the Function.
*
*/
{
	$sites = get_urls();

	echo '<form action=""><div>\n<select name="site" size="1" onchange="formHandler(this.form);" >'."\n";
	echo '<option value=".">' . sprintf(__("Sites referencing %s x %s", 'PostLocation'), get_Lat(), get_Lon()) . "</option>\n";
	foreach($sites as $site) {
			echo "\t".'<option value="'.$site[0].'">'.$site[1]."</option>\n";
	}
	echo '</select>\n</div></form>'."\n";
}

function get_map_link($index, $preText = '', $street = '', $city = '', $state = '', $zip = '', $country = '')
/**
* Returns a URL for the indexed site at the given address.
*
* Long description for the Function.
*
* @param	$index		The associative index of the URL array to return.
* @param	$lat		Optional latitude for the URL, if not specified then the current is used.
* @param	$lon		Optional longitude for the URL, if not specified then the current is used.
* @param	$preText	Optional text you would like to appear before the link
*
* @return	string		The URL of the requested site.
*/
{
	$street = $street == ''? get_Street() : $street;
	$city = $city == ''? get_City() : $city;
	$state = $state == ''? get_State() : $state;
	$zip = $zip == ''? get_Zip() : $zip;
	$country = $country == ''? get_Country() : $country;
	$urls = map_urls($street, $city, $state, $zip, $country);
	if(count(get_Location()) > 0)
	{
		return $preText . '<a href="' . $urls[$index][0] . '">' . get_Location() . '</a>';
	} 
	else
	{
		return NULL;
	}      
}
?>
