<?php

$lang->zittme_pay = 'Zittme Pay';

// Admin-Reiter
$lang->zpay_tab_config = 'Allgemein';
$lang->zpay_tab_gateway = 'Zahlungsarten';
$lang->zpay_tab_orders = 'Zahlungen';
$lang->zpay_tab_logs = 'Kommunikationsprotokoll';

$lang->about_zpay_config = 'Allgemeines Verhalten der Zahlungs-Engine. Module, die Zahlungen benötigen – etwa Shop oder Reservierung – teilen sich diese Einstellungen.';
$lang->about_zpay_gateway = 'Aktivieren Sie die gewünschten Zahlungsarten und tragen Sie deren Schlüssel ein. Eine Zahlungsart ohne Schlüssel erscheint nicht auf der Kassenseite.';
$lang->about_zpay_logs = 'Sämtliche Anfragen und Antworten rund um eine Zahlung. Das ist Ihr Nachweis bei Streitfällen – bewahren Sie es ausreichend lange auf.';

// Namen der Zahlungsarten
$lang->gateway_toss = 'Toss Payments';
$lang->gateway_banktransfer = 'Banküberweisung';
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

// Allgemein
$lang->zpay_enabled = 'Zahlungen aktivieren';
$lang->zpay_enabled_help = 'Ausschalten, um keine neuen Zahlungen mehr anzunehmen.';
$lang->zpay_test_mode = 'Testmodus';
$lang->zpay_test_mode_help = 'Zeigt an, dass die Testschlüssel des Anbieters verwendet werden. Es fließt kein echtes Geld.';
$lang->zpay_currency = 'Währung';
$lang->zpay_currency_help = 'The base currency for the whole site. Product prices, payments, credits, and statistics all use this currency. Example: KRW, USD, MXN. Only gateways that support it appear at checkout.';
$lang->zpay_extra_currencies = 'Additional currencies';
$lang->zpay_extra_currencies_help = 'Currencies allowed for parallel display and payment besides the base currency. Converted via the shared exchange rates (auto update recommended); per-currency item prices take priority. Coupons and credits work only on base-currency orders.';
$lang->zpay_order_prefix = 'Präfix der Bestellnummer';
$lang->zpay_order_prefix_help = 'Hilft, Ihre Bestellungen im Anbieter-Backend wiederzuerkennen. Buchstaben und Ziffern, höchstens 8 Zeichen.';

$lang->zpay_group_cancel = 'Stornierung und Rückerstattung';
$lang->zpay_allow_partial_cancel = 'Teilstornierung erlauben';
$lang->zpay_allow_partial_cancel_help = 'Erlaubt es, nur einen Teil des gezahlten Betrags zu erstatten.';
$lang->zpay_cancel_reasons = 'Stornierungsgründe';
$lang->zpay_cancel_reasons_help = 'Einer pro Zeile. Leer lassen, um den Grund jedes Mal einzugeben.';

$lang->zpay_group_notify = 'Benachrichtigungen';
$lang->zpay_notify_admin_email = 'E-Mail der Administration';
$lang->zpay_notify_events = 'Benachrichtigen bei';
$lang->zpay_notify_on_paid = 'Abgeschlossener Zahlung';
$lang->zpay_notify_on_cancel = 'Stornierter Zahlung';

$lang->zpay_group_security = 'Sicherheit und Protokoll';
$lang->zpay_log_retention_days = 'Aufbewahrung des Protokolls (Tage)';
$lang->zpay_log_retention_days_help = '0 bewahrt das Protokoll dauerhaft auf. Setzen Sie den Wert nicht zu niedrig – es ist Ihr Nachweis im Streitfall.';
$lang->zpay_webhook_ip_whitelist = 'Erlaubte Webhook-IPs';
$lang->zpay_webhook_ip_whitelist_help = 'Eine pro Zeile. Leer lassen, um alle IPs zuzulassen. Ein * am Ende erlaubt einen Bereich. Die IP-Prüfung ist nur eine Zusatzmaßnahme; die eigentliche Absicherung ist die erneute Abfrage beim Anbieter für jeden Webhook.';

$lang->zpay_group_notice = 'Rechtlicher Hinweis';
$lang->zpay_biz_notice = 'Hinweis im Fußbereich der Kassenseite';
$lang->zpay_biz_notice_help = 'Angaben zur Gewerbeanmeldung und Ähnliches, das unten auf der Kassenseite erscheint.';

