# ADAPTS - Automated Defense against Advanced Persistent Threats using Suspiciousness Tracking

I. ADAPTS Experiment Setup

1) Experiment Steps

	a) Creating the slice and loading the rspec
	
		1) In GENI, create or load an existing empty slice
		2) Load the included rspec "Dolus-ADAPTS.rspec" into the slice. For our experiment we used the following aggregates: exogeni.net:tamuvmsite for the Controller, Kentucky instaGENI for slave-switch-1 and slave-switch-1-1
			Illinois instaGENI for the root-swtich, and Clemson instaGENI for slave-switch-2. At one point, we had a failure of the slave-switch-2 on Clemson instaGENI and even though the devices did eventually come back up, the
			network connectivity between slave-switch-2 and root-switch could never be re-established. Alos, when attempting to install frenetic, some aggregates failed to install due to memory or storage constraints.
			It may take some testing to and recreating the slice on different aggregates in order to generate a stable testbed. It may also be useful to select an XOLarge for the Controller node, which may not be available on all aggregates.
		3) Another thing you could try. Reserve your controller device in a seperate slice by itself. That way if there is a failure on another aggregate, you can delete that slice and try again
			without losing all your setup on the controller.
		4) Reserve resrouces and wait for all nodes to be ready (green)
	
	b) Controller Installation
		
		1) Locate the details to ssh into the controller (i.e. ssh neelyt@128.194.6.135 -i ~/.ssh/id_geni_ssh_rsa.ppk) and login to the controller
		2) Ensure that the controller is running Ubuntu 16.04. Newer releases may work but have not been tested.
			lsb_release -a
		3) Run the following commands in order:
			sudo apt-get update
			sudo apt-get install -y python-setuptools python-dev build-essential libcurl4-openssl-dev opam curl apt dpkg mininet m4 zlib1g-dev python-pip libmysqlclient-dev apache2 php libapache2-mod-php libssl-dev
			sudo adduser --disabled-password --gecos "" frenetic
			sudo -H -u frenetic opam init -y
 			sudo -H -u frenetic opam switch 4.06.0 # This used to be 4.04.2 however, with recent Frenetic updates, it may be possible (or necessary) to use 4.05.0 or 4.06.0
			sudo -i -u frenetic
			opam switch #Check and see if you get a message about the environment variables not being setup
			eval `opam config env`
			eval `opam config env` #seems like it would have to be run twice in a row sometimes, like the environment variables wouldn't stick the first time
			echo 'eval `opam config env`' >>/home/frenetic/.profile #Add the opam environmental config to the frenetic user's profile so it runs each time a shell is opened.
			echo 'eval `opam config env`' >>/home/frenetic/.profile #Since it seems like it must be run twice each time.
			opam switch #The previous commands should have fixed the environment variables message
			git clone https://github.com/Travisivart/ADAPTS.git
			CTRL+D
			ifconfig #NOTE the details of the public facing ethernet port and public facing ip address for future reference
		4) Install frenetic
			sudo -H -u frenetic opam pin add frenetic -y https://github.com/frenetic-lang/frenetic.git
		5)	Initial startup of frenetic with no switches connected
			sudo -i -u frenetic
			pip install pycurl frenetic mysql
			~/.opam/4.06.0/bin/frenetic http-controller --verbosity debug #Adjust to opam version used above if different from 4.04.2
		6) With frenetic running, view the current switches using the REST web service, at the moment you should get no results since no switches are yet connected.
			http://controller:9000/current_switches #(Use the IP or hostname: E.g. http://128.194.6.135:9000/current_switches)
	
	b.1) Optional Tests for Ping Connectivity
		1) At this point, you can optionally login to different devices on the same switch or different switches and check to see if they are able to ping each other (the switches are allowing traffic through). Most of the time this shouldn't work but depending on the aggregate, the results may vary.
	
	c) For each root-switch Installation
		
		1)	Each root-switch installation may be slightly different depending on how many devices are connected to each. Follow the #NOTE below at the ifconfig to ensure you do not disable the wrong ports.
		2)	Locate the details to ssh into the root-switch (i.e. ssh neelyt@pc1.instageni.illinois.edu -p 27202 -i ~/.ssh/id_geni_ssh_rsa.ppk) and login to the root-switch 
		3)	Run the following commands in order:
			sudo apt-get update 
			sudo apt-get install -y openvswitch-switch tshark
			sudo ovs-vsctl add-br br0 
			ifconfig #NOTE  For these steps, you only want to run these on ethx which are on the local network (10.0.0.x).
				It will most likely be what is displayed here but you should check the output of your ifconfig to make sure.
				Be CERTAIN to NOT disable the main adapter (it should have an address other than 10.0.0.x  and will probably be eth0,
				otherwise you will disable your switch and be unable to reach it) 
			sudo ifconfig eth1 0
			sudo ifconfig eth2 0
			sudo ifconfig eth3 0
			sudo ifconfig eth4 0
			sudo ifconfig eth5 0
			sudo ovs-vsctl add-port br0 eth1
			sudo ovs-vsctl add-port br0 eth2
			sudo ovs-vsctl add-port br0 eth3
			sudo ovs-vsctl add-port br0 eth4
			sudo ovs-vsctl add-port br0 eth5
			sudo ovs-vsctl set-controller br0 tcp:<controller IP Address from step 1.b.xiv.1 above>:6633 #(E.g. sudo ovs-vsctl set-controller br0 tcp:192.122.236.102:6633)
			sudo ovs-vsctl set-fail-mode br0 secure
		4)	With frenetic running, view the current switches using the REST web service, at the moment you should get one result, the root-switch. Note the DPID number displayed for the switch. This is a 14+ digit number used to identify the switch later.
			http://controller:9000/current_switches #(Use the IP or hostname: E.g. http://128.194.6.135:9000/current_switches)
			
	d) For each slave-switch Installation
	
		1)	Each slave-switch installation may be slightly different depending on how many devices are connected to each. Follow the #NOTE below at the ifconfig to ensure you do not disable the wrong ports.
		2)	Locate the details to ssh into the slave-switch (i.e. ssh neelyt@pcvm1-13.geni.it.cornell.edu -i ~/.ssh/id_geni_ssh_rsa.ppk) and login to the root-switch 
		3)	Run the following commands in order:
			sudo apt-get update 
			sudo apt-get install -y openvswitch-switch tshark #NOTE - tshark will ask if you want to allow non superusers to capture packets, select No.
			sudo ovs-vsctl add-br br0
			ifconfig #NOTE  For these steps, you only want to run these on ethx which are on the local network (10.0.0.x). It will most likely be what is displayed here but you should check the output of your ifconfig to make sure. Be CERTAIN to NOT disable the main adapter (it should have an address other than 10.0.0.x  and will probably be eth0, otherwise you will disable your switch and be unable to reach it) 
			sudo ifconfig eth1 0
			sudo ifconfig eth2 0
			sudo ifconfig eth3 0
			sudo ifconfig eth4 0
			sudo ifconfig eth5 0
			sudo ifconfig eth6 0
			sudo ifconfig eth7 0
			sudo ovs-vsctl add-port br0 eth1
			sudo ovs-vsctl add-port br0 eth2
			sudo ovs-vsctl add-port br0 eth3
			sudo ovs-vsctl add-port br0 eth4
			sudo ovs-vsctl add-port br0 eth5
			sudo ovs-vsctl add-port br0 eth6
			sudo ovs-vsctl add-port br0 eth7
			sudo ovs-vsctl set-controller br0 tcp:<controller IP Address from step 1.b.xiv.1 above>:6633 #(i. e. sudo ovs-vsctl set-controller br0 tcp:192.122.236.102:6633)
			sudo ovs-vsctl set-fail-mode br0 secure
		4)	Just as above in the root-switch steps, with frenetic running, view the current switches using the REST web service. Note the new DPID number displayed for the switch. This is a 14+ digit number used to identify the switch later.
			http://controller:9000/current_switches #(Use the IP or hostname: E.g. http://128.194.6.135:9000/current_switches)
		5)	Repeat step d) for each additional switch which will be controlled by frenetic
	
	e) Install LAMP (Apache, MySQL, PHP) on the Controller
	
		1) Run the following script
			sudo apt-get -y install mysql-server apache2 php libapache2-mod-php php-mysql php-curl #NOTE enter a password for the root mysql account which will be used later
		2) set the apache default site to point to the ADAPTS/html/ directory
			sudo su
			mv /var/www/html /var/www/html2
			ln -s /home/frenetic/ADAPTS/html/ /var/www/html #Modify this to point to the dolus-defensebypretense/html/ directory
		3) Create the database and load the testing dataset
			sudo -i -u frenetic
			mysql -u root -p -e "create database mtd";
		4) Create the mtd mysql user account
			mysql -u root -p -e "GRANT ALL PRIVILEGES ON mtd.* TO 'mtd'@'localhost' IDENTIFIED BY 'enterYourPasswordHere_1908237!'";
			mysql -u root -p mtd -e "source ./ADAPTS/mysql/buildDB.sql"
			mysql -u root -p mtd -e "INSERT INTO mtd.login (adminUID, username, passwd, salt) VALUES (1, 'mtd', 'a8fad5c1f1be9558dd4205a716e2e28ba708f79c4e0c0f49b7eb5875b2aa2cd5', 'bea813a1a3c21b757e5ebec4e5fbc7e8a706a1f7608af9cfc21c2265ceacd8ee');";
				#This setups the default username and password for the Admin UI to mtd/admin  (Need to create a better method for handling this)
		5) Update usernames and passwords to use the mtd user and password you just created. (We are going to work to simplfy this process)
			ADAPTS\python\policyUpdater.py
			ADAPTS\python\newUpdater.py
			ADAPTS\python\databaseConnection.py
			ADAPTS\python\calcSSByTime.py
			
			
	f) Run Frenetic and setup default routing on the controller
		1)	Login as the frenetic user and start up frenetic on the controller
			sudo -i -u frenetic
			eval `opam config env` #May need to run this again...twice
			frenetic http-controller --verbosity debug
		2)	In a separate controller ssh session you can run the repeater script and test pings on each device to ensure there is connectivity
			sudo -i -u frenetic
			python ~/.opam/4.06.0/packages.dev/frenetic/src/lang/python/frenetic/examples/repeater.py
		3)	You should see the details in the frenetic console, each switch gets updated to send all packets out all ports
		
	g) Develop basic routing for network
		1) Go ahead and start close frenetic if running and start it up again, you should have blank OpenFlow tables displayed in the console.
		2) Login to server1, server2, server3 and attempt to ping each of the other servers. All the pings should fail.
		1) The first step in getting the network setup properly invovles writing the initial setup rules by which frenetic will route traffic. This is done through experimentation with frenetic.
		2) Run an ifconfig on the root-switch. Note the ethX port and the ip address for each.
			From our rspec, 10.0.0.1 is server1, 10.0.0.2 is server2, and 10.0.0.3 is server3
			We also know from the rspec, that 10.0.0.241 goes to slave_switch_1 and 10.0.0.242 goes to slave_switch_2
			For each ethX device, X is the port which that device (ip address) is tied to.
			So if eth4 is 10.0.0.101 then we know that eth4 goes to server1, thus server1 is on Port 4
			Likewise if eth3 is 10.0.0.102 then we know that eth3 goes to server2 thus server2 is on Port 3
			It works similarly for the switches, if eth1 goes to 10.0.0.241 then we know that slave_switch_1 is on Port 1
			Using this information, we will write a script will will send packets, which go through the root-switch, with a particular IP destination, out the appropriate port
		3) In a 2nd controller ssh session, use the file ADAPTS/python/examples/openNetwork-ADAPTS.py as a baseline to get started.
			As you can see, there is a section where the root_switch is defined, let's work on setting up the root switch and allowing
			connectivity between server1, server2, and server3.
		3) We can modify the python script to use the details from step d.2 and begin building out our packet filters for frenetic
			The first line defines the root-switch with its DPID which you should have obtained above. Go ahead and update that value.
			root_switch = 90784643546441
			The next line establishes that any packet which goes through the root_switch (SwitchEq(root_switch)) AND also
			has an IP4 Destination of 10.0.0.1 (IP4DstEq("10.0.0.1")) is going to be sent out Port 4 (on the root_switch)
			Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.1")) >> SetPort(4)
			Likewise, we set up a line for each device on our network, for now we can just stick to server1, server2, and server3. Your code should look similar to this:
			root_switch = 90784643546441
			pol = Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.1")) >> SetPort(4)
			pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.2")) >> SetPort(3)
			pol = pol | Filter(SwitchEq(root_switch) & IP4DstEq("10.0.0.3")) >> SetPort(2)
			app.update(pol)
		4) Now save this file, and go ahead and run the file with python:
			python openNetwork-ADAPTS-new.py
		5) In the frenetic console, you should see the new OpenFlow rules in the table for the root_switch
		6) Now try to ping between each of the server devices, the pings should work and each server will have full network connectivity between each other
		7) Now repeat these steps for each switch on your network. Continue to build out your rules as in the example file openNetwork-ADAPTS.py
		8) As an additional tip, remember that on the root-switch, if you want to reach user1, user2, or user5, the only path is through the slave_switch_1, so all packets destined for one of those addresses will all go
			through the port leading to the slave-switch-1. Then once the packets reach slave-switch-1, that swtich will have its own rules to distribute the packets appropriately (user1 and user2 packets go to their appropriate ports and user5 goes to the slave-switch-1-1)
			
	h)	Create the packet captures for calculating the suspiciousness scores
		1) If you haven't already, we need to install tshark on each of the switches. When prompted if you want to allow non superusers to capture packets, select No.
			sudo apt-get install -y tshark
		2) Each switch will need to have a specific tshark script which is slightly different depending on which devices are connected to which ports.
			For example, for our root-switch, since server1, server2, and server3 are all connected to Ports 4, 3, and 2 respectively, our tshark script will only capture packets on eth4, eth3, and eth2 as such:
			sudo tshark -i eth2 -i eth3 -eth4
			You don't want to capture traffic on eth1 and eth5 because then we will have packet duplication in the captures (For example, a ping from server1 to user1 will get captured going to the root-switch, out the root-switch to the slave-switch-1, coming into slave-switch-1, then out to user1)
			We also need to tell it what sort of data we are going to capture and send that data to a csv file so that it can be easily uploaded into the database:
			sudo tshark -i eth2 -i eth3 -i eth4 -T fields  -e frame.number -e frame.time_relative -e frame.time_epoch -e frame.protocols -e frame.len -e eth.src -e eth.dst -e eth.type -e ip.proto -e ip.src -e ip.dst -e tcp.srcport -e tcp.dstport -e udp.srcport -e udp.dstport -E header=y -E separator=, -E occurrence=f > ~/root-capture-MM-DD-YYYY.csv
			These are all the fields which we are currently collecting data for, additional fields can be added to the above line but will also need to have a matching column added to the database in the packet_logs table
		3) Now we need to decide what sort of scripts will be run on each device. This includes scripts for data exfiltration (what we are interested in), DDoS attacks, pings, and other types of benign traffic.
			As a simple example for you to follow, we will just use server1, server2, and server3 on the root-switch
			We will have each server run a different type of script
			server1 - data exfiltration by scp from server3
				scp -i ~/.ssh/key neelyt@server3
			server2 - DDoS attack on server3
			server3 - ping server1
			We need to put a file on server3 for server1 to attempt to exfiltrate, on server3 go ahead and run this line:
				wget http://download.blender.org/peach/bigbuckbunny_movies/BigBuckBunny_640x360.m4v
			On server1, you will need to upload your GENI ssh key so that you can establish an ssh/scp connection between them, add your key on server1 to ~/.ssh/key and then you can run the next script to perform the data exfiltration
				scp -i ~/.ssh/key neelyt@server3:/users/neelyt/BigBuckBunny_640x360.m4v ./
			
			Now with the scripts ready to run on each device:
				start frenetic on the controller
				start the tshark packet capture on each switch
				run each script on each device
			While these scripts are running, you will see the captures running on each switch, once you are finished with your packet capture, you can kill each of the captures with CTRL+C
			You should now have a file on each of your switches which is similar to: root-capture-MM-DD-YYYY.csv
			Use a tool such as filezilla to copy the file down to your local machine for easier editing
			We will need to add two columns on the far left for the switch DPID as well as a trace_id (an identifier for this particular packet capture), you can find an example of the column layout in ADAPTS/tshark/headers for tshark once in csv format.txt
			Fill in the switch_id with the switch DPID and the trace_id with a unique trace_id identifier, for our first example it can just be the number 1
			Copy the file to the controller, which can also be done with Filezilla
			Log into mysql as the mtd user in the shell and run this script to load the packet capture data into the database:
				LOAD DATA LOCAL INFILE 'root-capture-MM-DD-YYYY.csv' INTO TABLE mtd.packet_logs FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 LINES (switch_id,trace_id,frame_number,frame_time_relative,frame_time,frame_protocols,frame_len,eth_src,eth_dst,eth_type,ip_proto,ip_src,ip_dst,tcp_srcport,tcp_dstport,udp_srcport,udp_dstport);
			You may also need to popuate the devices table with some of your network device data:
				INSERT INTO `mtd`.`devices`(`deviceID`,`name`,`type`,`ipv4`,`ipv6`,`mac`) VALUES(1,'root-switch',1,' ',' ',' ');
				INSERT INTO `mtd`.`devices`(`deviceID`,`name`,`type`,`ipv4`,`ipv6`,`mac`) VALUES(2,'server1',2,'10.0.0.1',' ',' ');
				INSERT INTO `mtd`.`devices`(`deviceID`,`name`,`type`,`ipv4`,`ipv6`,`mac`) VALUES(3,'server2',2,'10.0.0.2',' ',' ');
				INSERT INTO `mtd`.`devices`(`deviceID`,`name`,`type`,`ipv4`,`ipv6`,`mac`) VALUES(4,'server3',2,'10.0.0.3',' ',' ');
		
	h-alternative) Calculate suspiciousness scores for the preloaded dataset
		1)	Navigate to dolus-defensebypretense/python
		2)	Modify the calcSS.py and calcSSByTime.py scripts to use your database username (mtd) and password (whatever you set it to above)
		3)	Run the scripts one after the other
			python calcSS.py
			python calcSSByTime.py
		4) Suspiciousness Scores are calculated and stored in the suspiciousness_scores and the suspiciousness_scores_by_time tables
			
	i) The results are sored in the database and cab be viewed in the Admin UI by navigating to:
		http://(public ip for the controller)//pages/index.php
		Login with the default username/password of mtd/admin
		View the suspiciousness scores for each device under the suspiciousness menu item
		
		
		
		
