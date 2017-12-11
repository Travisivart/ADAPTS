#!/bin/bash

sudo apt-get update

sudo apt-get install openvswitch-switch
#Create a new bridge which the controller will monitor
sudo ovs-vsctl add-br br0

#Turn off existing eth ports which go to machines
sudo ifconfig eth1 0
sudo ifconfig eth2 0
sudo ifconfig eth3 0

#Move the eth ports to the new bridge
sudo ovs-vsctl add-port br0 eth1
sudo ovs-vsctl add-port br0 eth2
sudo ovs-vsctl add-port br0 eth3

#Point the control to the <controller_ip> address
sudo ovs-vsctl set-controller br0 tcp:<controller_ip>:6633

#Set fail mode to secure so if the controller fails, all traffic is refused
sudo ovs-vsctl set-fail-mode br0 secure

#start frenetic
frenetic http-controller --verbosity debug

#login to attacker
#ping server
#ping should fail
#open a new ssh shell on the controller
#python ~/.opam/4.03.0/packages.dev/frenetic/lang/python/frenetic/examples/repeater.py
#The ping should now route to all machines