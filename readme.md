# Subscription Management API

## API Dokümantasyonu
API dokümantasyonuna [http://url/api/doc](http://url/api/doc) adresinden erişilebilir.

## Hızlı Bakış
* ### Device Register
  Dışarıdan sağlanan bilgilerle bir cihazı kaydeder. Eğer cihaz zaten varsa, cihazı döndürür. Bu, kullanıcıların cihazlarını yönetmelerini ve aboneliklerini cihazlarına bağlamalarını sağlar.
* ### Subscription Purchase
  Alınan fiş bilgilerine göre bir aboneliği başlatır. Eğer zaten pasif olmuş bir abonelik varsa, onu yeniler. Bu, kullanıcıların ödemelerini yapıp aboneliklerini etkinleştirmelerini sağlar.
* ### Subscription Check
  Kullanıcıların etkin abonelikleri hakkında bilgi döndürür. Bu, kullanıcıların mevcut abonelik durumlarını izlemelerine olanak tanır.
* ### Subscription Validate (Command)
  Süresi dolmuş ancak hala aktif olan abonelikleri bir kuyruk mekanizması aracılığıyla pasif hale getirme komutlarını içerir. Bu, sistemdeki geçersiz aboneliklerin düzenli olarak temizlenmesini sağlar.
* ### Callback Request
  Abonelik tarafında bir güncelleme olduğunda güncelleme türünü yakalar ve uygulama tablosundaki callback request URL'ine iletilir. İstenen durum kodu alınmazsa, yeniden denemek için isteği kuyruğa alır. Subscription tarafındaki güncellemelerin third party uygulamalara iletilmesini sağlar.

## Kuyruk Sistemi Detayları
Kullandığımız paket ayarları eksik olduğundan, DLX ve DLQ yapılandırmalarının RabbitMQ'da manuel olarak ayarlanması gerekmektedir. Kuyruktaki mesajlar, yapılandırmaya göre tekrar denenip kuyruktan çıkarılacaktır. Bu, sistemdeki iletişim hatalarını yönetmeyi ve veri bütünlüğünü korumayı sağlar.
