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

$status = 'ko';
if (!Context::getContext()->cookie->isLogged())
	Tools::redirect('authentication.php?back=order.php');

$vars = explode('__', Tools::getValue('key'));
$token = Tools::getValue('token', '');

$ocartid = (int)$vars[0];
if ($ocartid != 0)
{
	$klikandpay = new KlikAndPay();
	$orderid = Order::getOrderByCartId($ocartid);
	$order = new Order(Order::getOrderByCartId($ocartid));
	if ($order->hasBeenPaid())
		$status = 'ok';
	else
		$status = 'ok';
}
else
	$status = 'ko';

Tools::redirectLink(
	__PS_BASE_URI__.
	"order-confirmation.php?id_cart={$ocartid}&id_module={$klikandpay->id}&id_order={$order->id}&key={$order->secure_key}&status=".
	$status
);
?>
