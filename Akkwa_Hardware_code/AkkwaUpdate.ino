#include <WiFi.h>
#include <ESPAsyncWebServer.h>
#include <ESP32Servo.h>
#include <WiFiUdp.h>
#include <time.h>
#include <OneWire.h>
#include <DallasTemperature.h>

const char* ssid = "ZTE_2.4G_qApUTy";
const char* password = "CH4wTO1v";

// #define ONE_WIRE_BUS 15
// #define SensorPin 34

Servo myServo;
const int servoPin = 18;
const int relayPin = 19;
const int refillRelayPin = 21;
const int oxygenRelayPin = 22;
int scheduledHour = -1;
int scheduledMinute = -1;
bool hasSpun = false;

const char* ntpServer1 = "pool.ntp.org";
const char* ntpServer2 = "time.nist.gov";
const char* ntpServer3 = "time.google.com";
const char* timeZone = "PHT-8";

AsyncWebServer server(80);

// const int trigPin = 5;
// const int echoPin = 4;
// unsigned long lastUltrasonicSent = 0;
// const unsigned long ultrasonicInterval = 10000;
// unsigned long int avgValue;
// int buf[10], temp;

// OneWire oneWire(ONE_WIRE_BUS);
// DallasTemperature sensors(&oneWire);

// float measureDistance() {
//   digitalWrite(trigPin, LOW);
//   delayMicroseconds(2);
//   digitalWrite(trigPin, HIGH);
//   delayMicroseconds(10);
//   digitalWrite(trigPin, LOW);
//   long duration = pulseIn(echoPin, HIGH);
//   return duration * 0.0343 / 2;
// }

// void sendPhLevel() {
//   for (int i = 0; i < 10; i++) { // Get 10 sample values for smoothing
//     buf[i] = analogRead(SensorPin);
//     delay(10);
//   }

//   for (int i = 0; i < 9; i++) { // Sort values from small to large
//     for (int j = i + 1; j < 10; j++) {
//       if (buf[i] > buf[j]) {
//         temp = buf[i];
//         buf[i] = buf[j];
//         buf[j] = temp;
//       }
//     }
//   }

//   avgValue = 0;
//   for (int i = 2; i < 8; i++) // Take the average of 6 center samples
//     avgValue += buf[i];

//   float voltage = (float)avgValue / 6 * 3.3 / 4095; // Convert ADC to voltage
//   float phValue = 3.5 * voltage; // Convert voltage to pH value

//   Serial.print("pH: ");  
//   Serial.println(phValue, 2);
//   WiFiClient client;
//   Serial.println("Connecting to server...");
  
//   if (client.connect("192.168.1.6", 80)) {
//     Serial.println("Connected to server, sending data...");
//     client.print("GET /akkwa/dist/get-readings/fetch-latest/get_ph_sensor.php?ph_sensor=" + String(phValue, 2) + " HTTP/1.1\r\n");
//     client.print("Host: 192.168.1.6\r\n");
//     client.print("Connection: close\r\n");
//     client.println();
    
//     while (client.available()) {
//       String response = client.readString();
//       Serial.println("Server Response: " + response);
//     }

//     client.stop();
//     Serial.println("Water reading sent: " + String(phValue, 2) + " cm");
//   } else {
//     Serial.println("❌ Connection to server FAILED!");
//   }
// }

// void sendWaterTemperature() {
//   sensors.requestTemperatures();
//   float temperatureC = sensors.getTempCByIndex(0);
//   float temperatureF = sensors.getTempFByIndex(0);

//   WiFiClient client;
//   if (client.connect("192.168.1.6", 80)) {
//     Serial.println("✅ Connected to server, sending data...");

//     // Corrected GET request format
//     String url = "/akkwa/dist/get-readings/fetch-latest/get_water_temp.php?water_temp=" + 
//                  String(temperatureF, 2) + "&water_tempcel=" + String(temperatureC, 2);
    
