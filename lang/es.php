<?php

$lang->zittme_pay = 'Zittme Pay';

// Pestañas de administración
$lang->zpay_tab_config = 'General';
$lang->zpay_tab_gateway = 'Métodos de pago';
$lang->zpay_tab_orders = 'Pagos';
$lang->zpay_tab_logs = 'Registro de comunicaciones';

$lang->about_zpay_config = 'Comportamiento común del motor de pagos. Los módulos que necesitan cobrar, como comercio o reservas, comparten estos ajustes.';
$lang->about_zpay_gateway = 'Active los métodos de pago que necesite e introduzca sus claves. Un método sin claves nunca aparece en la página de pago.';
$lang->about_zpay_logs = 'Todas las peticiones y respuestas intercambiadas para un pago. Es su prueba ante una reclamación, así que consérvelas el tiempo suficiente.';

// Nombres de los métodos
$lang->gateway_toss = 'Toss Payments';
$lang->gateway_banktransfer = 'Transferencia bancaria';
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

// General
$lang->zpay_enabled = 'Activar pagos';
$lang->zpay_enabled_help = 'Desactive para dejar de aceptar nuevos pagos.';
$lang->zpay_test_mode = 'Modo de prueba';
$lang->zpay_test_mode_help = 'Indica que se usan las claves de prueba de la pasarela. No se mueve dinero real.';
$lang->zpay_currency = 'Moneda';
$lang->zpay_currency_help = 'The base currency for the whole site. Product prices, payments, credits, and statistics all use this currency. Example: KRW, USD, MXN. Only gateways that support it appear at checkout.';
$lang->zpay_extra_currencies = 'Additional currencies';
$lang->zpay_extra_currencies_help = 'Currencies allowed for parallel display and payment besides the base currency. Converted via the shared exchange rates (auto update recommended); per-currency item prices take priority. Coupons and credits work only on base-currency orders.';
$lang->zpay_order_prefix = 'Prefijo del número de pedido';
$lang->zpay_order_prefix_help = 'Ayuda a reconocer sus pedidos en el panel de la pasarela. Letras y números, máximo 8 caracteres.';

$lang->zpay_group_cancel = 'Cancelación y reembolso';
$lang->zpay_allow_partial_cancel = 'Permitir cancelación parcial';
$lang->zpay_allow_partial_cancel_help = 'Permite reembolsar solo una parte del importe pagado.';
$lang->zpay_cancel_reasons = 'Motivos de cancelación';
$lang->zpay_cancel_reasons_help = 'Uno por línea. Déjelo vacío para escribir el motivo cada vez.';

$lang->zpay_group_notify = 'Notificaciones';
$lang->zpay_notify_admin_email = 'Correo de administración';
$lang->zpay_notify_events = 'Avisarme cuando';
$lang->zpay_notify_on_paid = 'Se complete un pago';
$lang->zpay_notify_on_cancel = 'Se cancele un pago';

$lang->zpay_group_security = 'Seguridad y registro';
$lang->zpay_log_retention_days = 'Conservación del registro (días)';
$lang->zpay_log_retention_days_help = '0 conserva para siempre. No lo ponga demasiado bajo: este registro es su prueba ante una reclamación.';
$lang->zpay_webhook_ip_whitelist = 'IP permitidas para webhooks';
$lang->zpay_webhook_ip_whitelist_help = 'Una por línea. Déjelo vacío para permitir cualquier IP. Termine con * para cubrir un rango. El filtrado por IP es solo una medida secundaria; la defensa real es volver a consultar a la pasarela en cada webhook.';

$lang->zpay_group_notice = 'Aviso legal';
$lang->zpay_biz_notice = 'Aviso al pie de la página de pago';
$lang->zpay_biz_notice_help = 'Datos de registro mercantil y textos similares que se muestran al pie de la página de pago.';

