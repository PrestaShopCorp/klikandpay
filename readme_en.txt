KlikAndPay Prestashop Module
============================
Version 1.0

Installation & Configuration
----------------------------
-> Using the KlikAndPay Prestashop module requires an account on the Klik&Pay payment gateway (www.klikandpay.com)

1. Installation
---------------
1.1 From the Prestashop administration page, select the "Modules/Modules" menu
1.2 Click on "Add a module"
1.3 Select the module file (prestashop_knp_vX.Y.zip) and click on "Put this module online"
1.4 Search the KilkAndPay module in the list and enable it 

2 Module configuration
----------------------
2.1 Open the configuration page of the KlikAndPay module (configure button)
2.2 Provide your Klik&Pay ID (each currency requires a KlikAndPay ID account on Klik & Pay)
  - Get your Klik&Pay IDs
    You can get them from the Backoffice, under "Account Administration / Information on the account"
  - Get your Klik&Pay IDs from that was sent to you by Klik&Pay
  - Each ID has to be followed by the : character and the ISO code of the currency (ie. 1234567:EUR)
  - Each group ID/currency has to be written on a separate line
2.3 Generate the VKey by using the link provided below the field
2.4 Maintain the module in Test mode for testing
2.5a Enable the direct payment
     AND / OR
2.5b For payment with settlements, enable the multiple payment option and fill the minimum amounts

3 Klik&Pay BackOffice Configuration  
-----------------------------------
3.1 Retrieve the configuration information provided under the KlikAndPay module configuration page
- Accepted transaction URL
- Refused transaction URL
- Autorised URL
- Notification URL
3.2 Open your Klik&Pay BackOffice
3.3 In "Account set-up / Set-up", provide the accepted and refused URLs as specified by the module
3.3 In "Account set-up / dynamic return", provide the dynamic return URL as specified by the module
3.4 In "Account set-up / dynamic return", make sure the following variables are enabled:
    - MONTANTXKP
    - DEVISEXKP

4. Test
-------
4.1 You are now ready to make test transactions

5. Switch to production mode
----------------------------
5.1 In the Klik&Pay BackOffice, select "Account setup-up / TEST/PRODUCTION" and change to production mode
5.2 Back on the Klik&Pay Prestashop module configuration, change the mode to "Production" and save the settings

If you need any help, please open a ticket from the Backoffice (Hotline button, in the bottom-right corner)