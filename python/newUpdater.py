import MySQLdb
import MySQLdb.cursors
import requests, json
import frenetic
from frenetic.syntax import *
import urllib2

nodes = {   "server1" : "10.0.0.1",
            "server2" : "10.0.0.2",
            "server3" : "10.0.0.3",
            "qvm" : "10.0.0.4",
            "user1" : "10.0.0.5",
            "user2" : "10.0.0.6",
            "attacker1": "10.0.0.7",
            "attacker2": "10.0.0.8",
            "attacker3": "10.0.0.9"
        }

def get_updated_policy(current_policy, new_policy):
    if len(current_policy) == 0:
        current_policy =  new_policy
    else:
        current_policy = current_policy + " | " + new_policy

    return current_policy


def push_new_policy(new_policy):
    Pol = eval(new_policy)
    policy = json.dumps(Pol.to_json(),indent=4, sort_keys=False, separators=(',', ': '),ensure_ascii=False)

    url = 'http://localhost:9000/adminPol/update_json'
    req = urllib2.Request(url, str(policy), {'Content-Type': 'application/json'})
    f = urllib2.urlopen(req)
    f.close()

def generate_policy(sender, receiver, switch, sender_port, receiver_port, switch2):
    if switch2 == None:
        return ("Filter(SwitchEq('" + str(switch) + "') & IP4DstEq('" +
            str(nodes[receiver])+"') & IP4SrcEq('" + str(nodes[sender]) +
            "')) >> SetPort(" + str(receiver_port) + ") | Filter(SwitchEq('" +
            str(switch) + "') & IP4DstEq('" + str(nodes[sender]) + "') & IP4SrcEq('" +
            str(nodes[receiver]) + "')) >> SetPort(" + str(sender_port) + ")"
            )
    else:
        return ("Filter(SwitchEq('" + str(switch) + "') & IP4DstEq('" +
            str(nodes[receiver]) +"') & IP4SrcEq('" + str(nodes[sender]) +
            "')) >> SetPort(4) | Filter(SwitchEq('" + str(switch2) +
            "') & IP4DstEq('"+ str(nodes[receiver]) +"') & IP4SrcEq('" +
             str(nodes[sender]) +"')) >> SetPort("+str(receiver_port)+")"+
             "| Filter(SwitchEq('" + str(switch2) + "') & IP4DstEq('" +
             str(nodes[sender]) +"') & IP4SrcEq('" + str(nodes[receiver]) +
             "')) >> SetPort(3) | Filter(SwitchEq('" + str(switch) +
             "') & IP4DstEq('"+ str(nodes[sender]) +"') & IP4SrcEq('" +
              str(nodes[receiver]) +"')) >> SetPort("+str(sender_port)+")")

def update_policy(sender, receiver, switch, sender_port,receiver_port, current_policy, switch2):
    new_policy = generate_policy(sender, receiver, switch, sender_port, receiver_port, switch2)
    current_policy = get_updated_policy(current_policy, new_policy)
    push_new_policy(current_policy)
    print current_policy
    return current_policy


slaveswitch = 51570677359425
rootswitch = 99947915534402
#pol = Filter(SwitchEq(slaveswitch) & IP4DstEq("10.0.0.5")) >> SetPort(6)
#pol = pol | Filter(SwitchEq(slaveswitch) & IP4DstEq("10.0.0.7")) >> SetPort(2)
#pol = pol | Filter(SwitchEq(slaveswitch) & IP4DstEq("10.0.0.6")) >> SetPort(5)
#pol = pol | Filter(SwitchEq(slaveswitch) & IP4DstEq("10.0.0.8")) >> SetPort(1)
#pol = pol | Filter(SwitchEq(slaveswitch) & IP4DstEq("10.0.0.4")) >> SetPort(3)
#pol = pol | Filter(SwitchEq(slaveswitch) & IP4DstEq("10.0.0.9")) >> SetPort(7)
#pol = pol | Filter(SwitchEq(slaveswitch) & IP4DstNotEq("10.0.0.5") & IP4DstNotEq("10.0.0.7") & IP4DstNotEq("10.0.0.6") & IP4DstNotEq("10.0.0.8") & IP4DstNotEq("10.0.0.4") & IP4DstNotEq("10.0.0.9")) >> SetPort(4)



