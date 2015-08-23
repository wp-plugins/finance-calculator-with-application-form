<?php
/*
Plugin Name: Finance Calculator
Plugin URI: http://getbutterfly.com/wordpress-plugins-free/
Description: Finance Calculator is a drop in form for users to calculate indicative repayments. It can be implemented on a page or a post.
Author: Ciprian Popescu
Author URI: http://getbutterfly.com/
Version: 1.5.6

WP Finance Calculator WordPress Plugin
Copyright (C) 2010, 2011, 2012, 2013, 2014, 2015 Ciprian Popescu (getbutterfly@gmail.com)

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
define('WPFC_VERSION', '1.5.6');
//

// plugin localization
$plugin_dir = basename(dirname(__FILE__)); 
load_plugin_textdomain('wpfc', false, $plugin_dir . '/languages'); 
//

add_action('admin_menu', 'wpfcs_plugin_menu');

add_option('wpfc_finance_rate', 11);
add_option('wpfc_application_email', '');
add_option('wpfc_currency', 'EUR');
add_option('wpfc_currency_symbol', '&euro;');
add_option('wpfc_credit', 1);

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

function wpfcs_plugin_menu() {
	add_options_page(__('Finance Calculator', 'wpfc'), __('Finance Calculator', 'wpfc'), 'manage_options', 'wpfcs', 'wpfc_plugin_options');
}

function wpfc_plugin_options() {
    // See if the user has posted us some information // if they did, this hidden field will be set to 'Y'
	if(isset($_POST['wpfcs_submit'])) {
		update_option('wpfc_finance_rate', floatval($_POST['wpfc_finance_rate']));
		update_option('wpfc_application_email', sanitize_email($_POST['wpfc_application_email']));
		update_option('wpfc_currency', sanitize_text_field($_POST['wpfc_currency']));
		update_option('wpfc_currency_symbol', sanitize_text_field($_POST['wpfc_currency_symbol']));
		update_option('wpfc_credit', sanitize_text_field($_POST['wpfc_credit']));

		update_option('wpfcs_loan_options', sanitize_text_field($_POST['wpfcs_loan_options']));

		echo '<div class="updated"><p><strong>Settings saved.</strong></p></div>';
	}

	// read in existing option value from database
    $wpfc_finance_rate 		= get_option('wpfc_finance_rate');
    $wpfc_application_email = get_option('wpfc_application_email');
    $wpfc_currency 			= get_option('wpfc_currency');
    $wpfc_currency_symbol 	= get_option('wpfc_currency_symbol');
    $wpfc_credit 			= get_option('wpfc_credit');

	$wpfcs_loan_options 	= get_option('wpfcs_loan_options');
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>(<acronym title="WordPress Finance Calculator Suite">WPFCS</acronym>) Finance Calculator Suite Settings</h2>
		<p>You are currently using <b>Finance Calculator</b> version <b><?php echo WPFC_VERSION; ?></b> with <b><?php bloginfo('charset'); ?></b> charset.</p>
		<form name="form1" method="post" action="">
			<h3>Finance Calculator Options</h3>
			<p>
				<input type="number" name="wpfc_finance_rate" id="wpfc_finance_rate" value="<?php echo $wpfc_finance_rate; ?>" min="0" max="100" step="0.1"> <label for="wpfc_finance_rate">Finance Rate <span class="description">- Monthly payment will be calculated using this default rate.</span></label>
			</p>
			<p>
				<input type="email" name="wpfc_application_email" id="wpfc_application_email" value="<?php echo $wpfc_application_email; ?>" class="regular-text"> <label for="wpfc_application_email">Application Email</label>
				<br>
				<span class="description">Application emails will be sent to this address.</span>
			</p>
			<p>
				<input type="text" name="wpfc_currency" id="wpfc_currency" value="<?php echo $wpfc_currency; ?>" size="3"> <label for="wpfc_currency">Currency Code <span class="description">- Currency used in application emails. Use USD, EUR, GBP, YEN/JPY.</span></label>
				<br>
				<input type="text" name="wpfc_currency_symbol" id="wpfc_currency_symbol" value="<?php echo $wpfc_currency_symbol; ?>" size="3"> <label for="wpfc_currency_symbol">Currency Symbol <span class="description">- Currency used in application emails. Use characters ($, &euro;, &pound;, &yen;) for symbol.</span></label>
			</p>

			<h3>Loan Calculator Options</h3>
			<p>
				<input type="text" name="wpfcs_loan_options" id="wpfcs_loan_options" value="<?php echo $wpfcs_loan_options; ?>" class="regular-text"> <label for="<?php echo $wpfcs_loan_options; ?>">Loan Options</label>
				<br>
				<span class="description">These options will populate a <code>select</code> dropdown field (example: <b>name|percentage,name|percentage,name|percentage</b>)</span><br>
				<span class="description">e.g. Motor Loan|7.9,Standard Loan|9,College Loan|7,Green Loan|7.9,Secured Loan|5.5,Savers Loan|5.5</span>
			</p>

			<h3>General Options</h3>
			<p>
				<select name="wpfc_credit">
					<option value="1"<?php if($wpfc_credit == 1) echo ' selected' ; ?>>Yes, show a link at the bottom of the calculator form</option>
					<option value="0"<?php if($wpfc_credit == 0) echo ' selected' ; ?>>No, do not show</option>
				</select> <label for="wpfc_credit">Help the author by providing a backlink to the official plugin site (optional).</label>
			</p>
			<p class="submit">
				<input type="submit" name="wpfcs_submit" class="button-primary" value="Save Changes">
			</p>
		</form>

		<h3>Plugin Usage</h3>
		<p>Add the <code>[finance_calculator]</code> shortcode to any post or page to start using the finance calculator. The calculator will use the default finance rate.</p>
		<p>Add the <code>[loan_calculator]</code> shortcode to any post or page to start using the loan calculator.</p>
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
	</div>
    <div class="postbox">
        <div class="inside">
            <p>For support, feature requests and bug reporting, please visit the <a href="//getbutterfly.com/" rel="external">official website</a>.</p>
            <p>&copy;<?php echo date('Y'); ?> <a href="//getbutterfly.com/" rel="external"><strong>getButterfly</strong>.com</a> &middot; <a href="//getbutterfly.com/forums/" rel="external">Support forums</a> &middot; <a href="//getbutterfly.com/trac/" rel="external">Trac</a> &middot; <small>Code wrangling since 2005</small></p>
        </div>
    </div>
<?php
}

function display_loan_calculator($atts, $content = null) {
	extract(shortcode_atts(array(
		'rate' => get_option('wpfc_finance_rate'),
		'price' => ''
	), $atts));

	$display = '<script src="' . WPFC_PLUGIN_URL . '/includes/js.finance-1.4.js"></script>';
	$display .= '
	<form name="frmCalc">
		<table>
			<tr>
				<td><strong>Loan Type:</strong></td>
				<td>
					<select name="slt_type" id="slt_type">
						<option value="0">Select Loan Type...</option>';

						$wpfcs_loan_options = explode(',', get_option('wpfcs_loan_options'));
						foreach($wpfcs_loan_options as $cat) {
							$cat_taxs = explode('|', $cat);
							$display .= '<option value="' . $cat_taxs[1] . '">' . $cat_taxs[0] . ' (' . $cat_taxs[1] . '%)</option>';
						}

					$display .= '
					</select>
				</td>
			</tr>
			<tr>
				<td>Amount of Loan:</td>
				<td>' . get_option('wpfc_currency_symbol') . '<input name="txtAmt" type="text" id="txtAmt" size="10"></td>
			</tr>
			<tr>
				<td>Repayment Period in Years:</td>
				<td>
					<select name="txtYrs" id="txtYrs" onchange="calcAmt(this.form);">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
						<option value="19">19</option>
						<option value="20">20</option>
						<option value="21">21</option>
						<option value="22">22</option>
						<option value="23">23</option>
						<option value="24">24</option>
						<option value="25">25</option>
						<option value="26">26</option>
						<option value="27">27</option>
						<option value="28">28</option>
						<option value="29">29</option>
						<option value="30">30</option>
					</select>
					<input type="button" name="btnCalc" id="btnCalc" value="Calculate" onclick="calcAmt(this.form)">
				</td>
			</tr>
			<tr>
				<td>Weekly Payment:</td>
				<td>' . get_option('wpfc_currency_symbol') . '<input name="txtWk" type="text" size="10"></td>
			</tr>
			<tr>
				<td>Fortnightly Payment:</td>
				<td>' . get_option('wpfc_currency_symbol') . '<input name="txtFn" type="text" size="10"></td>
			</tr>
			<tr>
				<td>Monthly Payment:</td>
				<td>' . get_option('wpfc_currency_symbol') . '<input name="txtMnth" type="text" id="txtMnth2" size="10"></td>
			</tr>
			<tr>
				<td>Total Repayment Amount:</td>
				<td>' . get_option('wpfc_currency_symbol') . '<input name="txtTotal" type="text" id="txtTotal" size="10"></td>
			</tr>
			<tr>
				<td>Total Interest Payable:</td>
				<td>' . get_option('wpfc_currency_symbol') . '<input name="txtInt" type="text" id="txtInt" size="10"></td>
			</tr>
		</table>
	</form>';

	return $display;
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
		<h3>' . __('Finance Application Form', 'wpfc') . '</h3>
		<p>* ' . __('Required Fields', 'wpfc') . '</p>

<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" name="form1">
	<input type="hidden" name="finance_months" value="' . $financemonths . '">
	<input type="hidden" name="finance_payments" value="0">

	<input type="hidden" name="finance_Currency" value="' . $f_currency . '">
	<input type="hidden" name="Checkform" value="Yes">

	<p><strong>' . __('Vehicle Details', 'wpfc') . '</strong></p>

	<p>
		<input type="text" name="param_value1" value=""><input type="hidden" name="param_key1" value="Make"> ' . __('Make', 'wpfc') . '<br>
		<input type="text" name="param_value2" value=""><input type="hidden" name="param_key2" value="Model"> ' . __('Model', 'wpfc') . '<br>
		<input type="text" name="param_value3" value=""><input type="hidden" name="param_key3" value="Car Spec"> ' . __('Spec', 'wpfc') . '<br>
		<input type="text" name="lead_caryear" value="' . date('Y') . '"> ' . __('Year', 'wpfc') . '
	</p>

	<p><strong>' . __('Finance Details', 'wpfc') . '</strong></p>

	<p>
		' . $f_symbol . ' <input type="number" name="ListPrice" value="'.$listprice.'"> ' . __('List Price', 'wpfc') . '<br>
		' . $f_symbol . ' <input type="number" name="FinalPrice" value="'.$finalprice.'"> ' . __('Amount', 'wpfc') . '<br>
		' . $f_symbol . ' <input type="number" name="finance_deposit" value="'.$deposit.'"> ' . __('Deposit', 'wpfc') . '<br>
		' . $f_symbol . ' <input type="number" name="finance_TradeIn" value="'.$tradein.'"> ' . __('Trade In', 'wpfc') . '
	</p>

	<p><strong>' . __('Applicant Details', 'wpfc') . '</strong></p>

	<p>
		<input type="text" name="wpfc_forename" /><input type="hidden" name="wpfc_forename_required" value="' . __('Please enter your first name!', 'wpfc') . '" /> * ' . __('First Name', 'wpfc') . '<br>
		<input type="text" name="wpfc_surname" /><input type="hidden" name="wpfc_surname_required" value="' . __('Please enter your last name!', 'wpfc') . '" /> * ' . __('Last Name', 'wpfc') . '<br>
		<input type="text" size="3" name="wpfc_workphoneSTD" /> - <input type="text" size="15" name="wpfc_workphone" /> * ' . __('Work Phone', 'wpfc') . '<br>
		<input type="text" size="3" name="wpfc_homephoneSTD" /> - <input type="text" size="15" name="wpfc_homephone" /> * ' . __('Home Phone', 'wpfc') . '<br>
		<input type="text" size="3" name="wpfc_mobileSTD" /> - <input type="text" size="15" name="wpfc_mobile" /> * ' . __('Mobile Phone', 'wpfc') . '<br>
		<input type="email" name="wpfc_email" /> * ' . __('Email Address', 'wpfc') . '<br>
		<input type="email" name="EMAIL_2" /> * ' . __('Confirm Email Address', 'wpfc') . '<br>
		* ' . __('Address', 'wpfc') . '<br><textarea cols="40" rows="3" name="wpfc_address"></textarea><br>
		' . __('Previous Address', 'wpfc') . ' <em>(' . __('If less than 3 years', 'wpfc') . ')</em><br><textarea cols="40" rows="3" name="wpfc_prev_address"></textarea><br>
		<input type="number" min="0" max="100" name="wpfc_time_at_address" maxlength="2" /> * ' . __('Years at Address', 'wpfc') . '<br>
		<input type="number" min="0" max="100" name="wpfc_time_at_prev_address" maxlength="2" /> ' . __('Years at Previous Address', 'wpfc') . '<br>
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
		</select> * ' . __('Date of Birth', 'wpfc') . '<br>
		<select name="wpfc_live_arr">
			<option value="' . __('House Owner', 'wpfc') . '">' . __('House Owner', 'wpfc') . '</option>
			<option value="' . __('Tenant', 'wpfc') . '">' . __('Tenant', 'wpfc') . '</option>
			<option value="' . __('Living with Parents', 'wpfc') . '">' . __('Living with Parents', 'wpfc') . '</option>						
		</select> ' . __('Living Arrangement', 'wpfc') . '<br>
		<select name="wpfc_marital_status">
			<option value="' . __('Single', 'wpfc') . '">' . __('Single', 'wpfc') . '</option>
			<option value="' . __('Married', 'wpfc') . '">' . __('Married', 'wpfc') . '</option>
			<option value="' . __('Other', 'wpfc') . '">' . __('Other', 'wpfc') . '</option>				
		</select> ' . __('Marital Status', 'wpfc') . '<br>
		<select name="track_replymethod">
			<option value="' . __('Phone', 'wpfc') . '">' . __('Phone', 'wpfc') . '</option>
			<option value="' . __('Email', 'wpfc') . '">' . __('Email', 'wpfc') . '</option>	
		</select> ' . __('Reply By', 'wpfc') . '
	</p>

	<p><strong>' . __('Employment Details', 'wpfc') . '</strong></p>
	<p><input type="text" name="wpfc_occupation"> * ' . __('Occupation', 'wpfc') . '</p>
	<p>
		<input type="text" name="wpfc_company" /> * ' . __('Employer Name', 'wpfc') . '<br>
		<textarea cols="40" rows="3" name="wpfc_company_address"></textarea> * ' . __('Employer Address', 'wpfc') . '<br>
		<input type="number" name="wpfc_company_years" max="100"> <em>(' . __('years', 'wpfc') . ')</em> <input type="number" name="wpfc_company_months" max="12"> <em>(' . __('months', 'wpfc') . ')</em> * ' . __('Duration of Employment', 'wpfc') . '<br>
		' . $f_symbol . ' <input type="number" name="wpfc_income" /> * ' . __('Monthly Income (Net)', 'wpfc') . '<br>
		' . $f_symbol . ' <input type="number" name="wpfc_mortgage" /> * ' . __('Monthly Mortgage', 'wpfc') . '<br>
		' . $f_symbol . ' <input type="number" name="wpfc_spousenet" /> ' . __('Spouse Income (Net)', 'wpfc') . '<br>
		<input type="text" size="15" name="wpfc_bank" /> * ' . __('Bank', 'wpfc') . '<br>
		<input type="text" size="15" name="wpfc_branch" /> * ' . __('Branch', 'wpfc') . '<br>
		<input type="text" size="15" name="wpfc_accn" maxlength="8" /> * ' . __('Account Number', 'wpfc') . '
	</p>

	<p><strong>' . __('Additional Information', 'wpfc') . '</strong></p>
	<p>
		<textarea cols="40" rows="5" name="lead_comment"></textarea><br>
		<select name="CreditCheck">
			<option value="">' . __('Select an option...', 'wpfc') . '</option>
			<option value="' . __('Yes', 'wpfc') . '">' . __('Yes', 'wpfc') . '</option>
			<option value="">' . __('No', 'wpfc') . '</option>					
		</select> * ' . __('Do you consent to having your information credit checked', 'wpfc') . '
	</p>
	<p><input type="submit" value="' . __('Submit Finance Application', 'wpfc') . '" name="submit2"></p>
</form>
		';
		return $display;
	}

	elseif(isset($_POST['submit2'])) {
		$subject = '' . __('Finance Application Form Email', 'wpfc') . '';

		$message = 
			'' . __('Allow credit check?', 'wpfc') . ': ' . $_POST['CreditCheck'] . '<br>' .
			'' . __('Date of Birth', 'wpfc') . ': ' . $_POST['DobDay'].'/'.$_POST['DobMonth'].'/'.$_POST['DobYear'] . '<br>' .
			'' . __('Email Address', 'wpfc') . ': ' . $_POST['EMAIL_2'] . '<br>' .
			'' . __('Final price', 'wpfc') . ': ' . $_POST['FinalPrice'] . '<br>' .
			'' . __('List price', 'wpfc') . ': ' . $_POST['ListPrice'] . '<br>' .
			'' . __('Trade in', 'wpfc') . ': ' . $_POST['finance_TradeIn'] . '<br>' .
			'' . __('Deposit', 'wpfc') . ': ' . $_POST['finance_deposit'] . '<br>' .
			'' . __('Months', 'wpfc') . ': ' . $_POST['finance_months'] . '<br>' .
			'' . __('Comment', 'wpfc') . ': ' . $_POST['lead_comment'] . '<br>' .
			'' . __('Make', 'wpfc') . ': ' . $_POST['param_value1'] . '<br>' .
			'' . __('Model', 'wpfc') . ': ' . $_POST['param_value2'] . '<br>' .
			'' . __('Car spec', 'wpfc') . ': ' . $_POST['param_value3'] . '<br>' .
			'' . __('Account Number', 'wpfc') . ': ' . $_POST['wpfc_accn'] . '<br>' .
			'' . __('Address', 'wpfc') . ': ' . $_POST['wpfc_address'] . '<br>' .
			'' . __('Bank', 'wpfc') . ': ' . $_POST['wpfc_bank'] . '<br>' .
			'' . __('Branch', 'wpfc') . ': ' . $_POST['wpfc_branch'] . '<br>' .
			'' . __('Company', 'wpfc') . ': ' . $_POST['wpfc_company'] . '<br>' .
			'' . __('Company address', 'wpfc') . ': ' . $_POST['wpfc_company_address'] . '<br>' .
			'' . __('Company months', 'wpfc') . ': ' . $_POST['wpfc_company_months'] . '<br>' .
			'' . __('Company years', 'wpfc') . ': ' . $_POST['wpfc_company_years'] . '<br>' .
			'' . __('Email Address', 'wpfc') . ': ' . $_POST['wpfc_email'] . '<br>' .
			'' . __('Name', 'wpfc') . ': ' . $_POST['wpfc_forename'].': ' . $_POST['wpfc_surname'] . '<br>' .
			'' . __('Homephone', 'wpfc') . ': ' . $_POST['wpfc_homephoneSTD'].'-'.$_POST['wpfc_homephone'] . '<br>' .
			'' . __('Income', 'wpfc') . ': ' . $_POST['wpfc_income'] . '<br>' .
			'' . __('Live arr', 'wpfc') . ': ' . $_POST['wpfc_live_arr'] . '<br>' .
			'' . __('Marital Status', 'wpfc') . ': ' . $_POST['wpfc_marital_status'] . '<br>' .
			'' . __('Mobile', 'wpfc') . ': ' . $_POST['wpfc_mobileSTD'].'-'.$_POST['wpfc_mobile'] . '<br>' .
			'' . __('Mortgage', 'wpfc') . ': ' . $_POST['wpfc_mortgage'] . '<br>' .
			'' . __('Occupation', 'wpfc') . ': ' . $_POST['wpfc_occupation'] . '<br>' .
			'' . __('Previous address', 'wpfc') . ': ' . $_POST['wpfc_prev_address'] . '<br>' .
			'' . __('Spouse income', 'wpfc') . ': ' . $_POST['wpfc_spousenet'] . '<br>' .
			'' . __('Time at address', 'wpfc') . ': ' . $_POST['wpfc_time_at_address'] . '<br>' .
			'' . __('Time at previous address', 'wpfc') . ': ' . $_POST['wpfc_time_at_prev_address'] . '<br>' .
			'' . __('Workphone', 'wpfc') . ': ' . $_POST['wpfc_workphoneSTD'].'-'.$_POST['wpfc_workphone'] . '<br>' .
			'' . __('Reply method', 'wpfc') . ': ' . $_POST['track_replymethod'] . '<br>
		';

		$f_email = get_option('wpfc_application_email');

		function set_contenttype($content_type) {
			return 'text/html';
		}
		add_filter('wp_mail_content_type', 'set_contenttype');

		// send email using WordPress function
		$headers = '';
		$headers[] = "From: " . get_option('blogname') . "<" . $_POST['EMAIL_2'] . ">\r\n";
		$headers[] = "Content-Type: text/html;\r\n";

		$to = $f_email;
		$mail = wp_mail($to, $subject, $message, $headers);

		if($mail)
			echo '<h3>' . __('Thank you', 'wpfc') . '</h3><p>' . __('Your details have been sent to us and will be processed as soon as possible.', 'wpfc') . '</p>';
		else
			echo '<h3>' . __('Thank you', 'wpfc') . '</h3><p>' . __('An error occurred while sending application email!', 'wpfc') . '</p>';
	}

	else {
		$f_rate = $rate; // extract from shortcode instead of get_option('wpfc_finance_rate'); // added in 1.3.2
		$f_symbol = get_option('wpfc_currency_symbol');
		$display = '<script src="' . WPFC_PLUGIN_URL . '/includes/js.finance-1.4.js"></script>

		<p><em>' . __('The following calculator will give you indicative repayments.', 'wpfc') . '</em></p>
		<form name="Finance" action="' . $_SERVER['REQUEST_URI'] . '" method="post" onsubmit="Calculate();">
			<input name="PcentBalloon" value="0" type="hidden">
			<table border="0" summary="form">
				<tbody>';
					if($price != '')
						$display .= '<input name="NetAmount" value="' . $price . '" type="hidden">';
					else
						$display .= '<tr><td>' . __('Price of Car', 'wpfc') . '</td><td><input name="NetAmount" value="0" size="8" type="number" onfocus="Calculate();"></td></tr>';
					$display .= '
					<tr>
						<td>' . __('Finance Rate', 'wpfc') . '</td>
						<td><input name="Rate" value="' . $f_rate . '" type="number" min="0" max="100" step="0.1" onfocus="Calculate();">%</td>
					</tr>
					<tr>
						<td>' . __('Less Deposit', 'wpfc') . '</td>
						<td><input maxlength="8" name="Deposit" size="8" type="number" value="0" onfocus="Calculate();"></td>
					</tr>
					<tr>
						<td>' . __('Less Trade In Allowance', 'wpfc') . '</td>
						<td><input maxlength="8" name="TradeIn" size="8" type="number" value="0" onfocus="Calculate();"></td>
					</tr>
					<tr>
						<td colspan="2"><p>' . __('Monthly payment', 'wpfc') . ' <input name="Include" value="+" size="7" readonly="readonly" type="text"> ' . __('payment protection, presuming a typical APR of', 'wpfc') . ' ' . $f_rate . '%</p></td>
					</tr>
					<tr>
						<td class="finance_repayments"><input name="finance_Months" value="12" onclick="Calculate();" type="radio"> 12 ' . __('months', 'wpfc') . '</td>
						<td>
							' . $f_symbol . '<input value="0" name="monthpay1" size="7" readonly="readonly" type="text">/' . __('month', 'wpfc') . '
							<input value="0" name="finalpay1" size="10" type="hidden">
							<input value="0" name="credit1" size="10" type="hidden">
							<input value="0" name="total1" size="10" type="hidden">
						</td>
					</tr>
					<tr>
						<td><input name="finance_Months" value="24" onclick="Calculate();" type="radio"> 24 ' . __('months', 'wpfc') . '</td>
						<td>
							' . $f_symbol . '<input value="0" name="monthpay2" size="7" readonly="readonly">/' . __('month', 'wpfc') . '
							<input value="0" name="finalpay2" size="10" type="hidden">
							<input value="0" name="credit2" size="10" type="hidden">
							<input value="0" name="total2" size="10" type="hidden">
						</td>
					</tr>
					<tr>
						<td><input name="finance_Months" value="36" onclick="Calculate();" type="radio"> 36 ' . __('months', 'wpfc') . '</td>
						<td>
							' . $f_symbol . '<input value="0" name="monthpay3" size="7" readonly="readonly">/' . __('month', 'wpfc') . '
							<input value="0" name="finalpay3" size="10" type="hidden">
							<input value="0" name="credit3" size="10" type="hidden">
							<input value="0" name="total3" size="10" type="hidden">
						</td>
					</tr>
					<tr>
						<td><input name="finance_Months" value="48" onclick="Calculate();" type="radio"> 48 ' . __('months', 'wpfc') . '</td>
						<td>
							' . $f_symbol . '<input value="0" name="monthpay4" size="7" readonly="readonly">/' . __('month', 'wpfc') . '
							<input value="0" name="finalpay4" size="10" type="hidden">
							<input value="0" name="credit4" size="10" type="hidden">
							<input value="0" name="total4" size="10" type="hidden">
						</td>
					</tr>
					<tr>
						<td><input name="finance_Months" value="60" onclick="Calculate();" checked="checked" type="radio"> 60 ' . __('months', 'wpfc') . '</td>
						<td>
							' . $f_symbol . '<input value="0" name="monthpay5" size="7" readonly="readonly">/' . __('month', 'wpfc') . '
							<input value="0" name="finalpay5" size="10" type="hidden">
							<input value="0" name="credit5" size="10" type="hidden">
							<input value="0" name="total5" size="10" type="hidden">
						</td>
					</tr>
					<tr>
						<td colspan="2" class="financecost">' . __('Total cost of the credit:', 'wpfc') . ' ' . $f_symbol . '<input value="0" readonly="readonly" id="total_cost" size="8" type="text"></td>
					</tr>
					<tr>
						<td colspan="2"><input checked="checked" name="PPP" value="Yes" onclick="Calculate()" type="checkbox"> ' . __('Check/uncheck this box to view figures with/without Payment Protection', 'wpfc') . '</td>
					</tr>
					<tr>
						<td colspan="2">
							<p>
								<input onclick="Calculate()" value="' . __('Calculate', 'wpfc') . '" type="button" /> <input type="submit" name="submit" value="' . __('Make Finance Application', 'wpfc') . '">
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</form>';
		if(get_option('wpfc_credit') == 1)
			$display .= '<p><small>' . __('Finance Calculator created by', 'wpfc') . ' <a href="http://getbutterfly.com/" rel="external">getButterfly</a></small></p>';

		return $display;
	}
}

add_shortcode('finance_calculator', 'display_finance_calculator');
add_shortcode('loan_calculator', 'display_loan_calculator');

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
