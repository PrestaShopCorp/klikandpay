<?php
/**
 * 2012-2013 Klik&Pay
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 *  @author    Klik&Pay <info@klikandpay.com>
 *  @copyright 2012-2013 Klik&Pay
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of KliK&Pay
 */

require_once '../../config/config.inc.php';
require_once '../../init.php';
require_once 'klikandpay.php';

if (!defined('KLIKANDPAY'))
	define('KLIKANDPAY', 'EUR');

if (!defined('_PS_OS_PAYMENT_'))
	define('_PS_OS_PAYMENT_', Configuration::get('PS_OS_PAYMENT'));
if (!defined('_PS_OS_BANKWIRE_'))
	define('_PS_OS_BANKWIRE_', Configuration::get('PS_OS_BANKWIRE'));
if (!defined('_PS_OS_CHEQUE_'))
	define('_PS_OS_BANKWIRE_', Configuration::get('_PS_OS_CHEQUE_'));
if (!defined('_PS_OS_ERROR_'))
	define('_PS_OS_ERROR_', Configuration::get('PS_OS_ERROR'));

$eurcurrency = Currency::getIdByIsoCode(KLIKANDPAY, '');

$response = Tools::getValue('RESPONSE', '');
$numxkp = Tools::getValue('NUMXKP', '');
$paiement = Tools::getValue('PAIEMENT', '');
$montantxkp = Tools::getValue('MONTANTXKP', '');
$devisexkp = Tools::getValue('DEVISEXKP', '');
$ipxkp = Tools::getValue('IPXKP', '');
$paysrxkp = Tools::getValue('PAYSRXKP', '');
$scorexkp = Tools::getValue('SCOREXKP', '');
$paysbxkp = Tools::getValue('PAYSBXKP', '');
$token = Tools::getValue('token', '');

$module_payment = new KlikAndPay();

$xdata = $module_payment->getConfig();
$keyname = $xdata['GKEY'];

if ($keyname == '')
{
	echo 'KO keyname';
	die();
}

$vars = explode('__', Tools::getValue($keyname, ''));

$ocartid = $vars[0];
$signature = $vars[2];
$cart = new Cart((int)$ocartid);

if ((int)$vars[1] > 1)
	$montantxkp = number_format($cart->getOrderTotal(true, 3), 2, '.', '');

if ($cart->id_currency != Configuration::get('PS_CURRENCY_DEFAULT') && (int)$vars[1] > 1)
	$montantxkp = Tools::convertPrice($montantxkp, $cart->id_currency, true);

$knp_cur = Currency::getIdByIsoCode($devisexkp);

if (!$knp_cur)
{
	echo 'KO - missing_currency';
	die;
}

$accounts = $module_payment->getAccounts();
$sellerid = '';

if (count($accounts) == 1)
{
	$accounts = array_shift($accounts);
	$sellerid = $accounts['sellerid'];
	$eurcurrency = $accounts['currency'];
	$account_used = $accounts;
}
else
{
	foreach ($accounts as $account)
	{
		if ($knp_cur == $account['currency'])
		{
			$account_used[] = $account;
			$eurcurrency = $account['currency'];
		}
	}

	if (count($account_used) > 1)
	{
		foreach ($account_used as $account)
		{
			if ($account['3ds'] == 1 && $account['threshold'] > 0 && $account['threshold'] <= $montantxkp)
			{
				$account_used = $account;
				break;
			} elseif ($account['3ds'] == 0)
				$account_used = $account;
		}
		if (isset($account_used[0]['sellerid']))
			$account_used = $account_used[0];
	}
	else
		$account_used = $account_used[0];

	$sellerid = $account_used['sellerid'];
}

if ($sellerid == '')
{
	echo 'KO - missing_sellerid';
	die;
}

if (!$eurcurrency)
{
	echo 'KO - invalid_currency';
	die;
}

if ($knp_cur != $eurcurrency)
{
	echo 'KO - currency_mismatch';
	die;
}

if (!strpos($montantxkp, '.'))
	$montantxkp = $montantxkp.'.00';

$localsignature = md5($xdata['vkey'].md5($montantxkp.$sellerid.$ocartid));