Experiment example 1 - Basic ping network traffic

server1 - 
server2 - 
server3 - 
user1 - ping -c 100 10.0.0.1
user2 - ping -c 100 10.0.0.2
user5 - ping -c 100 10.0.0.3
attacker1 - ping -c 100 10.0.0.1
attacker2 - ping -c 100 10.0.0.2
attacker5 - ping -c 100 10.0.0.3
root-switch - sudo tshark -i eth2 -i eth3 -i eth4 -T fields  -e frame.number -e frame.time_relative -e frame.time_epoch -e frame.protocols -e frame.len -e eth.src -e eth.dst -e eth.type -e ip.proto -e ip.src -e ip.dst -e tcp.srcport -e tcp.dstport -e udp.srcport -e udp.dstport -E header=y -E separator=, -E occurrence=f > /users/neelyt/root-capture-02-12-2018.csv
slave-switch-1 - sudo tshark -i eth1 -i eth3 -i eth4 -i eth5 -T fields  -e frame.number -e frame.time_relative -e frame.time_epoch -e frame.protocols -e frame.len -e eth.src -e eth.dst -e eth.type -e ip.proto -e ip.src -e ip.dst -e tcp.srcport -e tcp.dstport -e udp.srcport -e udp.dstport -E header=y -E separator=, -E occurrence=f > /users/neelyt/slave-switch-1-capture-02-12-2018.csv
slave-switch-1-1 - sudo tshark -i eth1 -i eth3 -T fields  -e frame.number -e frame.time_relative -e frame.time_epoch -e frame.protocols -e frame.len -e eth.src -e eth.dst -e eth.type -e ip.proto -e ip.src -e ip.dst -e tcp.srcport -e tcp.dstport -e udp.srcport -e udp.dstport -E header=y -E separator=, -E occurrence=f > /users/neelyt/slave-switch-1-1-capture-02-12-2018.csv
controller - frenetic http-controller --verbosity debug
controller - python ADAPTS/python/examples/openNetwork-ADAPTS.py