// Zahlungsarten
$lang->zpay_enabled_gateways = 'Aktive Zahlungsarten';
$lang->zpay_enabled_gateways_help = 'Nur angehakte Zahlungsarten erscheinen auf der Kassenseite.';
$lang->zpay_not_configured = 'Schlüssel fehlen';
$lang->zpay_toss_client_key = 'Client-Schlüssel';
$lang->zpay_toss_secret_key = 'Geheimer Schlüssel';
$lang->zpay_toss_key_help = 'Wird im Händler-Backend von Toss Payments ausgestellt. Testschlüssel beginnen mit test_, Live-Schlüssel mit live_.';
$lang->zpay_webhook_url = 'Webhook-Adresse';
$lang->zpay_webhook_url_help = 'Tragen Sie diese Adresse im Webhook-Bereich Ihres Anbieters ein, damit asynchrone Meldungen wie Eingänge auf virtuellen Konten ankommen.';
$lang->zpay_bank_accounts = 'Bankkonten';
$lang->zpay_bank_accounts_help = 'Nur Zeilen mit Bank und Kontonummer werden gespeichert. Eine leere Zeile wird entfernt.';
$lang->zpay_bank_name = 'Bank';
$lang->zpay_bank_account = 'Kontonummer';
$lang->zpay_bank_holder = 'Kontoinhaber';
$lang->zpay_bank_due_days = 'Zahlungsfrist (Tage)';
$lang->zpay_bank_due_days_help = 'Nach Ablauf dieser Frist verfällt die Bestellung.';

// Kasse
$lang->zpay_checkout_title = 'Zur Kasse';
$lang->zpay_order_summary = 'Bestellübersicht';
$lang->zpay_order_code = 'Zahlungsnummer';
$lang->zpay_product = 'Artikel';
$lang->zpay_payer = 'Zahlende Person';
$lang->zpay_payer_phone = 'Telefon';
$lang->zpay_payer_email = 'E-Mail';
$lang->zpay_amount = 'Betrag';
$lang->zpay_select_method = 'Zahlungsart wählen';
$lang->zpay_depositor_name = 'Name der einzahlenden Person';
$lang->zpay_bank_due_notice = 'Bitte überweisen Sie den Betrag innerhalb von %d Tagen. Danach wird die Bestellung automatisch storniert.';
$lang->zpay_pay_button = '%s bezahlen';

// Ergebnis
$lang->zpay_result_paid = 'Zahlung abgeschlossen';
$lang->zpay_result_pending = 'Warten auf Ihre Überweisung';
$lang->zpay_result_cancelled = 'Zahlung storniert';
$lang->zpay_result_expired = 'Die Zahlungsfrist ist abgelaufen';
$lang->zpay_result_failed = 'Zahlung fehlgeschlagen';
$lang->zpay_bank_guide_title = 'Überweisungsdaten';
$lang->zpay_due_date = 'Zahlbar bis';
$lang->zpay_receipt = 'Beleg ansehen';
$lang->zpay_back_to_shop = 'Zurück';
$lang->zpay_cancelled_amount = 'Stornierter Betrag';

// Liste
$lang->zpay_order_detail = 'Zahlungsdetails';
$lang->zpay_source = 'Bezahlt für';
$lang->zpay_gateway = 'Zahlungsart';
$lang->zpay_pg_tid = 'Transaktionsnummer des Anbieters';
$lang->zpay_status = 'Status';
$lang->zpay_regdate = 'Erstellt';
$lang->zpay_paid_date = 'Bezahlt';
$lang->zpay_cancelled_date = 'Storniert';
$lang->zpay_ipaddress = 'IP-Adresse';
$lang->zpay_remain_amount = 'verbleibend';
$lang->zpay_total_orders = '%s Zahlungen';
$lang->zpay_no_orders = 'Noch keine Zahlungen.';
$lang->zpay_filter_all_status = 'Alle Status';
$lang->zpay_confirm_deposit = 'Zahlungseingang bestätigen';
$lang->zpay_confirm_deposit_ask = 'Haben Sie den Zahlungseingang geprüft? Nach der Bestätigung gilt die Zahlung als abgeschlossen.';
$lang->zpay_confirm_deposit_help = 'Klicken Sie hier, sobald Sie den Geldeingang gesehen haben. Die Zahlung gilt sofort als abgeschlossen und das anfragende Modul wird benachrichtigt.';
$lang->zpay_cancel_payment = 'Zahlung stornieren';
$lang->zpay_cancel_amount = 'Zu stornierender Betrag';
$lang->zpay_cancel_amount_help = 'Es kann nicht mehr als der verbleibende Betrag storniert werden.';
$lang->zpay_cancel_reason = 'Grund';