//     client.print(String("GET ") + url + " HTTP/1.1\r\n");
//     client.print("Host: 192.168.1.6\r\n");
//     client.print("User-Agent: ESP8266\r\n");
//     client.print("Connection: close\r\n");
//     client.println();

//     while (client.available()) {
//       String response = client.readString();
//       Serial.println("Server Response: " + response);
//     }

//     client.stop();
//     Serial.println("📡 Sent: " + url);
//   } else {
//     Serial.println("❌ Connection to server FAILED!");
//   }
// }

// void sendUltrasonicReading() {
//   float distance = measureDistance();
//   WiFiClient client;
//   Serial.println("Connecting to server...");
  
//   if (client.connect("192.168.1.6", 80)) {
//     Serial.println("Connected to server, sending data...");
//     client.print("GET /akkwa/dist/get-readings/fetch-latest/get_measurement.php?distance=" + String(distance, 2) + " HTTP/1.1\r\n");
//     client.print("Host: 192.168.1.6\r\n");
//     client.print("Connection: close\r\n");
//     client.println();
    
//     while (client.available()) {
//       String response = client.readString();
//       Serial.println("Server Response: " + response);
//     }

//     client.stop();
//     Serial.println("Ultrasonic reading sent: " + String(distance, 2) + " cm");
//   } else {
//     Serial.println("❌ Connection to server FAILED!");
//   }
// }

void spinServo() {
  myServo.write(0);
  delay(1400);
  myServo.write(90);
}

void controlRelay(bool state) {
  digitalWrite(relayPin, state ? HIGH : LOW);
  Serial.println(state ? "Relay turned ON" : "Relay turned OFF");
}

void controlRefillRelay(bool state) {
  digitalWrite(refillRelayPin, state ? HIGH : LOW);
  Serial.println(state ? "Relay turned ON" : "Relay turned OFF");
}
void controlOxygenRelay(bool state) {
  digitalWrite(oxygenRelayPin, state ? HIGH : LOW);
  Serial.println(state ? "Relay turned ON" : "Relay turned OFF");
}

