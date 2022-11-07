#Dylan Giliberto
#October 2022
#ulvweather.com
#A project to monitor and log weather data from a DIY weather station

#--Weather Station Python Script

#weatherTracker.py
#This is the program that collects and logs weather data and logs errors from the weather station
#This program is set to autorun on startup
#--Startup Config: /etc/profile

print("Welcome!")
print("Initializing...")

from os import stat
from pyfirmata import Arduino, util
from time import sleep
try:
    from smbus2 import SMBus
except ImportError:
    from smbus import SMBus
from bme280 import BME280
import RPi.GPIO as GPIO
import requests
import time
import signal
import sys
import socket

print("Setting Variables...")

windSpeedPin = 24
windDirPin = 0
wind_count = 0
rainPin = 25
rain_count = 0

bme280Pin = 26

uploadRate = 60 #Seconds bewteen data uploads

bme280Status = 1
arduinoStatus = 1

#Removed wind sample period, now uploads raw wind speed over sample period, API can average wind speed
#windSampleNum = 5 #Number of previous wind records to keep and average each upload

cycles = 0 #Record number of uploads since program began
status = 1 #TEMP
testing = 1 #SET MANUALLY IF IN TESTING MODE

directions = [
  "N", "NNE",  "NE",
  "ENE", "E", "ESE",
  "SE", "SSE", "S",
  "SSW", "SW", "WSW",
  "W", "WNW", "NW",
  "NNW", "N"
];

def logError(error):
    try:
        eData = {'time': time.time(), 'errorToLog': error}
        r = requests.post('https://api.ulvweather.com/logStationError.php', data = eData)
        print("Error: " + str(error))
        print("Logged Error, Code: " + str(r.status_code))
        res = r.text
        if(res):
            print("Response: " + str(res))
    except:
        print("Unable to Log Error...")

def logIP():
    try:
        testIP = "8.8.8.8"
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect((testIP, 0))
        ipaddr = s.getsockname()[0]
        host = socket.gethostname()
        eData = {'rpi_ip': ipaddr}
        r = requests.post('https://api.ulvweather.com/logIP.php', data = eData)
        print("Logged IP Addr, Code: " + str(r.status_code))
        res = r.text
        if(res):
            print("Response: " + str(res))
    except:
        print("No Network Connection!")
        print("Retrying in 30 seconds...")
        sleep(30)
        logIP()

try:
    uno = '/dev/ttyACM0'
    print("Establishing Connection to Arduino Uno at " + uno + "...")
    board = Arduino(uno)
    it = util.Iterator(board)
    it.start()
    board.analog[windDirPin].enable_reporting()
except:
    print("Error Connecting to Arduino!")
    logError("Error Connecting to Arduino!")
    arduinoStatus = 0
    status = 0
else:
    print("Connected to Arduino!")
    arduinoStatus = 1

def listInit():
    i = 0
    while i < windSampleNum:
        if i != 0:
            windSamples.append(None)
        i += 1

def signal_handler(sig, fram):
    GPIO.cleanup()
    sys.exit(0)

def wind_event(channel):
    global wind_count
    wind_count = wind_count +  1
    print("windy")

def rain_event(channel):
    global rain_count
    rain_count = rain_count + 1
    print("rainy")

def getWindAngle():
    if arduinoStatus:
        dir = board.analog[windDirPin].read()
        dir = dir * 360
        return(dir)
    return(-1)

def getCardinal(degree):
    return directions[round((degree / 360) * 16)]


def resetBme():
    GPIO.output(bme280Pin, False)
    sleep(5)
    GPIO.output(bme280Pin, True)
    return 5

