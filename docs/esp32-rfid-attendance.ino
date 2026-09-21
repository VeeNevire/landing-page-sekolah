#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>

#define SS_PIN 5
#define RST_PIN 4

MFRC522 rfid(SS_PIN, RST_PIN);
const char* ssid = "investa";
const char* password = "investa1";
// Jalankan Laravel: php artisan serve --host=0.0.0.0 --port=8000
const char* serverUrl = "http://192.168.1.12:8000/api/rfid/scan";
const char* deviceToken = "ISI_TOKEN_DARI_ENV_SERVER";
String lastUid;
unsigned long lastScan = 0;
unsigned long lastReconnect = 0;

void setup() {
  Serial.begin(115200);
  delay(1000);
  SPI.begin(18, 19, 23, 5);
  rfid.PCD_Init();
  rfid.PCD_AntennaOn();
  WiFi.mode(WIFI_STA);
  WiFi.setAutoReconnect(true);
  WiFi.begin(ssid, password);
  Serial.println("Menghubungkan Wi-Fi. Tempelkan kartu setelah terhubung.");
}

void loop() {
  static bool connectionReported = false;
  if (WiFi.status() != WL_CONNECTED) {
    connectionReported = false;
    if (millis() - lastReconnect >= 10000) {
      Serial.println("Wi-Fi terputus. Mencoba kembali; absensi belum dapat dikirim.");
      WiFi.reconnect();
      lastReconnect = millis();
    }
    return;
  }
  if (!connectionReported) {
    Serial.println("WiFi connected");
    Serial.print("IP ESP32: ");
    Serial.println(WiFi.localIP());
    Serial.println("Tempelkan kartu...");
    connectionReported = true;
  }
  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) return;
  String uid;
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (i) uid += ":";
    if (rfid.uid.uidByte[i] < 0x10) uid += "0";
    uid += String(rfid.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
  if (uid == lastUid && millis() - lastScan < 3000) return;
  lastUid = uid;
  lastScan = millis();

  HTTPClient http;
  Serial.println("Kartu terdeteksi! Mengirim ke server...");
  http.setConnectTimeout(3000);
  http.setTimeout(5000);
  http.begin(serverUrl);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");
  http.addHeader("X-RFID-Token", deviceToken);
  int code = http.POST("{\"uid\":\"" + uid + "\"}");
  Serial.printf("UID: %s | HTTP: %d\n", uid.c_str(), code);
  if (code > 0) Serial.println(http.getString());
  else Serial.println("Kirim gagal. Angkat dan tap ulang kartu setelah koneksi pulih.");
  http.end();
}
