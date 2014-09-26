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

if (!defined('_PS_VERSION_'))
	exit;

define('KNP_PAYMENT_URL', 'https://www.klikandpay.com/paiement');

if (!defined('KLIKANDPAY'))
	define('KLIKANDPAY', 'EUR');

class KlikAndPay extends PaymentModule
{
	/** @var array list templates */
	private $a_knpscripts = array(
		'1' => 'check.pl',
		'2' => 'checkxfois.pl',
		'3' => 'checkxfois.pl',
		'4' => 'checkxfois.pl',
		'5' => 'checkxfois.pl',
		'6' => 'checkxfois.pl',
		'diff' => 'checkdiff.pl',
		'abo' => 'checkabo.pl'
	);

	/**
	 * Constructeur de la classe
	 */
	public function __construct()
	{
		$this->name = 'klikandpay';
		$this->tab = 'payments_gateways';
		$this->author = 'Klik&Pay';
		$this->version = '3.9';
		

		parent::__construct();
		if (!defined('KLIKANDPAY'))
			define('KLIKANDPAY', 'EUR');

		$this->page = basename(__FILE__, '.php');
		$this->extension = sprintf('%010X', crc32('KlikAndPay_V_1.0')).'_';
		$this->displayName = $this->l('KlikAndPay');
		$this->description = $this->l('Allow your customers to pay with Klik & Pay');

		if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
			require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
		else if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))		    
		    $this->bootstrap = true;
	}

	/**
	 * Fonction d'installation du module
	 */
	public function install()
	{
		// Valeurs par défaut de la configuration
		$a_data = array(
			'test' => '1',
			'vkey' => '',
			'GKEY' => ''
		);

		return (
			parent::install()
			&& $this->registerHook('backOfficeHeader')
			&& Configuration::updateValue('KAP_NB_ACCOUNT', 0)
			&& $this->registerHook('payment')
			&& $this->registerHook('paymentReturn')
			&& $this->setConfig($a_data)
		);
	}

	/**
	 * Fonction de désinstallation du module
	 */
	public function uninstall()
	{
		Configuration::deleteByName($this->extension.'CFG'); // Supression des paramètres du module

		$i = 1; // Init
		$founded = 0; // Init

		$nb = (int)Configuration::get('KAP_NB_ACCOUNT'); // Nombre de compte enregistrés

		//Tant que l'on a pas trouvé tous les comptes
		while ($founded < $nb)
		{
			if (Configuration::getIdByName($this->extension.'KAPA'.$i) > 0)
			{ // Si cette variable existe
				Configuration::deleteByName($this->extension.'KAPA'.$i); // On la supprime
				$founded = $founded + 1; // On incrémente le compteur
			}
			$i++;
		}

		Configuration::deleteByName('KAP_NB_ACCOUNT'); // On supprime la variable indiquant le nombre de compte enregistrés

		parent::uninstall();

		return true;
	}

	/**
	 * Affichage et traitement des actions du module en Back-Office
	 */
	public function getContent()
	{
		$request_uri = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&module_name='.$this->name;

		$output = '<h2>'.$this->displayName.'</h2>';

		// Si on a cliqué sur le bouton "Ajouter un compte" ou "Editer ce compte"
		if (Tools::isSubmit('btnAddAccount') || Tools::isSubmit('btnEditAccount'))
		{
			// Stockage des données du formulaire dans un tableau
			$account = array(
				'id_account' => (int)isset($_REQUEST['id_account']) ? $_REQUEST['id_account'] : 0,
				'sellerid' => $_REQUEST['sellerid'],
				'currency' => (int)isset($_REQUEST['currency']) ? $_REQUEST['currency'] : 0,
				'3ds' => (int)isset($_REQUEST['3ds']) ? 1 : 0,
				'mode1' => (int)isset($_REQUEST['mode1']) ? 1 : 0,
				'modex' => (int)$_REQUEST['modex'],
				'step2' => $_REQUEST['step2'],
				'step3' => $_REQUEST['step3'],
				'step4' => $_REQUEST['step4'],
				'step5' => $_REQUEST['step5'],
				'step6' => $_REQUEST['step6'],
				'threshold' => (int)isset($_REQUEST['threshold']) ? $_REQUEST['threshold'] : - 1,
				'has_threshold' => (int)isset($_REQUEST['has_threshold']) ? $_REQUEST['has_threshold'] : 0
			);

			$id_account = $this->setAccount($account); // Ajout/Modification du compte

			if($id_account == 0)
			    Tools::redirectAdmin("$request_uri#conf"); // Redirection avec confirmation
			else if (Tools::isSubmit('btnAddAccount')) // Si Ajout de compte
				Tools::redirectAdmin("$request_uri&conf=3#$id_account"); // Redirection avec confirmation
			else // Si Modification de compte
				Tools::redirectAdmin("$request_uri&conf=4#$id_account"); // Redirection avec confirmation
		}

		// Si on a cliqué sur le bouton "Supprimer ce compte"
		if (Tools::isSubmit('btnDeleteAccount'))
		{
			$this->deleteAccount($_REQUEST['id_account']); // Supression du compte

			Tools::redirectAdmin("$request_uri&conf=1#conf"); // Redirection avec confirmation
		}

		// Si on a cliqué sur le bouton "Sauvegarder les paramètres"
		if (Tools::isSubmit('btnSaveConfig'))
		{
			$a_data = array(
				'test' => (int)$_REQUEST['test'],
				'vkey' => $_REQUEST['vkey'],
				'GKEY' => ''
			);

			$this->setConfig($a_data); // Enregistrement des données

			Tools::redirectAdmin("$request_uri&conf=6#conf"); // Redirection avec confirmation
		}
		if (Tools::isSubmit('generateVkey'))
		{
			$vkey = "";
			$values = array("0", "1", "2", "3", "4", "5",
			    "6", "7", "8", "9", "A", "Z",
			    "E", "R", "T", "Y", "U", "I",
			    "O", "P", "Q", "S", "D", "F",
			    "G", "H", "J", "K", "L", "M",
			    "W", "X", "C", "V", "B", "N");

			for ($i = 0; $i < 64; $i++) {
			    $index = rand(0, 35);
			    $vkey .= $values[$index];
			}
			
			$xdata = $this->getConfig(); // On récpère les paramètres du module
			 
			$xdata['vkey'] = $vkey;

			$this->setConfig($xdata); // Enregistrement des données

			Tools::redirectAdmin("$request_uri&conf=6#conf"); // Redirection avec confirmation
		}
		
		$this->context->smarty->assign('version', _PS_VERSION_);

		// On affiche le Back-Office du module
		return $output.$this->displayForm();
	}

	/**
	 * Include du fichier CSS du module en Back-Office
	 */
	public function hookBackOfficeHeader()
	{
		$this->context->controller->addCSS($this->_path.'css/knp.css', 'all'); // Ajout du CSS
	}

	/**
	 * Préparation des variables à afficher dans les formulaires du module en Back-Office
	 */
	public function displayForm()
	{
		$a_warnings = array(); // Initialisation de la variable

		if ((int)ini_get('memory_limit') < 384) // Si la limite de la mémoire est trop faible
			// Message d'avertissement
			$a_warnings[] = $this->l('Please update the PHP option "memory_limit" to a minimum of "384M". (Current value : ')
					.ini_get('memory_limit').$this->l(')');

		$xdata = $this->getConfig(); // Récupération des paramètres du module
		$accounts = $this->getAccounts(); // Récupération des comptes créés

		$this->context->smarty->assign('a_warnings', $a_warnings);
		$this->context->smarty->assign('xdata', $xdata);
		$this->context->smarty->assign('accounts', $accounts);
		$this->context->smarty->assign('req_uri', $_SERVER['REQUEST_URI']);
		$this->context->smarty->assign('mod_path', $this->_path);
		$this->context->smarty->assign('extension', $this->extension);
		$this->context->smarty->assign('mod_uri', _PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name);
		$this->context->smarty->assign('http_host', $_SERVER['HTTP_HOST']);

		$currencies = Currency::getCurrencies(); // Récupération des devises de la boutique

		$this->context->smarty->assign('currencies', $currencies); // Assignation des devises récupérées

		return $this->display(__FILE__, 'views/templates/admin/interface.tpl'); // Appel du fichier Interface.tpl
	}

	/**
	 * Ajout des valeurs passées en paramètre en BDD
	 */
	public function setConfig($a_data)
	{
		$a_current_config = $this->getConfig(); // Récupération des paramètres du module

		// Si la valeur GKEY est vide
		if (empty($a_current_config['GKEY']) && empty($a_data['GKEY']))
			$a_data['GKEY'] = $this->getGKEY(); // On l'ajoute
		else
			$a_data['GKEY'] = $a_current_config['GKEY']; // Sinon on garde la même

		// On encode les données et on retourne VRAI si tout s'est bien passé
		return Configuration::updateValue($this->extension.'CFG', base64_encode(Tools::jsonEncode((array)$a_data)));
	}

	/**
	 * Supression du compte dont l'id est passé en paramètre
	 */
	public function deleteAccount($id_account)
	{
		$nb = (int)Configuration::get('KAP_NB_ACCOUNT'); // Récupération du nombre de compte
		Configuration::updateValue('KAP_NB_ACCOUNT', ($nb - 1)); // On décrémente ce nombre

		return Configuration::deleteByName($this->extension.'KAPA'.$id_account); // On supprime le compte en question
	}

	/**
	 * Création d'un compte si id_account est vide
	 * Edition d'un compte si id_account n'est pas vide
	 */
	public function setAccount($account)
	{
		if ($account['id_account'] != 0)
		{ // Si on a un id de compte
			// On modifie le compte
			Configuration::updateValue($this->extension.'KAPA'.$account['id_account'], base64_encode(Tools::jsonEncode((array)$account)));
			return $account['id_account']; // On retourne son id
		}
		  
		$Gaccounts = $this->getAccounts();

		foreach($Gaccounts as $Gaccount){
		    if($Gaccount['sellerid'] == $account['sellerid'])
			return 0;			
		}		
	    
		// Sinon
		$i = 1; // Init

		// Tant que la valeur existe, on incrémente
		while (Configuration::getIdByName($this->extension.'KAPA'.$i) > 0)
			$i++;

		$nb = (int)Configuration::get('KAP_NB_ACCOUNT'); // On récupère le nombre de comptes
		Configuration::updateValue('KAP_NB_ACCOUNT', $nb + 1); // On incrémente ce nombre

		Configuration::updateValue($this->extension.'KAPA'.$i, base64_encode(Tools::jsonEncode((array)$account))); // On l'ajoute le nouveau compte

		return $i; // On retourne son id
	}

	/**
	 * Création de la GKEY
	 */
	public function getGKEY()
	{
		$value_r = rand(rand(1000, 10000), rand(15000, 40000));
		$value_md5 = md5($value_r * time());
		$value = Tools::substr($value_md5, 0, 8);

		$tab_alphabet = Array(
			'A', 'B', 'C', 'D', 'E', 'F',
			'G', 'H', 'I', 'J', 'K', 'L',
			'M', 'N', 'O', 'P', 'Q', 'R',
			'S', 'T', 'U', 'V', 'W', 'X',
			'Y', 'Z'
		);
		shuffle($tab_alphabet);
		for ($i = 0; $i < 8; $i++)
			if (is_numeric($value[$i]))
				$value[$i] = $tab_alphabet[$value[$i]];

		return $value;
	}

	/**
	 * Récupération des paramètres du module
	 */
	public function getConfig()
	{
		$a_data = (array)Tools::jsonDecode(base64_decode(Configuration::get($this->extension.'CFG'))); // Décodage

		// Si le tableau n'est pas bon on insère un tableau vide
		if ($a_data === false)
			$a_data = array();

		return $a_data; // On retourne le tableau
	}

	/**
	 * Récupération du/des compte(s) créé(s)
	 */
	public function getAccounts()
	{
		$i = 1; // Init
		$founded = 0; // Init
		$threshold_control = array(); // Init
		$accounts = array(); // Init

		$nb = (int)Configuration::get('KAP_NB_ACCOUNT'); // Récupération du nombre de comptes créés

		// Tant qu'on a pas trouvé tous les comptes
		while ($founded < $nb)
		{
			if (Configuration::getIdByName($this->extension.'KAPA'.$i) > 0)
			{ // Si la variable existe
				$accounts[$i] = (array)Tools::jsonDecode(base64_decode(Configuration::get($this->extension.'KAPA'.$i))); // On la stocke

				$threshold_control[$i]['3ds'] = $accounts[$i]['3ds']; // On stocke le 3DS
				$threshold_control[$i]['currency'] = $accounts[$i]['currency']; // On stocke la devise

				$founded = $founded + 1; // On incrémente le nombre de comptes trouvés
			}
			$i++; // On incrémente le compteur
		}

		if (Tools::isEmpty($accounts)) // Si aucun compte
			return array(); // On retourne un tableau vide

		// Pour chaque compte
		foreach ($accounts as $key => &$account)
		{
			$account['id_account'] = $key; // On stocke l'id

			if ($account['currency'] != 0)
			{ // Si la devise est différente de 0
				$currency = Currency::getCurrency($account['currency']); // On récupère le détail de la devise
				$account['currencyName'] = $currency['iso_code']; // On récupère le nom de la devise
			}

			$account['has_threshold'] = 0; // Init

			if ($account['3ds'] == 1)
			{ // Si le 3D Secure est activé pour ce compte
				foreach ($threshold_control as $key_c => &$value)
			{ // Pour chaque seuil de déclenchement
					if ($key_c == $key) // Si l'id est le même
						continue; // On passe au suivant
					if ($value['currency'] == $account['currency'] && $value['3ds'] == 0) // Si la devise est la même et que le 3D Secure n'est pas activé
						$account['has_threshold'] = 1; // On ajoute au compte en cours à avoir un seuil de déclenchement
				}
			}
		}

		return $accounts; // On retourne les comptes
	}

	/**
	 * Passage des paramètres du module ainsi que de données supplémentaires en smarty
	 */
	public function getData()
	{
		$xdata = $this->getConfig(); // On récpère les paramètres du module
		$amount = Tools::ps_round($this->context->cart->getOrderTotal()); // Calcul du montant du panier

		// Assignation des variables au smarty
		$this->context->smarty->assign('knpscript', $this->a_knpscripts);
		$this->context->smarty->assign('amount', $amount);
		$this->context->smarty->assign('test', $xdata['test']);
		$this->context->smarty->assign('vkey', $xdata['vkey']);
		$this->context->smarty->assign('modulehash', $this->name.' - '.$this->version.' - author: Klik &amp; Pay');
		$this->context->smarty->assign('modulename', $this->name);
		$this->display = true;
	}

	/**
	 * Fonction d'ffichage du/des bouton(s) de paiement lors de la phase 5 du tunnel d'achat
	 */
	public function hookPayment($params)
	{
		if (!$this->active) // Si le module est désactivé
			return; // on affiche pas les boutons de paiement

		$amount = number_format($params['cart']->getOrderTotal(true, 3), 2, '.', ''); // Calcul du montant du panier

		$form = array(); // Init
		$eurcurrency = 0; // Init

		$accounts = $this->getAccounts(); // Récpération des comptes

		if (empty($accounts)) // Si aucun compte n'est enregistré
			return false; // On affiche aucun bouton de paiement

		if (count($accounts) == 1)
		{ // Si un seul compte est enregistré
			$accounts = array_shift($accounts); // Amélioration de la lisibilité du tableau

			$eurcurrency = $accounts['currency']; // Stockage de la devise du compte

			$account_used = $accounts; // Stockage du compte dans la variable finale

		}
		else
		{ // Si plusieurs compte sont enregistrés

			foreach ($accounts as $account)
			{ // Pour chaque compte
				if ($this->context->cart->id_currency == $account['currency'])
			{ // Si la devise du compte est la même que le panier en cours
					$account_used[] = $account; // On stocke le compte dans un tableau
					$eurcurrency = $account['currency']; // On stocke la devise
				}
			}

			if (count($account_used) > 1)
			{ // Si on a plusieurs comptes avec la même devise que celle du panier
				foreach ($account_used as $account)
			{ // Pour chaque compte
					// Si le 3D Secure est activé
					// et que le seuil est activé
					// et que le montant du panier et supérieur ou égal au seuil
					if ($account['3ds'] == 1 && $account['threshold'] > 0 && $account['threshold'] <= $amount)
			{
						$account_used = $account; // On prend ce compte
						break; // On sort de la boucle
					} elseif ($account['3ds'] == 0)
						$account_used = $account; // On stocke temporairement ce compte
				}

				if (isset($account_used[0]['sellerid'])) // Si on a toujours plusieurs comptes
					$account_used = $account_used[0]; // On prend le premier
			}
			else // Sinon
				$account_used = $account_used[0]; // On améliore la lisibilité du tableau
		}

		if ($this->context->cart->id_currency != $eurcurrency) // Si toutefois la devise du panier est différente de celle du compte sélectionné
			return false; // On affiche pas de bouton de paiement

		if ($account_used['mode1'] == '1')
			if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
				$form[] = array('num' => '1', 'link' => __PS_BASE_URI__.'modules/'.$this->name.'/controllers/front/request.php?compat14&x=1');
			else
				$form[] = array('num' => '1', 'link' => $this->context->link->getModuleLink('klikandpay', 'request', array('x' => '1')));

		for ($i = 2; $i <= (int)$account_used['modex']; $i++)
		{
			if (!empty($account_used['step'.$i]) && (($account_used['step'.$i] == 0) || ((int)$account_used['step'.$i] <= (float)$amount)))
				if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
					$form[] = array('num' => $i, 'link' => __PS_BASE_URI__.'modules/'.$this->name.'/controllers/front/request.php?compat14&x='.$i);
				else
					$form[] = array('num' => $i, 'link' => $this->context->link->getModuleLink('klikandpay', 'request', array('x' => $i)));
		}

		$this->context->smarty->assign('formulaires', $form); // Assignation des données du formulaire
		return $this->display(__FILE__, 'views/templates/hook/'.$this->name.'.tpl'); // Appel de la page d'affichage des boutons
	}

	/**
	 * Fonction éxécutée lors du retour du client sur la boutique après un paiement
	 */
	public function hookPaymentReturn($params)
	{
		if (!$this->active) // Si le module n'est pas activé
			return; // On ne retourne rien

		if ($params['objOrder']->module != 'klikandpay') // Si le nom ne correspond pas à celui du module
			return;

		$status = Tools::getValue('status'); // Récupération du status

		if ($status == 'ok') // Si le status est ok
			$this->context->smarty->assign(array('status' => 'ok')); // on assigne ok
		else // Sinon
			$this->context->smarty->assign('status', 'failed'); // On assigne echec

		return $this->display(__FILE__, 'views/templates/hook/payment_return.tpl');// Appel de la page de retour de paiement
	}

	/**
	 * Récupération de toutes les données nécéssaires à KlikAndPay pour procéder à un paiement
	 */
	public function getRequestVars()
	{
		$this->getData(); // Assignation des variables au smarty
		$xdata = $this->getConfig(); // récupération des paramètres du module
		$accounts = $this->getAccounts(); // Récupération des comptes enregistrés
		$account_used = array(); // init
		$amount = number_format($this->context->cart->getOrderTotal(true, 3), 2, '.', ''); // Récupération du montant du panier en cours

		// Si aucun compte enregistré
		if (count($accounts) < 1)
		{
			Tools::redirect($this->context->link->getPageLink('order.php').'?step=3');
			die();
		}

		// Si le compte est unique
		if (count($accounts) == 1)
		{
			$accounts = array_shift($accounts); // On améliore la lisibilité du tableau
			$sellerid = $accounts['sellerid']; // on récupère l'id du compte
			$eurcurrency = $accounts['currency']; // On récupère la devise
			$account_used = $accounts; // On stocke le compte dans la variable finale
		}
		else
		{ // Sinon si on a plusieurs comptes enregistrés
			foreach ($accounts as $account)
		{ // Pour chaque compte
				if ($this->context->cart->id_currency == $account['currency'])
		{ // Si la devise est identique avec celle du panier
					$account_used[] = $account; // On stocke le compte en cours
					$eurcurrency = $account['currency']; // On stocke la devise
				}
			}

			if (count($account_used) > 1)
			{ // Si on a plusieurs comptes pour la même devise
				foreach ($account_used as $account)
			{ // pour chaque account
					// Si le 3D Secure est activé
					// et que le seuil est activé
					// et que le montant du panier et supérieur ou égal au seuil
					if ($account['3ds'] == 1 && $account['threshold'] > 0 && $account['threshold'] <= $amount)
			{
						$account_used = $account;
						break;
					} elseif ($account['3ds'] == 0)
						$account_used = $account; // On stocke temporairement le compte en cours
				}

				if (isset($account_used[0]['sellerid'])) // Si on a toujours plusieurs comptes
					$account_used = $account_used[0]; // On prend le premier
			}
			else // Sinon
				$account_used = $account_used[0]; // On améliore la lisibilité du tableau

			$sellerid = $account_used['sellerid']; // On stocke l'id du compte
		}

		if (!$eurcurrency)
		{ // Si aucune devise
			Tools::redirect($this->context->link->getPageLink('order.php').'?step=3'); // Redirection
			die();
		}

		if ($this->context->cookie->id_currency != $eurcurrency)
		{ // Si la devise ne correspond pas à celle du panier
			Tools::redirect($this->context->link->getPageLink('order.php').'?step=3'); // Redirection
			die();
		}

		if ($sellerid == '')
		{ // Si on aucun id de compte
			Tools::redirect($this->context->link->getPageLink('order.php').'?step=3'); // Redirection
			die();
		}

		$x = Tools::getValue('x');
		if ((int)$x < 1 || (int)$x > 6)
			$x = 1;

		$mode = (int)$x;
		$checked = false;
		$extra = '';

		$form = array();
		if ($mode > 1)
		{
			if ($account_used['step'.$mode] == '')
				$form[$mode] = '';
			elseif ($account_used['step'.$mode] == 0)
				$form[$mode] = $mode;
			elseif ((int)$account_used['step'.$mode] <= (float)$amount)
				$form[$mode] = $mode;

			$checked = true;
			$extra = $mode.'FOIS';
		}
		else
		{
			if ($account_used['mode1'] == '1')
				$checked = true;
			$extra = '';
		}

		if (!$checked)
		{
			Tools::redirect($this->context->link->getPageLink('order.php').'?step=3');
			die();
		}

		$invoice = new Address((int)$this->context->cart->id_address_invoice); // Récupération de la facture
		$country = new Country((int)$invoice->id_country); // Récupération du pays
		$customer = array(); // Init

		if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
			$customer['country'] = Tools::strtolower($country->iso_code);
		else
			$customer['country'] = Tools::strtolower($this->context->country->iso_code);

		$customeraccount = new Customer((int)$this->context->cart->id_customer); // Appel du client
		$customer['email'] = $customeraccount->email; // Récupération de l'email du client
		$lang_iso = Language::getIsoById((int)$this->context->cookie->id_lang); // Récupération de la langue

		// Création de la signature
		$signature = $this->context->cart->id.'__'.$mode.'__'.md5($xdata['vkey'].md5($amount.$sellerid.$this->context->cart->id));

		// Assignation des variables au smarty
		$this->context->smarty->assign('sellerid', $account_used['sellerid']);
		$this->context->smarty->assign('mode1', $account_used['mode1']);
		$this->context->smarty->assign('modex', $account_used['modex']);
		$this->context->smarty->assign('step2', $account_used['step2']);
		$this->context->smarty->assign('step3', $account_used['step3']);
		$this->context->smarty->assign('step4', $account_used['step4']);
		$this->context->smarty->assign('step5', $account_used['step5']);
		$this->context->smarty->assign('step6', $account_used['step6']);

		// Création d'un tableau comprenant les données importantes
		$smarty_vars = array(
			'knpscript' => $this->a_knpscripts[$mode],
			'invoice' => $invoice,
			'OrderKey' => $signature,
			'customer' => $customer,
			'amount' => $amount,
			'language' => $lang_iso,
			'testing' => $xdata['test'],
			'SellerID' => $sellerid,
			'extra' => $extra,
			'phone' => '',
			'knp_payment_url' => KNP_PAYMENT_URL
		);

		return $smarty_vars; // On retourne ce tableau
	}

	/**
	 * Détecte si le module Gestion d'abonnement éxiste
	 */
	/*
	static public function detectSubscriptionsManager()
	{
		$subscriptionsmanager = ModuleCore::getInstanceByName('subscriptionsmanager');

		if (!empty($subscriptionsmanager))
			return $subscriptionsmanager->active;

		return false;
	}
	*/
}

?>
