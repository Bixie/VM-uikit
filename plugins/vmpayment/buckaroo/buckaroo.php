<?php

/**
 * @version    $Id: standard.php,v 1.4 2005/05/27 19:33:57 ei
 * a special type of 'cash on delivey':
 * @author     Max Milbers, Val√©rie Isaksen
 * @version    $Id: standard.php 5122 2011-12-18 22:24:49Z alatak $
 * @package    VirtueMart
 * @subpackage payment
 * @copyright  Copyright (C) 2004-2008 soeren - All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 * http://virtuemart.net
 */

// Define constants
define('PLUGIN_VMPAYMENT_BUCKAROO_VERSION', "3.1.0");

// restrict direct access
defined('_JEXEC') or die('Restricted access');


// include required Virtuemart plugin
if (!class_exists('vmPSPlugin')) {
	require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}

// extend the Virtuemart plugin with Buckaroo
class plgVmPaymentBuckaroo extends vmPSPlugin {
	public static $_this = false;
	private $_testurl = "https://testcheckout.buckaroo.nl/html/pay.aspx";
	private $_url = "https://checkout.buckaroo.nl/html/pay.aspx";
	private $banks = array();
	private $banksSepa = array();
	private $GiftCards = array();

	// Virtuemart order payment states
	const PAYMENT_APPROVED_STATUS = 'payment_approved_status';
	const PAYMENT_DECLINED_STATUS = 'payment_declined_status';
	const PAYMENT_HELD_STATUS = 'payment_held_status';
	const PAYMENT_HELD_CREDIT3D_STATUS = "payment_held_credit3d_status";

	function __construct (& $subject, $config) {
		parent::__construct($subject, $config);
		$this->_loggable = true;
		$this->tableFields = array_keys($this->getTableSQLFields());
		$varsToPush = $this->getVarsToPush();
		$this->initBanks();
		$this->initBanksSepa();
		$this->initGiftCards();
		$this->initGenders();
		$this->initPolicy();
		$this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
		if (JRequest::getVar('option', '') == 'com_virtuemart' && in_array(JRequest::getVar('view', ''), array('checkout', 'cart'))) {
			// start including all required css and js files
			$document = & JFactory::getDocument();
			// include css
			$document->addStyleSheet(JURI::Root() . "plugins/vmpayment/buckaroo/css/buckaroo.css");
			$document->addStyleSheet(JURI::Root() . "plugins/vmpayment/buckaroo/css/jquery-ui.css");
			// include js
			$document->addScript(JURI::Root() . "plugins/vmpayment/buckaroo/js/jquery-latest.min.js");
			$document->addScript(JURI::Root() . "plugins/vmpayment/buckaroo/js/jquery-ui.js");
			$document->addScript(JURI::Root() . "plugins/vmpayment/buckaroo/js/jquery.validate.js");

			// Include dutch javascript validation messages if applicable
			$language = JFactory::getLanguage()->getTag();
			if ($language == 'nl-NL') {
				$document->addScript(JURI::Root() . "plugins/vmpayment/buckaroo/js/messages_nl.js");
			}

			$document->addScript(JURI::Root() . "plugins/vmpayment/buckaroo/js/buckaroo_custom.js");
		}
	}

	// Create the table for this plugin if it does not yet exist
	public function getVmPluginCreateTableSQL () {
		return $this->createTableSQL('Payment Buckaroo Gateway Table');
	}

	/**
	 * Fields to create the payment table
	 * @return string SQL Fields
	 */
	function getTableSQLFields () {
		$SQLfields = array(
			'id' => 'int(1) UNSIGNED NOT NULL AUTO_INCREMENT',
			'virtuemart_order_id' => 'int(1) UNSIGNED',
			'order_number' => ' char(64)',
			'virtuemart_paymentmethod_id' => 'mediumint(1) UNSIGNED',
			'payment_name' => 'varchar(5000)',
			'return_context' => 'char(255)',
			'cost_per_transaction' => 'decimal(10,2)',
			'cost_percent_total' => 'char(10)',
			'tax_id' => 'smallint(1)',
			'testmode' => 'mediumint(1)',
			'payment_key' => 'varchar(500)',
			'payment_method' => 'char(50)',
			'issuer' => 'char(90)',
			'statuscode' => 'char(10)',
			'statusmsg' => 'varchar(5000)',
			'transactions' => 'varchar(5000)',
		);
		return $SQLfields;
	}

	// iDEAL banks array selectlist (version 1)
	private function initBanks () {
		$this->banks = array(
			'0' => JText::_('VMPAYMENT_BUCKAROO_PAYMENT_BANKEN'),
			'0031' => 'ABN AMRO',
			'0761' => 'ASN Bank',
			'0091' => 'Friesland Bank',
			'0721' => 'ING',
			'0021' => 'Rabobank',
			'0751' => 'SNS Bank',
			'0771' => 'RegioBank',
			'0511' => 'Triodos Bank',
			'0161' => 'Van Lanschot',
			'0801' => 'Knab Bank',
		);
	}

	// iDEAL SEPA banks array selectlist (version 2)
	private function initBanksSepa () {
		$this->banksSepa = array(
			'ABNANL2A' => 'ABN AMRO',
			'ASNBNL21' => 'ASN Bank',
			'FRBKNL2L' => 'Friesland Bank',
			'INGBNL2A' => 'ING',
			'RABONL2U' => 'Rabobank',
			'SNSBNL2A' => 'SNS Bank',
			'RBRBNL21' => 'RegioBank',
			'TRIONL2U' => 'Triodos Bank',
			'FVLBNL22' => 'Van Lanschot',
			'KNABNL2H' => 'Knab Bank',
		);
	}

	// gift cards array selectlist
	private function initGiftCards () {
		$this->GiftCards = array(
			'0' => JText::_('VMPAYMENT_BUCKAROO_SELECT_NONE'),
			'babygiftcard' => 'Baby Giftcard',
			'babyparkgiftcard' => 'Babypark Giftcard',
			'beautywellness' => 'Beauty Wellness',
			'boekenbon' => 'Boekenbon',
			'boekenvoordeel' => 'Boekenvoordeel',
			'designshopsgiftcard' => 'Designshops Giftcard',
			'fijncadeau' => 'Fijn cadeau',
			'koffiecadeau' => 'Koffie Cadeau',
			'kokenzo' => 'Koken En Zo',
			'kookcadeau' => 'Kook Cadeau',
			'nationaleentertainmentcard' => 'Nationale Entertainment Card',
			'naturesgift' => 'Natures Gift',
			'podiumcadeaukaart' => 'Podium Cadeaukaart',
			'shoesaccessories' => 'Shoes Accessories',
			'webshopgiftcard' => 'Webshop Giftcard',
			'wijncadeau' => 'Wijn Cadeau',
			'wonenzo' => 'Wonen En Zo',
			'yourgift' => 'Your Gift',
			'fashioncheque' => 'Fashioncheque',
		);
	}

	// genders array selectlist
	private function initGenders () {
		$this->genders = array(
			'0' => JText::_('VMPAYMENT_BUCKAROO_GENDER_NONE'),
			'1' => JText::_('VMPAYMENT_BUCKAROO_GENDER_MALE'),
			'2' => JText::_('VMPAYMENT_BUCKAROO_GENDER_FEMALE'),
		);
	}

	// policy array selectlist
	private function initPolicy () {
		$this->policy = array(
			'0' => JText::_('VMPAYMENT_BUCKAROO_POLICY_NOACCEPT'),
			'1' => JText::_('VMPAYMENT_BUCKAROO_POLICY_ACCEPT'),
		);
	}

