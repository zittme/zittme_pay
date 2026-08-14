<?php

$lang->zittme_pay = 'Zittme Pay';

// Onglets d'administration
$lang->zpay_tab_config = 'Général';
$lang->zpay_tab_gateway = 'Moyens de paiement';
$lang->zpay_tab_orders = 'Paiements';
$lang->zpay_tab_logs = 'Journal des échanges';

$lang->about_zpay_config = 'Comportement commun du moteur de paiement. Les modules qui ont besoin de paiement, comme la boutique ou la réservation, partagent ces réglages.';
$lang->about_zpay_gateway = 'Activez les moyens de paiement souhaités et saisissez leurs clés. Un moyen sans clé n\'apparaît jamais sur la page de paiement.';
$lang->about_zpay_logs = 'Toutes les requêtes et réponses échangées pour un paiement. C\'est votre preuve en cas de litige : conservez-les suffisamment longtemps.';

// Noms des moyens de paiement
$lang->gateway_toss = 'Toss Payments';
$lang->gateway_banktransfer = 'Virement bancaire';
$lang->gateway_inicis = 'KG INICIS';
$lang->zpay_inicis_mid = 'INICIS merchant ID (MID)';
$lang->zpay_inicis_sign_key = 'Sign Key';
$lang->zpay_inicis_api_key = 'INIAPI Key (for refunds)';
$lang->zpay_inicis_key_help = 'Issued from the INICIS merchant admin. Refunds use a separate INIAPI key. In test mode, use the test merchant (INIpayTest) keys.';
$lang->gateway_kcp = 'NHN KCP';
$lang->zpay_kcp_site_cd = 'KCP site code (site_cd)';
$lang->zpay_kcp_cert_info = 'Service certificate (PEM body)';
$lang->zpay_kcp_priv_key = 'Merchant private key (PEM body)';
$lang->zpay_kcp_priv_pass = 'Private key password';
$lang->zpay_kcp_key_help = 'Paste the PEM file contents issued from the KCP admin certificate center. The private key is used only to sign refund requests. In test mode, use the test site code (T0000) and the developer center test certificate.';
$lang->gateway_nicepay = 'NICE Pay';
$lang->zpay_nicepay_client_id = 'NICE Pay Client ID';
$lang->zpay_nicepay_secret_key = 'Secret Key';
$lang->zpay_nicepay_key_help = 'Issued from the NICE Pay developer center (developers.nicepay.co.kr). Use sandbox keys in test mode and production keys issued after the merchant contract in production.';
$lang->gateway_portone = 'PortOne';
$lang->zpay_portone_store_id = 'PortOne Store ID';
$lang->zpay_portone_channel_key = 'Channel key';
$lang->zpay_portone_api_secret = 'V2 API Secret';
$lang->zpay_portone_key_help = 'Issued from the PortOne console (portone.io). The actual PG is chosen in the console channel settings. Use a test channel key for testing.';
$lang->gateway_paypal = 'PayPal';
$lang->zpay_paypal_client_id = 'PayPal Client ID';
$lang->zpay_paypal_secret = 'PayPal Secret';
$lang->zpay_paypal_key_help = 'Issued from your app on the PayPal developer console (developer.paypal.com). Use sandbox app keys in test mode and live app keys in production.';
$lang->zpay_paypal_currency = 'PayPal currency';
$lang->zpay_paypal_currency_help = 'PayPal does not support KRW. Order amounts are converted to this currency.';
$lang->zpay_paypal_exchange_rate = 'Exchange rate';
$lang->zpay_paypal_exchange_rate_help = 'KRW per 1 unit of the payment currency. Example: 1350 for USD. Refunds use the rate stored at payment time; the shop bears any exchange difference.';
$lang->zpay_exchange_rates = 'Shared exchange rates';
$lang->zpay_exchange_rates_help = 'KRW per 1 unit of each currency. Used by both Zittme Pay conversion and commerce multi-currency prices. Orders store the rate at payment time.';
$lang->zpay_fx_no_active = 'No additional currencies. Select currencies in Basic settings and rate rows appear here automatically.';
$lang->zpay_fx_auto_ph = 'Auto updated';
$lang->zpay_fx_code = 'Currency code';
$lang->zpay_fx_rate = 'Rate (KRW)';
$lang->zpay_fx_manual = 'Manual lock';
$lang->zpay_fx_manual_short = 'Lock';
$lang->zpay_exchange_auto = 'Auto update';
$lang->zpay_exchange_auto_label = 'Update once a day';
$lang->zpay_exchange_auto_help = 'Updated once a day on payment/view paths. Currencies with manual lock are not overwritten.';
$lang->zpay_fx_source_erapi = 'open.er-api.com (no key)';
$lang->zpay_fx_source_koreaexim = 'Korea Eximbank (API key required)';
$lang->zpay_fx_api_key = 'API key';
$lang->zpay_fx_updated = 'Last updated';
$lang->zpay_paypal_rate_shared_help = 'Uses the matching currency from the shared exchange rates above.';

