#!/bin/bash
sudo apt-get update
sudo apt-get install -y python-setuptools python-dev build-essential libcurl4-openssl-dev opam curl apt dpkg mininet m4 zlib1g-dev
sudo easy_install pip
sudo easy_install pycurl
sudo pip install pycurl

opam init -y

opam switch 4.04.2

eval `opam config env`
eval `opam config env`

opam pin add frenetic -y https://github.com/frenetic-lang/frenetic.git

sudo pip install frenetic ryu