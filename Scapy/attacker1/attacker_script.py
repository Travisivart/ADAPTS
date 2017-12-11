import logging
logging.getLogger("scapy.runtime").setLevel(logging.ERROR)
from scapy.all import *

print(srloop(IP(dst="10.0.0.1")/ICMP()/"Blah", inter = 1, timeout = .01, prnfail=lambda x:x.summary()))
