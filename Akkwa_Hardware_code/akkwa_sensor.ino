#include <WiFi.h>
#include <HTTPClient.h>
#include <OneWire.h>
#include <DallasTemperature.h>

// WiFi Credentials
const char* ssid = "ZTE_2.4G_qApUTy";
const char* password = "CH4wTO1v";

// Server URL
const char* serverName = "http://192.168.1.6/akkwa/dist/get-readings/fetch-latest/get_water_temp.php";
const char* distanceName = "http://192.168.1.6/akkwa/dist/get-readings/fetch-latest/get_measurement.php";
const char* phLevelServer = "http://192.168.1.6/akkwa/dist/get-readings/fetch-latest/get_ph_sensor.php";

// Data wire is connected to GPIO15
#define ONE_WIRE_BUS 15

// OneWire instance
OneWire oneWire(ONE_WIRE_BUS);

// DallasTemperature instance
DallasTemperature sensors(&oneWire);

// Ultrasonic sensor pins
const int trigPin = 5;
const int echoPin = 4;

// pH sensor pin
const int phPin = 34; // Change this to the appropriate pin for the pH sensor

void setup() {
    Serial.begin(115200);
    Serial.println("DS18B20 Temperature Sensor Test");

    // Start WiFi connection
    WiFi.begin(ssid, password);
    Serial.print("Connecting to WiFi");

    while (WiFi.status() != WL_CONNECTED) {
        delay(1000);
        Serial.print(".");
    }

    Serial.println("\nConnected to WiFi");
    sensors.begin();
    
    pinMode(trigPin, OUTPUT);
    pinMode(echoPin, INPUT);
    pinMode(phPin, INPUT); // Set the pH sensor pin as input
}

void loop() {
    sensors.requestTemperatures();  // Send command to get temperatures
    float temperatureC = sensors.getTempCByIndex(0);
    float temperatureF = sensors.getTempFByIndex(0);

    if (temperatureC != DEVICE_DISCONNECTED_C) {
        Serial.print("Temperature: ");
        Serial.print(temperatureC);
        Serial.println(" °C");
        Serial.print("Fahrenheit: ");
        Serial.print(temperatureF);
        Serial.println(" °F");

        sendToDatabase(temperatureC, temperatureF);  // Send data to the server
    } else {
        Serial.println("Error: Could not read temperature data");
        retryReadingTemperature(); // Retry function if the reading fails
    }

    float distance = measureDistance();
    sendDistanceToDatabase(distance);  // Send distance data

    float phValue = readPhSensor();
    sendPhLevelToDatabase(phValue);  // Send pH level data

    delay(10000);  // Wait 10 seconds before next reading
}

// Function to measure distance using ultrasonic sensor
float measureDistance() {
    digitalWrite(trigPin, LOW);
    delayMicroseconds(2);
    digitalWrite(trigPin, HIGH);
    delayMicroseconds(10);
    digitalWrite(trigPin, LOW);
    long duration = pulseIn(echoPin, HIGH);
    return duration * 0.0343 / 2;
}

// Function to read pH sensor with smoothing
float readPhSensor() {
    int buf[10];
    int temp;
    long avgValue = 0;

    // Get 10 sample values for smoothing
    for (int i = 0; i < 10; i++) {
        buf[i] = analogRead(phPin);  // Read the pH sensor
        delay(10);
    }

    // Sort values from small to large
    for (int i = 0; i < 9; i++) {
        for (int j = i + 1; j < 10; j++) {
            if (buf[i] > buf[j]) {
                temp = buf[i];
                buf[i] = buf[j];
                buf[j] = temp;
            }
        }
    }

    // Take the average of the 6 center samples
    for (int i = 2; i < 8; i++) {
        avgValue += buf[i];
    }

    // Convert ADC to voltage
    float voltage = (float)avgValue / 6 * 3.3 / 4095;
    
    // Convert voltage to pH value (this depends on your sensor's calibration)
    float phValue = 3.5 * voltage;

    Serial.print("pH: ");
    Serial.println(phValue, 2);

    return phValue;
}

// Function to send temperature data to the server
void sendToDatabase(float tempC, float tempF) {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        String url = String(serverName) + "?water_temp=" + String(tempF, 2) + "&water_tempcel=" + String(tempC, 2);

        Serial.print("Sending data to: ");
        Serial.println(url);

        http.begin(url);  // Specify request destination
        int httpResponseCode = http.GET();  // Send the request

        if (httpResponseCode > 0) {
            Serial.print("HTTP Response Code: ");
            Serial.println(httpResponseCode);
            String response = http.getString();
            Serial.println("Server Response: " + response);
        } else {
            Serial.print("Error on sending request: ");
            Serial.println(httpResponseCode);
        }

        http.end();  // Free resources
    } else {
        Serial.println("WiFi not connected. Skipping data send.");
    }
}

// Function to send distance data to the server
void sendDistanceToDatabase(float distance) {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        String url = String(distanceName) + "?distance=" + String(distance, 2);

        Serial.print("Sending distance data to: ");
        Serial.println(url);

        http.begin(url);  // Specify request destination
        int httpResponseCode = http.GET();  // Send the request

        if (httpResponseCode > 0) {
            Serial.print("HTTP Response Code: ");
            Serial.println(httpResponseCode);
            String response = http.getString();
            Serial.println("Server Response: " + response);
        } else {
            Serial.print("Error on sending request: ");
            Serial.println(httpResponseCode);
        }

        http.end();  // Free resources
    } else {
        Serial.println("WiFi not connected. Skipping data send.");
    }
}

// Function to send pH level data to the server
void sendPhLevelToDatabase(float phLevel) {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        String url = String(phLevelServer) + "?ph_sensor=" + String(phLevel, 2);

        Serial.print("Sending pH level data to: ");
        Serial.println(url);

        http.begin(url);  // Specify request destination
        int httpResponseCode = http.GET();  // Send the request

        if (httpResponseCode > 0) {
            Serial.print("HTTP Response Code: ");
            Serial.println(httpResponseCode);
            String response = http.getString();
            Serial.println("Server Response: " + response);
        } else {
            Serial.print("Error on sending request: ");
            Serial.println(httpResponseCode);
        }

        http.end();  // Free resources
    } else {
        Serial.println("WiFi not connected. Skipping data send.");
    }
}

// Function to retry reading the temperature
void retryReadingTemperature() {
    // Attempt to retry the temperature reading a few times before giving up
    int retries = 5;
    while (retries > 0) {
        delay(500);  // Delay between retries
        sensors.requestTemperatures();
        float temperatureC = sensors.getTempCByIndex(0);

        if (temperatureC != DEVICE_DISCONNECTED_C) {
            Serial.print("Retry successful! Temperature: ");
            Serial.print(temperatureC);
            Serial.println(" °C");
            break;
        } else {
            Serial.println("Retry failed. Attempting again...");
            retries--;
        }

        if (retries == 0) {
            Serial.println("All retry attempts failed. Skipping this reading.");
        }
    }
}
