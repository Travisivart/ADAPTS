from datetime import datetime
from scapy.all import *
from random import randint
from time import time, strftime,tzset
import csv
import os
import pymysql
import pickle

os.environ['TZ'] = 'America/Chicago'


server_down = randint(20,40)
count = 0
start = 0

blacklist = list()
def check_ip(ip):
	for atkr_ip in blacklist:
		if atkr_ip == ip:
			return True
	return False
def save_blacklist():
	try:
		thefile = open("blacklist.txt", "w")
		for item in blacklist:
  			thefile.write("%s\n" % item)
		thefile.close()
		print "Blacklist saved"
	except:
            	print("The blacklisted IPs couldn't be saved")

def load_blacklist():
	try:
		blacklist_file = open("blacklist.txt", "r")
            	for item in blacklist_file:
			blacklist.append(item) 
            	blacklist_file.close()
		print "Blacklist accessed"
	except:
            	print "No previous blacklisted IPs"

def update_database(IP, HWADR):
	hostname = 'pcvm3-28.instageni.illinois.edu'
	username = 'mtd7000'
	password = 'mtd'
	database = 'mtd'
	myConnection = pymysql.connect( host=hostname, user=username, passwd=password, db=database )
	cur = myConnection.cursor()
	insert = [IP,HWADR,str(datetime.now())]
	cur.execute("INSERT INTO mtd.blacklist VALUES (%s,%s,%s)" , insert )
	cur.close()
	myConnection.commit()

def get_age(start):
        current_time = time()
        difference = current_time - start
        hours = difference // 3600
        difference = difference % 3600
        mins = difference // 60
        seconds = difference % 60
        return int(seconds), int(mins), int(hours)

def my_response(incomming_packet):
        print "Attacker MAC: ", incomming_packet[Ether].src
	print "Attacker IP: ", incomming_packet[IP].src


	if (check_ip(incomming_packet[IP].src) == False):
		blacklist.append(incomming_packet[IP].src)
		update_database(str(incomming_packet[IP].src),str(incomming_packet[Ether].src))
		save_blacklist()

        global start
        if start == 0:
                start = time()
        current_seconds, current_minutes, current_hours = get_age(start)
        print "time since attack started: ",current_hours ,"h", current_minutes, "m", current_seconds,"s"
        if current_seconds < server_down and current_minutes < 1 and current_hours < 1:
                print "No more packets should be sent after: ", server_down - current_seconds, "s"
                response_packet_ip = IP(dst = "10.0.0.7", src = "10.0.0.1",  id = 1, ttl = 64, proto = "icmp")
		response_packet_icmp = ICMP(type = 0, code = 0, seq = 0x0, id = 0x0, chksum = 0x542b)
                load = "Blah"
                packet = response_packet_ip / response_packet_icmp / load
                packet.summary()
                send(packet)
        else:
                print "Tricking the attacker into thinking their attack was successful"
        
def main():
	global blacklist
	load_blacklist()
        sniff(filter = "icmp and src 10.0.0.7", iface = "eth1", prn = my_response)
main()


