<?php
/*
Plugin Name: WP Finance Calculator
Plugin URI: http://www.blogtycoon.net/wordpress-plugins/finance-calculator-with-application-form/
Description: WP Finance Calculator is a drop in form for users to calculate indicative repayments. It can be implemented on a page or a post.
Author: Ciprian Popescu
Author URI: http://www.blogtycoon.net/
Version: 1.2
*/

/*
WP Finance Calculator WordPress Plugin
Copyright (C) 2010 Ciprian Popescu

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

error_reporting(0); // Used for debug

if(!defined('WP_CONTENT_URL'))
	define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if(!defined('WP_PLUGIN_URL'))
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');

add_action('admin_menu', 'wpfc_plugin_menu');

add_option('wpfc_finance_rate', '', '', 'no');
add_option('wpfc_application_email', '', '', 'no');
add_option('wpfc_currency', '', '', 'no');
add_option('wpfc_currency_symbol', '', '', 'no');

function wpfc_plugin_menu() {
	add_options_page('WPFC Options', 'WPFC Options', 'manage_options', 'wpfc', 'wpfc_plugin_options');
}

function wpfc_plugin_options() {
	if(!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}

	$hidden_field_name = 'wpfc_submit_hidden';
	$data_field_name = 'wpfc_finance_rate';
	$email_field_name = 'wpfc_application_email';
	$currency_field_name = 'wpfc_currency';
	$symbol_field_name = 'wpfc_currency_symbol';

	// read in existing option value from database
    $option_value_data = get_option('wpfc_finance_rate');
    $option_value_email = get_option('wpfc_application_email');
    $option_value_currency = get_option('wpfc_currency');
    $option_value_symbol = get_option('wpfc_currency_symbol');

    // See if the user has posted us some information // if they did, this hidden field will be set to 'Y'
	if(isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {
		$option_value_data = $_POST[$data_field_name];
		$option_value_email = $_POST[$email_field_name];
		$option_value_currency = $_POST[$currency_field_name];
		$option_value_symbol = $_POST[$symbol_field_name];

		update_option('wpfc_finance_rate', $option_value_data);
		update_option('wpfc_application_email', $option_value_email);
		update_option('wpfc_currency', $option_value_currency);
		update_option('wpfc_currency_symbol', $option_value_symbol);
		?>
		<div class="updated"><p><strong><?php _e('Settings saved.', 'wpfc');?></strong></p></div>
		<?php
	}
	echo '<div class="wrap">';
		echo '<h2>'.__('Finance Calculator Settings', 'wpfc').'</h2>';
		?>
		<form name="form1" method="post" action="">
			<input type="hidden" name="<?php echo $hidden_field_name;?>" value="Y" />
			<p>
				<?php _e('Finance Rate:', 'wpfc');?> <input type="text" name="<?php echo $data_field_name;?>" value="<?php echo $option_value_data;?>" size="10" />
				<span class="description">Monthly payment will be calculated using this rate.</span>
			</p>
			<p>
				<?php _e('Application Email:', 'wpfc');?> <input type="text" name="<?php echo $email_field_name;?>" value="<?php echo $option_value_email;?>" size="20" />
				<span class="description">Application emails will be sent to this address.</span>
			</p>
			<p>
				<?php _e('Currency:', 'wpfc');?> <input type="text" name="<?php echo $currency_field_name;?>" value="<?php echo $option_value_currency;?>" size="3" /> 
				<?php _e('Currency Symbol:', 'wpfc');?> <input type="text" name="<?php echo $symbol_field_name;?>" value="<?php echo $option_value_symbol;?>" size="5" />
				<span class="description">Currency used in application emails. Use EUR, USD, GBP for currency and characters ($, &euro;, &pound;, &yen;) for symbol.</span>
			</p>
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Save Changes');?>" />
			</p>
		</form>

		<hr />
		<p>Add the <code>[finance_calculator]</code> shortcode to any post or page to start using the calculator.</p>

		<p>The payment protection insurance policy pays your loan or hires purchase agreement repayments if you are unable to work because of sickness, an accident or you are made unemployed. It will also provide benefit in the event of your death.</p>
		<p>Eligibility for payment protection is covered under the policy of each company. Please specify these details on the post or page itself.</p>

		<p>Payment protection insurance is a standard add-on feature for many large loans such as car loans, mortgages and other large bill obligations that could become a true nightmare should a disability or death occur. This plan can offer a true measure of security for those who have grave reservations about how a large debt would be paid should a disaster strike. Any person with a small savings reservoir or someone heavily in debt would be a prime candidate for such a safety-net plan. Making sure that a plan is sound and customer friendly remains the responsibility of the buyer.</p>

		<p>For support, feature requests and bug reporting, please visit the <a href="http://www.blogtycoon.net/wordpress-plugins/finance-calculator-with-application-form/" rel="external">official web site</a>.</p>
	</div>
<?php
}

function display_finance_calculator() {
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
<script type="text/javascript" src="'.WP_PLUGIN_URL.'/wp-finance-calculator/includes/email-validation-min.js"></script>
<h3>Finance Application Form</h3>
<p>* Required Fields</p>

<form action="'.$_SERVER['REQUEST_URI'].'" method="post" name="form1" onsubmit="return formvalidation(this)">
	<input type="hidden" name="finance_months" value="'.$financemonths.'" />
	<input type="hidden" name="finance_payments" value="0" />

	<input type="hidden" name="finance_Currency" value="'.$f_currency.'" />
	<input type="hidden" name="Checkform" value="Yes" />

	<p><strong>Vehicle Details</strong></p>

	<p><input type="text" name="param_value1" value="" /><input type="hidden" name="param_key1" value="Make" /> Make</p>
	<p><input type="text" name="param_value2" value="" /><input type="hidden" name="param_key2" value="Model" /> Model</p>
	<p><input type="text" name="param_value3" value="" /><input type="hidden" name="param_key3" value="Car Spec" /> Spec</div>
	<p><input type="text" name="lead_caryear" value="'.date('Y').'" /> Year</p>

	<p><strong>Finance Details</strong></p>

	<p>'.$f_symbol.' <input type="text" name="ListPrice" value="'.$listprice.'" /> List Price</p>
	<p>'.$f_symbol.' <input type="text" name="FinalPrice" value="'.$finalprice.'" /> Amount</p>
	<p>'.$f_symbol.' <input type="text" name="finance_deposit" value="'.$deposit.'" /> Deposit</p>
	<p>'.$f_symbol.' <input type="text" name="finance_TradeIn" value="'.$tradein.'" /> Trade In</p>

	<p><strong>Applicant Details</strong></p>

	<p><input type="text" name="wpfc_forename" /><input type="hidden" name="wpfc_forename_required" value="Please enter your FIRST NAME!" /> * First Name</p>
	<p><input type="text" name="wpfc_surname" /><input type="hidden" name="wpfc_surname_required" value="Please enter your SURNAME!" /> * Surname</p>
	<p><input type="text" size="3" name="wpfc_workphoneSTD" /> - <input type="text" size="15" name="wpfc_workphone" /> * Work Tel</p>
	<p><input type="text" size="3" name="wpfc_homephoneSTD" /> - <input type="text" size="15" name="wpfc_homephone" /> * Home Tel</p>
	<p><input type="text" size="3" name="wpfc_mobileSTD" /> - <input type="text" size="15" name="wpfc_mobile" /> * Mobile Tel</p>
	<p><input type="text" name="wpfc_email" /> * Email</p>
	<p><input type="text" name="EMAIL_2" /> * Confirm Email</p>
	<p><textarea cols="18" rows="3" name="wpfc_address"></textarea> * Address</p>
	<p><textarea cols="18" rows="3" name="wpfc_prev_address"></textarea> Previous Address <em>(If less than 3 years)</em></p>
	<p><input type="text" size="3" name="wpfc_time_at_address" maxlength="2" /> * Years at Address</p>
	<p><input type="text" size="3" name="wpfc_time_at_prev_address" maxlength="2" /> Years at Previous Address</p>
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
			for($d3=1990;$d3>=1940;$d3--) {
				$display .= '<option value="'.$d3.'">'.$d3.'</option>';
			}
			$display .= '
		</select> * Date of Birth
	</p>
	<p>
		<select name="wpfc_live_arr">
			<option value="House Owner">House Owner</option>
			<option value="Tenant">Tenant</option>
			<option value="Living with Parents">Living with Parents</option>						
		</select> Living Arrangement
	</p>
	<p>
		<select name="wpfc_marital_status">
			<option value="Single">Single</option>
			<option value="Married">Married</option>
			<option value="Other">Other</option>				
		</select> Marital Status
	</p>
	<p>
		<select name="track_replymethod">
			<option value="phone">Phone</option>
			<option value="email">Email</option>	
		</select> Reply By
	</p>

	<p><strong>Employment Details</strong></p>
	<p>';
	include('wp-content/plugins/wp-finance-calculator/addon_occupations.php');
	$display .= $display_occupations;
	$display .= '
	</p>
	<p><input type="text" size="15" name="wpfc_company" /> * Employers Name</p>
	<p><textarea cols="18" rows="3" name="wpfc_company_address"></textarea> * Employers Address</p>
	<p><input type="text" name="wpfc_company_years" size="3" maxlength="2" /> <em>(Yrs)</em> <input type="text" name="wpfc_company_months" size="3" maxlength="2" /> <em>(Mths)</em> * Duration of Employment</p>
	<p>'.$f_symbol.' <input type="text" size="15" name="wpfc_income" /> * Monthly Income (Net)</p>
	<p>'.$f_symbol.' <input type="text" size="15" name="wpfc_mortgage" /> * Monthly Mortgage</p>
	<p>'.$f_symbol.' <input type="text" size="15" name="wpfc_spousenet" /> Spouse Income (Net)</p>
	<p><input type="text" size="15" name="wpfc_bank" /> * Bank</p>
	<p><input type="text" size="15" name="wpfc_branch" /> * Branch</p>
	<p><input type="text" size="15" name="wpfc_accn" maxlength="8" /> * Account Number</p>

	<p><strong>Additional Information</strong> - to help us make a fast underwriting decision</p>
	<p><textarea cols="60" rows="5" name="lead_comment"></textarea></p>
	<p>
		<select name="CreditCheck">
			<option value="">-- Please Choose --</option>
			<option value="Yes">Yes</option>
			<option value="">No</option>					
		</select> * Do you consent to having your information credit checked
	</p>
	<p><input type="submit" value="Submit Finance Application" name="submit2" />
</form>
		';
		return $display;
	}

	elseif(isset($_POST['submit2'])) {
		$subject = 'Finance Application Form Email';

		$message = 
			'Allow credit check? '.$_POST['CreditCheck'].'<br />'.
			'Date of birth: '.$_POST['DobDay'].'/'.$_POST['DobMonth'].'/'.$_POST['DobYear'].'<br />'.
			'Email: '.$_POST['EMAIL_2'].'<br />'.
			'Final price: '.$_POST['FinalPrice'].'<br />'.
			'List price: '.$_POST['ListPrice'].'<br />'.
			'Trade in: '.$_POST['finance_TradeIn'].'<br />'.
			'Deposit: '.$_POST['finance_deposit'].'<br />'.
			'Months: '.$_POST['finance_months'].'<br />'.
			'Comment: '.$_POST['lead_comment'].'<br />'.
			'Make: '.$_POST['param_value1'].'<br />'.
			'Model: '.$_POST['param_value2'].'<br />'.
			'Car spec: '.$_POST['param_value3'].'<br />'.
			'Account: '.$_POST['wpfc_accn'].'<br />'.
			'Address: '.$_POST['wpfc_address'].'<br />'.
			'Bank: '.$_POST['wpfc_bank'].'<br />'.
			'Branch: '.$_POST['wpfc_branch'].'<br />'.
			'Company: '.$_POST['wpfc_company'].'<br />'.
			'Company address: '.$_POST['wpfc_company_address'].'<br />'.
			'Company months: '.$_POST['wpfc_company_months'].'<br />'.
			'Company years: '.$_POST['wpfc_company_years'].'<br />'.
			'Email: '.$_POST['wpfc_email'].'<br />'.
			'Name: '.$_POST['wpfc_forename'].' '.$_POST['wpfc_surname'].'<br />'.
			'Homephone: '.$_POST['wpfc_homephoneSTD'].'-'.$_POST['wpfc_homephone'].'<br />'.
			'Income: '.$_POST['wpfc_income'].'<br />'.
			'Live arr: '.$_POST['wpfc_live_arr'].'<br />'.
			'Marital status: '.$_POST['wpfc_marital_status'].'<br />'.
			'Mobile: '.$_POST['wpfc_mobileSTD'].'-'.$_POST['wpfc_mobile'].'<br />'.
			'Mortgage: '.$_POST['wpfc_mortgage'].'<br />'.
			'Occupation: '.$_POST['wpfc_occupation'].'<br />'.
			'Previous address: '.$_POST['wpfc_prev_address'].'<br />'.
			'Spouse income: '.$_POST['wpfc_spousenet'].'<br />'.
			'Time at address: '.$_POST['wpfc_time_at_address'].'<br />'.
			'Time at previous address: '.$_POST['wpfc_time_at_prev_address'].'<br />'.
			'Workphone: '.$_POST['wpfc_workphoneSTD'].'-'.$_POST['wpfc_workphone'].'<br />'.
			'Reply method: '.$_POST['track_replymethod'].'<br />
		';

		$f_email = get_option('wpfc_application_email');

		function set_contenttype($content_type) {
			return 'text/html';
		}
		add_filter('wp_mail_content_type','set_contenttype');

		// send email using WordPress function
		$to = $f_email;
		$mail = wp_mail($to, $subject, $message);

		// use for hindered WP installation // most likely never
		// $mail = $f_email;
		// mail($mail, $subject, $mesaj, "From: $mail\nContent-Type: text/html; charset=iso-8859-1");

		if($mail)
			echo '
				<h3>Thank you</h3>
				<p>Your details have been sent to us and will be processed as soon as possible.</p>
			';
		else
			echo '
				<h3>Thank you</h3>
				<p>An error occurred while sending application email!</p>
			';
	}

	else {
		$f_rate = get_option('wpfc_finance_rate');
		$f_symbol = get_option('wpfc_currency_symbol');
		$display = '
<script type="text/javascript">var finance_fees=0</script>
<script type="text/javascript" src="'.WP_PLUGIN_URL.'/wp-finance-calculator/includes/js_financecalc-min.js"></script>

<h3>Finance Calculator</h3>
<p><em>The following calculator will give you indicative repayments.</em></p>
<form name="Finance" action="'.$_SERVER['REQUEST_URI'].'" method="post" onsubmit="Calculate();">
	<div>
		<!-- Balloon value -->
		<input name="PcentBalloon" value="0" type="hidden" />
		<!-- Finance Display Option -->
		<input name="Rate" value="'.$f_rate.'" type="hidden" />
	</div>
	<table border="0" summary="form">
		<tbody>
			<tr>
				<td>Price of Car</td>
				<td><input name="NetAmount" value="0" size="8" type="text" /></td>
			</tr>
			<tr>
				<td>Finance Rate:</td>
				<td>'.$f_rate.'%</td>
			</tr>
			<tr>
				<td>Less Deposit:</td>
				<td><input maxlength="8" name="Deposit" size="8" value="0" onfocus="Calculate();" /></td>
			</tr>
			<tr>
				<td>Less Trade In Allowance:</td>
				<td><input maxlength="8" name="TradeIn" size="8" value="0" onfocus="Calculate();" /></td>
			</tr>
			<tr>
				<td colspan="2"><p>Monthly payment <input name="Include" value="including" size="7" readonly="readonly" type="text" /> payment protection, presuming a typical APR of '.$f_rate.'%:</p></td>
			</tr>
			<tr>
				<td class="finance_repayments"><input name="finance_Months" value="12" onclick="Calculate();" type="radio" /> 12 months: '.$f_symbol.'</td>
				<td>
					<input value="0" name="monthpay1" size="7" readonly="readonly" type="text" />/month
					<input value="0" name="finalpay1" size="10" type="hidden" />
					<input value="0" name="credit1" size="10" type="hidden" />
					<input value="0" name="total1" size="10" type="hidden" />
				</td>
			</tr>
			<tr>
				<td><input name="finance_Months" value="24" onclick="Calculate();" type="radio" /> 24 months: '.$f_symbol.'</td>
				<td>
					<input value="0" name="monthpay2" size="7" readonly="readonly" />/month
					<input value="0" name="finalpay2" size="10" type="hidden" />
					<input value="0" name="credit2" size="10" type="hidden" />
					<input value="0" name="total2" size="10" type="hidden" />
				</td>
			</tr>
			<tr>
				<td><input name="finance_Months" value="36" onclick="Calculate();" type="radio" /> 36 months: '.$f_symbol.'</td>
				<td>
					<input value="0" name="monthpay3" size="7" readonly="readonly" />/month
					<input value="0" name="finalpay3" size="10" type="hidden" />
					<input value="0" name="credit3" size="10" type="hidden" />
					<input value="0" name="total3" size="10" type="hidden" />
				</td>
			</tr>
			<tr>
				<td><input name="finance_Months" value="48" onclick="Calculate();" type="radio" /> 48 months: '.$f_symbol.'</td>
				<td>
					<input value="0" name="monthpay4" size="7" readonly="readonly" />/month
					<input value="0" name="finalpay4" size="10" type="hidden" />
					<input value="0" name="credit4" size="10" type="hidden" />
					<input value="0" name="total4" size="10" type="hidden" />
				</td>
			</tr>
			<tr>
				<td><input name="finance_Months" value="60" onclick="Calculate();" checked="checked" type="radio" /> 60 months: '.$f_symbol.'</td>
				<td>
					<input value="0" name="monthpay5" size="7" readonly="readonly" />/month
					<input value="0" name="finalpay5" size="10" type="hidden" />
					<input value="0" name="credit5" size="10" type="hidden" />
					<input value="0" name="total5" size="10" type="hidden" />
				</td>
			</tr>
			<tr>
				<td colspan="2" class="financecost"> Total cost of the credit: '.$f_symbol.'<input value="0" readonly="readonly" id="total_cost" size="8" type="text" /></td>
			</tr>
			<tr>
				<td colspan="2"><input checked="checked" name="PPP" value="Yes" onclick="Calculate()" type="checkbox" /> Check/uncheck this box to view figures with/without Payment Protection </td>
			</tr>
			<tr>
				<td colspan="2"><input onclick="Calculate()" value="Calculate" type="button" /> <input type="submit" name="submit" value="Make Finance Application" /></td>
			</tr>
		</tbody>
	</table>
</form>
';
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
}
?>
