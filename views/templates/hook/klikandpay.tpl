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

{foreach from=$formulaires item=formulaire}
    <p class="payment_module">
        <a href="{$formulaire.link|escape:'htmlall':'UTF-8'}">
      <img src="{$module_dir|escape:'htmlall':'UTF-8'}img/logo_payment.png" />
      {if $formulaire.num == 1}
        {l s='Payez avec Klik&Pay' mod='klikandpay'}      
      {elseif $formulaire.num == 2}                
        {l s='Payez avec Klik&Pay en 2 fois' mod='klikandpay'}
      {elseif $formulaire.num == 3}                
        {l s='Payez avec Klik&Pay en 3 fois' mod='klikandpay'}
      {elseif $formulaire.num == 4}                
        {l s='Payez avec Klik&Pay en 4 fois' mod='klikandpay'}
      {elseif $formulaire.num == 5}                
        {l s='Payez avec Klik&Pay en 5 fois' mod='klikandpay'}
      {elseif $formulaire.num == 6}                
        {l s='Payez avec Klik&Pay en 6 fois' mod='klikandpay'}
      {/if}
      </a>
    </p>
{/foreach}