if (Validate::isLoadedObject($cart) && $signature == $localsignature)
{
	$message = '
	FRACTIONNEMENT: '.$vars[1].'<br/>
	NUMXKP: '.$numxkp.'<br />
	PAIEMENT: '.$paiement.'<br />
	MONTANTXKP: '.$montantxkp.'<br />
	DEVISEXKP: '.$devisexkp.'<br />
	IPXKP: '.$ipxkp.'<br />
	PAYSRXKP: '.$paysrxkp.'<br />
	<b>SCOREXKP: '.$scorexkp.'</b><br />
	PAYSBXKP: '.$paysbxkp;

	$id_order = (int)Order::getOrderByCartId($cart->id);
	$customer = new Customer((int)$cart->id_customer);
	$order = new Order($id_order);

	if (!$order->hasBeenPaid())
	{
		switch ($response)
		{
			case '00':
				if ($cart->OrderExists() == 0)
				{
					$module_payment->validateOrder(
						$cart->id, _PS_OS_PAYMENT_, $montantxkp, $module_payment->displayName, $message,
						array('transaction_id' => $numxkp), $eurcurrency, false, $customer->secure_key
					);

					$id_order = (int)Order::getOrderByCartId($cart->id);
					$order = new Order($id_order);
					echo 'OK';
				}
				else
				{
					$id_order = (int)Order::getOrderByCartId($cart->id);
					$order = new Order($id_order);
					$history = new OrderHistory();
					$history->id_order = (int)$order->id;
					$history->changeIdOrderState(_PS_OS_PAYMENT_, (int)$order->id);

					echo 'OK ';
				}
				Configuration::updateValue('KLIKANDPAY_CONFIGURATION_OK', true);
				break;

			case 'AWAITING':
				if ($cart->OrderExists() == 0)
				{
					$module_payment->validateOrder(
						$cart->id, _PS_OS_BANKWIRE_, $montantxkp, $module_payment->displayName, $message,
						array('transaction_id' => $numxkp), $eurcurrency, false, $customer->secure_key
					);

					$id_order = (int)Order::getOrderByCartId($cart->id);
					$order = new Order($id_order);
					echo 'OK';
				}
				else
				{
					echo 'KO - order_exists';
					die;
				}
				break;

			case 'AWAITINGCHEQUE':
				if ($cart->OrderExists() == 0)
				{
					$module_payment->validateOrder(
						$cart->id, _PS_OS_CHEQUE_, $montantxkp, $module_payment->displayName, $message,
						array('transaction_id' => $numxkp), $eurcurrency, false, $customer->secure_key
					);

					$id_order = (int)Order::getOrderByCartId($cart->id);
					$order = new Order($id_order);
					echo 'OK';
				}
				else
				{
					echo 'KO - order_exists';
					die;
				}
				break;

			case 'REFUSED':
				if ($cart->OrderExists() == 0)
				{
					$module_payment->validateOrder(
						$cart->id, _PS_OS_ERROR_, $montantxkp, $module_payment->displayName, $message,
						array('transaction_id' => $numxkp), $eurcurrency, false, $customer->secure_key
					);

					$id_order = (int)Order::getOrderByCartId($cart->id);
					$order = new Order($id_order);
					echo 'OK';
				}
				else
				{
					echo 'KO - order_exists';
					die;
				}
				break;

			case 'CANCELED':
				if ($cart->OrderExists() == 0)
				{
					$module_payment->validateOrder(
						$cart->id, _PS_OS_CANCELED_, $montantxkp, $module_payment->displayName, $message,
						array('transaction_id' => $numxkp), $eurcurrency, false, $customer->secure_key
					);

					$id_order = (int)Order::getOrderByCartId($cart->id);
					$order = new Order($id_order);
					echo 'OK';
				}
				else
				{
					$id_order = (int)Order::getOrderByCartId($cart->id);
					$order = new Order($id_order);
					$history = new OrderHistory();
					$history->id_order = (int)$order->id;
					$history->changeIdOrderState(_PS_OS_CANCELED_, (int)$order->id);
					echo 'OK';
				}
				break;
		}
	}
	else
		echo 'KO - invalid_status';
}
else
	echo 'KO AUT';
?>
