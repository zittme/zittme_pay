<?php

$lang->zittme_pay = 'Zittme Pay';

// Yönetim sekmeleri
$lang->zpay_tab_config = 'Genel';
$lang->zpay_tab_gateway = 'Ödeme yöntemleri';
$lang->zpay_tab_orders = 'Ödemeler';
$lang->zpay_tab_logs = 'İletişim kaydı';

$lang->about_zpay_config = 'Ödeme motorunun ortak davranışı. Ticaret veya rezervasyon gibi ödeme gerektiren modüller bu ayarları birlikte kullanır.';
$lang->about_zpay_gateway = 'İstediğiniz ödeme yöntemlerini açın ve anahtarlarını girin. Anahtarı girilmemiş bir yöntem ödeme sayfasında görünmez.';
$lang->about_zpay_logs = 'Bir ödeme için gidip gelen tüm istek ve yanıtlar. Anlaşmazlık durumunda kanıtınızdır; yeterince uzun süre saklayın.';

// Ödeme yöntemi adları
$lang->gateway_toss = 'Toss Payments';
$lang->gateway_banktransfer = 'Banka havalesi';

// Genel
$lang->zpay_enabled = 'Ödemeyi etkinleştir';
$lang->zpay_enabled_help = 'Yeni ödeme almayı durdurmak için kapatın.';
$lang->zpay_test_mode = 'Test modu';
$lang->zpay_test_mode_help = 'Sağlayıcının test anahtarlarının kullanıldığını belirtir. Gerçek para hareketi olmaz.';
$lang->zpay_currency = 'Para birimi';
$lang->zpay_order_prefix = 'Sipariş numarası öneki';
$lang->zpay_order_prefix_help = 'Sağlayıcı panelinde siparişlerinizi tanımanıza yardım eder. Harf ve rakam, en fazla 8 karakter.';

$lang->zpay_group_cancel = 'İptal ve iade';
$lang->zpay_allow_partial_cancel = 'Kısmi iptale izin ver';
$lang->zpay_allow_partial_cancel_help = 'Ödenen tutarın yalnızca bir kısmının iade edilmesine izin verir.';
$lang->zpay_cancel_reasons = 'İptal nedenleri';
$lang->zpay_cancel_reasons_help = 'Her satıra bir tane. Boş bırakırsanız her seferinde elle yazılır.';

$lang->zpay_group_notify = 'Bildirimler';
$lang->zpay_notify_admin_email = 'Yönetici e-postası';
$lang->zpay_notify_events = 'Şu durumlarda bildir';
$lang->zpay_notify_on_paid = 'Ödeme tamamlandığında';
$lang->zpay_notify_on_cancel = 'Ödeme iptal edildiğinde';

$lang->zpay_group_security = 'Güvenlik ve kayıt';
$lang->zpay_log_retention_days = 'Kayıt saklama süresi (gün)';
$lang->zpay_log_retention_days_help = '0 süresiz saklar. Çok kısa ayarlamayın; bu kayıtlar anlaşmazlıkta kanıtınızdır.';
$lang->zpay_webhook_ip_whitelist = 'İzin verilen webhook IP\'leri';
$lang->zpay_webhook_ip_whitelist_help = 'Her satıra bir tane. Boş bırakılırsa IP kısıtı uygulanmaz. Sonuna * koyarak aralık belirtebilirsiniz. IP süzme yalnızca yardımcı bir önlemdir; asıl savunma her webhook geldiğinde sağlayıcıya yeniden sormaktır.';

$lang->zpay_group_notice = 'Yasal bildirim';
$lang->zpay_biz_notice = 'Ödeme sayfası alt bilgisi';
$lang->zpay_biz_notice_help = 'Ticaret sicil bilgileri gibi, ödeme sayfasının altında gösterilecek metin.';

// Ödeme yöntemleri
$lang->zpay_enabled_gateways = 'Etkin ödeme yöntemleri';
$lang->zpay_enabled_gateways_help = 'Ödeme sayfasında yalnızca işaretlediğiniz yöntemler görünür.';
$lang->zpay_not_configured = 'anahtar eksik';
$lang->zpay_toss_client_key = 'İstemci anahtarı';
$lang->zpay_toss_secret_key = 'Gizli anahtar';
$lang->zpay_toss_key_help = 'Toss Payments üye iş yeri panelinden alınır. Test anahtarları test_, canlı anahtarlar live_ ile başlar.';
$lang->zpay_webhook_url = 'Webhook adresi';
$lang->zpay_webhook_url_help = 'Sanal hesaba para girişi gibi eşzamansız bildirimleri alabilmek için bu adresi sağlayıcı panelinize kaydedin.';
$lang->zpay_bank_accounts = 'Banka hesapları';
$lang->zpay_bank_accounts_help = 'Yalnızca banka ve hesap numarası dolu satırlar kaydedilir. Satırı boşaltmak onu siler.';
$lang->zpay_bank_name = 'Banka';
$lang->zpay_bank_account = 'Hesap numarası';
$lang->zpay_bank_holder = 'Hesap sahibi';
$lang->zpay_bank_due_days = 'Ödeme süresi (gün)';
$lang->zpay_bank_due_days_help = 'Bu süre dolduğunda sipariş süresi geçmiş sayılır.';