// Status
$lang->zpay_status_ready = 'Warten auf Zahlung';
$lang->zpay_status_pending = 'Warten auf Eingang';
$lang->zpay_status_paid = 'Bezahlt';
$lang->zpay_status_cancelled = 'Storniert';
$lang->zpay_status_partial_cancelled = 'Teilweise storniert';
$lang->zpay_status_failed = 'Fehlgeschlagen';
$lang->zpay_status_expired = 'Abgelaufen';

// Protokoll
$lang->zpay_communication_log = 'Kommunikationsprotokoll';
$lang->zpay_log_action = 'Aktion';
$lang->zpay_log_result = 'Ergebnis';
$lang->zpay_log_response = 'Antwort';
$lang->zpay_no_logs = 'Keine Einträge.';
$lang->zpay_total_logs = '%s Einträge';
$lang->zpay_filter_all_action = 'Alle Aktionen';
$lang->zpay_filter_all_result = 'Alle Ergebnisse';
$lang->zpay_result_success = 'Erfolg';
$lang->zpay_result_fail = 'Fehler';
$lang->zpay_purge_logs = 'Einträge älter als %d Tage löschen';

// Meldungen
$lang->msg_pay_disabled = 'Zahlungen sind deaktiviert.';
$lang->msg_invalid_source = 'Das Zahlungsziel ist ungültig.';
$lang->msg_invalid_amount = 'Der Zahlungsbetrag ist ungültig.';
$lang->msg_order_not_found = 'Zahlungsauftrag nicht gefunden.';
$lang->msg_no_gateway_available = 'Es ist keine Zahlungsart verfügbar. Bitte wenden Sie sich an die Administration.';
$lang->msg_gateway_not_found = 'Zahlungsart nicht gefunden.';
$lang->msg_invalid_ticket = 'Diese Zahlungssitzung ist abgelaufen. Bitte beginnen Sie von vorn.';
$lang->msg_already_settled = 'Diese Zahlung wurde bereits abgeschlossen.';
$lang->msg_too_many_requests = 'Zu viele Zahlungsversuche. Bitte versuchen Sie es in Kürze erneut.';

$lang->msg_approve_success = 'Die Zahlung wurde freigegeben.';
$lang->msg_approve_failed = 'Die Zahlung konnte nicht freigegeben werden.';
$lang->msg_payment_cancelled = 'Die Zahlung wurde abgebrochen.';
$lang->msg_payment_not_completed = 'Diese Zahlung wurde nicht abgeschlossen.';
$lang->msg_amount_mismatch = 'Die Zahlung wurde gestoppt, weil der Betrag nicht zur Bestellung passt.';
$lang->msg_missing_payment_key = 'Die Transaktionsnummer des Anbieters fehlt.';
$lang->msg_unknown_pg_status = 'Unbekannter Zahlungsstatus.';
$lang->msg_pg_error = 'Der Zahlungsanbieter hat einen Fehler zurückgegeben.';
$lang->msg_pg_unreachable = 'Der Zahlungsanbieter ist nicht erreichbar.';
$lang->msg_query_not_supported = 'Diese Zahlungsart unterstützt keine Abfrage.';

$lang->msg_cancel_success = 'Die Zahlung wurde storniert.';
$lang->msg_cancel_failed = 'Die Zahlung konnte nicht storniert werden.';
$lang->msg_not_cancellable = 'In diesem Zustand ist keine Stornierung möglich.';
$lang->msg_invalid_cancel_amount = 'Der Stornobetrag ist ungültig.';
$lang->msg_partial_cancel_disabled = 'Teilstornierung ist nicht erlaubt.';
$lang->msg_cancel_record_failed = 'Der Anbieter hat storniert, die Erfassung hier ist jedoch fehlgeschlagen. Bitte wenden Sie sich an die Administration.';
$lang->cancel_default_reason = 'Kundenwunsch';

