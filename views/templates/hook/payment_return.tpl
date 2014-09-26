{*
* 2012-2013 Klik&Pay
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*
*  @author Klik&Pay <info@klikandpay.com>
*  @copyright  2012-2013 Klik&Pay
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of KliK&Pay
*}
{if $status == 'ok'}
	<p>{l s='Your order on' mod='klikandpay'} <span class="bold">{$shop_name|escape:'htmlall':'UTF-8'}</span> {l s='is complete.' mod='klikandpay'}
		<br /><br />{l s='You can see your order' mod='klikandpay'} <a href="{$base_dir|escape:'htmlall':'UTF-8'}history.php">{l s='in your order history' mod='klikandpay'}</a>.
		<br /><br />{l s='For any questions or for further information, please contact our' mod='klikandpay'} <a href="{$base_dir|escape:'htmlall':'UTF-8'}contact-form.php">{l s='customer support' mod='klikandpay'}</a>.
	</p>
{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, you can contact our' mod='klikandpay'} 
		<a href="{$base_dir|escape:'htmlall':'UTF-8'}contact-form.php">{l s='customer support' mod='klikandpay'}</a>.
	</p>
{/if}
