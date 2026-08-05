<?php

$lang->zittme_pay = 'Zittme Pay';

// Вкладки администратора
$lang->zpay_tab_config = 'Основное';
$lang->zpay_tab_gateway = 'Способы оплаты';
$lang->zpay_tab_orders = 'Платежи';
$lang->zpay_tab_logs = 'Журнал обмена';

$lang->about_zpay_config = 'Общее поведение платёжного движка. Модули, которым нужна оплата (магазин, бронирование), используют эти настройки совместно.';
$lang->about_zpay_gateway = 'Включите нужные способы оплаты и введите ключи. Способ без ключей никогда не появится на странице оплаты.';
$lang->about_zpay_logs = 'Все запросы и ответы, которыми обменивались при оплате. Это ваше доказательство при споре, поэтому храните их достаточно долго.';

// Названия способов оплаты
$lang->gateway_toss = 'Toss Payments';
$lang->gateway_banktransfer = 'Банковский перевод';

// Основное
$lang->zpay_enabled = 'Включить оплату';
$lang->zpay_enabled_help = 'Отключите, чтобы перестать принимать новые платежи.';
$lang->zpay_test_mode = 'Тестовый режим';
$lang->zpay_test_mode_help = 'Показывает, что используются тестовые ключи провайдера. Реальные деньги не списываются.';
$lang->zpay_currency = 'Валюта';
$lang->zpay_order_prefix = 'Префикс номера заказа';
$lang->zpay_order_prefix_help = 'Помогает узнавать ваши заказы в панели провайдера. Буквы и цифры, не более 8 символов.';

$lang->zpay_group_cancel = 'Отмена и возврат';
$lang->zpay_allow_partial_cancel = 'Разрешить частичную отмену';
$lang->zpay_allow_partial_cancel_help = 'Позволяет вернуть только часть оплаченной суммы.';
$lang->zpay_cancel_reasons = 'Причины отмены';
$lang->zpay_cancel_reasons_help = 'По одной в строке. Оставьте пустым, чтобы вводить причину каждый раз.';

$lang->zpay_group_notify = 'Уведомления';
$lang->zpay_notify_admin_email = 'E-mail администратора';
$lang->zpay_notify_events = 'Уведомлять, когда';
$lang->zpay_notify_on_paid = 'Платёж завершён';
$lang->zpay_notify_on_cancel = 'Платёж отменён';

$lang->zpay_group_security = 'Безопасность и журнал';
$lang->zpay_log_retention_days = 'Срок хранения журнала (дней)';
$lang->zpay_log_retention_days_help = '0 — хранить бессрочно. Не ставьте слишком мало: журнал служит доказательством при споре.';
$lang->zpay_webhook_ip_whitelist = 'Разрешённые IP для вебхуков';
$lang->zpay_webhook_ip_whitelist_help = 'По одному в строке. Пусто — разрешены любые IP. Символ * в конце задаёт диапазон. Фильтрация по IP — лишь вспомогательная мера; настоящая защита в том, что на каждый вебхук мы заново запрашиваем провайдера.';

$lang->zpay_group_notice = 'Правовая информация';
$lang->zpay_biz_notice = 'Текст внизу страницы оплаты';
$lang->zpay_biz_notice_help = 'Регистрационные данные и подобный текст, отображаемый внизу страницы оплаты.';

// Способы оплаты
$lang->zpay_enabled_gateways = 'Активные способы оплаты';
$lang->zpay_enabled_gateways_help = 'На странице оплаты появятся только отмеченные способы.';
$lang->zpay_not_configured = 'нет ключей';
$lang->zpay_toss_client_key = 'Клиентский ключ';
$lang->zpay_toss_secret_key = 'Секретный ключ';
$lang->zpay_toss_key_help = 'Выдаётся в кабинете продавца Toss Payments. Тестовые ключи начинаются с test_, боевые — с live_.';
$lang->zpay_webhook_url = 'Адрес вебхука';
$lang->zpay_webhook_url_help = 'Укажите этот адрес в настройках вебхуков у провайдера, чтобы получать асинхронные уведомления, например о зачислении на виртуальный счёт.';
$lang->zpay_bank_accounts = 'Банковские счета';
$lang->zpay_bank_accounts_help = 'Сохраняются только строки, где заполнены и банк, и номер счёта. Очистите строку, чтобы удалить её.';
$lang->zpay_bank_name = 'Банк';
$lang->zpay_bank_account = 'Номер счёта';
$lang->zpay_bank_holder = 'Владелец счёта';
$lang->zpay_bank_due_days = 'Срок оплаты (дней)';
$lang->zpay_bank_due_days_help = 'По истечении срока заказ считается просроченным.';

