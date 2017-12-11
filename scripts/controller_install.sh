sudo apt-get update
#sudo apt-get upgrade -y # If you do choose to run the upgrade, if you are prompted to keep or overwrite files, keep all the local installed files. If it asks which drive to install GRUB to, select all drives.
sudo apt-get install -y python-setuptools python-dev build-essential libcurl4-openssl-dev opam curl apt dpkg mininet m4 zlib1g-dev python-pip libmysqlclient-dev apache2 php libapache2-mod-php
sudo adduser --disabled-password --gecos "" frenetic
sudo rm -R /var/www/html
sudo ln -s /home/frenetic/dolus-defensebypretense/html/ /var/www/html
sudo -H -u frenetic opam init -y
sudo -H -u frenetic opam switch 4.04.2
sudo -i -u frenetic
eval `opam config env`
eval `opam config env`
sudo easy_install pip
sudo easy_install pycurl
sudo pip install pycurl
opam pin add frenetic -y https://github.com/frenetic-lang/frenetic.git
sudo pip install frenetic ryu mysqlclient pycurl
~/.opam/4.04.2/bin/frenetic http-controller --verbosity debug