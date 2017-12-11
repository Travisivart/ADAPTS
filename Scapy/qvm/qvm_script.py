from datetime import datetime
from scapy.all import *
from random import randint
from time import time
import csv


server_down = randint(20,40)
count = 0
start = 0

#def write_csv(info):
 #       try:
  #              csvfile = open('info.csv', 'a+')
   #             fieldnames = ['Time', 'IP Source', 'IP Destination',"Attack Time"]
    #            writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
                #if csvfile.tell() == 0:
                        #writer.writeheader()
     #           writer.writerow({'Time': str(datetime.now()), 'IP Source': info[IP].src, 'IP Destination' : info[IP].dst, 'Attack Time' : str(get_age(start))+"s"})
      #          csvfile.close()
       # except:
        #        print ("CSV file couldn't be loaded.")


def my_response(incomming_packet):
    print incomming_packet.show()
    server_ip = "10.0.0.1"
    #server_mac = "02:90:40:48:3c:38"
    global start
    if start == 0:
        start = time()
    current_seconds, current_minutes, current_hours = get_age(start)
    print ("time since attack started: ", current_hours, "h", current_minutes, "m", current_seconds, "s")
    if current_seconds < server_down and current_minutes < 1 and current_hours < 1:
        print ("No more packets should be sent after: ", server_down - current_seconds, "s")
        #response_packet_ether = Ether(type = incomming_packet[Ether].type, src = server_mac, dst = incomming_packet[Ether].src)
#        print("test1")
	response_packet_ip = IP(dst=incomming_packet[IP].src, src = server_ip, id=1, ttl=64, proto="icmp", version = 4L, ihl = incomming_packet[IP].ihl, frag = incomming_packet[IP].frag, len = incomming_packet[IP].len)
 #       print("test2")
#        response_packet_ip = IP(dst="10.0.0.7", src="10.0.0.1", id=RandShort(), ttl=64, proto="icmp")
        response_packet_icmp = ICMP(type=0, code=0, seq=0x0, id=0x0, chksum=0x542b)
 #       print("test3")
        load = "Blah"
	packet = response_packet_ip/response_packet_icmp/load
        send(packet)
	print packet[IP].dst
    elif (current_seconds >= server_down and current_minutes < 1 and current_hours < 1):
        print ("Tricking the attacker into thinking their attack was successful")
        write_csv(incomming_packet)

def get_age(start):
        current_time = time()

        difference = current_time - start
        hours = difference // 3600
        difference = difference % 3600
        mins = difference // 60
        seconds = difference % 60
        return int(seconds), int(mins), int(hours)


def main():
    #sniff(filter="icmp and src 10.0.0.7 or icmp and src 10.0.0.8 or icmp and src 10.0.0.9", iface="eth1", prn=my_response)
    #sniff(filter="icmp and src 10.0.0.7", iface ="eth1", prn=my_response)
    sniff(filter="icmp and src 10.0.0.7 or src 10.0.0.8 or src 10.0.0.9", iface="eth1", prn=my_response)
    #sniff(iface="eth1", prn=my_response)
main()

