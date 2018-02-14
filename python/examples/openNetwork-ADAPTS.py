import frenetic
from frenetic.syntax import *

#slave switch Port 1 > attacker2
#Slave switch Port 2 > attacker1
#slave switch Port 5 > user2
#Slave switch Port 6 > user1

class MyApp(frenetic.App):

    def __init__(self):
        frenetic.App.__init__(self)
        self.topo = {}

    def connected(self):
        root_switch = 90784643546441
        pol = Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.1")) >> SetPort(4)
        pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.2")) >> SetPort(3)
        pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.3")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.11")) >> SetPort(1)
        pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.12")) >> SetPort(1)
        pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.13")) >> SetPort(5)
        pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.14")) >> SetPort(5)
        pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.15")) >> SetPort(1)
        pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.21")) >> SetPort(1)
        pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.22")) >> SetPort(1)
        pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.23")) >> SetPort(5)
        pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.24")) >> SetPort(5)
        pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.25")) >> SetPort(1)

        slave_switch_1 = 69094430468420
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.1")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.2")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.3")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.11")) >> SetPort(3)
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.12")) >> SetPort(4)
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.13")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.14")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.15")) >> SetPort(6)
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.21")) >> SetPort(5)
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.22")) >> SetPort(1)
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.23")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.24")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1) & IP4DstEq("10.0.0.25")) >> SetPort(6)

        slave_switch_1_1 = 244438919251780
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.1")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.2")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.3")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.11")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.12")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.13")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.14")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.15")) >> SetPort(3)
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.21")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.22")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.23")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.24")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_1_1) & IP4DstEq("10.0.0.25")) >> SetPort(1)

        slave_switch_2 = 170388182923340
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.1")) >> SetPort(3)
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.2")) >> SetPort(3)
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.3")) >> SetPort(3)
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.11")) >> SetPort(3)
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.12")) >> SetPort(3)
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.13")) >> SetPort(5)
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.14")) >> SetPort(2)
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.15")) >> SetPort(3)
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.21")) >> SetPort(3)
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.22")) >> SetPort(3)
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.23")) >> SetPort(1)
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.24")) >> SetPort(4)
        pol = pol | Filter(SwitchEq(slave_switch_2) & IP4DstEq("10.0.0.25")) >> SetPort(3)

        app.update(pol)
        # The controller may already be connected to several switches on startup.
        # This ensures that we probe them too.
#        def handle_current_switches(switches):
#            for switch_id in switches:
#                self.switch_up(switch_id, switches[switch_id])
#        self.current_switches(callback=handle_current_switches)

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