// Général
$lang->zpay_enabled = 'Activer les paiements';
$lang->zpay_enabled_help = 'Désactivez pour ne plus accepter de nouveaux paiements.';
$lang->zpay_test_mode = 'Mode test';
$lang->zpay_test_mode_help = 'Indique que les clés de test du prestataire sont utilisées. Aucun argent réel ne circule.';
$lang->zpay_currency = 'Devise';
$lang->zpay_currency_help = 'The base currency for the whole site. Product prices, payments, credits, and statistics all use this currency. Example: KRW, USD, MXN. Only gateways that support it appear at checkout.';
$lang->zpay_extra_currencies = 'Additional currencies';
$lang->zpay_extra_currencies_help = 'Currencies allowed for parallel display and payment besides the base currency. Converted via the shared exchange rates (auto update recommended); per-currency item prices take priority. Coupons and credits work only on base-currency orders.';
$lang->zpay_order_prefix = 'Préfixe du numéro de commande';
$lang->zpay_order_prefix_help = 'Permet de reconnaître vos commandes dans la console du prestataire. Lettres et chiffres, 8 caractères maximum.';

$lang->zpay_group_cancel = 'Annulation et remboursement';
$lang->zpay_allow_partial_cancel = 'Autoriser l\'annulation partielle';
$lang->zpay_allow_partial_cancel_help = 'Permet de rembourser seulement une partie du montant payé.';
$lang->zpay_cancel_reasons = 'Motifs d\'annulation';
$lang->zpay_cancel_reasons_help = 'Un par ligne. Laissez vide pour saisir le motif à chaque fois.';

$lang->zpay_group_notify = 'Notifications';
$lang->zpay_notify_admin_email = 'Courriel de l\'administration';
$lang->zpay_notify_events = 'Me notifier lorsque';
$lang->zpay_notify_on_paid = 'Un paiement aboutit';
$lang->zpay_notify_on_cancel = 'Un paiement est annulé';

$lang->zpay_group_security = 'Sécurité et journalisation';
$lang->zpay_log_retention_days = 'Conservation du journal (jours)';
$lang->zpay_log_retention_days_help = '0 conserve indéfiniment. Ne réglez pas trop bas : ce journal est votre preuve en cas de litige.';
$lang->zpay_webhook_ip_whitelist = 'IP autorisées pour les webhooks';
$lang->zpay_webhook_ip_whitelist_help = 'Une par ligne. Laissez vide pour tout autoriser. Terminez par * pour couvrir une plage. Le filtrage par IP n\'est qu\'une mesure secondaire ; la vraie défense est de réinterroger le prestataire à chaque webhook.';

$lang->zpay_group_notice = 'Mentions légales';
$lang->zpay_biz_notice = 'Mention en bas de la page de paiement';
$lang->zpay_biz_notice_help = 'Numéro d\'immatriculation et mentions similaires affichés en bas de la page de paiement.';