LOAD DATA LOCAL INFILE 'capture-02-12-2018.csv' INTO TABLE mtd.packet_logs FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 LINES (switch_id,trace_id,frame_number,frame_time_relative,frame_time,frame_protocols,frame_len,eth_src,eth_dst,eth_type,ip_proto,ip_src,ip_dst,tcp_srcport,tcp_dstport,udp_srcport,udp_dstport);

python ADAPTS/python/calcSS.py 1
python ADAPTS/python/calcSSByTime.py 1

Experiment example 2 - Full attack traffic

server1 - vlc -Idummy /var/www/html/bbb.mp4 --repeat --sout '#std{access=udp{ttl=7},mux=ts,dst=10.0.0.11,port=1234}'
server2 - 
server3 - 
user1 - vlc -Idummy udp://@:1234 --sout file/ts:bbb-vlc.mp4
user2 - ping 10.0.0.2
user5 - wget http://10.0.0.1/bbb.mp4
attacker1 - sudo slowhttptest -c 1000 -B -g -o my_body_stats -i 110 -r 200 -s 8192 -t FAKEVERB -u http://10.0.0.1 -x 10 -p 3
attacker2 - scp -i .ssh/key neelyt@10.0.0.2:/var/www/html/bbb.mp4 ./
attacker5 - ping 10.0.0.3
root-switch - sudo tshark -i eth2 -i eth3 -i eth4 -T fields  -e frame.number -e frame.time_relative -e frame.time_epoch -e frame.protocols -e frame.len -e eth.src -e eth.dst -e eth.type -e ip.proto -e ip.src -e ip.dst -e tcp.srcport -e tcp.dstport -e udp.srcport -e udp.dstport -E header=y -E separator=, -E occurrence=f > /users/neelyt/root-capture-02-13-2018.csv
slave-switch-1 - sudo tshark -i eth1 -i eth3 -i eth4 -i eth5 -T fields  -e frame.number -e frame.time_relative -e frame.time_epoch -e frame.protocols -e frame.len -e eth.src -e eth.dst -e eth.type -e ip.proto -e ip.src -e ip.dst -e tcp.srcport -e tcp.dstport -e udp.srcport -e udp.dstport -E header=y -E separator=, -E occurrence=f > /users/neelyt/slave-switch-1-capture-02-13-2018.csv
slave-switch-1-1 - sudo tshark -i eth1 -i eth3 -T fields  -e frame.number -e frame.time_relative -e frame.time_epoch -e frame.protocols -e frame.len -e eth.src -e eth.dst -e eth.type -e ip.proto -e ip.src -e ip.dst -e tcp.srcport -e tcp.dstport -e udp.srcport -e udp.dstport -E header=y -E separator=, -E occurrence=f > /users/neelyt/slave-switch-1-1-capture-02-13-2018.csv
controller - frenetic http-controller --verbosity debug
controller - python ADAPTS/python/examples/openNetwork-ADAPTS.py

