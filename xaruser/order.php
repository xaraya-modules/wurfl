<?php
/**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Review and submit order
 */
function shop_user_order() {

	// Redirects at the start of the user functions are just a way to make sure someone isn't where they don't need to be
	if (!xarUserIsLoggedIn() || !isset($_SESSION['shop']) || empty($_SESSION['shop'])) {
		xarResponse::Redirect(xarModURL('shop','user','main'));
		return;
	}

	if(!xarVarFetch('placeorder', 'str', $placeorder, NULL, XARVAR_NOT_REQUIRED)) {return;}

	sys::import('modules.dynamicdata.class.objects.master');
	
	$shippingobject = DataObjectMaster::getObject(array('name' => 'shop_shippingaddresses'));
	$shippingobject->getItem(array('itemid' => $_SESSION['shippingaddress']));
	$shippingvals = $shippingobject->getFieldValues();
	$data['shippingvals'] = $shippingvals;

	$data['products'] = $_SESSION['products'];
	$data['total'] = $_SESSION['total'];
	$time = time();
	$_SESSION['time'] = $time;

	$paymentobject = DataObjectMaster::getObject(array('name' => 'shop_paymentmethods'));
	$paymentmethod = $_SESSION['paymentmethod'];
	$paymentobject->getItem(array('itemid' => $paymentmethod));
	$values = $paymentobject->getFieldValues();
	$data['payvalues'] = $values;

	if ($placeorder) { 		

		/*if (isset($exp_date)) {
			$exp_month = substr($exp_date,0,2);
			$exp_year = substr($exp_date,2,4);
			$reverse_date = $exp_year . $exp_month;
			$minimum_date = date('ym',time());
			if ($minimum_date > $reverse_date) {
				$_SESSION['errors']['exp_date'] = true;
			}
		}*/
 
		// A few more things
		$values['date'] = $time;
		$values['products'] = serialize($data['products']);
		$values['total'] =  $_SESSION['total'];

		/*****************************/
		/***** PAYMENT PROCESSING ****/
		/*****************************/

		$response = xarMod::APIFunc('shop','admin','handlepgresponse', array('transfields' => $values));
		
		if (isset($response['trans_id']) && !empty($response['trans_id'])) { 
			// We have a successful transaction...
			$data['response'] = $response;
			$values['pg_transaction_id'] = $response['trans_id'];

			$transobject = DataObjectMaster::getObject(array('name' => 'shop_transactions'));
			$tid = $transobject->createItem($values);

			$_SESSION['order']['products'] = $_SESSION['products'];
			$_SESSION['order']['tid'] = $tid;
			$_SESSION['order']['date'] = date('F j, Y g:i a',$_SESSION['time']);

			unset($_SESSION['pg_response']); // This is set in shop_adminapi_handlepgresponse()
			unset($_SESSION['payment']); 

			//Need to clear all this now that the purchase went through.  Doing so ensures we don't re-submit the order
			unset($_SESSION['errors']);
			unset($_SESSION['shop']);
			unset($_SESSION['products']);

			xarResponse::Redirect(xarModURL('shop','user','complete'));

		} else {
			// There must be a problem...
			$pg_key = xarModVars::get('shop','pg_key');
			// Assuming we're using the key field for all payment gateways for keys, passwords and the like...
			if (empty($pg_key)) {
				$_SESSION['pg_response']['msg'] .= "<p style='color:red'><strong>Looks like you haven't entered a payment gateway key.  <a href='".xarModURL('shop','admin','overview')."'>Read me</a>.</strong></p>";
			}

			xarResponse::Redirect(xarModURL('shop','user','order'));
		}

	}

	return $data;
 

}

?>