// Оплата
$lang->zpay_checkout_title = 'Оплата';
$lang->zpay_order_summary = 'Состав заказа';
$lang->zpay_order_code = 'Номер заказа';
$lang->zpay_product = 'Товар';
$lang->zpay_payer = 'Плательщик';
$lang->zpay_payer_phone = 'Телефон';
$lang->zpay_payer_email = 'E-mail';
$lang->zpay_amount = 'Сумма';
$lang->zpay_select_method = 'Выберите способ оплаты';
$lang->zpay_depositor_name = 'Имя отправителя перевода';
$lang->zpay_bank_due_notice = 'Пожалуйста, переведите сумму в течение %d дней. По истечении срока заказ будет отменён автоматически.';
$lang->zpay_pay_button = 'Оплатить %s';

// Результат
$lang->zpay_result_paid = 'Оплата завершена';
$lang->zpay_result_pending = 'Ожидаем ваш перевод';
$lang->zpay_result_cancelled = 'Платёж отменён';
$lang->zpay_result_expired = 'Срок оплаты истёк';
$lang->zpay_result_failed = 'Оплата не прошла';
$lang->zpay_bank_guide_title = 'Реквизиты для перевода';
$lang->zpay_due_date = 'Оплатить до';
$lang->zpay_receipt = 'Посмотреть чек';
$lang->zpay_back_to_shop = 'Назад';
$lang->zpay_cancelled_amount = 'Отменённая сумма';

// Список
$lang->zpay_order_detail = 'Детали платежа';
$lang->zpay_source = 'За что оплата';
$lang->zpay_gateway = 'Способ';
$lang->zpay_pg_tid = 'Идентификатор транзакции';
$lang->zpay_status = 'Статус';
$lang->zpay_regdate = 'Создан';
$lang->zpay_paid_date = 'Оплачен';
$lang->zpay_cancelled_date = 'Отменён';
$lang->zpay_ipaddress = 'IP-адрес';
$lang->zpay_remain_amount = 'остаток';
$lang->zpay_total_orders = 'Всего %s';
$lang->zpay_no_orders = 'Платежей пока нет.';
$lang->zpay_filter_all_status = 'Все статусы';
$lang->zpay_confirm_deposit = 'Подтвердить поступление';
$lang->zpay_confirm_deposit_help = 'Нажмите, когда убедитесь, что деньги поступили. Платёж сразу считается завершённым, а модуль-заказчик получает уведомление.';
$lang->zpay_cancel_payment = 'Отменить платёж';
$lang->zpay_cancel_amount = 'Сумма отмены';
$lang->zpay_cancel_amount_help = 'Нельзя отменить больше, чем остаток.';
$lang->zpay_cancel_reason = 'Причина';

// Статусы
$lang->zpay_status_ready = 'Ожидает оплаты';
$lang->zpay_status_pending = 'Ожидает поступления';
$lang->zpay_status_paid = 'Оплачен';
$lang->zpay_status_cancelled = 'Отменён';
$lang->zpay_status_partial_cancelled = 'Частично отменён';
$lang->zpay_status_failed = 'Неудачно';
$lang->zpay_status_expired = 'Просрочен';

// Журнал
$lang->zpay_communication_log = 'Журнал обмена';
$lang->zpay_log_action = 'Действие';
$lang->zpay_log_result = 'Результат';
$lang->zpay_log_response = 'Ответ';
$lang->zpay_no_logs = 'Записей нет.';
$lang->zpay_total_logs = 'Всего %s записей';
$lang->zpay_filter_all_action = 'Все действия';
$lang->zpay_filter_all_result = 'Все результаты';
$lang->zpay_result_success = 'Успех';
$lang->zpay_result_fail = 'Ошибка';
$lang->zpay_purge_logs = 'Удалить записи старше %d дней';

// Сообщения
$lang->msg_pay_disabled = 'Приём платежей отключён.';
$lang->msg_invalid_source = 'Некорректный объект оплаты.';
$lang->msg_invalid_amount = 'Некорректная сумма платежа.';
$lang->msg_order_not_found = 'Платёжный заказ не найден.';
$lang->msg_no_gateway_available = 'Нет доступных способов оплаты. Обратитесь к администратору сайта.';
$lang->msg_gateway_not_found = 'Способ оплаты не найден.';
$lang->msg_invalid_ticket = 'Сессия оплаты истекла. Пожалуйста, начните заново.';
$lang->msg_already_settled = 'Этот платёж уже обработан.';
$lang->msg_too_many_requests = 'Слишком много попыток оплаты. Повторите чуть позже.';