LOAD DATA LOCAL INFILE 'capture-02-13-2018.csv' INTO TABLE mtd.packet_logs FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 LINES (switch_id,trace_id,frame_number,frame_time_relative,frame_time,frame_protocols,frame_len,eth_src,eth_dst,eth_type,ip_proto,ip_src,ip_dst,tcp_srcport,tcp_dstport,udp_srcport,udp_dstport);

python ADAPTS/python/calcSS.py 2
python ADAPTS/python/calcSSByTime.py 2


Experiment example 3 - Full attack traffic with blocking

server1 - 
server2 - iperf -s
server3 - 
user1 - iperf -c 10.0.0.2
user2 - ping 10.0.0.2
user5 - wget http://10.0.0.1/bbb.mp4
attacker1 - scp -i .ssh/key neelyt@10.0.0.3:/var/www/html/bbb.mp4 ./
attacker2 - scp -i .ssh/key neelyt@10.0.0.2:/var/www/html/bbb.mp4 ./
attacker5 - sudo slowhttptest -c 1000 -B -g -o my_body_stats -i 110 -r 200 -s 8192 -t FAKEVERB -u http://10.0.0.1 -x 10 -p 3
root-switch - sudo tshark -i eth2 -i eth3 -i eth4 -T fields  -e frame.number -e frame.time_relative -e frame.time_epoch -e frame.protocols -e frame.len -e eth.src -e eth.dst -e eth.type -e ip.proto -e ip.src -e ip.dst -e tcp.srcport -e tcp.dstport -e udp.srcport -e udp.dstport -E header=y -E separator=, -E occurrence=f > /users/neelyt/root-capture-02-14-2018.csv
slave-switch-1 - sudo tshark -i eth1 -i eth3 -i eth4 -i eth5 -T fields  -e frame.number -e frame.time_relative -e frame.time_epoch -e frame.protocols -e frame.len -e eth.src -e eth.dst -e eth.type -e ip.proto -e ip.src -e ip.dst -e tcp.srcport -e tcp.dstport -e udp.srcport -e udp.dstport -E header=y -E separator=, -E occurrence=f > /users/neelyt/slave-switch-1-capture-02-14-2018.csv
slave-switch-1-1 - sudo tshark -i eth1 -i eth3 -T fields  -e frame.number -e frame.time_relative -e frame.time_epoch -e frame.protocols -e frame.len -e eth.src -e eth.dst -e eth.type -e ip.proto -e ip.src -e ip.dst -e tcp.srcport -e tcp.dstport -e udp.srcport -e udp.dstport -E header=y -E separator=, -E occurrence=f > /users/neelyt/slave-switch-1-1-capture-02-14-2018.csv
controller - frenetic http-controller --verbosity debug
controller - python ADAPTS/python/examples/openNetwork-ADAPTS.py

