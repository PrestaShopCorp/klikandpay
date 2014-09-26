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

if (Tools::getIsset('compat14'))
{
	require(dirname(__FILE__).'/../../../../config/config.inc.php');
	require(dirname(__FILE__).'/../../../../init.php');
	require(dirname(__FILE__).'/../../../../header.php');
	require_once(_PS_MODULE_DIR_.'klikandpay/klikandpay.php');

	$kp = new KlikAndPay();
	$vars = $kp->getRequestVars();

	Context::getContext()->smarty->assign($vars);
	Context::getContext()->smarty->display(dirname(__FILE__).'/../../views/templates/front/request.tpl');
	die;
}

class KlikAndPayRequestModuleFrontController extends ModuleFrontController {

	public function initContent()
	{
		parent::initContent();

		require_once(_PS_MODULE_DIR_.'klikandpay/klikandpay.php');

		$kp = new KlikAndPay();
		$vars = $kp->getRequestVars();

		/*
		// Si le module de gestion des abonnements est installé et activé sur la boutique
		if (KlikAndPay::detectSubscriptionsManager()) {

			$cart = new Cart($this->context->cart->id); // Récupération du panier

			$products = $cart->getProducts(); // Récupération des produits du panier

			$i = 1; // Initialisation du compteur à 1
			$subscriptions = NULL; // Initialisation de la variable contenant les champs requis par KlikAndPay pour faire de l'abonnement
			$amount = 0; // Initialisation du montant à 0
			$subAmount = 0; // Initialisation du montant des abonnements

			// Pour chaque produit du panier
			foreach ($products as $product) {

				$quantity = 1; // Initialisation de la quantité à 1 (minimum possible)
				$schema = NULL; // Initialisation du schéma pour chaque produit

				// Pour chaque quantité de produit
				while ($quantity <= $product['cart_quantity']) {


					// Récupération du schéma lié au produit
					$schema = SMSchema::getList(array(
					'id_product' => $product['id_product'],
					'id_product_attribute' => $product['id_product_attribute'],
					'locked' => 0,
					'active' => 1
				));

				// Si le schéma est présent et qu'il est en récurrent
				if ($schema != NULL && $schema['one_shot'] == 0) {

					$sch = new SMSchema($schema['id_schema']); // Nouvelle instance du schéma
					// Si les variables ID_CUSTOMER ou ID_CART n'éxistent pas
					if (!isset($subscriptions['ID_CUSTOMER']) || !isset($subscriptions['ID_CART'])) {
						$subscriptions['ID_CUSTOMER'] = $this->context->customer->id; // On insère l'id du client en cours
						$subscriptions['ID_CART'] = $cart->id; // On insère l'id du panier en cours
					}

					$subscriptions['DURATION_' . $i] = $sch->duration; // Insertion de la durée
					$subscriptions['FREQUENCY_' . $i] = $sch->frequency; // insertion de la fréquence
					// Insertion du prix du produit avec sa déclinaison
					$subscriptions['AMOUNT_' . $i] = Product::getPriceStatic($sch->id_product, TRUE, $sch->id_product_attribute);
					$subscriptions['ID_SCHEMA_' . $i] = $sch->id; // Insertion de l'id du schéma lié
					// Si le schéma comprend une réduction
					if ($sch->discount_mode != NULL) {
						$subscriptions['IS_DISCOUNT_'.$i] = 1; // On indique a KlikAndPay
						// On indique le montant de la période d'essai
						$subscriptions['DISCOUNT_VALUE_'.$i] = $sch->getReccuringPrice();
						// On indique le nombre de mois de la période d'essai
						$subscriptions['DURATION_' . $i] = (int) $sch->duration - (int) $sch->discount_nb_months;

						$subAmount += (float) $sch->getReccuringPrice(); // On ajoute le prix du produit au total
					}
					else
						$subAmount += (float) Product::getPriceStatic($sch->id_product, TRUE, $sch->id_product_attribute);  // On ajoute le prix du produit au total

					$i = $i + 1; // On incrémente le compteur

					// Sinon (pas de schéma ou schéma avec paiement direct)
					} else {

						if ($product['id_product_attribute'] != 0) // Si aucune déclinaison
							// On ajoute le prix du produit au total
							$amount += (float) Product::getPriceStatic($product['id_product'], TRUE, $product['id_product_attribute']);
						else // Sinon
							$amount += (float) Product::getPriceStatic($product['id_product'], TRUE); // On ajoute le prix du produit au total
					}

					$quantity = $quantity + 1; // On incrémente la quantité
				}
			}
		}

			// Si le montant des abonnements
			if ($subAmount != 0)
				$vars['amount'] = $amount; // On modifie le montant du panier par le montant des produits classiques + ceux des abonnements directs

			if ($subscriptions != NULL) // Si il y a bien un abonnement dans le panier
				$this->context->smarty->assign('subscriptions', $subscriptions); // on assigne la variable afin qu'elle soit traitée par le fichier TPL
		}*/

		$this->context->smarty->assign($vars);
		$this->setTemplate('request.tpl');
	}
}

?>