void setup() {
  Serial.begin(115200);
  myServo.attach(servoPin);
  pinMode(relayPin, OUTPUT);
  digitalWrite(relayPin, LOW);
  pinMode(refillRelayPin, OUTPUT);
  digitalWrite(refillRelayPin, LOW);
  pinMode(oxygenRelayPin, OUTPUT);
  digitalWrite(oxygenRelayPin, LOW);
  // pinMode(trigPin, OUTPUT);
  // pinMode(echoPin, INPUT);

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");
  Serial.println(WiFi.localIP());

  configTime(0, 0, ntpServer1, ntpServer2, ntpServer3);
  setenv("TZ", timeZone, 1);
  tzset();

  server.on("/spin", HTTP_GET, [](AsyncWebServerRequest *request){
    spinServo();
    request->send(200, "text/plain", "Servo spun!");
  });

  server.on("/set_time", HTTP_GET, [](AsyncWebServerRequest *request){
    if (request->hasParam("hour") && request->hasParam("minute")) {
      scheduledHour = request->getParam("hour")->value().toInt();
      scheduledMinute = request->getParam("minute")->value().toInt();
      Serial.printf("Scheduled time set to: %02d:%02d\n", scheduledHour, scheduledMinute);
    }
    request->send(200, "text/plain", "Time set to " + String(scheduledHour) + ":" + String(scheduledMinute) + "!");
  });

  server.on("/relay", HTTP_GET, [](AsyncWebServerRequest *request){
    AsyncWebServerResponse *response = request->beginResponse(200, "text/plain", "Relay state updated");
    response->addHeader("Access-Control-Allow-Origin", "*");  // Allow requests from any origin
    response->addHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
    response->addHeader("Access-Control-Allow-Headers", "Content-Type");
    request->send(response); // Send the response

    if (request->hasParam("state")) {
        String state = request->getParam("state")->value();
        digitalWrite(relayPin, state == "on" ? HIGH : LOW);
        Serial.println(state == "on" ? "✅ Relay TURNED ON from webpage!" : "❌ Relay TURNED OFF from webpage!");
    } else {
        Serial.println("🚨 Missing 'state' parameter!");
    }
  });

  server.on("/refill", HTTP_GET, [](AsyncWebServerRequest *request){
    AsyncWebServerResponse *response = request->beginResponse(200, "text/plain", "Relay state updated");
    response->addHeader("Access-Control-Allow-Origin", "*");  // Allow requests from any origin
    response->addHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
    response->addHeader("Access-Control-Allow-Headers", "Content-Type");
    request->send(response); // Send the response

    if (request->hasParam("state")) {
        String state = request->getParam("state")->value();
        digitalWrite(refillRelayPin, state == "on" ? HIGH : LOW);
        Serial.println(state == "on" ? "✅ RefillRelay TURNED ON from webpage!" : "❌ RefillRelay TURNED OFF from webpage!");
    } else {
        Serial.println("🚨 Missing 'state' parameter!");
    }
  });
  server.on("/oxygen", HTTP_GET, [](AsyncWebServerRequest *request){
    AsyncWebServerResponse *response = request->beginResponse(200, "text/plain", "Relay state updated");
    response->addHeader("Access-Control-Allow-Origin", "*");  // Allow requests from any origin
    response->addHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
    response->addHeader("Access-Control-Allow-Headers", "Content-Type");
    request->send(response); // Send the response

    if (request->hasParam("state")) {
        String state = request->getParam("state")->value();
        digitalWrite(oxygenRelayPin, state == "on" ? HIGH : LOW);
        Serial.println(state == "on" ? "✅ RefillRelay TURNED ON from webpage!" : "❌ RefillRelay TURNED OFF from webpage!");
    } else {
        Serial.println("🚨 Missing 'state' parameter!");
    }
  });

  // Handle OPTIONS preflight request (CORS Fix)
  server.on("/relay", HTTP_OPTIONS, [](AsyncWebServerRequest *request){
    AsyncWebServerResponse *response = request->beginResponse(204);
    response->addHeader("Access-Control-Allow-Origin", "*");
    response->addHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
    response->addHeader("Access-Control-Allow-Headers", "Content-Type");
    request->send(response);
  });

  server.on("/refill", HTTP_OPTIONS, [](AsyncWebServerRequest *request){
    AsyncWebServerResponse *response = request->beginResponse(204);
    response->addHeader("Access-Control-Allow-Origin", "*");
    response->addHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
    response->addHeader("Access-Control-Allow-Headers", "Content-Type");
    request->send(response);
  });
  server.on("/oxygen", HTTP_OPTIONS, [](AsyncWebServerRequest *request){
    AsyncWebServerResponse *response = request->beginResponse(204);
    response->addHeader("Access-Control-Allow-Origin", "*");
    response->addHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
    response->addHeader("Access-Control-Allow-Headers", "Content-Type");
    request->send(response);
  });

  server.begin();
}

void loop() {
  struct tm timeinfo;
  if (!getLocalTime(&timeinfo)) {
    Serial.println("Failed to obtain time");
    delay(5000);
    return;
  }

  int currentHour = timeinfo.tm_hour;
  int currentMinute = timeinfo.tm_min;
  Serial.printf("Current Time: %02d:%02d\n", currentHour, currentMinute);
  delay(5000);

  if (currentHour == scheduledHour && currentMinute == scheduledMinute && !hasSpun) {
    Serial.println("Auto-spinning now...");
    spinServo();
    hasSpun = true;
  }

  if (currentHour != scheduledHour || currentMinute != scheduledMinute) {
    hasSpun = false;
  }

  // Send data every 10 seconds
  // if (millis() - lastUltrasonicSent >= ultrasonicInterval) {
  //   Serial.println("Sending data to server...");
  //   // sendUltrasonicReading();
  //   // sendWaterTemperature();
  //   // sendPhLevel();
  //   lastUltrasonicSent = millis();
  // }
}