// Ödeme sayfası
$lang->zpay_checkout_title = 'Ödeme';
$lang->zpay_order_summary = 'Sipariş özeti';
$lang->zpay_order_code = 'Sipariş numarası';
$lang->zpay_product = 'Ürün';
$lang->zpay_payer = 'Ödeyen';
$lang->zpay_payer_phone = 'Telefon';
$lang->zpay_payer_email = 'E-posta';
$lang->zpay_amount = 'Tutar';
$lang->zpay_select_method = 'Ödeme yöntemi seçin';
$lang->zpay_depositor_name = 'Gönderen adı';
$lang->zpay_bank_due_notice = 'Lütfen %d gün içinde havale yapın. Süre dolduğunda sipariş otomatik olarak iptal edilir.';
$lang->zpay_pay_button = '%s öde';

// Sonuç
$lang->zpay_result_paid = 'Ödeme tamamlandı';
$lang->zpay_result_pending = 'Havaleniz bekleniyor';
$lang->zpay_result_cancelled = 'Ödeme iptal edildi';
$lang->zpay_result_expired = 'Ödeme süresi doldu';
$lang->zpay_result_failed = 'Ödeme başarısız oldu';
$lang->zpay_bank_guide_title = 'Havale bilgileri';
$lang->zpay_due_date = 'Son ödeme';
$lang->zpay_receipt = 'Makbuzu gör';
$lang->zpay_back_to_shop = 'Geri dön';
$lang->zpay_cancelled_amount = 'İptal edilen tutar';

// Liste
$lang->zpay_order_detail = 'Ödeme ayrıntısı';
$lang->zpay_source = 'Ödeme konusu';
$lang->zpay_gateway = 'Yöntem';
$lang->zpay_pg_tid = 'Sağlayıcı işlem numarası';
$lang->zpay_status = 'Durum';
$lang->zpay_regdate = 'Oluşturulma';
$lang->zpay_paid_date = 'Ödenme';
$lang->zpay_cancelled_date = 'İptal';
$lang->zpay_ipaddress = 'IP adresi';
$lang->zpay_remain_amount = 'kalan';
$lang->zpay_total_orders = 'Toplam %s ödeme';
$lang->zpay_no_orders = 'Henüz ödeme yok.';
$lang->zpay_filter_all_status = 'Tüm durumlar';
$lang->zpay_confirm_deposit = 'Ödemeyi onayla';
$lang->zpay_confirm_deposit_help = 'Paranın hesabınıza geçtiğini gördükten sonra basın. Ödeme anında tamamlanmış sayılır ve talep eden modüle bildirilir.';
$lang->zpay_cancel_payment = 'Ödemeyi iptal et';
$lang->zpay_cancel_amount = 'İptal tutarı';
$lang->zpay_cancel_amount_help = 'Kalan tutardan fazlasını iptal edemezsiniz.';
$lang->zpay_cancel_reason = 'Neden';

// Durumlar
$lang->zpay_status_ready = 'Ödeme bekleniyor';
$lang->zpay_status_pending = 'Havale bekleniyor';
$lang->zpay_status_paid = 'Ödendi';
$lang->zpay_status_cancelled = 'İptal edildi';
$lang->zpay_status_partial_cancelled = 'Kısmen iptal edildi';
$lang->zpay_status_failed = 'Başarısız';
$lang->zpay_status_expired = 'Süresi doldu';

// Kayıt
$lang->zpay_communication_log = 'İletişim kaydı';
$lang->zpay_log_action = 'İşlem';
$lang->zpay_log_result = 'Sonuç';
$lang->zpay_log_response = 'Yanıt';
$lang->zpay_no_logs = 'Kayıt yok.';
$lang->zpay_total_logs = 'Toplam %s kayıt';
$lang->zpay_filter_all_action = 'Tüm işlemler';
$lang->zpay_filter_all_result = 'Tüm sonuçlar';
$lang->zpay_result_success = 'Başarılı';
$lang->zpay_result_fail = 'Başarısız';
$lang->zpay_purge_logs = '%d günden eski kayıtları sil';

// İletiler
$lang->msg_pay_disabled = 'Ödeme özelliği kapalı.';
$lang->msg_invalid_source = 'Ödeme konusu geçersiz.';
$lang->msg_invalid_amount = 'Ödeme tutarı geçersiz.';
$lang->msg_order_not_found = 'Ödeme siparişi bulunamadı.';
$lang->msg_no_gateway_available = 'Kullanılabilir ödeme yöntemi yok. Lütfen site yöneticisine başvurun.';
$lang->msg_gateway_not_found = 'Ödeme yöntemi bulunamadı.';
$lang->msg_invalid_ticket = 'Bu ödeme oturumunun süresi doldu. Lütfen baştan başlayın.';
$lang->msg_already_settled = 'Bu ödeme zaten sonuçlandırılmış.';
$lang->msg_too_many_requests = 'Çok fazla ödeme denemesi yapıldı. Lütfen biraz sonra tekrar deneyin.';

