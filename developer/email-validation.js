function emailvalidation(entered, alertbox) {
	with(entered) {
		apos = value.indexOf('@');
		dotpos = value.lastIndexOf('.');
		lastpos = value.length-1;
		if(apos<1 || dotpos-apos<2 || lastpos-dotpos>3 || lastpos-dotpos<2) {
			if(alertbox) {
				alert(alertbox);
			}
			return false;
		}
		else {
			return true;
		}
	}
}

function emptyvalidation(entered, alertbox) {
	with(entered) {
		if(value == null || value == '') {
			if(alertbox != '') {
				alert(alertbox);
			}
			return false;
		}
		else {
			return true;
		}
	}
}

function accvalidation(entered, alertbox) {
	with(entered) {
		if(value.length<8) {
			if(alertbox != '') {
				alert(alertbox);
			}
			return false;
		}
		else {
			return true;
		}
	}
}

function checkEmail(entered, alertbox) {
	with(entered) {
		if(form1.wpfc_email.value != form1.EMAIL_2.value) {
			if(alertbox != '') {
				alert(alertbox);
			}
			return false;
		}
		else {
			return true;
		}
	}
}

function phvalidation(entered, alertbox) {
	with(entered) {
		if(value.length<7) {
			if(alertbox != '') {
				alert(alertbox);
			}
			return false;
		}
		else {
			return true;
		}
	}
}

function formvalidation(thifform) {
	with(thifform) {
		if(emptyvalidation(wpfc_forename,'Please enter your First name!') == false) {
			wpfc_forename.focus();
			return false;
		}
		if(emptyvalidation(wpfc_surname,'Please enter your Surname!') == false) {
			wpfc_surname.focus();
			return false;
		}
		if(emailvalidation(wpfc_email,'Please enter valid e-mail address!') == false) {
			wpfc_email.focus();
			return false;
		}
		if(checkEmail(EMAIL_2,'Please re-confirm your email!') == false) {
			EMAIL_2.focus();
			return false;
		}
		if(emptyvalidation(wpfc_address,'Please enter your Address!') == false) {
			wpfc_address.focus();
			return false;
		}
		if(emptyvalidation(wpfc_time_at_address,'Please enter the time at this address!') == false) {
			wpfc_time_at_address.focus();
			return false;
		}
		if(form1.wpfc_time_at_address.value < 3) {
			if(emptyvalidation(wpfc_prev_address,'Please enter your previous address!') == false) {
				wpfc_prev_address.focus();
				return false;
			}
		}
		if(emptyvalidation(wpfc_homephone,'Please enter your home telephone number!') == false) {
			wpfc_homephone.focus();
			return false;
		}
		if(emptyvalidation(wpfc_workphone,'Please enter your work telephone number!') == false) {
			wpfc_workphone.focus();
			return false;
		}
		if(emptyvalidation(wpfc_mobile,'Please enter your mobile number!') == false) {
			wpfc_mobile.focus();
			return false;
		}
		if(phvalidation(wpfc_mobile,'Please enter your full mobile number!') == false) {
			wpfc_mobile.focus();
			return false;
		}
		if(emptyvalidation(DobDay,'Please enter your Date of Birth!') == false) {
			DobDay.focus();
			return false;
		}
		if(emptyvalidation(DobMonth,'Please enter your Date of Birth!') == false) {
			DobMonth.focus();
			return false;
		}
		if(emptyvalidation(wpfc_occupation,'Please enter your Occupation!') == false) {
			wpfc_occupation.focus();
			return false;
		}
		if(emptyvalidation(wpfc_company,'Please enter the name of your employer!') == false) {
			wpfc_company.focus();
			return false;
		}
		if(emptyvalidation(wpfc_company_address,'Please enter the address of your employer!') == false) {
			wpfc_company_address.focus();
			return false;
		}
		if(emptyvalidation(wpfc_company_years,'Please enter the number of years you have been employed!') == false) {
			wpfc_company_years.focus();
			return false;
		}
		if(emptyvalidation(wpfc_income,'Please enter your monthly income!') == false) {
			wpfc_income.focus();
			return false;
		}
		if(emptyvalidation(wpfc_mortgage,'Please enter your monthly mortgage!') == false) {
			wpfc_mortgage.focus();
			return false;
		}
		if(form1.wpfc_marital_status[0].checked == true) {
			if(emptyvalidation(wpfc_spousenet,'Please enter your spouse income!') == false) {
				wpfc_spousenet.focus();
				return false;
			}
			if(emptyvalidation(wpfc_spouse_occup,'Please enter your spouses occupation!') == false) {
				wpfc_spouse_occup.focus();
				return false;
			}
		}
		if(emptyvalidation(wpfc_bank,'Please enter the name of your bank!') == false) {
			wpfc_bank.focus();
			return false;
		}
		if(emptyvalidation(wpfc_branch,'Please enter the branch of your bank!') == false) {
			wpfc_branch.focus();
			return false;
		}
		if(accvalidation(wpfc_accn,'Please enter a valid account number!') == false) {
			wpfc_accn.focus();
			return false;
		}
		if(emptyvalidation(CreditCheck,'You must consent to a credit check!') == false) {
			CreditCheck.focus();
			return false;
		}
	}
}