$lang->msg_no_bank_account = 'Es ist kein Bankkonto hinterlegt.';
$lang->msg_bank_registered = 'Die Kontodaten werden unten angezeigt. Bitte überweisen Sie fristgerecht.';
$lang->msg_bank_manual_refund = 'Rückerstattungen bei Banküberweisung muss die Administration manuell anweisen.';
$lang->msg_not_pending = 'Diese Bestellung wartet nicht auf einen Zahlungseingang.';
$lang->msg_deposit_confirmed = 'Der Zahlungseingang wurde bestätigt, die Zahlung ist abgeschlossen.';
$lang->msg_log_retention_disabled = 'Die Aufbewahrung steht auf 0, daher wird nichts gelöscht.';

// Kaufbestätigung und manuelle Rückerstattung
$lang->zpay_status_confirmed = 'Bestätigt';
$lang->zpay_confirm_date = 'Bestätigt am';
$lang->zpay_auto_cancel_days = 'Stornofrist beim Anbieter (Tage)';
$lang->zpay_auto_cancel_days_help = 'Nach Ablauf dieser Frist wird keine Stornierung beim Anbieter mehr versucht, sondern eine manuelle Rückerstattung eingereiht — nach der Abrechnung ist die Kartenstornierung gesperrt. 0 bedeutet keine Begrenzung.';
$lang->zpay_allow_force_cancel = 'Erzwungene Stornierung erlauben';
$lang->zpay_allow_force_cancel_help = 'Erlaubt der Administration, eine bereits bestätigte Zahlung zu stornieren. Ausschalten macht die Bestätigung endgültig.';
$lang->zpay_force_cancel = 'Erzwungene Stornierung';
$lang->zpay_force_cancel_confirm = 'Mir ist bewusst, dass diese Zahlung bestätigt ist, und ich storniere sie dennoch';
$lang->zpay_force_cancel_help = 'Diese Zahlung ist bereits bestätigt. Zum Stornieren müssen Sie unten ausdrücklich die erzwungene Stornierung wählen.';
$lang->zpay_no_auto_cancel_help = 'Diese Zahlungsart lässt sich nicht automatisch stornieren. Die Stornierung aktualisiert nur die Aufzeichnungen — das Geld muss die Administration manuell überweisen.';
$lang->zpay_manual_refund_title = 'Manuelle Rückerstattung';
$lang->zpay_manual_refund_help = 'Die Stornierung ist erfasst, das Geld wurde aber noch nicht überwiesen. Überweisen Sie den unten genannten Betrag und markieren Sie ihn als gesendet.';
$lang->zpay_manual_refund_done = 'Als gesendet markieren';
$lang->zpay_manual_refund_sent = 'Rückerstattung gesendet';
$lang->zpay_pending_refund_notice = '%s Rückerstattung(en) wurden noch nicht überwiesen. Bitte prüfen.';

$lang->msg_confirm_success = 'Der Kauf wurde bestätigt.';
$lang->msg_already_confirmed = 'Diese Zahlung war bereits bestätigt.';
$lang->msg_not_confirmable = 'Diese Zahlung kann in ihrem aktuellen Zustand nicht bestätigt werden.';
$lang->msg_confirmed_not_cancellable = 'Eine bestätigte Zahlung kann nicht storniert werden. Dafür ist eine erzwungene Stornierung durch die Administration nötig.';
$lang->msg_force_cancel_disabled = 'Die erzwungene Stornierung bestätigter Zahlungen ist nicht erlaubt.';
$lang->msg_cancel_manual_refund_queued = 'Storniert. Diese Zahlungsart kann nicht automatisch erstattet werden, daher muss die Administration das Geld manuell überweisen.';
$lang->msg_no_pending_refund = 'Es steht keine Rückerstattung zur Überweisung an.';
$lang->msg_refund_completed = 'Als gesendet erfasst.';
$lang->zpay_paypal_allow_krw = 'Umgerechnete Zahlung für KRW-Bestellungen';
$lang->zpay_paypal_allow_krw_label = 'PayPal für Bestellungen in KRW zulassen';
$lang->zpay_paypal_allow_krw_help = 'PayPal rechnet nicht in Won ab. Ist diese Option aktiv, wird eine KRW-Bestellung in die oben gewählte Zahlungswährung umgerechnet. Der Käufer sieht eine Fremdwährungszahlung, und der tatsächlich erstattete Betrag kann vom Bestellwert abweichen, weil sich der Kurs ändert. Wenn Sie ins Ausland verkaufen, ist die Preisauszeichnung in dieser Währung sicherer. Ein Wechselkurs muss hinterlegt sein.';
