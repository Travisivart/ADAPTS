import frenetic
from frenetic.syntax import *
from frenetic.packet import *

#slave switch Port 1 > attacker2
#Slave switch Port 2 > attacker1
#slave switch Port 5 > user2
#Slave switch Port 6 > user1

class MyApp(frenetic.App):

    def __init__(self):
        frenetic.App.__init__(self)
        self.topo = {}

    def connected(self):
#        pol = Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.5")) >> SetPort(1)
#        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.5")) >> SetPort(2)
#        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.5")) >> SetPort(3)
#        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.5")) >> SetPort(4)
#        pol = Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.5")) >> SetPort(5)
#        pol = Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.5")) >> SetPort(5)
        pol = Filter(SwitchEq(51570677359425) & IP4SrcEq("10.0.0.7") & IP4DstEq("10.0.0.5")) >> SendToController("attacker")
#        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.5")) >> SetPort(7)
#        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.5")) >> SetPort(8)
#        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.7")) >> SetPort(1)
        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.7")) >> SendToController("attacker")
#        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.7")) >> SetPort(3)
#        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.7")) >> SetPort(4)
#        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.7")) >> SetPort(5)
#        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.7")) >> SetPort(6)
#        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.7")) >> SetPort(7)
#        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.7")) >> SetPort(8)
        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.6")) >> SetPort(5)
        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.8")) >> SetPort(1)

        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.4")) >> SetPort(3)

        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.9")) >> SetPort(7)
        pol = pol | Filter(SwitchEq(51570677359425) & IP4DstNotEq("10.0.0.5") & IP4DstNotEq("10.0.0.7") & IP4DstNotEq("10.0.0.6") & IP4DstNotEq("10.0.0.8") & IP4DstNotEq("10.0.0.4") & IP4DstNotEq("10.0.0.9")) >> SetPort(4)


        pol = pol | Filter(SwitchEq(99947915534402) & IP4DstEq("10.0.0.2")) >> SetPort(1)
        pol = pol | Filter(SwitchEq(99947915534402) & IP4DstEq("10.0.0.1")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(99947915534402) & IP4DstNotEq("10.0.0.2") & IP4DstNotEq("10.0.0.1") & IP4DstNotEq("10.0.0.3")) >> SetPort(3)
        pol = pol | Filter(SwitchEq(99947915534402) & IP4DstEq("10.0.0.3")) >> SetPort(4)
        app.update(pol)
        # The controller may already be connected to several switches on startup.
        # This ensures that we probe them too.
#        def handle_current_switches(switches):
#            for switch_id in switches:
#                self.switch_up(switch_id, switches[switch_id])
#        self.current_switches(callback=handle_current_switches)

 
    def packet_in(self, dpid, port_id, payload):
        pkt = Packet.from_payload(dpid, port_id, payload)
        print("dpid:" + str(dpid))
        print("port_id:" + str(port_id))
        print("payload:" + str(payload))
        print("pkt.ip4Dst:" + str(pkt.ip4Dst))
        print("pkt.ethType:" + str(pkt.ethType))
        print("pkt.ip4Src:" + str(pkt.ip4Src))
        if pkt.ip4Src == "10.0.0.7":
            print("1")
            pkt.ip4Dst = "10.0.0.6"
            print("2")
            self.pkt_out(dpid, pkt.to_payload(), SetPort(5))
            print("3")
        if pkt.ip4Dst == "10.0.0.7":
#            if pkt.ethType == 0x806:
            pkt.ip4Src = "10.0.0.5"
            print("4")
            self.pkt_out(dpid, pkt.to_payload(), SetPort(2))
        

    def policy(self):
        return Union(self.sw_policy(sw) for sw in self.topo.keys())

    def sw_policy(self, sw):
        ports = self.topo[sw]
        p = Union(self.port_policy(in_port, ports) for in_port in ports)
        return Filter( SwitchEq(sw) ) >> p

    def port_policy(self, in_port, ports):
        p = SetPort([port for port in ports if port != in_port])
        return Filter( PortEq(in_port) ) >> p

    def switch_up(self,switch_id,ports):
        self.topo[switch_id] = ports
        app.update(self.policy())

    def switch_down(self,switch_id):
        del self.topo[switch_id]
        app.update(self.policy())

    def port_up(self,switch_id, port_id):
        self.topo[switch_id].append(port_id)
        app.update(self.policy())

    def port_down(self,switch_id, port_id):
        self.topo[switch_id].remove(port_id)
        app.update(self.policy())

app = MyApp()
app.start_event_loop()

