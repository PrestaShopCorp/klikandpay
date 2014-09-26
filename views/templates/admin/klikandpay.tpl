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



<form id="main-form" action="{$req_uri|escape:'htmlall':'UTF-8'}" method="post" >
    
    
    <fieldset>
	<legend>{l s='Procedure a suivre pour utiliser KlikAndPay' mod='klikandpay'}</legend>
	    <ul>
		<li>1. {l s='Creez un compte avec le lien suivant : ' mod='klikandpay'}<a target='_BLANK' href='https://www.klikandpay.com/cgi-bin/inscription.pl'>https://www.klikandpay.com/cgi-bin/inscription.pl</a></li>
		<li>2. {l s='Ajoutez un compte en cliquant sur le bouton "Ajouter un compte" en haut de la page' mod='klikandpay'}</li>
		<li>3. {l s='Passez le' mod='klikandpay'} <a href="#vkey">{l s='module' mod='klikandpay'}</a> {l s='et le' mod='klikandpay'} <a href="https://www.klikandpay.com/marchands/index.cgi" target="_blank">{l s='compte KlikAndPay' mod='klikandpay'}</a> {l s='en mode production' mod='klikandpay'}</li>
	    </ul>
    </fieldset>
    
     <fieldset>
	<legend>{l s='Procedure a suivre pour utiliser KlikAndPay' mod='klikandpay'}</legend>
	
	<div class="information">
	    <img src='{$module_dir|escape:'htmlall':'UTF-8'}/img/klikandpay_logo.png' title='KlikAndPay logo' style=" width: 400px;" />
	</div>
	<div class="information left">	   
		{l s='Klik & Pay est une solution globale de paiement sécurisé accessible sur PC, Tablette ou Smartphone.' mod='klikandpay'}<br/>
		{l s='Partenaire de banques et d’acquéreurs internationaux, Klik & Pay accompagne ses marchands depuis 15 ans, en France, en Europe et partout dans le monde.' mod='klikandpay'}
	</div>
		
	<div class="information left">{l s='Nos clients nous recommandent pour :' mod='klikandpay'}<br/>
	{l s='- Notre solution complète sans avoir besoin de contrat VAD' mod='klikandpay'}<br/>
	{l s='- Une tarification compétitive, sans frais d’abonnement ou d’installation' mod='klikandpay'}<br/>
	{l s='- Un contrôle anti-fraude associé à un compte avec ou sans 3D Secure' mod='klikandpay'}<br/>
	{l s='- Notre service client multilingue joignable par téléphone ou par mail' mod='klikandpay'}<br/>
	{l s='- Nos conseils pour développer leur activité, les accompagner à l’international.' mod='klikandpay'}</div>
	<div class="information left">{l s='Pour ouvrir un compte, suivez ce lien: ' mod='klikandpay'}<a target='_BLANK' href='https://www.klikandpay.com/cgi-bin/inscription.pl'>https://www.klikandpay.com/cgi-bin/inscription.pl</a> {l s='ou envoyez nous un email à ' mod='klikandpay'}<a href="mailto:market@klikandpay.com">market@klikandpay.com</a></div>
	<div class="information left">{l s='Si vous avez déjà un compte marchand Klik & Pay, vous pouvez directement passer au paramétrage du module PrestaShop ci-dessous.' mod='klikandpay'}</div>

	<div class="center small" style="margin: 0; padding: 0;">{l s='Établissement de Paiement agrée CSSF n°15/14 au Luxembourg exerçant en Libre Prestation de Service (LPS) en France' mod='klikandpay'}</div>

    </fieldset>

     <fieldset>
	<legend>{l s='Procedure a suivre pour utiliser KlikAndPay' mod='klikandpay'}</legend>
        <div id="tab-config" class="block-config">          
                       
            
            <label for="vkey">{l s='Vkey' mod='klikandpay'}</label>
	    <div class="margin-form">
		<input type="text" id="vkey" name="vkey" value="{$xdata.vkey|escape:'htmlall':'UTF-8'}" style="width: 550px"/>
		<p class="clear">
		    {l s='vkey will be used to encrypt data. Click' mod='klikandpay'}
		    <input type="submit" id="generateVkey" name="generateVkey" value="{l s='here' mod='klikandpay'}" class="button" /> {l s='to generate one' mod='klikandpay'}	
		</p>
	    </div>  

	    
	    <label for="test">{l s='Mode de paiement' mod='klikandpay'}</label>
	    <div class="margin-form">
		<input type="radio" id="test" name="test" value="1" {if $xdata.test != 0}checked="checked"{/if}) /> {l s='Test' mod='klikandpay'}
		<input type="radio" name="test" value="0" {if $xdata.test == 0}checked="checked"{/if}} /> {l s='Production' mod='klikandpay'}
		<p class="clear">{l s='Choisissez le mode de paiement du module' mod='klikandpay'}</p>
	    </div>
	
	<input type="submit" id="btnSaveConfig" name="btnSaveConfig" value="{l s='Save settings' mod='klikandpay'}" class="button" />

    </fieldset>

     <fieldset>
        <legend>{l s='Informations' mod='klikandpay'}</legend>
	
	{if sizeof($a_warnings)}
    {foreach from=$a_warnings item="sWarning"}
	<div class="warn warn alert alert-message">{$sWarning|escape:'UTF-8'}</div>
    {/foreach}
{/if}

	
        <a class="button" href="https://www.klikandpay.com/marchands/index.cgi" target="_blank">{l s='Clic Here to go to console' mod='klikandpay'}</a><br/><br/>
        {l s='In order to use your KlikAndPay payment module, you have to configure your KlikAndPay account (sandbox account as well as live account) Log in to KlikAndPay and set following parameters' mod='klikandpay'}
        <br /><br />
        <div class="information">
	    <b>{l s='URL accepted transaction' mod='klikandpay'}</b>
	    <span>{$mod_uri|escape:'htmlall':'UTF-8'}/checkOrder.php?key=</span>
	</div>
        <div class="information">
	    <b>{l s='URL refused transaction' mod='klikandpay'}</b>
	    <span>{$mod_uri|escape:'htmlall':'UTF-8'}/checkOrder.php?ko=</span>
	</div>
        <div class="information">
	    <b>{l s='Check if this url is already allowed or add it' mod='klikandpay'}</b>
	    <span>{$http_host|escape:'htmlall':'UTF-8'}</span>
	</div>

        <div class="information">
	    <b>{l s='URL de retour dynamique' mod='klikandpay'}</b>
	    <span>{l s='Goto Setting-up the account > Dynamic return > URL of your validation script and set :' mod='klikandpay'}{$mod_uri|escape:'htmlall':'UTF-8'}/auto-response.php?{$xdata.GKEY|escape:'htmlall':'UTF-8'}=</span>
	</div>
	<div class="information">
	    <b>{l s='Make sure the following options are checked' mod='klikandpay'}</b>
		<span>MONTANTXKP et DEVISEXKP</span>
	</div>
		
	<div class="information">
	    {l s='Now you are ready to test the payment process. ' mod='klikandpay'}
	    {l s='When the tests are completed, move your account to PRODUCTION mode by going to: Setting-up the account > TEST / PRODUCTION then you can set Testing mode to No at the top of this page.' mod='klikandpay'}
	</div>
	<p>{l s='Have a trouble ? please contact the support' mod='klikandpay'}: <a href="mailto:support@klikandpay.com">support@klikandpay.com</a></p>
    </fieldset>

    
</form>