// Métodos de pago
$lang->zpay_enabled_gateways = 'Métodos activos';
$lang->zpay_enabled_gateways_help = 'Solo los métodos marcados aparecen en la página de pago.';
$lang->zpay_not_configured = 'faltan claves';
$lang->zpay_toss_client_key = 'Clave de cliente';
$lang->zpay_toss_secret_key = 'Clave secreta';
$lang->zpay_toss_key_help = 'Se obtiene en el panel de comercio de Toss Payments. Las claves de prueba empiezan por test_ y las reales por live_.';
$lang->zpay_webhook_url = 'Dirección del webhook';
$lang->zpay_webhook_url_help = 'Registre esta dirección en el panel de su pasarela para recibir avisos asíncronos, como los ingresos en cuentas virtuales.';
$lang->zpay_bank_accounts = 'Cuentas bancarias';
$lang->zpay_bank_accounts_help = 'Solo se guardan las filas con banco y número de cuenta. Vacíe una fila para eliminarla.';
$lang->zpay_bank_name = 'Banco';
$lang->zpay_bank_account = 'Número de cuenta';
$lang->zpay_bank_holder = 'Titular';
$lang->zpay_bank_due_days = 'Plazo de pago (días)';
$lang->zpay_bank_due_days_help = 'Pasado ese plazo, el pedido caduca.';

// Pago
$lang->zpay_checkout_title = 'Pagar';
$lang->zpay_order_summary = 'Resumen del pedido';
$lang->zpay_order_code = 'Número de pago';
$lang->zpay_product = 'Artículo';
$lang->zpay_payer = 'Pagador';
$lang->zpay_payer_phone = 'Teléfono';
$lang->zpay_payer_email = 'Correo';
$lang->zpay_amount = 'Importe';
$lang->zpay_select_method = 'Elija un método de pago';
$lang->zpay_depositor_name = 'Nombre del ordenante';
$lang->zpay_bank_due_notice = 'Realice la transferencia en un plazo de %d días. Pasado ese plazo el pedido se cancela automáticamente.';
$lang->zpay_pay_button = 'Pagar %s';

// Resultado
$lang->zpay_result_paid = 'Pago completado';
$lang->zpay_result_pending = 'Esperando su transferencia';
$lang->zpay_result_cancelled = 'Pago cancelado';
$lang->zpay_result_expired = 'El plazo de pago ha vencido';
$lang->zpay_result_failed = 'El pago ha fallado';
$lang->zpay_bank_guide_title = 'Datos para la transferencia';
$lang->zpay_due_date = 'Pagar antes del';
$lang->zpay_receipt = 'Ver recibo';
$lang->zpay_back_to_shop = 'Volver';
$lang->zpay_cancelled_amount = 'Importe cancelado';

// Listado
$lang->zpay_order_detail = 'Detalle del pago';
$lang->zpay_source = 'Concepto';
$lang->zpay_gateway = 'Método';
$lang->zpay_pg_tid = 'Identificador de transacción';
$lang->zpay_status = 'Estado';
$lang->zpay_regdate = 'Creado';
$lang->zpay_paid_date = 'Pagado';
$lang->zpay_cancelled_date = 'Cancelado';
$lang->zpay_ipaddress = 'Dirección IP';
$lang->zpay_remain_amount = 'restante';
$lang->zpay_total_orders = '%s pagos';
$lang->zpay_no_orders = 'Todavía no hay pagos.';
$lang->zpay_filter_all_status = 'Todos los estados';
$lang->zpay_confirm_deposit = 'Confirmar ingreso';
$lang->zpay_confirm_deposit_ask = '¿Ha verificado el ingreso? Al confirmarlo, el pago pasará a estar completado.';
$lang->zpay_confirm_deposit_help = 'Pulse cuando haya comprobado que el dinero ha llegado. El pago se completa de inmediato y se avisa al módulo que lo solicitó.';
$lang->zpay_cancel_payment = 'Cancelar pago';
$lang->zpay_cancel_amount = 'Importe a cancelar';
$lang->zpay_cancel_amount_help = 'No puede cancelar más del importe restante.';
$lang->zpay_cancel_reason = 'Motivo';