// Moyens de paiement
$lang->zpay_enabled_gateways = 'Moyens de paiement actifs';
$lang->zpay_enabled_gateways_help = 'Seuls les moyens cochés apparaissent sur la page de paiement.';
$lang->zpay_not_configured = 'clés manquantes';
$lang->zpay_toss_client_key = 'Clé client';
$lang->zpay_toss_secret_key = 'Clé secrète';
$lang->zpay_toss_key_help = 'Délivrée dans la console marchand de Toss Payments. Les clés de test commencent par test_ et les clés de production par live_.';
$lang->zpay_webhook_url = 'Adresse du webhook';
$lang->zpay_webhook_url_help = 'Enregistrez cette adresse dans la console de votre prestataire afin de recevoir les notifications asynchrones, comme les virements sur compte virtuel.';
$lang->zpay_bank_accounts = 'Comptes bancaires';
$lang->zpay_bank_accounts_help = 'Seules les lignes comportant une banque et un numéro de compte sont enregistrées. Videz une ligne pour la supprimer.';
$lang->zpay_bank_name = 'Banque';
$lang->zpay_bank_account = 'Numéro de compte';
$lang->zpay_bank_holder = 'Titulaire';
$lang->zpay_bank_due_days = 'Délai de paiement (jours)';
$lang->zpay_bank_due_days_help = 'Passé ce délai, la commande expire.';

// Paiement
$lang->zpay_checkout_title = 'Paiement';
$lang->zpay_order_summary = 'Récapitulatif';
$lang->zpay_order_code = 'Numéro de paiement';
$lang->zpay_product = 'Article';
$lang->zpay_payer = 'Payeur';
$lang->zpay_payer_phone = 'Téléphone';
$lang->zpay_payer_email = 'Courriel';
$lang->zpay_amount = 'Montant';
$lang->zpay_select_method = 'Choisissez un moyen de paiement';
$lang->zpay_depositor_name = 'Nom du donneur d\'ordre';
$lang->zpay_bank_due_notice = 'Merci d\'effectuer le virement sous %d jours. Passé ce délai, la commande est annulée automatiquement.';
$lang->zpay_pay_button = 'Payer %s';

// Résultat
$lang->zpay_result_paid = 'Paiement effectué';
$lang->zpay_result_pending = 'En attente de votre virement';
$lang->zpay_result_cancelled = 'Paiement annulé';
$lang->zpay_result_expired = 'Le délai de paiement est dépassé';
$lang->zpay_result_failed = 'Le paiement a échoué';
$lang->zpay_bank_guide_title = 'Coordonnées bancaires';
$lang->zpay_due_date = 'À payer avant le';
$lang->zpay_receipt = 'Voir le reçu';
$lang->zpay_back_to_shop = 'Retour';
$lang->zpay_cancelled_amount = 'Montant annulé';

// Liste
$lang->zpay_order_detail = 'Détail du paiement';
$lang->zpay_source = 'Objet du paiement';
$lang->zpay_gateway = 'Moyen';
$lang->zpay_pg_tid = 'Identifiant de transaction';
$lang->zpay_status = 'Statut';
$lang->zpay_regdate = 'Créé le';
$lang->zpay_paid_date = 'Payé le';
$lang->zpay_cancelled_date = 'Annulé le';
$lang->zpay_ipaddress = 'Adresse IP';
$lang->zpay_remain_amount = 'restant';
$lang->zpay_total_orders = '%s paiements';
$lang->zpay_no_orders = 'Aucun paiement pour le moment.';
$lang->zpay_filter_all_status = 'Tous les statuts';
$lang->zpay_confirm_deposit = 'Confirmer la réception';
$lang->zpay_confirm_deposit_ask = 'Avez-vous vérifié la réception du paiement ? Une fois confirmé, le paiement sera marqué comme terminé.';
$lang->zpay_confirm_deposit_help = 'Cliquez une fois que vous avez constaté l\'arrivée des fonds. Le paiement est aussitôt considéré comme abouti et le module demandeur est notifié.';
$lang->zpay_cancel_payment = 'Annuler le paiement';
$lang->zpay_cancel_amount = 'Montant à annuler';
$lang->zpay_cancel_amount_help = 'Vous ne pouvez pas annuler plus que le montant restant.';
$lang->zpay_cancel_reason = 'Motif';