$lang->msg_approve_success = 'Ödeme onaylandı.';
$lang->msg_approve_failed = 'Ödeme onaylanamadı.';
$lang->msg_payment_cancelled = 'Ödeme iptal edildi.';
$lang->msg_payment_not_completed = 'Bu ödeme tamamlanmadı.';
$lang->msg_amount_mismatch = 'Tutar sipariş ile uyuşmadığı için ödeme durduruldu.';
$lang->msg_missing_payment_key = 'Sağlayıcı işlem numarası eksik.';
$lang->msg_unknown_pg_status = 'Bilinmeyen ödeme durumu.';
$lang->msg_pg_error = 'Ödeme sağlayıcısı bir hata döndürdü.';
$lang->msg_pg_unreachable = 'Ödeme sağlayıcısına ulaşılamadı.';
$lang->msg_query_not_supported = 'Bu ödeme yöntemi sorgulamayı desteklemiyor.';

$lang->msg_cancel_success = 'Ödeme iptal edildi.';
$lang->msg_cancel_failed = 'Ödeme iptal edilemedi.';
$lang->msg_not_cancellable = 'Bu ödeme mevcut durumunda iptal edilemez.';
$lang->msg_invalid_cancel_amount = 'İptal tutarı geçersiz.';
$lang->msg_partial_cancel_disabled = 'Kısmi iptale izin verilmiyor.';
$lang->msg_cancel_record_failed = 'Sağlayıcı ödemeyi iptal etti ancak burada kaydedilemedi. Lütfen site yöneticisine başvurun.';
$lang->cancel_default_reason = 'Müşteri talebi';

$lang->msg_no_bank_account = 'Kayıtlı banka hesabı yok.';
$lang->msg_bank_registered = 'Hesap bilgileri aşağıda gösteriliyor. Lütfen süresi içinde havale edin.';
$lang->msg_bank_manual_refund = 'Banka havalesi iadelerini yöneticinin elle göndermesi gerekir.';
$lang->msg_not_pending = 'Bu sipariş havale bekleme durumunda değil.';
$lang->msg_deposit_confirmed = 'Havale onaylandı ve ödeme tamamlandı.';
$lang->msg_log_retention_disabled = 'Saklama süresi 0 olduğu için hiçbir şey silinmiyor.';

// Satın alma onayı ve elle iade
$lang->zpay_status_confirmed = 'Onaylandı';
$lang->zpay_confirm_date = 'Onay tarihi';
$lang->zpay_auto_cancel_days = 'Sağlayıcı üzerinden iptal süresi (gün)';
$lang->zpay_auto_cancel_days_help = 'Bu süre dolduktan sonra sağlayıcı üzerinden iptal denenmez, elle iade kuyruğa alınır; çünkü hakediş tamamlandıktan sonra kart iptali engellenir. 0 sınır yok demektir.';
$lang->zpay_allow_force_cancel = 'Zorunlu iptale izin ver';
$lang->zpay_allow_force_cancel_help = 'Yöneticinin onaylanmış bir ödemeyi de iptal edebilmesini sağlar. Kapatırsanız onay nihai olur.';
$lang->zpay_force_cancel = 'Zorunlu iptal';
$lang->zpay_force_cancel_confirm = 'Bu ödemenin onaylandığını biliyorum ve yine de iptal ediyorum';
$lang->zpay_force_cancel_help = 'Bu ödeme zaten onaylanmış. İptal etmek için aşağıdan zorunlu iptali açıkça seçmelisiniz.';
$lang->zpay_no_auto_cancel_help = 'Bu ödeme yöntemi otomatik iptal edilemez. İptal yalnızca kayıtları günceller; parayı yöneticinin elle göndermesi gerekir.';
$lang->zpay_manual_refund_title = 'Elle iade';
$lang->zpay_manual_refund_help = 'İptal kaydedildi ancak para henüz gönderilmedi. Aşağıdaki tutarı gönderdikten sonra tamamlandı olarak işaretleyin.';
$lang->zpay_manual_refund_done = 'Gönderildi olarak işaretle';
$lang->zpay_manual_refund_sent = 'İade gönderildi';
$lang->zpay_pending_refund_notice = 'Gönderilmemiş %s iade var. Lütfen kontrol edin.';

$lang->msg_confirm_success = 'Satın alma onaylandı.';
$lang->msg_already_confirmed = 'Bu ödeme zaten onaylanmıştı.';
$lang->msg_not_confirmable = 'Bu ödeme mevcut durumunda onaylanamaz.';
$lang->msg_confirmed_not_cancellable = 'Onaylanmış bir ödeme iptal edilemez. Yönetici tarafından zorunlu iptal gerekir.';
$lang->msg_force_cancel_disabled = 'Onaylanmış ödemelerin zorunlu iptaline izin verilmiyor.';
$lang->msg_cancel_manual_refund_queued = 'İptal edildi. Bu ödeme yöntemi otomatik iade edilemediği için yöneticinin parayı elle göndermesi gerekir.';
$lang->msg_no_pending_refund = 'Gönderilmeyi bekleyen iade yok.';
$lang->msg_refund_completed = 'Gönderildi olarak kaydedildi.';