// Estados
$lang->zpay_status_ready = 'Pendiente de pago';
$lang->zpay_status_pending = 'Pendiente de ingreso';
$lang->zpay_status_paid = 'Pagado';
$lang->zpay_status_cancelled = 'Cancelado';
$lang->zpay_status_partial_cancelled = 'Parcialmente cancelado';
$lang->zpay_status_failed = 'Fallido';
$lang->zpay_status_expired = 'Caducado';

// Registro
$lang->zpay_communication_log = 'Registro de comunicaciones';
$lang->zpay_log_action = 'Acción';
$lang->zpay_log_result = 'Resultado';
$lang->zpay_log_response = 'Respuesta';
$lang->zpay_log_depositor = 'Depositante';
$lang->zpay_log_due = 'Vence';
$lang->zpay_no_logs = 'Sin registros.';
$lang->zpay_total_logs = '%s registros';
$lang->zpay_filter_all_action = 'Todas las acciones';
$lang->zpay_filter_all_result = 'Todos los resultados';
$lang->zpay_result_success = 'Correcto';
$lang->zpay_result_fail = 'Fallo';
$lang->zpay_purge_logs = 'Borrar registros de más de %d días';

// Mensajes
$lang->msg_pay_disabled = 'Los pagos están desactivados.';
$lang->msg_invalid_source = 'El concepto del pago no es válido.';
$lang->msg_invalid_amount = 'El importe del pago no es válido.';
$lang->msg_order_not_found = 'No se ha encontrado el pedido de pago.';
$lang->msg_no_gateway_available = 'No hay ningún método de pago disponible. Póngase en contacto con la administración del sitio.';
$lang->msg_gateway_not_found = 'No se ha encontrado el método de pago.';
$lang->msg_invalid_ticket = 'Esta sesión de pago ha caducado. Vuelva a empezar.';
$lang->msg_already_settled = 'Este pago ya se ha procesado.';
$lang->msg_too_many_requests = 'Demasiados intentos de pago. Inténtelo de nuevo en unos instantes.';

$lang->msg_approve_success = 'El pago ha sido aprobado.';
$lang->msg_approve_failed = 'No se ha podido aprobar el pago.';
$lang->msg_payment_cancelled = 'El pago ha sido cancelado.';
$lang->msg_payment_not_completed = 'Este pago no se completó.';
$lang->msg_amount_mismatch = 'Se ha detenido el pago porque el importe no coincide con el del pedido.';
$lang->msg_missing_payment_key = 'Falta el identificador de transacción.';
$lang->msg_unknown_pg_status = 'Estado de pago desconocido.';
$lang->msg_pg_error = 'La pasarela de pago ha devuelto un error.';
$lang->msg_pg_unreachable = 'No se ha podido contactar con la pasarela de pago.';
$lang->msg_paypal_auth_failed = 'Fallo de autenticacion con PayPal. Estas conectando a %s. Comprueba que las claves correspondan a ese modo.';
$lang->paypal_mode_sandbox = 'Sandbox (prueba)';
$lang->paypal_mode_live = 'Produccion';
$lang->zpay_paypal_mode = 'Conectando a';
$lang->zpay_paypal_mode_help = 'Con el modo de prueba activado se conecta al sandbox; desactivado, a produccion. Las claves deben coincidir.';
$lang->zpay_paypal_test = 'Probar conexion';
$lang->zpay_paypal_testing = 'Comprobando...';
$lang->msg_paypal_test_ok = 'Conexion con PayPal correcta.';
$lang->msg_paypal_test_empty = 'Introduce primero el client ID y el secreto.';
$lang->msg_query_not_supported = 'Este método de pago no admite consultas.';

$lang->msg_cancel_success = 'El pago ha sido cancelado.';
$lang->msg_cancel_failed = 'No se ha podido cancelar el pago.';
$lang->msg_not_cancellable = 'Este pago no se puede cancelar en su estado actual.';
$lang->msg_invalid_cancel_amount = 'El importe de cancelación no es válido.';
$lang->msg_partial_cancel_disabled = 'No se permite la cancelación parcial.';
$lang->msg_cancel_record_failed = 'La pasarela canceló el pago, pero no se pudo registrar aquí. Póngase en contacto con la administración del sitio.';
$lang->cancel_default_reason = 'Petición del cliente';