// Statuts
$lang->zpay_status_ready = 'En attente de paiement';
$lang->zpay_status_pending = 'En attente de réception';
$lang->zpay_status_paid = 'Payé';
$lang->zpay_status_cancelled = 'Annulé';
$lang->zpay_status_partial_cancelled = 'Partiellement annulé';
$lang->zpay_status_failed = 'Échoué';
$lang->zpay_status_expired = 'Expiré';

// Journal
$lang->zpay_communication_log = 'Journal des échanges';
$lang->zpay_log_action = 'Action';
$lang->zpay_log_result = 'Résultat';
$lang->zpay_log_response = 'Réponse';
$lang->zpay_no_logs = 'Aucun enregistrement.';
$lang->zpay_total_logs = '%s enregistrements';
$lang->zpay_filter_all_action = 'Toutes les actions';
$lang->zpay_filter_all_result = 'Tous les résultats';
$lang->zpay_result_success = 'Succès';
$lang->zpay_result_fail = 'Échec';
$lang->zpay_purge_logs = 'Supprimer les enregistrements de plus de %d jours';

// Messages
$lang->msg_pay_disabled = 'Les paiements sont désactivés.';
$lang->msg_invalid_source = 'L\'objet du paiement est invalide.';
$lang->msg_invalid_amount = 'Le montant du paiement est invalide.';
$lang->msg_order_not_found = 'Commande de paiement introuvable.';
$lang->msg_no_gateway_available = 'Aucun moyen de paiement disponible. Merci de contacter l\'administration du site.';
$lang->msg_gateway_not_found = 'Moyen de paiement introuvable.';
$lang->msg_invalid_ticket = 'Cette session de paiement a expiré. Merci de recommencer.';
$lang->msg_already_settled = 'Ce paiement a déjà été traité.';
$lang->msg_too_many_requests = 'Trop de tentatives de paiement. Merci de réessayer dans un instant.';

$lang->msg_approve_success = 'Le paiement a été validé.';
$lang->msg_approve_failed = 'Le paiement n\'a pas pu être validé.';
$lang->msg_payment_cancelled = 'Le paiement a été annulé.';
$lang->msg_payment_not_completed = 'Ce paiement n\'a pas abouti.';
$lang->msg_amount_mismatch = 'Le paiement a été interrompu car le montant ne correspond pas à la commande.';
$lang->msg_missing_payment_key = 'L\'identifiant de transaction est absent.';
$lang->msg_unknown_pg_status = 'Statut de paiement inconnu.';
$lang->msg_pg_error = 'Le prestataire de paiement a renvoyé une erreur.';
$lang->msg_pg_unreachable = 'Impossible de joindre le prestataire de paiement.';
$lang->msg_query_not_supported = 'Ce moyen de paiement ne permet pas la consultation.';

$lang->msg_cancel_success = 'Le paiement a été annulé.';
$lang->msg_cancel_failed = 'Le paiement n\'a pas pu être annulé.';
$lang->msg_not_cancellable = 'Ce paiement ne peut pas être annulé dans son état actuel.';
$lang->msg_invalid_cancel_amount = 'Le montant d\'annulation est invalide.';
$lang->msg_partial_cancel_disabled = 'L\'annulation partielle n\'est pas autorisée.';
$lang->msg_cancel_record_failed = 'Le prestataire a bien annulé le paiement, mais son enregistrement ici a échoué. Merci de contacter l\'administration du site.';
$lang->cancel_default_reason = 'Demande du client';

