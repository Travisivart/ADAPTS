import frenetic
from frenetic.syntax import *
from frenetic.packet import *

class MyApp(frenetic.App):



    def __init__(self):
        frenetic.App.__init__(self)

    def connected(self):
        self.update( id >>SendToController("repeater_app"))
        # The controller may already be connected to several switches on startup.
        # This ensures that we probe them too.
        def handle_current_switches(switches):
            for switch_id in switches:
                self.switch_up(switch_id, switches[switch_id])
        self.current_switches(callback=handle_current_switches)

    def switch_up(self,switch_id,ports):
        print "switch_up(switch_id=%s)" % switch_id

    def switch_down(self,switch_id):
        print "switch_down(switch_id=%s)" % switch_id

    def port_up(self,switch_id, port_id):
        print "port_up(switch_id=%s, port_id=%d)" % (switch_id, port_id)

    def port_down(self,switch_id, port_id):
        print "port_down(switch_id=%s, port_id=%d)" % (switch_id, port_id)

    def packet_in(self,switch_id, port_id, payload):
        pkt = Packet.from_payload(switch_id, port_id, payload)
        print "packet in from switch:",switch_id," port:",port_id,"SrcIP",pkt.ip4Src,"DestIP",pkt.ip4Dst
        

app = MyApp()
app.start_event_loop()