#pol = pol | Filter(SwitchEq(rootswitch) & IP4DstEq("10.0.0.2")) >> SetPort(1)
#pol = pol | Filter(SwitchEq(rootswitch) & IP4DstEq("10.0.0.1")) >> SetPort(2)
#pol = pol | Filter(SwitchEq(rootswitch) & IP4DstNotEq("10.0.0.2") & IP4DstNotEq("10.0.0.1") & IP4DstNotEq("10.0.0.3")) >> SetPort(3)
#pol = pol | Filter(SwitchEq(rootswitch) & IP4DstEq("10.0.0.3")) >> SetPort(4)
#app.update(pol)
def main():

    current_policy = ""

    while True:
        db = MySQLdb.connect(user='root', passwd='1234', host='127.0.0.1', db='mtd',cursorclass=MySQLdb.cursors.DictCursor)
        cursor = db.cursor()

        user_input = raw_input("Enter policy: ")

        # Slave switch policies direct neighbors

        if user_input == 'user1 to user2' or user_input == 'user2 to user1':
            current_policy = update_policy("user1", "user2", slaveswitch, 6,5, current_policy,None)

        elif user_input == 'user1 to attacker1' or user_input == 'attacker1 to user1':
            current_policy = update_policy("user1", "attacker1", slaveswitch, 6,2, current_policy,None)

        elif user_input == 'user1 to attacker2' or user_input == 'attacker2 to user1':
            current_policy = update_policy("user1", "attacker2", slaveswitch, 6,1, current_policy,None)

        elif user_input == 'user1 to attacker3' or user_input == 'attacker3 to user1':
            current_policy = update_policy("user1", "attacker3", slaveswitch, 6,7, current_policy,None)

        elif user_input == 'user1 to qvm' or user_input == 'qvm to user1':
                current_policy = update_policy("user1", "qvm", slaveswitch, 6,3, current_policy,None)

        elif user_input == 'user2 to attacker1' or user_input == 'attacker1 to user2':
            current_policy = update_policy("user2", "attacker1", slaveswitch, 5,2, current_policy,None)

        elif user_input == 'user2 to attacker2' or user_input == 'attacker2 to user2':
            current_policy = update_policy("user2", "attacker2", slaveswitch, 5,1, current_policy,None)

        elif user_input == 'user2 to attacker3' or user_input == 'attacker3 to user2':
            current_policy = update_policy("user2", "attacker3", slaveswitch, 5,7, current_policy,None)

        elif user_input == 'user2 to qvm' or user_input == 'qvm to user2':
            current_policy = update_policy("user2", "qvm", slaveswitch, 5,3, current_policy,None)

        elif user_input == 'attacker1 to qvm' or user_input == 'qvm to attacker1':
            current_policy = update_policy("attacker1", "qvm", slaveswitch, 2,3, current_policy,None)

        elif user_input == 'attacker1 to attacker2' or user_input == 'attacker2 to attacker1':
            current_policy = update_policy("attacker1", "attacker2", slaveswitch, 2,1, current_policy,None)

        elif user_input == 'attacker1 to attacker3' or user_input == 'attacker3 to attacker1':
            current_policy = update_policy("attacker1", "attacker3", slaveswitch, 2,7, current_policy,None)

        elif user_input == 'attacker2 to attacker3' or user_input == 'attacker3 to attacker2':
            current_policy = update_policy("attacker2", "attacker3", slaveswitch, 1,7, current_policy,None)

        elif user_input == 'attacker2 to qvm' or user_input == 'qvm to attacker2':
            current_policy = update_policy("attacker2", "qvm", slaveswitch, 1,3, current_policy,None)

        elif user_input == 'attacker3 to qvm' or user_input == 'qvm to attacker3':
            current_policy = update_policy("attacker3", "qvm", slaveswitch, 7,3, current_policy,None)

        # Root swith policies direct neighbors

        elif user_input == 'server1 to server2' or user_input == 'server2 to server1':
            current_policy = update_policy("server1", "server2", rootswitch, 2,1, current_policy,None)

        elif user_input == 'server1 to server3' or user_input == 'server3 to server1':
                 current_policy = update_policy("server1", "server3", rootswitch, 2,4, current_policy,None)

        elif user_input == 'server2 to server3' or user_input == 'server3 to server2':
            current_policy = update_policy("server1", "server2", rootswitch, 4,2, current_policy,None)

        # cross swith user1 to servers

        elif user_input == 'user1 to server1' or user_input == 'server1 to user1':
            current_policy = update_policy("user1", "server1", slaveswitch, 6,2, current_policy, rootswitch)

        elif user_input == 'user1 to server2' or user_input == 'server2 to user1':
            current_policy = update_policy("user1", "server2", slaveswitch, 6,1, current_policy, rootswitch)

        elif user_input == 'user1 to server3' or user_input == 'server3 to user1':
            current_policy = update_policy("user1", "server3", slaveswitch, 6,4, current_policy, rootswitch)

        # cross switch user2 to servers

        elif user_input == 'user2 to server1' or user_input == 'server2 to user2':
            current_policy = update_policy("user2", "server1", slaveswitch, 5,2, current_policy, rootswitch)

        elif user_input == 'user2 to server2' or user_input == 'server2 to user2':
            current_policy = update_policy("user2", "server2", slaveswitch, 5,1, current_policy, rootswitch)

        elif user_input == 'user2 to server3' or user_input == 'server3 to user2':
            current_policy = update_policy("user2", "server3", slaveswitch, 5,4, current_policy, rootswitch)


        # cross switch attacker1 to servers

        elif user_input == 'attacker1 to server1' or user_input == 'server2 to attacker1':
            current_policy = update_policy("attacker1", "server1", slaveswitch, 2,2, current_policy, rootswitch)

        elif user_input == 'attacker1 to server2' or user_input == 'server2 to attacker1':
            current_policy = update_policy("attacker1", "server2", slaveswitch, 2,1, current_policy, rootswitch)

        elif user_input == 'attacker1 to server3' or user_input == 'server3 to attacker1':
            current_policy = update_policy("attacker1", "server3", slaveswitch, 2,4, current_policy, rootswitch)


        # cross switch attacker2 to servers

        elif user_input == 'attacker2 to server1' or user_input == 'server2 to attacker2':
            current_policy = update_policy("attacker2", "server1", slaveswitch, 1,2, current_policy, rootswitch)

        elif user_input == 'attacker2 to server2' or user_input == 'server2 to attacker2':
            current_policy = update_policy("attacker2", "server2", slaveswitch, 1,1, current_policy, rootswitch)

        elif user_input == 'attacker2 to server3' or user_input == 'server3 to attacker2':
            current_policy = update_policy("attacker2", "server3", slaveswitch, 1,4, current_policy, rootswitch)

        # cross switch attacker3 to servers

        elif user_input == 'attacker3 to server1' or user_input == 'server2 to attacker3':
            current_policy = update_policy("attacker3", "server1", slaveswitch, 7,2, current_policy, rootswitch)

        elif user_input == 'attacker3 to server2' or user_input == 'server2 to attacker3':
            current_policy = update_policy("attacker3", "server2", slaveswitch, 7,1, current_policy, rootswitch)

        elif user_input == 'attacker3 to server3' or user_input == 'server3 to attacker3':
            current_policy = update_policy("attacker3", "server3", slaveswitch, 7,4, current_policy, rootswitch)

        # cross switch qvm to servers

        elif user_input == 'qvm to server1' or user_input == 'server2 to qvm':
            current_policy = update_policy("qvm", "server1", slaveswitch, 3,2, current_policy, rootswitch)

        elif user_input == 'qvm to server2' or user_input == 'server2 to qvm':
            current_policy = update_policy("qvm", "server2", slaveswitch, 3,1, current_policy, rootswitch)

        elif user_input == 'qvm to server3' or user_input == 'server3 to qvm':
            current_policy = update_policy("qvm", "server3", slaveswitch, 3,4, current_policy, rootswitch)

        elif user_input == 'exit':
            break

        else:
            print "Invalid Policy"


        cursor = db.cursor()

        db.close()

if __name__ == '__main__':
    main()