if __name__ == '__main__':
    
    print("Setting up RPi GPIO...")
    GPIO.setmode(GPIO.BCM)

    GPIO.setup(windSpeedPin, GPIO.IN, pull_up_down=GPIO.PUD_UP)
    GPIO.setup(rainPin, GPIO.IN, pull_up_down=GPIO.PUD_UP)
    GPIO.setup(bme280Pin, GPIO.OUT, initial=GPIO.HIGH)
   
    print("Logging IP Address...")
    logIP()

    print("Setting up interrupts...")

    GPIO.add_event_detect(windSpeedPin, GPIO.FALLING, callback=wind_event, bouncetime=50)
    GPIO.add_event_detect(rainPin, GPIO.FALLING, callback=rain_event, bouncetime=50)

    print("Setting up BME280 connection")

    try:
        bus = SMBus(1)
        bme280 = BME280(i2c_dev=bus)
    except:
        print("Error Connecting to BME280!")
        logError("Error Connecting to BME280!")
        bme280Status = 0
        status = 0
    else:
        bme280Status = 1

    print("Collecting Inital Data, Please Wait " + str(uploadRate) + " seconds...")

    #New system to increase accuracy of loop timing.
    #60 second delay + calc time was not giving reliable upload intervals

    #startTime - time before processes
    #endTime   - time once all processes complete

    sleep(uploadRate) #initial Wait

    while True:
        startTime = time.time()

        cycles += 1

        #Wind Speed (MPH)
        windSpeed = wind_count * (2.25 / uploadRate)

        #Rain Volume per sample period, (bucket tips) * ((bucket volume) / (collection area))
        rainVolume = rain_count * (0.153 / 15.55)
        rainRateHourly = rainVolume * (3600 / uploadRate)

        #BME280 Values
        if(bme280Status):
            try:
                temp = bme280.get_temperature()
                pressure = bme280.get_pressure()
                humidity = bme280.get_humidity()
            except:
                print("Error recieveing from BME280!")
                logError("Error recieveing from BME280!")
                status = 0
                temp = -1
                pressure = -1
                humidity = -1
        else:
            temp = -1
            pressure = -1
            humidity = -1

        #Wind Angle
        windDegree = getWindAngle()
        windDirection = getCardinal(windDegree)

        #temp = 30 #commands.getstatusoutput('vcgencmd measure_temp')[1] #TEMPORARY USE CPU TEMP AS TEMP
        #humidity = 50  #TEMP
        #pressure = 100 #TEMP
        timeSent = time.time()
        cpuTemp = 30 #commands.getstatusoutput('vcgencmd measure_temp')[1]
        
        #print("Cycle #: " + str(cycles) + ", Rate: " + str(uploadRate))
        #print(str(windDegree) + " Degrees " + windDirection)
        #print(str(windSpeed) + " MPH")
        #print("Rain Volume: " + str(rainVolume) + " inches")
        #print("Rainfall Rate: " + str(rainRateHourly) + " inches per hour")
        #print(str(temp) + " C, " + str(humidity) + "% Humidity, " + str(pressure) + " hPa")

        print("+====================================+")
        print("| Cycle Num....." + str(cycles))
        print("| Date.........." + time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(timeSent)))
        print("| Temperature..." + str(temp) + " C")
        print("| Humidity......" + str(humidity) + "%")
        print("| Pressure......" + str(pressure) + " hPa")
        print("| Avg Wind......" + str(windSpeed) + " mph")
        print("| Wind Angle...." + str(windDegree) + " degrees")
        print("| Wind Dir......" + str(windDirection))
        print("| Rain Volume..." + str(rainVolume) + "\"")
        print("+=====================================+")
        print()

        if(testing or cycles > 1):
            wData = {
                'time': timeSent,
                'temp': temp,
                'humidity': humidity,
                'pressure': pressure,
                'windSpeed': windSpeed,
                'windDirection': windDirection,
                'windDegree': windDegree,
                'rainVolume': rainVolume,
                'rainRateHourly': rainRateHourly,
                'cpuTemp': cpuTemp,
                'status': status,
                'testing': testing
            }
            print("Preparing to send data...")
            r = None
            try:
                r = requests.post('https://api.ulvweather.com/postWeatherData.php', data = wData)
            except:
                print("Could Not Send Weather Data, Code: " + str(r.status_code))
            else:
                print("Sent Weather Data, Code: " + str(r.status_code))
                error = r.text
                if(error):
                    print("Response: " + str(error))
        else:
            print("Data not logged, first 1 run(s) are discarded when not in testing mode")
        wind_count = 0
        rain_count = 0
        bme280Status = 1
        status = 1
        print()

        endTime = time.time()
        waitTime = uploadRate - (endTime - startTime)
        sleep(waitTime if waitTime > 10 else 60)
        

    signal.signal(signal.SIGINT, signal_handler)
    signal.pause()