$lang->msg_no_bank_account = 'Aucun compte bancaire n\'est enregistré.';
$lang->msg_bank_registered = 'Les coordonnées bancaires sont affichées ci-dessous. Merci de virer le montant dans les délais.';
$lang->msg_bank_manual_refund = 'Les remboursements par virement doivent être effectués manuellement par l\'administration.';
$lang->msg_not_pending = 'Cette commande n\'est pas en attente de réception.';
$lang->msg_deposit_confirmed = 'La réception a été confirmée et le paiement est désormais abouti.';
$lang->msg_log_retention_disabled = 'La conservation est réglée sur 0 : rien n\'est supprimé.';

// Confirmation d'achat et remboursement manuel
$lang->zpay_status_confirmed = 'Confirmé';
$lang->zpay_confirm_date = 'Date de confirmation';
$lang->zpay_auto_cancel_days = 'Délai d\'annulation chez le prestataire (jours)';
$lang->zpay_auto_cancel_days_help = 'Passé ce délai, aucune annulation n\'est tentée chez le prestataire et un remboursement manuel est mis en attente, car l\'annulation par carte est bloquée une fois le paiement reversé. 0 signifie sans limite.';
$lang->zpay_allow_force_cancel = 'Autoriser l\'annulation forcée';
$lang->zpay_allow_force_cancel_help = 'Permet à l\'administration d\'annuler un paiement déjà confirmé. Désactivez pour rendre la confirmation définitive.';
$lang->zpay_force_cancel = 'Annulation forcée';
$lang->zpay_force_cancel_confirm = 'Je comprends que ce paiement est confirmé et souhaite l\'annuler quand même';
$lang->zpay_force_cancel_help = 'Ce paiement est déjà confirmé. Pour l\'annuler, vous devez choisir explicitement l\'annulation forcée ci-dessous.';
$lang->zpay_no_auto_cancel_help = 'Ce moyen de paiement ne peut pas être annulé automatiquement. L\'annulation ne met à jour que les enregistrements : l\'administration doit envoyer l\'argent manuellement.';
$lang->zpay_manual_refund_title = 'Remboursement manuel';
$lang->zpay_manual_refund_help = 'L\'annulation est enregistrée mais l\'argent n\'a pas encore été envoyé. Virez le montant ci-dessous puis marquez-le comme envoyé.';
$lang->zpay_manual_refund_done = 'Marquer comme envoyé';
$lang->zpay_manual_refund_sent = 'Remboursement envoyé';
$lang->zpay_pending_refund_notice = '%s remboursement(s) n\'ont pas été envoyés. Merci de les vérifier.';

$lang->msg_confirm_success = 'L\'achat a été confirmé.';
$lang->msg_already_confirmed = 'Ce paiement était déjà confirmé.';
$lang->msg_not_confirmable = 'Ce paiement ne peut pas être confirmé dans son état actuel.';
$lang->msg_confirmed_not_cancellable = 'Un paiement confirmé ne peut pas être annulé. Une annulation forcée par l\'administration est nécessaire.';
$lang->msg_force_cancel_disabled = 'L\'annulation forcée des paiements confirmés n\'est pas autorisée.';
$lang->msg_cancel_manual_refund_queued = 'Annulé. Ce moyen de paiement ne permet pas le remboursement automatique : l\'administration doit envoyer l\'argent manuellement.';
$lang->msg_no_pending_refund = 'Aucun remboursement en attente d\'envoi.';
$lang->msg_refund_completed = 'Enregistré comme envoyé.';
$lang->zpay_paypal_allow_krw = 'Paiement converti pour les commandes en KRW';
$lang->zpay_paypal_allow_krw_label = 'Autoriser PayPal pour les commandes en KRW';
$lang->zpay_paypal_allow_krw_help = 'PayPal ne règle pas en wons. Avec cette option activée, une commande en KRW est convertie dans la devise de paiement choisie ci-dessus. L’acheteur voit un paiement en devise étrangère et le montant réellement remboursé peut différer du total de la commande car le taux évolue. Si vous vendez à l’étranger, fixer le prix dans cette devise est plus sûr. Un taux de change doit être défini.';
