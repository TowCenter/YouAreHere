You Are Here
============

You Are Here web server and repository for spoken stories [This description is FPO].

Support for You Are Here is provided by the Tow Center for Digital Journalism at Columbia University and the Knight Foundation. This code is published under the [AGPLv3](http://www.gnu.org/licenses/agpl-3.0.html).

How to install
--------------
Assuming you are starting with a fresh [Raspbian](http://www.raspberrypi.org/downloads/) install on your SD card, these are the steps for installing You are Here on your Raspberry Pi. It is also assumed that you have one wireless USB adapter and one Huawei E303 3G Modem attached to your RPi's onboard USB ports. The wireless radio must work with the nl80211 driver.

* set up your Raspberry Pi with a basic configuration

        sudo raspi-config

* clone the repository into your home folder (assuming /home/pi)

        git clone https://github.com/TowCenter/YouAreHere.git && cd YouAreHere

* run the installation script

        cd YouAreHere
        sudo ./install.sh

* After the installation script completes, you must SSH into the RPi and do two things: 1/ stop dnsmasq and 2/ manually set the routing gateway to that of the 3G modem (this will be fixed eventually):
	
		sudo /etc/init.d/dnsmasq stop
		sudo routes del default
		sudo routes add default gw 10.64.64.64

The installation process takes about 5 minutes. You will be prompted to name your wireless access point. After it has completed, you will have a running lighttpd web server, a connection to a 3G data network and will be broadcasting a wireless access point. Connecting to the network and navigating to http://192.168.100.1 in a browser window will take you to the root of the web server, currently served from /var/www.

References
----------
* [You Are Here website](http://youarehere.network/)
* [Tow Center](http://towcenter.org/)
* [lighttpd](http://www.lighttpd.net/)
* [Using a Raspberry Pi with a 3G USB Stick](http://copyndone.com/2015/06/27/guide-how-to-use-raspberry-pi-with-3g-usb-stick/)
* [Raspberry Pi](http://www.raspberrypi.org/)