$lang->msg_no_bank_account = 'No hay ninguna cuenta bancaria registrada.';
$lang->msg_bank_registered = 'Abajo se muestran los datos de la cuenta. Realice la transferencia dentro del plazo.';
$lang->msg_bank_manual_refund = 'Los reembolsos por transferencia debe enviarlos manualmente la administración.';
$lang->msg_not_pending = 'Este pedido no está pendiente de ingreso.';
$lang->msg_deposit_confirmed = 'Se ha confirmado el ingreso y el pago está completado.';
$lang->msg_log_retention_disabled = 'La conservación está en 0, así que no se borra nada.';

// Confirmación de compra y reembolso manual
$lang->zpay_status_confirmed = 'Confirmado';
$lang->zpay_confirm_date = 'Fecha de confirmación';
$lang->zpay_auto_cancel_days = 'Plazo de cancelación en la pasarela (días)';
$lang->zpay_auto_cancel_days_help = 'Pasado este plazo no se intenta cancelar en la pasarela y se pone en cola un reembolso manual, porque la cancelación con tarjeta se bloquea una vez liquidado el pago. 0 significa sin límite.';
$lang->zpay_allow_force_cancel = 'Permitir cancelación forzada';
$lang->zpay_allow_force_cancel_help = 'Permite que la administración cancele un pago ya confirmado. Desactívelo para que la confirmación sea definitiva.';
$lang->zpay_force_cancel = 'Cancelación forzada';
$lang->zpay_force_cancel_confirm = 'Entiendo que este pago está confirmado y aun así quiero cancelarlo';
$lang->zpay_force_cancel_help = 'Este pago ya está confirmado. Para cancelarlo debe elegir explícitamente la cancelación forzada más abajo.';
$lang->zpay_no_auto_cancel_help = 'Este método de pago no se puede cancelar automáticamente. La cancelación solo actualiza los registros; el dinero debe enviarlo la administración manualmente.';
$lang->zpay_manual_refund_title = 'Reembolso manual';
$lang->zpay_manual_refund_help = 'La cancelación está registrada pero el dinero aún no se ha enviado. Transfiera el importe indicado y márquelo como enviado.';
$lang->zpay_manual_refund_done = 'Marcar como enviado';
$lang->zpay_manual_refund_sent = 'Reembolso enviado';
$lang->zpay_pending_refund_notice = 'Hay %s reembolso(s) sin enviar. Revíselos, por favor.';

$lang->msg_confirm_success = 'La compra ha sido confirmada.';
$lang->msg_already_confirmed = 'Este pago ya estaba confirmado.';
$lang->msg_not_confirmable = 'Este pago no se puede confirmar en su estado actual.';
$lang->msg_confirmed_not_cancellable = 'Un pago confirmado no se puede cancelar. Requiere una cancelación forzada por parte de la administración.';
$lang->msg_force_cancel_disabled = 'No se permite la cancelación forzada de pagos confirmados.';
$lang->msg_cancel_manual_refund_queued = 'Cancelado. Este método de pago no admite reembolso automático, así que la administración debe enviar el dinero manualmente.';
$lang->msg_no_pending_refund = 'No hay ningún reembolso pendiente de envío.';
$lang->msg_refund_completed = 'Registrado como enviado.';
$lang->zpay_paypal_allow_krw = 'Pago convertido para pedidos en KRW';
$lang->zpay_paypal_allow_krw_label = 'Permitir PayPal en pedidos en KRW';
$lang->zpay_paypal_allow_krw_help = 'PayPal no liquida en wones. Con esta opción activada, un pedido en KRW se convierte a la moneda de pago elegida arriba. El comprador verá un pago en moneda extranjera y el importe realmente devuelto puede diferir del total del pedido porque el tipo de cambio varía. Si vendes al extranjero, es más seguro fijar el precio en esa moneda. Debe haber un tipo de cambio configurado.';
