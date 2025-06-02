#include <WiFi.h>
#include <HTTPClient.h>

#define WIFI_SSID "ZTE_2.4G_qApUTy"
#define WIFI_PASSWORD "CH4wTO1v"
#define SERVER_URL "http://localhost/akkwa/dist/save_battery.php"

#define ADC_PIN 34
#define ADC_MAX 4095
#define REF_VOLTAGE 3.3
#define VOLTAGE_DIVIDER_RATIO 5.15 // Adjust based on actual resistor values

void setup() {
    Serial.begin(115200);
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    
    Serial.print("Connecting to WiFi...");
    while (WiFi.status() != WL_CONNECTED) {
        Serial.print(".");
        delay(1000);
    }
    Serial.println("\nWiFi connected!");
}

void loop() {
    int adcValue = analogRead(ADC_PIN);

    // Convert ADC reading to voltage
    float voltage = (adcValue * REF_VOLTAGE) / ADC_MAX;
    float batteryVoltage = voltage * VOLTAGE_DIVIDER_RATIO; // Scale up

    Serial.print("Raw ADC: ");
    Serial.print(adcValue);
    Serial.print(" | Voltage Measured: ");
    Serial.print(voltage, 2);
    Serial.print("V | Battery Voltage: ");
    Serial.print(batteryVoltage, 2);
    Serial.println("V");

    int batteryPercentage = getBatteryPercentage(batteryVoltage);
    
    Serial.print("Battery: ");
    Serial.print(batteryPercentage);
    Serial.println("%");

    sendToDatabase(batteryPercentage);

    delay(60000);
}

int getBatteryPercentage(float voltage) {
    if (voltage >= 12.8) return 100;
    else if (voltage >= 12.6) return 90;
    else if (voltage >= 12.4) return 80;
    else if (voltage >= 12.2) return 70;
    else if (voltage >= 12.0) return 60;
    else if (voltage >= 11.8) return 50;
    else if (voltage >= 11.6) return 40;
    else if (voltage >= 11.4) return 30;
    else if (voltage >= 11.2) return 20;
    else if (voltage >= 11.0) return 10;
    else return 0;
}


void sendToDatabase(int percentage) {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.begin(SERVER_URL);
        http.addHeader("Content-Type", "application/x-www-form-urlencoded");

      
        String postData = "battery_percentage=" + String(percentage);
        
        int httpResponseCode = http.POST(postData);

        Serial.print("Server Response Code: ");
        Serial.println(httpResponseCode);

        if (httpResponseCode > 0) {
            Serial.print("Server Response: ");
            Serial.println(http.getString());
        } else {
            Serial.print("Error sending data. HTTP Code: ");
            Serial.println(httpResponseCode);
        }

        http.end();
    } else {
        Serial.println("WiFi disconnected! Reconnecting...");
        WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
        delay(5000);
    }
}
