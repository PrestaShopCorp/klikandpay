KlikAndPay Prestashop Module
============================
Version 1.0


Installation & Configuration
----------------------------
-> La configuration nécessite d'avoir un compte auprès de Klik&Pay (www.klikandpay.com)

1. Installation
---------------
1.1 Dans l'interface d'administration de Prestashop, allez sur la page "Modules/Modules"
1.2 Cliquez sur "Ajouter un module"
1.3 Sélectionnez le fichier du module (prestashop_knp_vX.Y.zip) puis "Mettre ce module en ligne"
1.4 Recherche le module KlikAndPay dans la liste et activez-le 

2 Configuration module
----------------------
2.1 Ouvrir l'écran de configuration du module KlikAndPay (bouton configurer)
2.2 Saisir le ou les identifiants Klik&Pay (plusieurs identifiants permettent de gérer plusieurs devises)
  - Se munir du (des) identifant(s)
    Ceux-ci sont visible dans le Backoffice, dans la section "Adminsitration du compte / Infromation sur le compte" 
  - Chaque identifiant doit être suivi du symbole : et du code ISO de la devise du compte (p.ex. 1234567:EUR)
  - Chaque ensemble identifiant/devise est séparé par un retour à la ligne
2.3 Générer la Vkey à l'aide du bouton prévu sous le champ (va créer une chaîne de caractères aléatoires)
2.4 Laisser le module en mode Test pour les essais
2.5a Activer le paiement immédiat
     ET / OU
2.5b Activer le paiement fractionné et saisir les montants minimum correspondants

3 Configuration BackOffice Klik&Pay
-----------------------------------
3.1 Récupérer les informations indiquées sous le formulaire de configuration du module KlikAndPay
- URL de transaction acceptée
- URL de transaction refusée
- URL autorisée
- URL de notification
3.2 Ouvrir votre BackOffice Klik&Pay
3.3 Dans "Paramètre du compte / Paramétrage", saisir les URL « transaction acceptée » & « refusée » selon les indications du module
3.3 Dans "Paramètre du compte / Retour dynamique", saisir l'URL de retour dynamique selon les indications du module
3.4 Dans "Paramètre du compte / Retour dynamique", activer la réception des variables :
    - MONTANTXKP
    - DEVISEXKP

4. Test
-------
4.1 Vous pouvez effectuer les essais de transactions

5. Passage en mode production
------------------------------
5.1 Dans le BackOffice Klik&Pay, sélectionner l'option "Paramétrage du compte / TEST/PRODUCTION" puis passer en mode production
5.2 Dans l'écran de configuration du module Klik&Pay Prestashop, changer le mode en "Production" et enregistrer les changements

En cas de problèmes, veuillez ouvrir un ticket depuis le Backoffice (bouton Hotline en bas à droite - adressé au service technique)