LOAD DATA LOCAL INFILE 'capture-02-14-2018.csv' INTO TABLE mtd.packet_logs FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 LINES (switch_id,trace_id,frame_number,frame_time_relative,frame_time,frame_protocols,frame_len,eth_src,eth_dst,eth_type,ip_proto,ip_src,ip_dst,tcp_srcport,tcp_dstport,udp_srcport,udp_dstport);

python ADAPTS/python/calcSS.py 3
python ADAPTS/python/calcSSByTime.py 3



DELETE FROM mtd.suspiciousness_scores; 
INSERT mtd.suspiciousness_scores SELECT * FROM mtd.suspiciousness_scores_1;
DELETE FROM mtd.suspiciousness_scores_by_time; 
INSERT mtd.suspiciousness_scores_by_time SELECT * FROM mtd.suspiciousness_scores_by_time_1;

DELETE FROM mtd.suspiciousness_scores; 
INSERT mtd.suspiciousness_scores SELECT * FROM mtd.suspiciousness_scores_2;
DELETE FROM mtd.suspiciousness_scores_by_time; 
INSERT mtd.suspiciousness_scores_by_time SELECT * FROM mtd.suspiciousness_scores_by_time_2;

DELETE FROM mtd.suspiciousness_scores; 
INSERT mtd.suspiciousness_scores SELECT * FROM mtd.suspiciousness_scores_3;
DELETE FROM mtd.suspiciousness_scores_by_time; 
INSERT mtd.suspiciousness_scores_by_time SELECT * FROM mtd.suspiciousness_scores_by_time_3;

DELETE FROM mtd.suspiciousness_scores; 
INSERT mtd.suspiciousness_scores SELECT * FROM mtd.suspiciousness_scores_test_1_w;
DELETE FROM mtd.suspiciousness_scores_by_time; 
INSERT mtd.suspiciousness_scores_by_time SELECT * FROM mtd.suspiciousness_scores_by_time_1_w;

DELETE FROM mtd.suspiciousness_scores; 
INSERT mtd.suspiciousness_scores SELECT * FROM mtd.suspiciousness_scores_test_2_w;
DELETE FROM mtd.suspiciousness_scores_by_time; 
INSERT mtd.suspiciousness_scores_by_time SELECT * FROM mtd.suspiciousness_scores_by_time_2_w;

DELETE FROM mtd.suspiciousness_scores; 
INSERT mtd.suspiciousness_scores SELECT * FROM mtd.suspiciousness_scores_test_3_w;
DELETE FROM mtd.suspiciousness_scores_by_time; 
INSERT mtd.suspiciousness_scores_by_time SELECT * FROM mtd.suspiciousness_scores_by_time_3_w;