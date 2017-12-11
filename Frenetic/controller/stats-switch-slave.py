import sys, logging
import frenetic
import pymysql
from frenetic.syntax import *
from network_information_base import *
from tornado.ioloop import PeriodicCallback, IOLoop
from functools import partial

hostname = 'pcvm3-28.instageni.illinois.edu'
username = 'mtd7000'
password = 'mtd'
database = 'mtd'
myConnection = pymysql.connect( host=hostname, user=username, passwd=password, db=database )

def doUpdate( conn, switch, data ) :
    cur = conn.cursor()
    insert = (switch, data['port_no'], data['rx_packets'], data['tx_packets'], data['rx_bytes'], data['tx_bytes'], data['rx_dropped'], data['tx_dropped'], data['rx_errors'], data['tx_errors'], data['rx_fram_err'], data['rx_over_err'], data['rx_crc_err'], data['collisions'], data['rx_packets'], switch, data['port_no'], data['tx_packets'], switch, data['port_no'], data['rx_bytes'], switch, data['port_no'], data['tx_bytes'], switch, data['port_no'])
    cur.execute("INSERT INTO mtd.logs "
                "(switch_id, port_id, timestamp, rx_packets, tx_packets, rx_bytes, tx_bytes, rx_dropped, tx_dropped, rx_errors, tx_errors, rx_fram_err, rx_over_err, rx_crc_err, collisions, delta_rx_packets, delta_tx_packets, delta_rx_bytes, delta_tx_bytes) "
                "VALUES(%s, %s, NOW(), %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, "
                "%s-(select rx_packets from mtd.logs a2 where a2.switch_id = %s and a2.port_id = %s and a2.timestamp = (select max(timestamp) from mtd.logs a22 "
                "where a22.switch_id = a2.switch_id and a22.port_id = a2.port_id and a22.timestamp < NOW())),"
                "%s-(select tx_packets from mtd.logs a2 where a2.switch_id = %s and a2.port_id = %s and a2.timestamp = (select max(timestamp) from mtd.logs a22 "
                "where a22.switch_id = a2.switch_id and a22.port_id = a2.port_id and a22.timestamp < NOW())),"
                "%s-(select rx_bytes from mtd.logs a2 where a2.switch_id = %s and a2.port_id = %s and a2.timestamp = (select max(timestamp) from mtd.logs a22 "
                "where a22.switch_id = a2.switch_id and a22.port_id = a2.port_id and a22.timestamp < NOW())),"
                "%s-(select tx_bytes from mtd.logs a2 where a2.switch_id = %s and a2.port_id = %s and a2.timestamp = (select max(timestamp) from mtd.logs a22 "
                "where a22.switch_id = a2.switch_id and a22.port_id = a2.port_id and a22.timestamp < NOW()))"
                ");", insert)
    cur.close()
    conn.commit()
    oldData = data

class StatsApp1(frenetic.App):

  client_id = "stats"

  def __init__(self):
    frenetic.App.__init__(self)   
    self.nib = NetworkInformationBase(logging)  

  def connected(self):
    def handle_current_switches(switches):
      logging.info("Connected to Frenetic - Stats for switch: " + str(switches.keys()[0]))
      dpid = switches.keys()[0]
      self.nib.set_dpid(dpid)
      self.nib.set_ports( switches[dpid] )
      PeriodicCallback(self.count_ports, 5000).start()
    self.current_switches(callback=handle_current_switches)

  def print_count(self, future, switch):
    data = future.result()
#    myConnection = pymysql.connect( host=hostname, user=username, passwd=password, db=database )
    doUpdate(myConnection, switch, data)
#    myConnection.close()

  def count_ports(self):
    switch_id = self.nib.get_dpid()
#    print self.nib.all_ports()
    for port in self.nib.all_ports():
      ftr = self.port_stats(switch_id, str(port))
      f = partial(self.print_count, switch = switch_id)
      IOLoop.instance().add_future(ftr, f)

if __name__ == '__main__':
#  logging.basicConfig(\
#    stream = sys.stderr, \
#    format='%(asctime)s [%(levelname)s] %(message)s', level=logging.INFO \
#  )
  app = StatsApp1()
  app.start_event_loop()  

