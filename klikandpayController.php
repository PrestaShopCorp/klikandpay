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

class KlikAndPayModuleFrontController extends ModuleFrontController
{
	public function __construct()
	{
		$this->controller_type = 'front';

		if (is_null($this->display_header))
			$this->display_header = true;

		if (is_null($this->display_footer))
			$this->display_footer = false;

		$this->context = Context::getContext();
		$this->context->controller = $this;
		// Usage of ajax parameter is deprecated
		$this->ajax = false;
	}


	public function displayFooter()
	{
		$this->display_footer = true;
		$this->initFooter();
		$this->smartyOutputContent(_PS_THEME_DIR_.'footer.tpl');
	}
}
