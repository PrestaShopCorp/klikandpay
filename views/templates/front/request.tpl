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

<p class="center" style="display: inline-block; line-height: 24px;"><img style="float: left; margin-right: 10px;" src="/modules/klikandpay/img/ajax-loader.gif" title="loading"/> {l s='Please wait while sending informations to klikandpay' mod='klikandpay'}</p>
{if $testing}
<form name="klikandpay" id="klikandpay" method="POST" action="{$knp_payment_url|escape:'htmlall':'UTF-8'}test/{$knpscript|escape:'htmlall':'UTF-8'}">
{else}
<form name="klikandpay" id="klikandpay" method="POST" action="{$knp_payment_url|escape:'htmlall':'UTF-8'}/{$knpscript|escape:'htmlall':'UTF-8'}">
{/if}

<input type="hidden" name="SOCIETE" value="{$invoice->company|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="NOM" value="{$invoice->lastname|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="PRENOM" value="{$invoice->firstname|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="ADRESSE" value="{$invoice->address1|escape:'htmlall':'UTF-8'}{if $invoice->address2!=''} {$invoice->address2|escape:'htmlall':'UTF-8'}{/if}">
<input type="hidden" name="CODEPOSTAL" value="{$invoice->postcode|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="VILLE" value="{$invoice->city|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="PAYS" value="{$customer.country|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="TEL" value="{if $invoice->phone_mobile!=''}{$invoice->phone_mobile|escape:'htmlall':'UTF-8'}{else}{$invoice->phone|escape:'htmlall':'UTF-8'}{/if}">
<input type="hidden" name="EMAIL" value="{$customer.email|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="ID" value="{$SellerID|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="L" value="{$language|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="MONTANT" value="{$amount|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="MODULE" value="PRESTASHOP">
<input type="hidden" name="MODULE_VERSION" value="3.0">
{if $extra!=""}<input type="hidden" name="EXTRA" value="{$extra|escape:'htmlall':'UTF-8'}"/>{/if}
<input type="hidden" name="RETOUR" value="{$OrderKey|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="RETOURVOK" value="{$OrderKey|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="RETOURVHS" value="{$OrderKey|escape:'htmlall':'UTF-8'}">
{*
{if !empty($subscriptions)}
    {foreach from=$subscriptions key=key item=value}
	<input type="hidden" name="{$key|escape:'htmlall':'UTF-8'}" value="{$value|escape:'htmlall':'UTF-8'}">
    {/foreach}
{/if}
*}
</form>


{literal}
<script type="text/javascript">
  $(document).ready(function() { $("#klikandpay").get(0).submit(); })	
</script>
{/literal}