	// this function is called after order confirmation
	// sets and stores data and then redirects to the chosen paymentmethod
	function plgVmConfirmedOrder ($cart, $order) {

		if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_paymentmethod_id))) {
			return null; // Another method was selected, do nothing
		}

		if (!$this->selectedThisElement($method->payment_element)) {
			return false;
		}

		$session = JFactory::getSession();
		$this->_debug = $method->testmode;
		$this->logInfo('plgVmConfirmedOrder order number: ' . $order['details']['BT']->order_number, 'message');

		if (!class_exists('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php');
		}

		if (!class_exists('VirtueMartModelCurrency')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'currency.php');
		}

		$data['brq_culture'] = JFactory::getLanguage()->getTag();

		if (!class_exists('TableVendors')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'table' . DS . 'vendors.php');
		}

		$vendorModel = VmModel::getModel('Vendor');
		$vendorModel->setId(1);
		$vendor = $vendorModel->getVendor();
		$vendorModel->addImages($vendor, 1);
		$this->getPaymentCurrency($method);
		$q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="' . $method->payment_currency . '" ';
		$db = JFactory::getDBO();
		$db->setQuery($q);
		$data['brq_currency'] = $db->loadResult();

		$paymentCurrency = CurrencyDisplay::getInstance($method->payment_currency);
		$data['brq_amount'] = round($paymentCurrency->convertCurrencyTo($method->payment_currency, $order['details']['BT']->order_total, false), 2);
		$cd = CurrencyDisplay::getInstance($cart->pricesCurrency);

		if ($data['brq_amount'] <= 0) {
			vmInfo(JText::_('VMPAYMENT_BUCKAROO_PAYMENT_AMOUNT_INCORRECT'));
			return false;
		}

		if (empty($method->websitekey)) {
			vmInfo(JText::_('VMPAYMENT_BUCKAROO_WEBSITE_KEY_NOT_SET'));
			return false;
		}

		if (empty($method->secretkey)) {
			vmInfo(JText::_('VMPAYMENT_BUCKAROO_SECRET_KEY_NOT_SET'));
			return false;
		}

		$data['brq_websitekey'] = $method->websitekey;
		$data['brq_invoicenumber'] = $order['details']['BT']->order_number;

		// create the return urls
		$data['brq_return'] = JROUTE::_(JURI::root() . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&on=' . $order['details']['BT']->order_number . '&pm=' . $order['details']['BT']->virtuemart_paymentmethod_id . "Itemid=" . $_REQUEST['Itemid']);
		$data['brq_returnerror'] = JROUTE::_(JURI::root() . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginUserPaymentCancel&on=' . $order['details']['BT']->order_number . '&pm=' . $order['details']['BT']->virtuemart_paymentmethod_id . "Itemid=" . $_REQUEST['Itemid']);
		$data['brq_returnreject'] = JROUTE::_(JURI::root() . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginUserPaymentCancel&on=' . $order['details']['BT']->order_number . '&pm=' . $order['details']['BT']->virtuemart_paymentmethod_id . "Itemid=" . $_REQUEST['Itemid']);
		$data['brq_returncancel'] = JROUTE::_(JURI::root() . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginUserPaymentCancel&on=' . $order['details']['BT']->order_number . '&pm=' . $order['details']['BT']->virtuemart_paymentmethod_id . "Itemid=" . $_REQUEST['Itemid']);

		$this->getPaymentMethode($data, $method, $order);

		$data[0]['brq_signature'] = $this->calculateDigitalSignature($data, $method->secretkey);

		// Prepare data that should be stored in the database
		$dbValues['order_number'] = $order['details']['BT']->order_number;
		$dbValues['virtuemart_order_id'] = $order['details']['BT']->virtuemart_order_id;
		$dbValues['payment_method_id'] = $order['details']['BT']->virtuemart_paymentmethod_id;
		$dbValues['return_context'] = JFactory::getSession()->getId();
		$dbValues['payment_name'] = parent::renderPluginName($method);
		$dbValues['cost_per_transaction'] = $method->cost_per_transaction;
		$dbValues['cost_percent_total'] = $method->cost_percent_total;
		$dbValues['testmode'] = $method->testmode;
		$dbValues['payment_key'] = $data[0]['brq_signature'];
		$dbValues['payment_method'] = $method->payment_method;

		$this->storePSPluginInternalData($dbValues);

		$url = $method->testmode == 1 ? $this->_testurl : $this->_url;

		// redirect form - future to do: redirect directly after order confirmation
		$html = '<html><head><title>Redirection</title></head><body><div style="margin: auto; text-align: center;">';
		$html .= '<form action="' . $url . '" method="post" name="vm_buckaroo_form" >';
		$html .= '<input type="submit"  value="' . JText::_('VMPAYMENT_BUCKAROO_REDIRECT_MESSAGE') . '" />';
		foreach ($data[0] as $name => $value) {
			$html .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '" />';
		}
		$html .= '</form></div>';
		$html .= ' <script type="text/javascript">';
		$html .= ' document.vm_buckaroo_form.submit();';
		$html .= ' </script></body></html>';

		// 2 = don't delete the cart, don't send email and don't redirect
		$cart->_confirmDone = false;
		$cart->_dataValidated = false;
		$cart->setCartIntoSession();

		JRequest::setVar('html', $html);
	}

	// get the chosen paymentmethod and set the data that will be passed to the payment engine
	private function getPaymentMethode (&$data, &$method, &$order) {
		// get joomla session
		$session = JFactory::getSession();
		// get the session values and store them in variables
		$bank = $session->get('brq_service_ideal_issuer', 0, 'vm');
		$sepa_bank = $session->get('sepa_issuer', 0, 'vm');
		$gender_online_giro = $session->get('brq_service_onlinegiro_customergender', 0, 'vm');
		$gender_transfer = $session->get('brq_service_transfer_customergender', 0, 'vm');
		$gender_payment_guarantee = $session->get('brq_service_paymentguarantee_CustomerGender', 0, 'vm');
		$birthdate = $session->get('brq_service_paymentguarantee_CustomerBirthDate', 0, 'vm');
		$accountnumber = $session->get('brq_service_paymentguarantee_CustomerAccountNumber', 0, 'vm');
		$giftcards = $session->get('brq_service_giftcard_method', 0, 'vm');
		$user =& JFactory::getUser();
		$user_id = $user->get('id');

		// Get payment method variables
		$invoice_delay = $method->invoice_delay;
		if (is_null($invoice_delay) || $invoice_delay < 0 || $invoice_delay == "") $invoice_delay = 14;
		if ($invoice_delay > 30) $invoice_delay = 30; // maximum delay is 30 days
		$phonenumber = $session->get('brq_service_paymentguarantee_PhoneNumber', 0, 'vm');
		$mobilephonenumber = $session->get('brq_service_paymentguarantee_MobilePhoneNumber', 0, 'vm');

		// universal values
		$data['brq_description'] = $method->payment_desc;
		$data['brq_platform_name'] = "VirtueMart";
		$data['brq_platform_version'] = vmVersion::$RELEASE;
		$data['brq_module_supplier'] = "Dutch Open Projects";
		$data['brq_module_name'] = "Free";
		$data['brq_module_version'] = PLUGIN_VMPAYMENT_BUCKAROO_VERSION;

		// switch all methods
		// all previously implemented paymentmethods before 25-03-2013
		switch ($method->payment_method) {
			case 'buckaroo':
				$data['brq_payment_method'] = "buckaroo";
				$data['brq_service_buckaroo_action'] = "pay";
				break;
			case 'paypal':
				$data['brq_payment_method'] = "paypal";
				$data['brq_service_paypal_action'] = "pay";
				$data['brq_service_paypal_buyeremail'] = $order['details']['BT']->email;
				break;
			case 'ideal':
				$data['brq_payment_method'] = "ideal";
				$data['brq_service_ideal_version'] = "1";
				$data['brq_service_ideal_action'] = "pay";
				$data['brq_service_ideal_issuer'] = $bank;
				break;
			case 'visa':
				$data['brq_payment_method'] = "visa";
				$data['brq_service_visa_action'] = "pay";
				break;
			case 'mastercard':
				$data['brq_payment_method'] = "mastercard";
				$data['brq_service_mastercard_action'] = "pay";
				break;
			case 'directdebit':
				$data['brq_payment_method'] = "directdebit";
				$data['brq_service_directdebit_action'] = "pay";
				$data['brq_service_directdebit_customeraccountnumber'];
				$data['brq_service_directdebit_customeraccountname'];
				$data['brq_collectdate'];
				break;
			case 'amex':
				$data['brq_payment_method'] = "amex";
				$data['brq_service_amex_action'] = "pay";
				break;
			case 'giropay':
				$data['brq_payment_method'] = "giropay";
				$data['brq_service_giropay_action'] = "pay";
				break;
			case 'paysafecard':
				$data['brq_payment_method'] = "paysafecard";
				$data['brq_service_paysafecard_action'] = "pay";
				break;
			case 'maestro':
				$data['brq_payment_method'] = "maestro";
				$data['brq_service_maestro_action'] = "pay";
				break;
			case 'sofortueberweisung':
				$data['brq_payment_method'] = "sofortueberweisung";
				$data['brq_service_soforueberwisung_action'] = "pay";
				break;
			case 'transfer':
				$data['brq_payment_method'] = "transfer";
				$data['brq_service_transfer_action'] = "pay";
				$data['brq_service_transfer_customeremail'] = $order['details']['BT']->email;
				$data['brq_service_transfer_customercountry'] = ShopFunctions::getCountryByID($order['details']['BT']->virtuemart_country_id, 'country_2_code');
				$data['brq_service_transfer_customergender'] = $gender_transfer;
				$data['brq_service_transfer_CustomerFirstName'] = $order['details']['BT']->first_name;
				$data['brq_service_transfer_customerLastName'] = $order['details']['BT']->middle_name ? $order['details']['BT']->middle_name . " " . $order['details']['BT']->last_name : $order['details']['BT']->last_name;
				$data['brq_service_transfer_sendmail'] = 'TRUE';
				break;

			// all new paymentmethods implemented after 25-02-2013
			case 'idealprocessing':
				$data['brq_payment_method'] = "ideal";
				$data['brq_service_ideal_version'] = "2";
				$data['brq_service_ideal_action'] = "pay";
				$data['brq_service_ideal_issuer'] = $sepa_bank;
				break;
			case 'visaelectron':
				$data['brq_payment_method'] = "visaelectron";
				$data['brq_service_visaelectron_action'] = "pay";
				break;
			case 'vpay':
				$data['brq_payment_method'] = "vpay";
				$data['brq_service_vpay_action'] = "pay";
				break;
			case 'emaestro':
				$data['brq_payment_method'] = "maestro";
				$data['brq_service_emaestro_action'] = "pay";
				break;
			case 'bancontactmrcash':
				$data['brq_payment_method'] = "bancontactmrcash";
				$data['brq_service_bancontactmrcash_action'] = "pay";
				break;
			case 'empayment':
				$data['brq_payment_method'] = "empayment";
				$data['brq_service_empayment_action'] = "pay";
				$data['brq_service_empayment_emailaddress'] = $order['details']['BT']->email;
				$data['brq_service_empayment_ClientInfo_browserAgent'] = $_SERVER['HTTP_USER_AGENT'];
				$data['brq_beneficiaryaccount'] = $method->websitekey;
				break;
			case 'onlinegiro':
				$data['brq_payment_method'] = "onlinegiro";
				$data['brq_service_onlinegiro_action'] = "paymentinvitation";
				$data['brq_service_onlinegiro_customergender'] = $gender_online_giro;
				$data['brq_service_onlinegiro_customeremail'] = $order['details']['BT']->email;
				$data['brq_service_onlinegiro_customerfirstname'] = $order['details']['BT']->first_name;
				$data['brq_service_onlinegiro_customerlastname'] = $order['details']['BT']->middle_name ? $order['details']['BT']->middle_name . " " . $order['details']['BT']->last_name : $order['details']['BT']->last_name;
				break;
			case 'ukash':
				$data['brq_payment_method'] = "ukash";
				$data['brq_service_ukash_action'] = "pay";
				break;
			case 'paymentguarantee':
				$data['brq_payment_method'] = "paymentguarantee";
				$data['brq_service_paymentguarantee_action'] = "PaymentInvitation";
				// required fields
				$data['brq_service_paymentguarantee_CustomerEmail'] = $order['details']['BT']->email;
				$data['brq_service_paymentguarantee_AmountVat'] = round($order['details']['BT']->order_total, 2);
				$data['brq_service_paymentguarantee_CustomerFirstName'] = $order['details']['BT']->first_name;
				$data['brq_service_paymentguarantee_CustomerLastName'] = $order['details']['BT']->middle_name ? $order['details']['BT']->middle_name . " " . $order['details']['BT']->last_name : $order['details']['BT']->last_name;
				// custom form fields for payment guarantee
				$data['brq_service_paymentguarantee_CustomerGender'] = $gender_payment_guarantee;
				$data['brq_service_paymentguarantee_CustomerBirthDate'] = $birthdate;
				$data['brq_service_paymentguarantee_CustomerAccountNumber'] = $accountnumber;
				$data['brq_service_paymentguarantee_PhoneNumber'] = $phonenumber;
				$data['brq_service_paymentguarantee_MobilePhoneNumber'] = $mobilephonenumber;
				// end of custom fields for payment guarantee
				$data['brq_service_paymentguarantee_CustomerCode'] = $user_id;
				$invoice_date = date("Y-m-d", strtotime(date("Y-m-d", mktime()) . " + " . $invoice_delay . " day"));
				$data['brq_service_paymentguarantee_InvoiceDate'] = $invoice_date;
				$data['brq_service_paymentguarantee_DateDue'] = date("Y-m-d", strtotime($invoice_date . " + 14 day"));
				$data['brq_service_paymentguarantee_CustomerInitials'] = $order['details']['BT']->first_name[0];
				// address type invoice(1)
				$data['brq_service_paymentguarantee_address_AddressType_1'] = "INVOICE,SHIPPING";
				$data['brq_service_paymentguarantee_address_Street_1'] = preg_replace('/[^\\/\-a-z\s]/i', '', $order['details']['BT']->address_1);
				$data['brq_service_paymentguarantee_address_HouseNumber_1'] = preg_replace("/[^0-9]/", "", $order['details']['BT']->address_1);
				$data['brq_service_paymentguarantee_address_ZipCode_1'] = $order['details']['BT']->zip;
				$data['brq_service_paymentguarantee_address_City_1'] = $order['details']['BT']->city;
				$data['brq_service_paymentguarantee_address_Country_1'] = ShopFunctions::getCountryByID($order['details']['BT']->virtuemart_country_id, 'country_2_code');
				break;
			// cadeaukaarten & loyalty - to do: partial payments
			case 'cadeaukaartenandloyalty':
				$data['brq_payment_method'] = $giftcards;
				$data['brq_service_' . ($giftcards) . '_action'] = "pay";
				$data['brq_service_giftcard_method'] = $giftcards;
				break;
			// end of new payment methods

			default:
				break;
		}
	}

	// calculates and returns the required signature
	private function calculateDigitalSignature (&$data, $secretkey) {
		// unset the signature before it is recreated
		unset($data['brq_signature']);
		$data = $this->buckarooSort($data);
		$signatureString = '';

		foreach ($data[0] as $key => $value) {
			$signatureString .= $key . '=' . urldecode($value);
		}
		$signatureString .= $secretkey;

		//return the SHA1 encoded string for comparison
		$signature = SHA1($signatureString);

		return $signature;
	}

	// array sort function
	private function buckarooSort ($array) {
		$arrayToSort = array();
		$origArray = array();

		foreach ($array as $key => $value) {
			$arrayToSort[strtolower($key)] = $value;
			// stores the original value in an array
			$origArray[strtolower($key)] = $key;
		}

		ksort($arrayToSort);
		$sortedArray = array();
		foreach ($arrayToSort as $key => $value) {
			//switch the lowercase keys back to their originals
			$key = $origArray[$key];
			$sortedArray[$key] = $value;
		}

		return array($sortedArray);
	}

	// Display stored payment data for an order
	function plgVmOnShowOrderBEPayment ($virtuemart_order_id, $payment_method_id) {

		if (!$this->selectedThisByMethodId($payment_method_id)) {
			return null; // Another method was selected, do nothing
		}

		if (!($paymentTable = $this->_getBuckarooInternalData($virtuemart_order_id))) {
			// JError::raiseWarning(500, $db->getErrorMsg());
			return null;
		}

		$html = '<table class="adminlist">' . "\n";
		$html .= $this->getHtmlHeaderBE();
		$html .= $this->getHtmlRowBE('BUCKAROO_PAYMENT_NAME', $paymentTable->payment_method);
		$html .= $this->getHtmlRowBE('BUCKAROO_PAYMENT_STATUSCODE', $paymentTable->statuscode);
		$html .= $this->getHtmlRowBE('BUCKAROO_PAYMENT_STATUSMSG', $paymentTable->statusmsg);
		$html .= $this->getHtmlRowBE('BUCKAROO_PAYMENT_PAYMENTDATE', $paymentTable->modified_on);
		$html .= '</table>' . "\n";
		return $html;
	}

	function getCosts (VirtueMartCart $cart, $method, $cart_prices) {
		if (preg_match('/%$/', $method->cost_percent_total)) {
			$cost_percent_total = substr($method->cost_percent_total, 0, -1);
		} else {
			$cost_percent_total = $method->cost_percent_total;
		}

		return ($method->cost_per_transaction + ($cart_prices['salesPrice'] * $cost_percent_total * 0.01));
	}

	/**
	 * Check if the payment conditions are fulfilled for this payment method
	 * @author: Valerie Isaksen
	 * @param $cart_prices : cart prices
	 * @param $payment
	 * @return true: if the conditions are fulfilled, false otherwise
	 */
	protected function checkConditions ($cart, $method, $cart_prices) {
		$this->convert($method);
		// $params = new JParameter($payment->payment_params);
		$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		$amount = $cart_prices['salesPrice'];
		$amount_cond = ($amount >= $method->min_amount AND $amount <= $method->max_amount
			OR
			($method->min_amount <= $amount AND ($method->max_amount == 0)));

		if (!$amount_cond) {
			return false;
		}
		$countries = array();

		if (!empty($method->countries)) {
			if (!is_array($method->countries)) {
				$countries[0] = $method->countries;
			} else {
				$countries = $method->countries;
			}
		}

		// probably did not gave his BT:ST address
		if (!is_array($address)) {
			$address = array();
			$address['virtuemart_country_id'] = 0;
		}

		if (!isset($address['virtuemart_country_id'])) {
			$address['virtuemart_country_id'] = 0;
		}

		if (count($countries) == 0 || in_array($address['virtuemart_country_id'], $countries) || count($countries) == 0) {
			return true;
		}

		return false;
	}

	// convert amounts with float
	function convert ($method) {
		$method->min_amount = (float)$method->min_amount;
		$method->max_amount = (float)$method->max_amount;
	}

	// install the payment plugin table if it doesn't exist yet
	function plgVmOnStoreInstallPaymentPluginTable ($jplugin_id) {
		return $this->onStoreInstallPluginTable($jplugin_id);
	}


	function onStoreInstallPluginTable ($jplugin_id) {

		/**
		 * Add new order state to order_status table
		 */
		if (!class_exists('VirtueMartModelOrderStatus')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orderstatus.php');
		}
		$orderstatus = new VirtueMartModelOrderstatus();
		$orderstates = $orderstatus->getOrderStatusList();
		$do_insert = TRUE;
		foreach ($orderstates as $orderstate) {
			if ($orderstate->order_status_code == 'D') {
				$do_insert = FALSE;
				break;
			}
		}

		if ($do_insert) {

			// Insert new order status
			$orderstatus = new stdClass();
			$orderstatus->order_status_code = 'D';
			$orderstatus->order_status_name = 'Merchant approval required';
			$orderstatus->order_status_description = '3D secure payment status not approved or unknown - see Buckaroo payment plaza';
			$orderstatus->order_stock_handle = 'R'; // keep stock items as reserved
			$orderstatus->ordering = count($orderstates) + 1;
			$orderstatus->virtuemart_vendor_id = 1;

			try {
				// Insert the object into the user profile table.
				$result = JFactory::getDbo()->insertObject('#__virtuemart_orderstates', $orderstatus);
			} catch (Exception $e) {
				// catch any errors.
				vmdebug(print_r($e, TRUE));
			}

		}

		/**
		 * Install payment plugin table
		 */
		return parent::onStoreInstallPluginTable($jplugin_id);
	}

	/**
	 * This event is fired after the payment method has been selected. It can be used to store
	 * additional payment info in the cart.
	 * @author Max Milbers
	 * @author Valerie isaksen
	 * @param VirtueMartCart $cart : the actual cart
	 * @return null if the payment was not selected, true if the data is valid, error message if the data is not vlaid
	 */
	public function plgVmOnSelectCheckPayment (VirtueMartCart $cart) {

		// Retrieve payment method info
		$method = $this->getVmPluginMethod($cart->virtuemart_paymentmethod_id);

		if ($method->payment_element != 'buckaroo') {
			return null; // Another method was selected, do nothing
		}

		$bank = JRequest::getVar('brq_service_ideal_issuer', 0);
		$sepa_bank = JRequest::getVar('sepa_issuer', 0);
		$gender_online_giro = JRequest::getVar('gender_online_giro', 0);
		$gender_payment_guarantee = JRequest::getVar('gender_payment_guarantee', 0);
		$gender_transfer = JRequest::getVar('gender_transfer', 0);
		$birthdate = JRequest::getVar('birthdate', 0);
		$accountnumber = JRequest::getVar('accountnumber', 0);
		$phonenumber = JRequest::getVar('phonenumber', 0);
		$mobilephonenumber = JRequest::getVar('mobilephonenumber', 0);
		$policy = JRequest::getVar('policy', 0);
		$giftcards = JRequest::getVar('giftcards', 0);

		$session = JFactory::getSession();
		$session->set('brq_service_ideal_issuer', $bank, 'vm');
		$session->set('sepa_issuer', $sepa_bank, 'vm');
		$session->set('brq_service_onlinegiro_customergender', $gender_online_giro, 'vm');
		$session->set('brq_service_paymentguarantee_CustomerGender', $gender_payment_guarantee, 'vm');
		$session->set('brq_service_paymentguarantee_CustomerBirthDate', $birthdate, 'vm');
		$session->set('brq_service_paymentguarantee_CustomerAccountNumber', $accountnumber, 'vm');
		$session->set('brq_service_paymentguarantee_Policy', $policy, 'vm');
		$session->set('brq_service_paymentguarantee_PhoneNumber', $phonenumber, 'vm');
		$session->set('brq_service_paymentguarantee_MobilePhoneNumber', $mobilephonenumber, 'vm');
		$session->set('brq_service_giftcard_method', $giftcards, 'vm');

		// check if the user has selected an paymentmethod
		if ($method->payment_method == NULL) {
			vmWarn(JText::_('VMPAYMENT_BUCKAROO_SELECT_PAYMENT'));
			return false;
		}

		// check ideal or idealprocessing(sepa ideal)
		if (($method->payment_method == "ideal" && empty($bank)) || ($method->payment_method == "idealprocessing" && empty($sepa_bank))) {
			vmWarn(JText::_('VMPAYMENT_BUCKAROO_PAYMENT_SELECT_BANK_INVALID'));
			return false;
		}

		// check paymentguarantee
		if ($method->payment_method == "paymentguarantee") {
			if (empty($gender_payment_guarantee) || empty($birthdate) || empty($accountnumber) || empty($policy)) {
				vmWarn(JText::_('VMPAYMENT_BUCKAROO_FILL_REQUIRED_FIELDS'));
				return false;
			}
		}

		// check onlinegiro
		if ($method->payment_method == "onlinegiro" && empty($gender_online_giro)) {
			vmWarn(JText::_('VMPAYMENT_BUCKAROO_FILL_REQUIRED_FIELDS'));
			return false;
		}

		// check cadeaukaarten and loyalty
		if ($method->payment_method == "cadeaukaartenandloyalty" && $giftcards == "0") {
			vmWarn(JText::_('VMPAYMENT_BUCKAROO_FILL_REQUIRED_FIELDS'));
			return false;
		}

		return $this->OnSelectCheck($cart);
	}

	/**
	 * plgVmDisplayListFEPayment
	 * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for example
	 * @param object  $cart     Cart object
	 * @param integer $selected ID of the method selected
	 * @return boolean True on succes, false on failures, null when this plugin was not selected.
	 *                          On errors, JError::raiseWarning (or JError::raiseError) must be used to set a message.
	 * @author Valerie Isaksen
	 * @author Max Milbers
	 */
	public function plgVmDisplayListFEPayment (VirtueMartCart $cart, $selected = 0, &$htmlIn) {

		if ($this->getPluginMethods($cart->vendorId) === 0) {
			if (empty($this->_name)) {
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_('COM_VIRTUEMART_CART_NO_' . strtoupper($this->_psType)));
				return false;
			} else {
				return false;
			}
		}

		$method_name = $this->_psType . '_name';
		$session = JFactory::getSession();

		foreach ($this->methods as $method) {

			if ($this->checkConditions($cart, $method, $cart->pricesUnformatted)) {
				$methodSalesPrice = $this->calculateSalesPrice($cart, $method, $cart->pricesUnformatted);
				$method->$method_name = $this->renderPluginName($method);

				// some paymentmethods require additional fields
				// paymentguarantee
				if ($method->payment_method == "paymentguarantee") {
					$htmlI = $this->getPluginHtml($method, $selected, $methodSalesPrice);
					$htmlI .= '<div id="wrapper_paymentdetails' . ($method->virtuemart_paymentmethod_id) . '" class="wrapper_paymentdetails"><table>';
					$htmlI .= '<tr><td class="brq_label">' . (JText::_('VMPAYMENT_BUCKAROO_ACCOUNTNUMBER')) . '</td> <td> <input type="text" name="accountnumber" value="' . ($session->get('brq_service_paymentguarantee_CustomerAccountNumber', '', 'vm')) . '" id="accountnumber" /></td></tr>';
					$htmlI .= '<tr><td class="brq_label">* ' . (JText::_('VMPAYMENT_BUCKAROO_GENDER')) . '</td> <td> ';
					$htmlI .= JHTML::_('Select.genericlist', $this->genders, 'gender_payment_guarantee', 'class="selectlist"', 'value', 'text', $session->get('brq_service_paymentguarantee_CustomerGender', 0, 'vm'));
					$htmlI .= '</td></tr>';
					$htmlI .= '<tr><td class="brq_label">' . (JText::_('VMPAYMENT_BUCKAROO_BIRTHDATE')) . '</td> <td> <input type="text" name="birthdate" value="' . ($session->get('brq_service_paymentguarantee_CustomerBirthDate', '', 'vm')) . '" id="birthdate" /> </td></tr>';
					$htmlI .= '<tr><td class="brq_label">' . (JText::_('VMPAYMENT_BUCKAROO_POLICY_1')) . '<a href="' . ($method->policy) . '" target="_blank">' . (JText::_('VMPAYMENT_BUCKAROO_POLICY_2')) . '</a>:</td> <td> ';
					$htmlI .= JHTML::_('Select.genericlist', $this->policy, 'policy', 'class="selectlist"', 'value', 'text', $session->get('brq_service_paymentguarantee_Policy', 0, 'vm'));
					$htmlI .= '</td></tr>';
					$htmlI .= '<tr><td class="brq_label">' . (JText::_('VMPAYMENT_BUCKAROO_PHONENUMBER')) . '</td> <td> <input type="text" name="phonenumber" value="' . ($session->get('brq_service_paymentguarantee_PhoneNumber', '', 'vm')) . '" id="phonenumber" /></td></tr>';
					$htmlI .= '<tr><td class="brq_label">' . (JText::_('VMPAYMENT_BUCKAROO_MOBILEPHONENUMBER')) . '</td> <td> <input type="text" name="mobilephonenumber" value="' . ($session->get('brq_service_paymentguarantee_MobilePhoneNumber', '', 'vm')) . '" id="mobilephonenumber" /></td></tr>';
					$htmlI .= '</table><small><i>' . (JText::_('VMPAYMENT_BUCKAROO_REQUIRED_FIELDS')) . '</i></small></div>';
					$html[] = $htmlI;
				} // onlinegiro
				else if ($method->payment_method == "onlinegiro") {
					$htmlI = $this->getPluginHtml($method, $selected, $methodSalesPrice);
					$htmlI .= '<div id="wrapper_paymentdetails' . ($method->virtuemart_paymentmethod_id) . '" class="wrapper_paymentdetails"><table>';
					$htmlI .= '<tr><td class="brq_label">* ' . (JText::_('VMPAYMENT_BUCKAROO_GENDER')) . '</td> <td> ';
					$htmlI .= JHTML::_('Select.genericlist', $this->genders, 'gender_online_giro', 'class="selectlist"', 'value', 'text', $session->get('brq_service_onlinegiro_customergender', 0, 'vm'));
					$htmlI .= '</td></tr>';
					$htmlI .= '</table><small><i>' . (JText::_('VMPAYMENT_BUCKAROO_REQUIRED_FIELDS')) . '</i></small></div>';
					$html[] = $htmlI;
				} // cadeaukaarten and loyalty
				else if ($method->payment_method == "cadeaukaartenandloyalty") {
					$htmlI = $this->getPluginHtml($method, $selected, $methodSalesPrice);
					$htmlI .= '<div id="wrapper_paymentdetails' . ($method->virtuemart_paymentmethod_id) . '" class="wrapper_paymentdetails"><table>';
					$htmlI .= '<tr><td class="brq_label">' . (JText::_('VMPAYMENT_BUCKAROO_SELECT_GIFTCARD')) . '</td> <td> ';
					$htmlI .= JHTML::_('Select.genericlist', $this->GiftCards, 'giftcards', 'class="selectlist"', 'value', 'text', $session->get('brq_service_giftcard_method', 0, 'vm'));
					$htmlI .= '</td></tr>';
					$htmlI .= '</table><small><i>' . (JText::_('VMPAYMENT_BUCKAROO_REQUIRED_FIELDS')) . '</i></small></div>';
					$html[] = $htmlI;
				} // ideal
				else if ($method->payment_method == "ideal") {
					$htmlI = $this->getPluginHtml($method, $selected, $methodSalesPrice);
					$htmlI .= '<div id="wrapper_paymentdetails' . ($method->virtuemart_paymentmethod_id) . '" class="wrapper_paymentdetails"><table>';
					$htmlI .= '<tr><td class="brq_label">' . (JText::_('VMPAYMENT_BUCKAROO_PAYMENT_SELECT_BANK')) . '</td> <td> ';
					$htmlI .= JHTML::_('Select.genericlist', $this->banks, 'brq_service_ideal_issuer', 'class="selectlist"', 'key', 'value', $session->get('brq_service_ideal_issuer', 0, 'vm'));
					$htmlI .= '</td></tr>';
					$htmlI .= '</table><small><i>' . (JText::_('VMPAYMENT_BUCKAROO_REQUIRED_FIELDS')) . '</i></small></div>';
					$html[] = $htmlI;
				} // idealprocessing (ideal sepa)
				else if ($method->payment_method == "idealprocessing") {
					$htmlI = $this->getPluginHtml($method, $selected, $methodSalesPrice);
					$htmlI .= '<div id="wrapper_paymentdetails' . ($method->virtuemart_paymentmethod_id) . '" class="wrapper_paymentdetails"><table>';
					$htmlI .= '<tr><td class="brq_label">' . (JText::_('VMPAYMENT_BUCKAROO_PAYMENT_SELECT_BANK')) . '</td> <td> ';
					$banksSepa[] = JHTML::_('Select.option', '', JText::_('VMPAYMENT_BUCKAROO_PAYMENT_BANKEN'));
					$banksSepa[] = JHTML::_('Select.optgroup', JText::_('VMPAYMENT_BUCKAROO_NETHERLANDS'));
					foreach ($this->banksSepa as $key => $value) {
						$banksSepa[] = JHTML::_('Select.option', $key, $value);
					}
					$htmlI .= JHTML::_('Select.genericlist', $banksSepa, 'sepa_issuer', 'class="selectlist"', 'value', 'text', $session->get('sepa_issuer', 0, 'vm'));
					$htmlI .= '</td></tr>';
					$htmlI .= '</table><small><i>' . (JText::_('VMPAYMENT_BUCKAROO_REQUIRED_FIELDS')) . '</i></small></div>';
					$html[] = $htmlI;
				} // Bank transfer
				else if ($method->payment_method == "transfer") {
					$htmlI = $this->getPluginHtml($method, $selected, $methodSalesPrice);
					$htmlI .= '<div id="wrapper_paymentdetails' . ($method->virtuemart_paymentmethod_id) . '" class="wrapper_paymentdetails"><table>';
					$htmlI .= '<tr><td class="brq_label">' . (JText::_('VMPAYMENT_BUCKAROO_GENDER')) . '</td> <td> ';
					$htmlI .= JHTML::_('Select.genericlist', $this->genders, 'gender_transfer', 'class="selectlist"', 'value', 'text', $session->get('brq_service_transfer_customergender', 0, 'vm'));
					$htmlI .= '</td></tr>';
					$htmlI .= '</table><small><i>' . (JText::_('VMPAYMENT_BUCKAROO_REQUIRED_FIELDS')) . '</i></small></div>';
					$html[] = $htmlI;
				} else {
					$html[] = $this->getPluginHtml($method, $selected, $methodSalesPrice);
				}
			}
		}

		if (!empty($html)) {
			$htmlIn[] = $html;
			return true;
		}

		return false;
	}

	// render the plugin name and description
	protected function renderPluginName ($plugin) {
		$return = '';
		$plugin_name = $this->_psType . '_name';
		$plugin_desc = $this->_psType . '_desc';
		$description = '';

		if (!empty($plugin->$plugin_desc)) {
			$description = '<dd class="' . $this->_type . '_description">' . $plugin->$plugin_desc . '</dd>';
		}
		$pluginName = $return . '<dl class="uk-description-list uk-margin-remove"><dt class="' . $this->_type . '_name">';
		$pluginName .= '<i class="uk-icon-money uk-margin-small-right"></i>' . $plugin->$plugin_name . '</dt>' . $description . '</dl>';

		return $pluginName;
	}

	/*
	   * plgVmonSelectedCalculatePricePayment
	   * Calculate the price (value, tax_id) of the selected method
	   * It is called by the calculator
	   * This function does NOT to be reimplemented. If not reimplemented, then the default values from this function are taken.
	   * @author Valerie Isaksen
	   * @cart: VirtueMartCart the current cart
	   * @cart_prices: array the new cart prices
	   * @return null if the method was not selected, false if the shiiping rate is not valid any more, true otherwise
	   */
	public function plgVmonSelectedCalculatePricePayment (VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {
		return $this->onSelectedCalculatePrice($cart, $cart_prices, $cart_prices_name);
	}

	function plgVmgetPaymentCurrency ($virtuemart_paymentmethod_id, &$paymentCurrencyId) {

		if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
			return null; // Another method was selected, do nothing
		}

		if (!$this->selectedThisElement($method->payment_element)) {
			return false;
		}
		$this->getPaymentCurrency($method);

		$paymentCurrencyId = $method->payment_currency;
	}

	/**
	 * plgVmOnCheckAutomaticSelectedPayment
	 * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
	 * The plugin must check first if it is the correct type
	 * @author Valerie Isaksen
	 * @param VirtueMartCart cart: the cart object
	 * @return null if no plugin was found, 0 if more then one plugin was found,  virtuemart_xxx_id if only one plugin is found
	 */
	function plgVmOnCheckAutomaticSelectedPayment (VirtueMartCart $cart, array $cart_prices = array(), &$paymentCounter) {
		return $this->onCheckAutomaticSelected($cart, $cart_prices, $paymentCounter);
	}

	/**
	 * This method is fired when showing the order details in the frontend.
	 * It displays the method-specific data.
	 * @param integer $order_id The order ID
	 * @return mixed Null for methods that aren't active, text (HTML) otherwise
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 */
	public function plgVmOnShowOrderFEPayment ($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {

		if (!$this->selectedThisByMethodId($virtuemart_paymentmethod_id)) {
			return null; // Another method was selected, do nothing
		}

		if (!($paymentTable = $this->_getBuckarooInternalData($virtuemart_order_id))) {
			// JError::raiseWarning(500, $db->getErrorMsg());
			return null;
		}

		$html = '<table class="adminlist">' . "\n";
		$html .= $this->getHtmlHeaderBE();
		$html .= $this->getHtmlRowBE('BUCKAROO_PAYMENT_NAME', $paymentTable->payment_method);
		$html .= $this->getHtmlRowBE('BUCKAROO_PAYMENT_STATUSCODE', $paymentTable->statuscode);
		$html .= $this->getHtmlRowBE('BUCKAROO_PAYMENT_STATUSMSG', $paymentTable->statusmsg);
		$html .= $this->getHtmlRowBE('BUCKAROO_PAYMENT_PAYMENTDATE', $paymentTable->modified_on);
		$html .= '</table>' . "\n";

		return $html;
	}

	/**
	 * This method is fired when printing an Order
	 * It displays the the payment method-specific data.
	 * @param integer $_virtuemart_order_id The order ID
	 * @param integer $method_id            method used for this order
	 * @return mixed Null when for payment methods that were not selected, text (HTML) otherwise
	 * @author Valerie Isaksen
	 */
	function plgVmonShowOrderPrintPayment ($order_number, $method_id) {
		return $this->onShowOrderPrint($order_number, $method_id);
	}

	function plgVmDeclarePluginParamsPayment ($name, $id, &$data) {
		return $this->declarePluginParams('payment', $name, $id, $data);
	}

	function plgVmSetOnTablePluginParamsPayment ($name, $id, &$table) {
		return $this->setOnTablePluginParams($name, $id, $table);
	}

	/**
	 * This event is fired when the  method notifies you when an event occurs that affects the order.
	 * Typically,  the events  represents for payment authorizations, Fraud Management Filter actions and other actions,
	 * such as refunds, disputes, and chargebacks.
	 * NOTE for Plugin developers:
	 *  If the plugin is NOT actually executed (not the selected payment method), this method must return NULL
	 * @param      $return_context      : it was given and sent in the payment form. The notification should return it back.
	 *                                  Used to know which cart should be emptied, in case it is still in the session.
	 * @param int  $virtuemart_order_id : payment  order id
	 * @param char $new_status          : new_status for this order id.
	 * @return mixed Null when this method was not selected, otherwise the true or false
	 * @author Valerie Isaksen
	 */
	public function plgVmOnPaymentNotification ($virtuemart_order_id, $new_status) {
		vmdebug('BUCKAROO plgVmOnPaymentNotification', JRequest::get('post'));

		// Process payment
		$result = $this->processPayment();
		if (empty($result)) return $result;

		return true;
	}

	/**
	 * plgVmOnPaymentResponseReceived
	 * This event is fired when the  method returns to the shop after the transaction
	 *  the method itself should send in the URL the parameters needed
	 * NOTE for Plugin developers:
	 *  If the plugin is NOT actually executed (not the selected payment method), this method must return NULL
	 * @param int  $virtuemart_order_id : should return the virtuemart_order_id
	 * @param text $html                : the html to display
	 * @return mixed Null when this method was not selected, otherwise the true or false
	 * @author Valerie Isaksen
	 */
	function plgVmOnPaymentResponseReceived (&$html) {
		// Load required classes
		if (!class_exists('VirtueMartCart')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		}

		vmdebug('BUCKAROO plgVmOnPaymentResponseReceived', JRequest::get('post'));

		// Process payment & give user payment feedback
		$payment_method = $result = $this->processPayment($user_feedback = true);
		if (empty($result)) return $result;

		// Reset ideal user session variables
		$session = JFactory::getSession();
		if ($payment_method == 'ideal') {
			$session->set('brq_service_ideal_issuer', 0, 'vm');
		}
		if ($payment_method == 'idealprocessing') {
			$session->set('sepa_issuer', 0, 'vm');
		}

		// Empty the cart
		$cart = VirtueMartCart::getCart();
		$cart->emptyCart();

		// Set html response
		$html = $this->_getPaymentResponseHtml($paymentTable, $orderDetail);

		return true;
	}

	protected function processPayment ($user_feedback = false) {
		// Load required classes
		if (!class_exists('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php');
		}

		// Get order info from database
		$order_number = JRequest::getString('brq_invoicenumber', JRequest::getString('on', ''), 'post');
		if (!($orderDetail = VirtueMartModelOrders::getOrder(VirtueMartModelOrders::getOrderIdByOrderNumber($order_number)))) {
			return null;
		}

		// Init variables
		$virtuemart_order_id = $orderDetail['details']['BT']->virtuemart_order_id;
		$virtuemart_paymentmethod_id = $orderDetail['details']['BT']->virtuemart_paymentmethod_id;
		$vendorId = 0;
		$statuscode = JRequest::getString('brq_statuscode', '', 'post');
		$statusmsg = JRequest::getString('brq_statusmessage', '', 'post');
		$payment_amount = (float)JRequest::getString('brq_amount', 0, 'post');
		$buckarooPaymentStatus = new BuckarooPaymentStatus($statuscode);

		// Check basic requirements
		if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
			return null;
		}
		if (!$this->selectedThisElement($method->payment_element)) {
			return null;
		}
		if (!($paymentTable = $this->getDataByOrderId($virtuemart_order_id))) {
			JError::raiseWarning(500, "Table not found");
			return '';
		}

		// Check if payment reponse is sound
		if (!($this->checkBuckarooSignature($method->secretkey))) {
			JError::raiseWarning(500, Jtext::_('VMPAYMENT_BUCKAROO_PAYMENT_NOTBYBUCKAROO'));
			return false;
		}

		// Prepare data that should be stored in the database
		$dbValues['virtuemart_order_id'] = $paymentTable->virtuemart_order_id;
		$dbValues['order_number'] = $paymentTable->order_number;
		$dbValues['payment_method_id'] = $paymentTable->virtuemart_paymentmethod_id;

		$dbValues['payment_name'] = $paymentTable->payment_name;
		$dbValues['cost_per_transaction'] = $paymentTable->cost_per_transaction;
		$dbValues['cost_percent_total'] = $paymentTable->cost_percent_total;
		$dbValues['testmode'] = $paymentTable->testmode;

		$dbValues['payment_method'] = $paymentTable->payment_method;
		$dbValues['statuscode'] = $statuscode;
		$dbValues['statusmsg'] = $statusmsg;
		$dbValues['transactions'] = JRequest::getString('brq_transactions', '', 'post');

		$this->storePSPluginInternalData($dbValues, 'virtuemart_order_id', true);

		// Process payment amount
		if ($buckarooPaymentStatus->isSucces()) {

			$order_payment = & $orderDetail['details']['BT']->order_payment;
			$order_total = & $orderDetail['details']['BT']->order_total;
			$order_payment += $payment_amount;
			if ($order_payment < $order_total) {
				// Payment is not done in full, set status to waiting for consumer action
				$buckarooPaymentStatus->setStatus(BuckarooPaymentStatus::WAITING_CONSUMER_ACTION);
			} else if ($order_payment > $order_total) {
				$order_payment = $order_total;
			}

			// Set status message for partial payments
			if ($payment_amount != $order_total && !$user_feedback) {
				$statusmsg = "Payment of amount " . $payment_amount;
			}

		}

		// Check 3D secure credit card enrolled status
		if ($this->isHeldCredit3d()) {
			$buckarooPaymentStatus->setStatus(BuckarooPaymentStatus::WAITING_MERCHANT_ACTION);
		}

		// Update order status
		if ($user_feedback) {
			$statusmsg = "Buckaroo response: " . $statusmsg;
		} else {
			$statusmsg = "Buckaroo notification: " . $statusmsg;
		}
		// Set VM internal order status code according to value defined in XML or
		// (if applicable) overwritten by user defined value from datbase.
		// For example: payment_held_status => P
		$payment_status = $method->{$buckarooPaymentStatus->getPaymentStatus()};
		$send_email = false;
		if ($user_feedback) {
			$buckarooPaymentStatus->setUserFeedback();
			$send_email = true;
		}

		$this->updateOrder($orderDetail, $payment_status, $statusmsg, $send_email);

		return $paymentTable->payment_method;
	}

	/**
	 * 3D secure credit card payment check
	 * @return boolean
	 *   If not 3D secure enrolled or unknown, return TRUE, else FALSE
	 */
	protected function isHeldCredit3d () {
		// 3d processing - available cards are: mastercard, visa, Amex, maestro, Vpay, visaelectron
		$payment_methods = array(
			'brq_SERVICE_mastercard_Enrolled',
			'brq_SERVICE_visa_Enrolled',
			'brq_SERVICE_Amex_Enrolled',
			'brq_SERVICE_maestro_Enrolled',
			'brq_SERVICE_Vpay_Enrolled',
			'brq_SERVICE_visaelectron_Enrolled'
		);

		$input_value = FALSE;
		// foreach all possible values
		foreach ($payment_methods as $pmethod) {
			$input_value = JRequest::getString($pmethod, '', 'post');

			if ($input_value !== FALSE) {
				$secure_processing_enrolled = $input_value;
				if ($secure_processing_enrolled == 'N' || $secure_processing_enrolled == 'U') {
					// Wanneer wel gevraagd 3D secure maar niet afgehandeld OF Wanneer wel 3D secure gevraagd en status onbekend
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Updates (virtuemart's) order with buckaroo status and payment
	 * @param array  $order
	 * @param string $status_msg
	 */
	protected function updateOrder ($order, $status, $status_msg, $send_email) {

		$orders = VmModel::getModel('orders');
		$order_update = array(
			'customer_notified' => (int)$send_email,
			'order_status' => $status,
			'comments' => urldecode($status_msg),
			'order_payment' => $order['details']['BT']->order_payment,
		);

		$orders->updateStatusForOneOrder($order['details']['BT']->virtuemart_order_id, $order_update, true);
	}

	// this function is called after the user is redirect back to the merchants website
	// the table displays the chosen paymentmethod, ordernumber and the paid amount
	function _getPaymentResponseHtml ($buckarooTable, &$orderDetail) {
		// Load required classes
		if (!class_exists('VirtueMartModelCurrency')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'currency.php');
		}

		$html = '<div class="order">' . "\n";
		$html .= '<table class="vmorder-done">' . "\n";
		$html .= $this->getHtmlRow('BUCKAROO_PAYMENT_NAME', $buckarooTable->payment_method, "class='vmorder-done-payinfo'");

		if (!empty($buckarooTable)) {
			$currency = CurrencyDisplay::getInstance('');
			$html .= $this->getHtmlRow('BUCKAROO_ORDER_NUMBER', $buckarooTable->order_number, "class='vmorder-done-nr'");
			$html .= $this->getHtmlRow('STANDARD_AMOUNT', $currency->priceDisplay($orderDetail['details']['BT']->order_total), "class='vmorder-done-amount'");
		}

		$html .= '</table></div>' . "\n";

		return $html;
	}

	// cancelled payment
	function plgVmOnUserPaymentCancel () {
		vmdebug('BUCKAROO plgVmOnPaymentCancel', JRequest::get('post'));

		if (!class_exists('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php');
		}

		$order_number = JRequest::getString('brq_invoicenumber', JRequest::getString('on', ''), 'post');
		$order = VirtueMartModelOrders::getOrder($order_number);
		$virtuemart_paymentmethod_id = JRequest::getInt('pm', '');

		if (empty($order_number) or empty($virtuemart_paymentmethod_id) or !$this->selectedThisByMethodId($virtuemart_paymentmethod_id)) {
			return null;
		}

		if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number))) {
			return null;
		}

		$method = $this->getVmPluginMethod($virtuemart_paymentmethod_id);

		if (!($paymentTable = $this->getDataByOrderId($virtuemart_order_id))) {
			vmError(Jtext::_('VMPAYMENT_BUCKAROO_PAYMENT_CANCELLED'));
			return null;
		}

		$data = $this->checkBuckarooSignature($method->secretkey);
		VmInfo(Jtext::_('VMPAYMENT_BUCKAROO_PAYMENT_CANCELLED'));
		$session = JFactory::getSession();
		$return_context = $session->getId();

		if (strcmp($paymentTable->buckaroo_custom, $return_context) === 0) {
			$this->handlePaymentUserCancel($virtuemart_order_id);
		}

		return true;
	}

	/**
	 * @return array
	 */
	private function checkBuckarooSignature ($secretkey) {
		$data = array();
		$app = JFactory::getApplication();
		foreach (JRequest::get("post") as $key => $value) {
			$data[$key] = urldecode($app->input->getString($key, ""));
		}

		if (!($data['brq_signature'] == $this->calculateDigitalSignature($data, $secretkey))) {
			return false;
		}

		return true;
	}

	function _getBuckarooInternalData ($virtuemart_order_id, $order_number = '') {
		$db = JFactory::getDBO();
		$q = 'SELECT * FROM `' . $this->_tablename . '` WHERE ';

		if ($order_number) {
			$q .= " `order_number` = '" . $order_number . "'";
		} else {
			$q .= ' `virtuemart_order_id` = ' . $virtuemart_order_id;
		}

		$db->setQuery($q);

		if (!($paymentTable = $db->loadObject())) {
			//JError::raiseWarning(500, $db->getErrorMsg());
			return '';
		}

		return $paymentTable;
	}
}

/**
 * Wrapper class for buckaroo payment status codes

 */
class BuckarooPaymentStatus {

	// All buckaroo payment return status codes
	const PAYMENT_SUCCESS = 190;
	const PAYMENT_FAILURE = 490;
	const VALIDATION_ERROR = 491;
	const TECHNICAL_ERROR = 492;
	const PAYMENT_REJECTED = 690;
	const WAITING_USER_INPUT = 790;
	const WAITING_PROCESSOR = 791;
	const WAITING_CONSUMER_ACTION = 792;
	const CANCELLED_BY_CONSUMER = 890;
	const CANCELLED_BY_MERCHANT = 891;
	const WAITING_MERCHANT_ACTION = 1001; // custom added status to handle 3D secure held status

	public $status;

	public function __construct ($status) {
		$this->status = $status;
	}

	/**
	 * Get virtuemart order status code
	 * @return string
	 *   name of payment method status variable
	 */
	public function getPaymentStatus () {
		switch ($this->status) {
			case self::PAYMENT_SUCCESS:
				return plgVmPaymentBuckaroo::PAYMENT_APPROVED_STATUS;
			case self::WAITING_MERCHANT_ACTION:
				return plgVmPaymentBuckaroo::PAYMENT_HELD_CREDIT3D_STATUS;
			default:
				return plgVmPaymentBuckaroo::PAYMENT_HELD_STATUS;
		}
	}

	/**
	 * Return true when status is PAYMENT_SUCCES/190
	 */
	public function isSucces () {
		return ($this->status == self::PAYMENT_SUCCESS);
	}

	public function setStatus ($status) {
		$this->status = $status;
	}

	/**
	 * Return payment status message for user
	 * @return string
	 */
	public function setUserFeedback () {

		if ($this->getPaymentStatus() == plgVmPaymentBuckaroo::PAYMENT_APPROVED_STATUS) {
			return vmInfo(JText::_("VMPAYMENT_BUCKAROO_PAYMENT_SUCCESS")); // "betaling gelukt"
		} else {

			switch ($this->status) {
				case self::PAYMENT_REJECTED:
					return vmWarn(JText::_("VMPAYMENT_BUCKAROO_PAYMENT_REJECTED")); // "betaling afgewezen"

				case self::PAYMENT_FAILURE:
				case self::VALIDATION_ERROR:
				case self::TECHNICAL_ERROR:
					// "betaling mislukt"
					return vmError(JText::_("VMPAYMENT_BUCKAROO_PAYMENT_FAILURE"));

				case self::WAITING_USER_INPUT:
				case self::WAITING_CONSUMER_ACTION:
					// "actie benodigd van gebruiker"
					return vmInfo(JText::_("VMPAYMENT_BUCKAROO_USER_ACTION_REQUIRED"));

				case self::CANCELLED_BY_CONSUMER:
				case self::CANCELLED_BY_MERCHANT:
					// "betaling geannuleerd"
					return vmWarn(JText::_("VMPAYMENT_BUCKAROO_PAYMENT_CANCELLED"));

				default:
					// "betaling in behandeling"
					return vmInfo(JText::_("VMPAYMENT_BUCKAROO_PAYMENT_PROCESSING"));
			}
		}

	}

}

// Noclosing tag