# ADAPTS - Automated Defense against Advanced Persistent Threats using Suspiciousness Tracking

I. ADAPTS Experiment Setup

1) GENI Slice Creation

	a) Creating the slice and loading the rspec
	
		i) In GENI, create or load an existing empty slice
		ii) Load the included rspec "adapts-geni-slice.rspec" into the slice (NOTE: For our experiment we used the Cornell InstaGENI aggregate, results varried depending on the aggregate. Some aggregates failed to install Frenetic later on. It may also be useful to select an XOLarge for the Controller node, which may not be available on all aggregates)
		iii) Reserve resrouces and wait for all nodes to be ready (green)
	
	b) Controller Installation
		
		i) Locate the details to ssh into the controller (i.e. ssh neelyt@pcvm1-12.geni.it.cornell.edu -i ~/.ssh/id_geni_ssh_rsa.ppk) and login to the controller
		ii) Ensure that the controller is running Ubuntu 16.04.
			lsb_release -a
		iii)  Run the following commands in order:
			sudo apt-get update
			#sudo apt-get upgrade -y #If you do choose to run the upgrade, if you are prompted to keep or overwrite files, keep all the local installed files. If it asks which drive to install GRUB to, select all drives.
			sudo apt-get install -y python-setuptools python-dev build-essential libcurl4-openssl-dev opam curl apt dpkg mininet m4 zlib1g-dev python-pip libmysqlclient-dev apache2 php libapache2-mod-php
			sudo adduser --disabled-password --gecos "" frenetic
			sudo rm -R /var/www/html
			sudo ln -s /home/frenetic/dolus-defensebypretense/html/ /var/www/html
			sudo -H -u frenetic opam init -y
			sudo -H -u frenetic opam switch 4.04.2 #With recent Frenetic updates, it may be possible (or necessary) to use 4.05.0 and 4.06.0
			sudo -i -u frenetic
			eval `opam config env`
			eval `opam config env` #seems like it would have to be run twice in a row sometimes, like the environment variables wouldn't stick the first time
			sudo easy_install pip
			sudo easy_install pycurl
			sudo pip install pycurl
			opam pin add frenetic -y https://github.com/frenetic-lang/frenetic.git
			sudo pip install frenetic ryu mysqlclient pycurl
			~/.opam/4.04.2/bin/frenetic http-controller --verbosity debug #Adjust to opam version used above if different from 4.04.2
			ifconfig #NOTE the details of the public facing ethernet port and public facing ip address for future reference
	
	c) root-switch Installation
		
		i)  Locate the details to ssh into the root-switch (i.e. ssh neelyt@pcvm1-12.geni.it.cornell.edu -i ~/.ssh/id_geni_ssh_rsa.ppk) and login to the root-switch 
		ii)  Run the following commands in order:
			sudo apt-get update 
			sudo apt-get install -y openvswitch-switch 
			sudo ovs-vsctl add-br br0 
			ifconfig #NOTE  For these steps, you only want to run these on ethx which are on the local network (10.0.0.x). It will most likely be what is displayed here but you should check the output of your ifconfig to make sure. Be CERTAIN to NOT disable the main adapter (it should have an address other than 10.0.0.x  and will probably be eth0, otherwise you will disable your switch and be unable to reach it) 
			sudo ifconfig eth1 0
			sudo ifconfig eth2 0
			sudo ifconfig eth3 0
			sudo ifconfig eth4 0
			sudo ovs-vsctl add-port br0 eth1
			sudo ovs-vsctl add-port br0 eth2
			sudo ovs-vsctl add-port br0 eth3
			sudo ovs-vsctl add-port br0 eth4
			sudo ovs-vsctl set-controller br0 tcp:<controller IP Address from step 1.b.xiv.1 above>:6633 #(i. e. sudo ovs-vsctl set-controller br0 tcp:192.122.236.102:6633)
			sudo ovs-vsctl set-fail-mode br0 secure
			
	d) slave-switch Installation
	
		i)  Locate the details to ssh into the slave-switch (i.e. ssh neelyt@pcvm1-13.geni.it.cornell.edu -i ~/.ssh/id_geni_ssh_rsa.ppk) and login to the root-switch 
		ii)  Run the following commands in order:
			sudo apt-get update 
			sudo apt-get install -y openvswitch-switch 
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
	
	e) Install LAMP (Apache, MySQL, PHP) on the Controller
	
		i) Run the following script
			sudo apt-get -y install mysql-server apache2 php libapache2-mod-php php-mysql php-curl #NOTE enter a password for the root mysql account which will be used later
		ii) set the apache default site to point to the dolus-defensebypretense/html/ directory
			sudo su
			mv /var/www/html /var/www/html2
			ln -s dolus-defensebypretense/html/ /var/www/html #Modify this to point to the dolus-defensebypretense/html/ directory
		iii) Create the database
			mysql -u root -p mtd > dolus-defensebypretense/buildDB.sql
		
		
II. Running Frenetic and generating a dataset

	a) For these next steps, we will need to do some investigation of our GENI slice network in order to determine which devices are on which ports on each switch.
	b) Login to the root-switch
		ifconfig
	c) Take note of each ethernet device (eth1, eth2...ethx) and the local ip address associated with each (in our experiment, it would be something like 10.0.0.13)
	d) Login to each device connected to the root-switch and run ifconfig
	e) Take note of the local ip address on the device and relate that to the ip address and ethernet device on the on the switch. The port number is the number from the ethernet device.
		i) For example, if you login to the root-switch and ifconfig displays eth4 with ip address 10.0.0.14
		ii) Then while checking devices on the root-swtich you login to server2 and ifconfig displays 10.0.0.14 as the ip for server2, then server2 is plugged into port eth4, port 4, on the root-switch
	f) Do the same steps for the slave-switch and repeat until you have a mapping of all the switches and which port goes to which device and ip address
	
	