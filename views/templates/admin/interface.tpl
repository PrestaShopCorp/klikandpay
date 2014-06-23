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
<script>
 {literal}
	$(document).ready(function(){	
	    
	    function init(){
		
		var hash = window.location.hash.substring(1);
		
		if(hash){
		    
		$('#tabs-'+hash).addClass('selected');
		$('.tab-block').hide();
		
		$('#block-tabs-'+hash).show();
		    
		}
		else{
		    $('#tabs-conf').addClass('selected');
		}
		
	    }
		
	    $('.tab-selector').click(function(){
		
		//alert($(this).attr('id'));
		
		$('.tab-selector').removeClass('selected');
		$(this).addClass('selected');
		$('.tab-block').hide();
		
		$('#block-'+$(this).attr('id')).show();
		
	    });
	    
	    init();
	    
	    
			
	});
  {/literal}
</script>

<div id="tabs" class="tabs-bottom">
<!-- Tabs for main interface --> 
  <ul class="tab">
	<li><a id="tabs-conf" class="tab-selector button" href="#conf">{l s='Settings' mod='klikandpay'} </a></li>
	<li><a id="tabs-add" class="tab-selector button" href="#add">{l s='Add account' mod='klikandpay'}</a></li>
	
	{foreach $accounts as $account}  
	    <li><a id="tabs-{$account.id_account}" class="tab-selector button" href="#{$account.id_account}">{l s='Manage account' mod='klikandpay'} {$account.sellerid} - {$account.currencyName} - {if $account.3ds == 1}{l s='With 3D Secure' mod='klikandpay'}{else}{l s='No 3D Secure' mod='klikandpay'}{/if}</a></li>
	{/foreach} 
  </ul>

  {$account = 0}
  <div id="block-tabs-conf" class="tab-block">
	{if $version > '1.6.0.0'}{include file="./klikandpay_1.6.tpl"}{else}{include file="./klikandpay.tpl"}{/if}
  </div>
  
    <div id="block-tabs-add" class="tab-block" style="display: none;">
	{if $version > '1.6.0.0'}{include file="./loop_account_1.6.tpl"}{else}{include file="./loop_account.tpl"}{/if}	          
    </div>
    
 {foreach $accounts as $account}      
    <div id="block-tabs-{$account.id_account|escape:'htmlall':'UTF-8'}" class="tab-block" style="display: none;">
	{if $version > '1.6.0.0'}{include file="./loop_account_1.6.tpl"}{else}{include file="./loop_account.tpl"}{/if}
    </div>
 {/foreach} 
  
</div>