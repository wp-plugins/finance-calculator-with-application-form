<?php
/*
Plugin Name: Finance Calculator
Plugin URI: http://getbutterfly.com/wordpress-plugins/finance-calculator-with-application-form/
Description: Finance Calculator is a drop in form for users to calculate indicative repayments. It can be implemented on a page or a post.
Author: Ciprian Popescu
Author URI: http://getbutterfly.com/
Version: 1.4.2

WP Finance Calculator WordPress Plugin
Copyright (C) 2010, 2011, 2012, 2013 Ciprian Popescu (getbutterfly@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// plugin paths
define('WPFC_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
define('WPFC_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)));
define('WPFC_VERSION', '1.4.2');
//

// plugin localization
$plugin_dir = basename(dirname(__FILE__)); 
load_plugin_textdomain('wpfc', false, $plugin_dir . '/languages'); 
//

add_action('admin_menu', 'wpfc_plugin_menu');

add_option('wpfc_finance_rate', 11);
add_option('wpfc_application_email', '');
add_option('wpfc_currency', 'EUR');
add_option('wpfc_currency_symbol', '&euro;');
add_option('wpfc_credit', 0);

// Change email sender name from "WordPress" to the blog's name
if(!class_exists('wp_mail_from')) {
	class wp_mail_from {
		function wp_mail_from() {
			add_filter('wp_mail_from_name', array(&$this, 'fb_mail_from_name'));
		}

		// new name
		function fb_mail_from_name() {
			$name = get_option('blogname');
			$name = esc_attr($name);
			return $name;
		}
	}

	$wp_mail_from = new wp_mail_from();
}

function wpfc_plugin_menu() {
	add_options_page(_('Finance Calculator', 'wpfc'), _('Finance Calculator', 'wpfc'), 'manage_options', 'wpfc', 'wpfc_plugin_options');
}

function wpfc_plugin_options() {
	$hidden_field_name 		= 'wpfc_submit_hidden';
	$data_field_name 		= 'wpfc_finance_rate';
	$email_field_name 		= 'wpfc_application_email';
	$currency_field_name 	= 'wpfc_currency';
	$symbol_field_name 		= 'wpfc_currency_symbol';
	$credit_field_name 		= 'wpfc_credit';

	// read in existing option value from database
    $option_value_data 		= get_option('wpfc_finance_rate');
    $option_value_email 	= get_option('wpfc_application_email');
    $option_value_currency 	= get_option('wpfc_currency');
    $option_value_symbol 	= get_option('wpfc_currency_symbol');
    $option_value_credit 	= get_option('wpfc_credit');

    // See if the user has posted us some information // if they did, this hidden field will be set to 'Y'
	if(isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {
		$option_value_data = $_POST[$data_field_name];
		$option_value_email = $_POST[$email_field_name];
		$option_value_currency = $_POST[$currency_field_name];
		$option_value_symbol = $_POST[$symbol_field_name];
		$option_value_credit = $_POST[$credit_field_name];

		update_option('wpfc_finance_rate', $option_value_data);
		update_option('wpfc_application_email', $option_value_email);
		update_option('wpfc_currency', $option_value_currency);
		update_option('wpfc_currency_symbol', $option_value_symbol);
		update_option('wpfc_credit', $option_value_credit);
		?>
		<div class="updated"><p><strong>Settings saved.</strong></p></div>
	<?php } ?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>(<acronym title="WordPress Finance Calculator">WPFC</acronym>) Finance Calculator Settings</h2>
		<p>You are currently using <b>Finance Calculator</b> version <b><?php echo WPFC_VERSION; ?></b> with <b><?php bloginfo('charset'); ?></b> charset.</p>
		<h3>Plugin Options</h3>
		<form name="form1" method="post" action="">
			<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y" />
			<p>
				<input type="number" name="<?php echo $data_field_name; ?>" id="<?php echo $data_field_name; ?>" value="<?php echo $option_value_data; ?>" min="0" max="100"> <label for="<?php echo $data_field_name; ?>">Finance Rate <span class="description">- Monthly payment will be calculated using this default rate.</span></label>
			</p>
			<p>
				<input type="email" name="<?php echo $email_field_name; ?>" id="<?php echo $email_field_name; ?>" value="<?php echo $option_value_email; ?>" class="regular-text"> <label for="<?php echo $email_field_name; ?>">Application Email</label>
				<br>
				<span class="description">Application emails will be sent to this address.</span>
			</p>
			<p>
				<input type="text" name="<?php echo $currency_field_name; ?>" id="<?php echo $currency_field_name; ?>" value="<?php echo $option_value_currency; ?>" size="3"> <label for="<?php echo $currency_field_name; ?>">Currency Code <span class="description">- Currency used in application emails. Use USD, EUR, GBP, YEN/JPY.</span></label>
				<br>
				<input type="text" name="<?php echo $symbol_field_name; ?>" id="<?php echo $symbol_field_name; ?>" value="<?php echo $option_value_symbol; ?>" size="3"> <label for="<?php echo $symbol_field_name; ?>">Currency Symbol <span class="description">- Currency used in application emails. Use characters ($, &euro;, &pound;, &yen;) for symbol.</span></label>
			</p>
			<p>
				<select name="wpfc_credit">
					<option value="1"<?php if($option_value_credit == 1) echo ' selected="selected"' ; ?>>Yes, show a link at the bottom of the calculator form</option>
					<option value="0"<?php if($option_value_credit == 0) echo ' selected="selected"' ; ?>>No, do not show</option>
				</select> <label for="wpfc_credit">Help the author by providing a backlink to the official plugin site (optional).</label>
			</p>
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="Save Changes">
			</p>
		</form>

		<h3>Plugin Usage</h3>
		<p>Add the <code>[finance_calculator]</code> shortcode to any post or page to start using the calculator. The calculator will use the default finance rate.</p>
		<p>
			In order to correctly render the currency symbol, make sure you have the correct encoding in your theme header:<br>
			<code>&lt;meta http-equiv=&quot;content-type&quot; content=&quot;text/html; charset=utf-8&quot; /&gt;</code> or <code>&lt;meta charset=&quot;utf-8&quot;&gt;</code> if you are using HTML5.
		</p>
		<p>
			<strong>Notes:</strong><br />
			You can override the default finance rate by adding a <code><strong>rate</strong></code> parameter to the shortcode. Example: <code>[finance_calculator rate=&quot;27&quot;]</code>.<br />
			You can restrict the price field adding a <code><strong>price</strong></code> parameter to the shortcode. Example: <code>[finance_calculator price=&quot;16000&quot;]</code>.<br />
			<small>Do not use comma or period inside the price parameter (i.e. do not use <b>16.000</b> or <b>16,000</b> - use <b>16000</b>)</small>
		</p>

		<p>The payment protection insurance policy pays your loan or hires purchase agreement repayments if you are unable to work because of sickness, an accident or unemployment. It will also provide benefit in the event of your death.</p>
		<p>Eligibility for payment protection is covered under the policy of each company. Please specify these details on the post or page itself.</p>

		<p>Payment protection insurance is a standard add-on feature for many large loans such as car loans and other large bill obligations that could become a true nightmare should a disability or death occurs. This plan can offer a true measure of security for those who have grave reservations about how a large debt would be paid should a disaster strike. Any person with a small savings reservoir or someone heavily in debt would be a prime candidate for such a safety-net plan. Making sure that a plan is sound and customer friendly remains the responsibility of the buyer.</p>

		<h3>Plugin Support</h3>
		<p>For support, feature requests and bug reporting, please visit the <a href="http://getbutterfly.com/wordpress-plugins/finance-calculator-with-application-form/" rel="external">official website</a>.</p>
	</div>
<?php
}

function display_finance_calculator($atts, $content = null) {
	extract(shortcode_atts(array(
		'rate' => get_option('wpfc_finance_rate'),
		'price' => ''
	), $atts));
	if(isset($_POST['submit'])) {
		$listprice 	= $_POST['NetAmount'];
		$amount 	= $_POST['NetAmount'];
		$deposit 	= $_POST['Deposit'];
		$tradein 	= $_POST['TradeIn'];
		$financemonths = $_POST['finance_Months'];
		$finalprice = $listprice - ($deposit + $tradein);
		$f_symbol = get_option('wpfc_currency_symbol');
		$f_currency = get_option('wpfc_currency');

		$display = '
		<h3>' . _('Finance Application Form', 'wpfc') . '</h3>
		<p>* ' . _('Required Fields', 'wpfc') . '</p>

<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" name="form1">
	<input type="hidden" name="finance_months" value="' . $financemonths . '">
	<input type="hidden" name="finance_payments" value="0">

	<input type="hidden" name="finance_Currency" value="' . $f_currency . '">
	<input type="hidden" name="Checkform" value="Yes">

	<p><strong>' . _('Vehicle Details', 'wpfc') . '</strong></p>

	<p>
		<input type="text" name="param_value1" value=""><input type="hidden" name="param_key1" value="Make"> ' . _('Make', 'wpfc') . '<br>
		<input type="text" name="param_value2" value=""><input type="hidden" name="param_key2" value="Model"> ' . _('Model', 'wpfc') . '<br>
		<input type="text" name="param_value3" value=""><input type="hidden" name="param_key3" value="Car Spec"> ' . _('Spec', 'wpfc') . '<br>
		<input type="text" name="lead_caryear" value="' . date('Y') . '"> ' . _('Year', 'wpfc') . '
	</p>

	<p><strong>' . _('Finance Details', 'wpfc') . '</strong></p>

	<p>
		' . $f_symbol . ' <input type="number" name="ListPrice" value="'.$listprice.'"> ' . _('List Price', 'wpfc') . '<br>
		' . $f_symbol . ' <input type="number" name="FinalPrice" value="'.$finalprice.'"> ' . _('Amount', 'wpfc') . '<br>
		' . $f_symbol . ' <input type="number" name="finance_deposit" value="'.$deposit.'"> ' . _('Deposit', 'wpfc') . '<br>
		' . $f_symbol . ' <input type="number" name="finance_TradeIn" value="'.$tradein.'"> ' . _('Trade In', 'wpfc') . '
	</p>

	<p><strong>' . _('Applicant Details', 'wpfc') . '</strong></p>

	<p>
		<input type="text" name="wpfc_forename" /><input type="hidden" name="wpfc_forename_required" value="' . _('Please enter your first name!', 'wpfc') . '" /> * ' . _('First Name', 'wpfc') . '<br>
		<input type="text" name="wpfc_surname" /><input type="hidden" name="wpfc_surname_required" value="' . _('Please enter your last name!', 'wpfc') . '" /> * ' . _('Last Name', 'wpfc') . '<br>
		<input type="text" size="3" name="wpfc_workphoneSTD" /> - <input type="text" size="15" name="wpfc_workphone" /> * ' . _('Work Phone', 'wpfc') . '<br>
		<input type="text" size="3" name="wpfc_homephoneSTD" /> - <input type="text" size="15" name="wpfc_homephone" /> * ' . _('Home Phone', 'wpfc') . '<br>
		<input type="text" size="3" name="wpfc_mobileSTD" /> - <input type="text" size="15" name="wpfc_mobile" /> * ' . _('Mobile Phone', 'wpfc') . '<br>
		<input type="email" name="wpfc_email" /> * ' . _('Email Address', 'wpfc') . '<br>
		<input type="email" name="EMAIL_2" /> * ' . _('Confirm Email Address', 'wpfc') . '<br>
		* ' . _('Address', 'wpfc') . '<br><textarea cols="40" rows="3" name="wpfc_address"></textarea><br>
		' . _('Previous Address', 'wpfc') . ' <em>(' . _('If less than 3 years', 'wpfc') . ')</em><br><textarea cols="40" rows="3" name="wpfc_prev_address"></textarea><br>
		<input type="number" min="0" max="100" name="wpfc_time_at_address" maxlength="2" /> * ' . _('Years at Address', 'wpfc') . '<br>
		<input type="number" min="0" max="100" name="wpfc_time_at_prev_address" maxlength="2" /> ' . _('Years at Previous Address', 'wpfc') . '<br>
	</p>
	<p>
		<select name="DobDay">
			<option value="">--</option>';
			for($d1=1;$d1<=31;$d1++) {
				$display .= '<option value="'.$d1.'">'.$d1.'</option>';
			}
			$display .= '
		</select> / <select name="DobMonth">
			<option value="">--</option>';
			for($d2=1;$d2<=12;$d2++) {
				$display .= '<option value="'.$d2.'">'.$d2.'</option>';
			}
			$display .= '
		</select> / <select name="DobYear">
			<option value="">--</option>';
			for($d3=2000;$d3>=1940;$d3--) {
				$display .= '<option value="'.$d3.'">'.$d3.'</option>';
			}
			$display .= '
		</select> * ' . _('Date of Birth', 'wpfc') . '<br>
		<select name="wpfc_live_arr">
			<option value="' . _('House Owner', 'wpfc') . '">' . _('House Owner', 'wpfc') . '</option>
			<option value="' . _('Tenant', 'wpfc') . '">' . _('Tenant', 'wpfc') . '</option>
			<option value="' . _('Living with Parents', 'wpfc') . '">' . _('Living with Parents', 'wpfc') . '</option>						
		</select> ' . _('Living Arrangement', 'wpfc') . '<br>
		<select name="wpfc_marital_status">
			<option value="' . _('Single', 'wpfc') . '">' . _('Single', 'wpfc') . '</option>
			<option value="' . _('Married', 'wpfc') . '">' . _('Married', 'wpfc') . '</option>
			<option value="' . _('Other', 'wpfc') . '">' . _('Other', 'wpfc') . '</option>				
		</select> ' . _('Marital Status', 'wpfc') . '<br>
		<select name="track_replymethod">
			<option value="' . _('Phone', 'wpfc') . '">' . _('Phone', 'wpfc') . '</option>
			<option value="' . _('Email', 'wpfc') . '">' . _('Email', 'wpfc') . '</option>	
		</select> ' . _('Reply By', 'wpfc') . '
	</p>

	<p><strong>' . _('Employment Details', 'wpfc') . '</strong></p>
	<p><input type="text" name="wpfc_occupation"> * ' . _('Occupation', 'wpfc') . '</p>
	<p>
		<input type="text" name="wpfc_company" /> * ' . _('Employer Name', 'wpfc') . '<br>
		<textarea cols="40" rows="3" name="wpfc_company_address"></textarea> * ' . _('Employer Address', 'wpfc') . '<br>
		<input type="number" name="wpfc_company_years" max="100"> <em>(' . _('years', 'wpfc') . ')</em> <input type="number" name="wpfc_company_months" max="12"> <em>(' . _('months', 'wpfc') . ')</em> * ' . _('Duration of Employment', 'wpfc') . '<br>
		' . $f_symbol . ' <input type="number" name="wpfc_income" /> * ' . _('Monthly Income (Net)', 'wpfc') . '<br>
		' . $f_symbol . ' <input type="number" name="wpfc_mortgage" /> * ' . _('Monthly Mortgage', 'wpfc') . '<br>
		' . $f_symbol . ' <input type="number" name="wpfc_spousenet" /> ' . _('Spouse Income (Net)', 'wpfc') . '<br>
		<input type="text" size="15" name="wpfc_bank" /> * ' . _('Bank', 'wpfc') . '<br>
		<input type="text" size="15" name="wpfc_branch" /> * ' . _('Branch', 'wpfc') . '<br>
		<input type="text" size="15" name="wpfc_accn" maxlength="8" /> * ' . _('Account Number', 'wpfc') . '
	</p>

	<p><strong>' . _('Additional Information', 'wpfc') . '</strong></p>
	<p>
		<textarea cols="40" rows="5" name="lead_comment"></textarea><br>
		<select name="CreditCheck">
			<option value="">' . _('Select an option...', 'wpfc') . '</option>
			<option value="' . _('Yes', 'wpfc') . '">' . _('Yes', 'wpfc') . '</option>
			<option value="">' . _('No', 'wpfc') . '</option>					
		</select> * ' . _('Do you consent to having your information credit checked', 'wpfc') . '
	</p>
	<p><input type="submit" value="' . _('Submit Finance Application', 'wpfc') . '" name="submit2"></p>
</form>
		';
		return $display;
	}

	elseif(isset($_POST['submit2'])) {
		$subject = '' . _('Finance Application Form Email', 'wpfc') . '';

		$message = 
			'' . _('Allow credit check?', 'wpfc') . ': ' . $_POST['CreditCheck'] . '<br>' .
			'' . _('Date of Birth', 'wpfc') . ': ' . $_POST['DobDay'].'/'.$_POST['DobMonth'].'/'.$_POST['DobYear'] . '<br>' .
			'' . _('Email Address', 'wpfc') . ': ' . $_POST['EMAIL_2'] . '<br>' .
			'' . _('Final price', 'wpfc') . ': ' . $_POST['FinalPrice'] . '<br>' .
			'' . _('List price', 'wpfc') . ': ' . $_POST['ListPrice'] . '<br>' .
			'' . _('Trade in', 'wpfc') . ': ' . $_POST['finance_TradeIn'] . '<br>' .
			'' . _('Deposit', 'wpfc') . ': ' . $_POST['finance_deposit'] . '<br>' .
			'' . _('Months', 'wpfc') . ': ' . $_POST['finance_months'] . '<br>' .
			'' . _('Comment', 'wpfc') . ': ' . $_POST['lead_comment'] . '<br>' .
			'' . _('Make', 'wpfc') . ': ' . $_POST['param_value1'] . '<br>' .
			'' . _('Model', 'wpfc') . ': ' . $_POST['param_value2'] . '<br>' .
			'' . _('Car spec', 'wpfc') . ': ' . $_POST['param_value3'] . '<br>' .
			'' . _('Account Number', 'wpfc') . ': ' . $_POST['wpfc_accn'] . '<br>' .
			'' . _('Address', 'wpfc') . ': ' . $_POST['wpfc_address'] . '<br>' .
			'' . _('Bank', 'wpfc') . ': ' . $_POST['wpfc_bank'] . '<br>' .
			'' . _('Branch', 'wpfc') . ': ' . $_POST['wpfc_branch'] . '<br>' .
			'' . _('Company', 'wpfc') . ': ' . $_POST['wpfc_company'] . '<br>' .
			'' . _('Company address', 'wpfc') . ': ' . $_POST['wpfc_company_address'] . '<br>' .
			'' . _('Company months', 'wpfc') . ': ' . $_POST['wpfc_company_months'] . '<br>' .
			'' . _('Company years', 'wpfc') . ': ' . $_POST['wpfc_company_years'] . '<br>' .
			'' . _('Email Address', 'wpfc') . ': ' . $_POST['wpfc_email'] . '<br>' .
			'' . _('Name', 'wpfc') . ': ' . $_POST['wpfc_forename'].': ' . $_POST['wpfc_surname'] . '<br>' .
			'' . _('Homephone', 'wpfc') . ': ' . $_POST['wpfc_homephoneSTD'].'-'.$_POST['wpfc_homephone'] . '<br>' .
			'' . _('Income', 'wpfc') . ': ' . $_POST['wpfc_income'] . '<br>' .
			'' . _('Live arr', 'wpfc') . ': ' . $_POST['wpfc_live_arr'] . '<br>' .
			'' . _('Marital Status', 'wpfc') . ': ' . $_POST['wpfc_marital_status'] . '<br>' .
			'' . _('Mobile', 'wpfc') . ': ' . $_POST['wpfc_mobileSTD'].'-'.$_POST['wpfc_mobile'] . '<br>' .
			'' . _('Mortgage', 'wpfc') . ': ' . $_POST['wpfc_mortgage'] . '<br>' .
			'' . _('Occupation', 'wpfc') . ': ' . $_POST['wpfc_occupation'] . '<br>' .
			'' . _('Previous address', 'wpfc') . ': ' . $_POST['wpfc_prev_address'] . '<br>' .
			'' . _('Spouse income', 'wpfc') . ': ' . $_POST['wpfc_spousenet'] . '<br>' .
			'' . _('Time at address', 'wpfc') . ': ' . $_POST['wpfc_time_at_address'] . '<br>' .
			'' . _('Time at previous address', 'wpfc') . ': ' . $_POST['wpfc_time_at_prev_address'] . '<br>' .
			'' . _('Workphone', 'wpfc') . ': ' . $_POST['wpfc_workphoneSTD'].'-'.$_POST['wpfc_workphone'] . '<br>' .
			'' . _('Reply method', 'wpfc') . ': ' . $_POST['track_replymethod'] . '<br>
		';

		$f_email = get_option('wpfc_application_email');

		function set_contenttype($content_type) {
			return 'text/html';
		}
		add_filter('wp_mail_content_type','set_contenttype');

		// send email using WordPress function
		$headers = 
			"MIME-Version: 1.0\n".
			"From: ".$_POST['EMAIL_2']."\n".
			"Content-Type: text/html; charset=\"".get_settings('blog_charset')."\"\n";

		$to = $f_email;
		$mail = wp_mail($to, $subject, $message, $headers);

		if($mail)
			echo '
				<h3>' . _('Thank you', 'wpfc') . '</h3>
				<p>' . _('Your details have been sent to us and will be processed as soon as possible.', 'wpfc') . '</p>
			';
		else
			echo '
				<h3>' . _('Thank you', 'wpfc') . '</h3>
				<p>' . _('An error occurred while sending application email!', 'wpfc') . '</p>
			';
	}

	else {
		$f_rate = $rate; // extract from shortcode instead of get_option('wpfc_finance_rate'); // added in 1.3.2
		$f_symbol = get_option('wpfc_currency_symbol');
		$display = '<script src="' . WPFC_PLUGIN_URL . '/includes/js.finance-1.4.js"></script>

		<p><em>' . _('The following calculator will give you indicative repayments.', 'wpfc') . '</em></p>
		<form name="Finance" action="' . $_SERVER['REQUEST_URI'] . '" method="post" onsubmit="Calculate();">
			<input name="PcentBalloon" value="0" type="hidden">
			<table border="0" summary="form">
				<tbody>';
					if($price != '')
						$display .= '<input name="NetAmount" value="' . $price . '" type="hidden">';
					else
						$display .= '<tr><td>' . _('Price of Car', 'wpfc') . '</td><td><input name="NetAmount" value="0" size="8" type="number" onfocus="Calculate();"></td></tr>';
					$display .= '
					<tr>
						<td>' . _('Finance Rate', 'wpfc') . '</td>
						<td><input name="Rate" value="' . $f_rate . '" type="number" min="0" max="100" step="0.1" onfocus="Calculate();">%</td>
					</tr>
					<tr>
						<td>' . _('Less Deposit', 'wpfc') . '</td>
						<td><input maxlength="8" name="Deposit" size="8" type="number" value="0" onfocus="Calculate();"></td>
					</tr>
					<tr>
						<td>' . _('Less Trade In Allowance', 'wpfc') . '</td>
						<td><input maxlength="8" name="TradeIn" size="8" type="number" value="0" onfocus="Calculate();"></td>
					</tr>
					<tr>
						<td colspan="2"><p>' . _('Monthly payment', 'wpfc') . ' <input name="Include" value="+" size="7" readonly="readonly" type="text"> ' . _('payment protection, presuming a typical APR of', 'wpfc') . ' ' . $f_rate . '%</p></td>
					</tr>
					<tr>
						<td class="finance_repayments"><input name="finance_Months" value="12" onclick="Calculate();" type="radio"> 12 ' . _('months', 'wpfc') . '</td>
						<td>
							' . $f_symbol . '<input value="0" name="monthpay1" size="7" readonly="readonly" type="text">/' . _('month', 'wpfc') . '
							<input value="0" name="finalpay1" size="10" type="hidden">
							<input value="0" name="credit1" size="10" type="hidden">
							<input value="0" name="total1" size="10" type="hidden">
						</td>
					</tr>
					<tr>
						<td><input name="finance_Months" value="24" onclick="Calculate();" type="radio"> 24 ' . _('months', 'wpfc') . '</td>
						<td>
							' . $f_symbol . '<input value="0" name="monthpay2" size="7" readonly="readonly">/' . _('month', 'wpfc') . '
							<input value="0" name="finalpay2" size="10" type="hidden">
							<input value="0" name="credit2" size="10" type="hidden">
							<input value="0" name="total2" size="10" type="hidden">
						</td>
					</tr>
					<tr>
						<td><input name="finance_Months" value="36" onclick="Calculate();" type="radio"> 36 ' . _('months', 'wpfc') . '</td>
						<td>
							' . $f_symbol . '<input value="0" name="monthpay3" size="7" readonly="readonly">/' . _('month', 'wpfc') . '
							<input value="0" name="finalpay3" size="10" type="hidden">
							<input value="0" name="credit3" size="10" type="hidden">
							<input value="0" name="total3" size="10" type="hidden">
						</td>
					</tr>
					<tr>
						<td><input name="finance_Months" value="48" onclick="Calculate();" type="radio"> 48 ' . _('months', 'wpfc') . '</td>
						<td>
							' . $f_symbol . '<input value="0" name="monthpay4" size="7" readonly="readonly">/' . _('month', 'wpfc') . '
							<input value="0" name="finalpay4" size="10" type="hidden">
							<input value="0" name="credit4" size="10" type="hidden">
							<input value="0" name="total4" size="10" type="hidden">
						</td>
					</tr>
					<tr>
						<td><input name="finance_Months" value="60" onclick="Calculate();" checked="checked" type="radio"> 60 ' . _('months', 'wpfc') . '</td>
						<td>
							' . $f_symbol . '<input value="0" name="monthpay5" size="7" readonly="readonly">/' . _('month', 'wpfc') . '
							<input value="0" name="finalpay5" size="10" type="hidden">
							<input value="0" name="credit5" size="10" type="hidden">
							<input value="0" name="total5" size="10" type="hidden">
						</td>
					</tr>
					<tr>
						<td colspan="2" class="financecost">' . _('Total cost of the credit:', 'wpfc') . ' ' . $f_symbol . '<input value="0" readonly="readonly" id="total_cost" size="8" type="text"></td>
					</tr>
					<tr>
						<td colspan="2"><input checked="checked" name="PPP" value="Yes" onclick="Calculate()" type="checkbox"> ' . _('Check/uncheck this box to view figures with/without Payment Protection', 'wpfc') . '</td>
					</tr>
					<tr>
						<td colspan="2">
							<p>
								<input onclick="Calculate()" value="' . _('Calculate', 'wpfc') . '" type="button" /> <input type="submit" name="submit" value="' . _('Make Finance Application', 'wpfc') . '">
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</form>';
		if(get_option('wpfc_credit') == 1)
			$display .= '<p><small>' . _('Finance Calculator created by', 'wpfc') . ' <a href="http://getbutterfly.com/" rel="external">getButterfly</a></small></p>';

		return $display;
	}
}

add_shortcode('finance_calculator', 'display_finance_calculator');

// Check for uninstall hook
if(function_exists('register_uninstall_hook'))
	register_uninstall_hook(__FILE__, 'wpfc_uninstall');

// Uninstall function
function wpfc_uninstall() {
	delete_option('wpfc_finance_rate');
	delete_option('wpfc_application_email');
	delete_option('wpfc_currency');
	delete_option('wpfc_currency_symbol');
	delete_option('wpfc_credit');
}
?>
