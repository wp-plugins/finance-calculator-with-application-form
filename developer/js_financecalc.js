function IsNumeric(strUserValue) {
	var strValidChars = '0123456789.';
	var intIsNumeric = true;
	for(var intCount = 0; intCount < strUserValue.length; intCount++) {
		if(strValidChars.indexOf(strUserValue.charAt(intCount)) == -1) intIsNumeric = false;
	}
	return(intIsNumeric) && (strUserValue != '');
}
	
function Calculate(){
	var wpfcInc = 'including';
	var wpfcExc = 'excluding';
	var PPPRate = 1.15;
	if(!(IsNumeric(document.Finance.Rate.value) && IsNumeric(document.Finance.NetAmount.value) && IsNumeric(document.Finance.Deposit.value) && IsNumeric(document.Finance.TradeIn.value)))
		alert('You must enter a valid number.');
	else {
		if((document.Finance.Rate.value<0) || (document.Finance.Rate.value>100))
			alert('APR must be between 0 and 100');
		else {
			if((parseInt(document.Finance.NetAmount.value) < parseInt(document.Finance.Deposit.value)))
				alert('The cash deposit may not be greater than the value of the car.');
			else {
				BalloonValue = parseFloat(document.Finance.NetAmount.value) * parseFloat(document.Finance.PcentBalloon.value/100);
				AmtFinanced = parseFloat(document.Finance.NetAmount.value) - parseFloat(BalloonValue) - parseFloat(document.Finance.Deposit.value) - parseFloat(document.Finance.TradeIn.value);
				NetCost = parseFloat(document.Finance.NetAmount.value) - parseFloat(document.Finance.Deposit.value) - parseFloat(document.Finance.TradeIn.value);
				if(document.Finance.Rate.value == 0) {
					MonthlyRate = 0;
					SubTotal1 = 0;
				}
				else {
					MonthlyRate = Math.pow((1 + (document.Finance.Rate.value/100)),(1/12)) - 1;
					SubTotal1 = (1/MonthlyRate);
				}

				Periods = 12;
				if(document.Finance.Rate.value == 0) {
					if(document.Finance.PPP.checked == true) {
						document.Finance.Include.value = wpfcInc;
						document.Finance.monthpay1.value = Math.round(((AmtFinanced/Periods)*100)/100)*PPPRate;
					}
					else {
						document.Finance.Include.value = wpfcExc;
						document.Finance.monthpay1.value = Math.round((AmtFinanced/Periods)*100)/100;
					}
					document.Finance.finalpay1.value = Math.round((BalloonValue)*100)/100;
					document.Finance.total1.value = AmtFinanced;
					document.Finance.credit1.value = 0;
				}
				else {
					SubTotal2 = (1/(MonthlyRate*(Math.pow(1+MonthlyRate,Periods))));
					MonthlyFinanceCost1 = (AmtFinanced/(SubTotal1-SubTotal2));
					MonthlyFinanceCost2 = (BalloonValue*MonthlyRate);
					if(document.Finance.PPP.checked == true) {
						document.Finance.Include.value = wpfcInc;
						document.Finance.monthpay1.value = Math.round((((MonthlyFinanceCost1+MonthlyFinanceCost2)*100)*PPPRate))/100;
					}
					else {
						document.Finance.Include.value = wpfcExc;
						document.Finance.monthpay1.value = Math.round(((MonthlyFinanceCost1+MonthlyFinanceCost2))*100)/100;
					}
					document.Finance.finalpay1.value = Math.round((BalloonValue)*100)/100;
					document.Finance.total1.value = Math.round((((MonthlyFinanceCost1+MonthlyFinanceCost2)*Periods)+BalloonValue)*100)/100;
					document.Finance.credit1.value = Math.round((document.Finance.total1.value-NetCost)*100)/100;
				}

				Periods=24;
				if(document.Finance.Rate.value == 0) {
					if(document.Finance.PPP.checked == true) {
						document.Finance.monthpay2.value = Math.round(((AmtFinanced/Periods)*100)/100)*PPPRate;
					}
					else {
						document.Finance.monthpay2.value = Math.round((AmtFinanced/Periods)*100)/100;
					}
					document.Finance.finalpay2.value = Math.round((BalloonValue)*100)/100;
					document.Finance.total2.value = AmtFinanced;
					document.Finance.credit2.value = 0;
				}
				else {
					SubTotal2 = (1/(MonthlyRate*(Math.pow(1+MonthlyRate,Periods))));
					MonthlyFinanceCost1 = (AmtFinanced/(SubTotal1-SubTotal2));
					MonthlyFinanceCost2 = (BalloonValue*MonthlyRate);
					if(document.Finance.PPP.checked == true) {
						document.Finance.monthpay2.value = Math.round((((MonthlyFinanceCost1+MonthlyFinanceCost2)*100)*PPPRate))/100;}
					else {
						document.Finance.monthpay2.value = Math.round(((MonthlyFinanceCost1+MonthlyFinanceCost2))*100)/100;
					}
					document.Finance.finalpay2.value = Math.round((BalloonValue)*100)/100;
					document.Finance.total2.value = Math.round((((MonthlyFinanceCost1+MonthlyFinanceCost2)*Periods)+BalloonValue)*100)/100;
					document.Finance.credit2.value = Math.round((document.Finance.total2.value-NetCost)*100)/100;
				}

				Periods=36;
				if(document.Finance.Rate.value == 0) {
					if(document.Finance.PPP.checked == true) {
						document.Finance.monthpay3.value = Math.round(((AmtFinanced/Periods)*100)/100)*PPPRate;
					}
					else {
						document.Finance.monthpay3.value = Math.round((AmtFinanced/Periods)*100)/100;
					}
					document.Finance.finalpay3.value = Math.round((BalloonValue)*100)/100;
					document.Finance.total3.value = AmtFinanced;
					document.Finance.credit3.value = 0;
				}
				else {
					SubTotal2 = (1/(MonthlyRate*(Math.pow(1+MonthlyRate,Periods))));
					MonthlyFinanceCost1 = (AmtFinanced/(SubTotal1-SubTotal2));
					MonthlyFinanceCost2 = (BalloonValue*MonthlyRate);
					if(document.Finance.PPP.checked == true) {
						document.Finance.monthpay3.value = Math.round((((MonthlyFinanceCost1+MonthlyFinanceCost2)*100)*PPPRate))/100;
					}
					else {
						document.Finance.monthpay3.value = Math.round(((MonthlyFinanceCost1+MonthlyFinanceCost2))*100)/100;
					}
					document.Finance.finalpay3.value = Math.round((BalloonValue)*100)/100;
					document.Finance.total3.value = Math.round((((MonthlyFinanceCost1+MonthlyFinanceCost2)*Periods)+BalloonValue)*100)/100;
					document.Finance.credit3.value = Math.round((document.Finance.total3.value-NetCost)*100)/100;
				}

				Periods = 48;
				if(document.Finance.Rate.value == 0) {
					if(document.Finance.PPP.checked == true) {
						document.Finance.monthpay4.value = Math.round(((AmtFinanced/Periods)*100)/100)*PPPRate;
					}
					else {
						document.Finance.monthpay4.value = Math.round((AmtFinanced/Periods)*100)/100;
					}
					document.Finance.finalpay4.value = Math.round((BalloonValue)*100)/100;
					document.Finance.total4.value = AmtFinanced;
					document.Finance.credit4.value = 0;
				}
				else {
					SubTotal2 = (1/(MonthlyRate*(Math.pow(1+MonthlyRate,Periods))));
					MonthlyFinanceCost1 = (AmtFinanced/(SubTotal1-SubTotal2));
					MonthlyFinanceCost2 = (BalloonValue*MonthlyRate);
					if(document.Finance.PPP.checked == true) {
						document.Finance.monthpay4.value = Math.round((((MonthlyFinanceCost1+MonthlyFinanceCost2)*100)*PPPRate))/100;
					}
					else {
						document.Finance.monthpay4.value = Math.round(((MonthlyFinanceCost1+MonthlyFinanceCost2))*100)/100;
					}
					document.Finance.finalpay4.value = Math.round((BalloonValue)*100)/100;
					document.Finance.total4.value = Math.round((((MonthlyFinanceCost1+MonthlyFinanceCost2)*Periods)+BalloonValue)*100)/100;
					document.Finance.credit4.value = Math.round((document.Finance.total4.value-NetCost)*100)/100;
				}

				Periods = 60;
				if(document.Finance.Rate.value == 0) {
					if(document.Finance.PPP.checked == true) {
						document.Finance.monthpay5.value = Math.round(((AmtFinanced/Periods)*100)/100)*PPPRate;
					}
					else {
						document.Finance.monthpay5.value = Math.round((AmtFinanced/Periods)*100)/100;
					}
					document.Finance.finalpay5.value = Math.round((BalloonValue)*100)/100;
					document.Finance.total5.value = AmtFinanced;
					document.Finance.credit5.value = 0;
				}
				else {
					SubTotal2 = (1/(MonthlyRate*(Math.pow(1+MonthlyRate,Periods))));
					MonthlyFinanceCost1 = (AmtFinanced/(SubTotal1-SubTotal2));
					MonthlyFinanceCost2 = (BalloonValue*MonthlyRate);
					if(document.Finance.PPP.checked == true) {
						document.Finance.monthpay5.value = Math.round((((MonthlyFinanceCost1+MonthlyFinanceCost2)*100)*PPPRate))/100;
					}
					else {
						document.Finance.monthpay5.value = Math.round(((MonthlyFinanceCost1+MonthlyFinanceCost2))*100)/100;
					}
					document.Finance.finalpay5.value = Math.round((BalloonValue)*100)/100;
					document.Finance.total5.value = Math.round((((MonthlyFinanceCost1+MonthlyFinanceCost2)*Periods)+BalloonValue)*100)/100;
					document.Finance.credit5.value = Math.round((document.Finance.total5.value-NetCost)*100)/100;
				}
			}
		}
	}

	for(var c=0;c<document.Finance.finance_Months.length;c++) {
		if(document.Finance.finance_Months[c].checked) {
			ppayment = parseFloat(eval('document.Finance.monthpay'+(c+1)+'.value*document.Finance.finance_Months['+c+'].value'));
			break;
		}
	}
	document.getElementById('total_cost').value = Math.round(ppayment*100)/100;
}