$lang->msg_approve_success = 'Платёж подтверждён.';
$lang->msg_approve_failed = 'Не удалось подтвердить платёж.';
$lang->msg_payment_cancelled = 'Платёж отменён.';
$lang->msg_payment_not_completed = 'Этот платёж не был завершён.';
$lang->msg_amount_mismatch = 'Оплата остановлена: сумма не совпадает с суммой заказа.';
$lang->msg_missing_payment_key = 'Отсутствует идентификатор транзакции.';
$lang->msg_unknown_pg_status = 'Неизвестный статус платежа.';
$lang->msg_pg_error = 'Платёжный провайдер вернул ошибку.';
$lang->msg_pg_unreachable = 'Не удалось связаться с платёжным провайдером.';
$lang->msg_query_not_supported = 'Этот способ оплаты не поддерживает запрос состояния.';

$lang->msg_cancel_success = 'Платёж отменён.';
$lang->msg_cancel_failed = 'Не удалось отменить платёж.';
$lang->msg_not_cancellable = 'В текущем состоянии платёж отменить нельзя.';
$lang->msg_invalid_cancel_amount = 'Некорректная сумма отмены.';
$lang->msg_partial_cancel_disabled = 'Частичная отмена не разрешена.';
$lang->msg_cancel_record_failed = 'Провайдер отменил платёж, но записать это у нас не удалось. Обратитесь к администратору сайта.';
$lang->cancel_default_reason = 'По просьбе клиента';

$lang->msg_no_bank_account = 'Банковский счёт не указан.';
$lang->msg_bank_registered = 'Реквизиты показаны ниже. Пожалуйста, переведите сумму в срок.';
$lang->msg_bank_manual_refund = 'Возврат по банковскому переводу администратор выполняет вручную.';
$lang->msg_not_pending = 'Этот заказ не ожидает поступления средств.';
$lang->msg_deposit_confirmed = 'Поступление подтверждено, платёж завершён.';
$lang->msg_log_retention_disabled = 'Срок хранения журнала равен 0, поэтому ничего не удаляется.';

// Подтверждение покупки и ручной возврат
$lang->zpay_status_confirmed = 'Подтверждён';
$lang->zpay_confirm_date = 'Дата подтверждения';
$lang->zpay_auto_cancel_days = 'Срок отмены через провайдера (дней)';
$lang->zpay_auto_cancel_days_help = 'По истечении этого срока отмена через платёжного провайдера не выполняется, а ставится ручной возврат: после расчётов отмена по карте становится невозможной. 0 — без ограничения.';
$lang->zpay_allow_force_cancel = 'Разрешить принудительную отмену';
$lang->zpay_allow_force_cancel_help = 'Позволяет администратору отменить даже подтверждённый платёж. Если выключено, подтверждение окончательно.';
$lang->zpay_force_cancel = 'Принудительная отмена';
$lang->zpay_force_cancel_confirm = 'Я понимаю, что платёж подтверждён, и всё равно отменяю его';
$lang->zpay_force_cancel_help = 'Этот платёж уже подтверждён. Чтобы отменить его, необходимо явно выбрать принудительную отмену ниже.';
$lang->zpay_no_auto_cancel_help = 'Этот способ оплаты нельзя отменить автоматически. Отмена изменит только записи — деньги администратор должен перевести вручную.';
$lang->zpay_manual_refund_title = 'Ручной возврат';
$lang->zpay_manual_refund_help = 'Отмена записана, но деньги ещё не отправлены. Переведите указанную сумму и отметьте выполнение.';
$lang->zpay_manual_refund_done = 'Отметить как отправленное';
$lang->zpay_manual_refund_sent = 'Возврат отправлен';
$lang->zpay_pending_refund_notice = 'Не отправлено возвратов: %s. Проверьте, пожалуйста.';

$lang->msg_confirm_success = 'Покупка подтверждена.';
$lang->msg_already_confirmed = 'Этот платёж уже был подтверждён.';
$lang->msg_not_confirmable = 'В текущем состоянии подтверждение невозможно.';
$lang->msg_confirmed_not_cancellable = 'Подтверждённый платёж нельзя отменить. Требуется принудительная отмена администратором.';
$lang->msg_force_cancel_disabled = 'Принудительная отмена подтверждённых платежей не разрешена.';
$lang->msg_cancel_manual_refund_queued = 'Отменено. Этот способ оплаты не возвращается автоматически, поэтому администратор должен перевести деньги вручную.';
$lang->msg_no_pending_refund = 'Нет возвратов, ожидающих перевода.';
$lang->msg_refund_completed = 'Отмечено как отправленное.';
