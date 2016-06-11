#! /bin/bash
#
# Raspberry Pi YouAreHere Installation script
# Sarah Grant
# Updated 21 April 2016
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# SOME DEFAULT VALUES
#
# WIRELESS RADIO DRIVER
RADIO_DRIVER=nl80211

# ACCESS POINT
AP_CHAN=3
AP_SSID=you.are.here
AP_IP=192.168.100.1

# DNSMASQ STUFF
DHCP_START=192.168.100.101
DHCP_END=192.168.100.254
DHCP_NETMASK=255.255.255.0
DHCP_LEASE=1h

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# CHECK USER PRIVILEGES
(( `id -u` )) && echo "This script must be ran with root privileges, try prefixing with sudo. i.e sudo $0" && exit 1

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# BEGIN INSTALLATION PROCESS
#
echo "●▬▬▬▬---▬▬▬▬▬▬▬*'¨'*▬▬▬▬▬▬▬---▬▬▬▬●"
echo "|    You Are Here Installation    |"
echo "●▬▬▬▬▬▬▬▬---▬▬▬*,_,*▬▬▬---▬▬▬▬▬▬▬▬●"
echo ""

read -p "This installation script will configure a wireless access point, captive portal, apache web server and ppp 3G data link. Make sure you have a USB wifi radio connected to your Raspberry Pi before proceeding. Press any key to continue..."
echo ""
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# SOFTWARE INSTALL
#
# update the packages
echo "Updating apt-get and installing hostapd, dnsmasq, ppp, usb-modeswitch, usb-modeswitch-data, apache web server, iw package for network interface configuration..."
apt-get update && apt-get install -y npm hostapd dnsmasq iw ppp usb-modeswitch usb-modeswitch-data apache2 apache2-utils
echo ""
echo "Configuring apache web server..."
chown www-data:www-data /var/www
chmod 775 /var/www
usermod -a -G www-data pi

a2enmod proxy_http proxy proxy_connect ssl cache_disk rewrite

# append scripts/mod_proxy.conf to end of /etc/apache2/apache2.conf...
cat scripts/mod_proxy.conf >> /etc/apache2/apache2.conf

# copy scripts/999-youarehere-rewrite.conf to mods-enabled...
cp scripts/999-youarehere-rewrite.conf /etc/apache2/mods-enabled

# restart apache
service apache2 reload
apachectl restart

# CHECK USB WIFI HARDWARE IS FOUND
# and that iw list does not fail with 'nl80211 not found'
echo -en "checking that nl80211 USB wifi radio is plugged in...				"
iw list > /dev/null 2>&1 | grep 'nl80211 not found'
rc=$?
if [[ $rc = 0 ]] ; then
	echo -en "[FAIL]\n"
	echo "Make sure you are using a wifi radio that runs via the nl80211 driver."
	exit $rc
else
	echo -en "[OK]\n"
fi

echo "Configuring Raspberry Pi as Access Point..."
echo ""

# ask how they want to configure their access point
read -p "Wifi Channel Number [$AP_CHAN]: " -e t1
if [ -n "$t1" ]; then AP_CHAN="$t1";fi

read -p "Wifi SSID [$AP_SSID]: " -e t1
if [ -n "$t1" ]; then AP_SSID="$t1";fi

read -p "DHCP starting address [$DHCP_START]: " -e t1
if [ -n "$t1" ]; then DHCP_START="$t1";fi

read -p "DHCP ending address [$DHCP_END]: " -e t1
if [ -n "$t1" ]; then DHCP_END="$t1";fi

read -p "DHCP netmask [$DHCP_NETMASK]: " -e t1
if [ -n "$t1" ]; then DHCP_NETMASK="$t1";fi

read -p "DHCP length of lease [$DHCP_LEASE]: " -e t1
if [ -n "$t1" ]; then DHCP_LEASE="$t1";fi

# backup the existing interfaces file
echo -en "Creating backup of network interfaces configuration file... 			"
cp /etc/network/interfaces /etc/network/interfaces.bak
rc=$?
if [[ $rc != 0 ]] ; then
	echo -en "[FAIL]\n"
	exit $rc
else
	echo -en "[OK]\n"
fi

# CONFIGURE /etc/network/interfaces
echo -en "Creating new network interfaces configuration file with your settings... 	"
cat <<EOF > /etc/network/interfaces
auto lo
iface lo inet loopback

auto eth0
allow-hotplug eth0
iface eth0 inet manual

auto wlan0
allow-hotplug wlan0
iface wlan0 inet static
address $AP_IP
netmask 255.255.255.0

auto gprs
iface gprs inet ppp
provider gprs

EOF
rc=$?
if [[ $rc != 0 ]] ; then
    	echo -en "[FAIL]\n"
	echo ""
	exit $rc
else
	echo -en "[OK]\n"
fi

# create hostapd init file
echo -en "Creating default hostapd file...			"
cat <<EOF > /etc/default/hostapd
DAEMON_CONF="/etc/hostapd/hostapd.conf"
EOF
rc=$?
if [[ $rc != 0 ]] ; then
	echo -en "[FAIL]\n"
	echo ""
	exit $rc
else
	echo -en "[OK]\n"
fi

# create hostapd configuration with user's settings
echo -en "Creating hostapd.conf file...				"
cat <<EOF > /etc/hostapd/hostapd.conf
interface=wlan0
driver=$RADIO_DRIVER
ctrl_interface=/var/run/hostapd
ctrl_interface_group=0
ssid=$AP_SSID
hw_mode=g
channel=$AP_CHAN
beacon_int=100
auth_algs=1
wpa=0
macaddr_acl=0
EOF
rc=$?
if [[ $rc != 0 ]] ; then
	echo -en "[FAIL]\n"
	exit $rc
else
	echo -en "[OK]\n"
fi

# CONFIGURE dnsmasq
echo -en "Creating dnsmasq configuration file... 			"
cat <<EOF > /etc/dnsmasq.conf
interface=wlan0
address=/#/$AP_IP
address=/youarehere.com/$AP_IP
address=/apple.com/0.0.0.0
address=/phiffer.org/173.220.23.228
address=/youarehere.network/192.30.252.153
dhcp-range=$DHCP_START,$DHCP_END,$DHCP_NETMASK,$DHCP_LEASE
EOF
rc=$?
if [[ $rc != 0 ]] ; then
    	echo -en "[FAIL]\n"
	echo ""
	exit $rc
else
	echo -en "[OK]\n"
fi

# Need to create /etc/usb_modeswitch.conf and prompt for values:
# (DefaultVendor and DefaultProduct are obtainable from lsusb and reading what 
# is listed for the Huawei device, after ID)
# DefaultVendor=0x12d1
# DefaultProduct=0x14fe
# MessageEndpoint="0x01"
# MessageContent="55534243123456780000000000000011062000000101000100000000000000"

# Also need to create /etc/ppp/peers/gprs file
# Need to prompt for user, APN
# user ""
# connect "/usr/sbin/chat -v -f /etc/chatscripts/gprs -T fast.t-mobile.com"
# /dev/ttyUSB0
# noipdefault
# defaultroute
# replacedefaultroute
# hide-password
# noauth
# persist
# usepeerdns

# Copy over scripts to configure Huawei E303 3G Modem
cp scripts/usb_modeswitch.conf /etc/usb_modeswitch.conf
cp scripts/gprs /etc/ppp/peers/gprs
cp scripts/huawei_e303.rules /etc/udev/rules.d/huawei_e303.rules
cp scripts/blacklist-bc.conf /etc/modprobe.d/blacklist-bc.conf

# reload rules so data stick is automatically mode switched from storage device to modem
udevadm control --reload-rules

# Build files
# cd www/
# npm install
rsync -r www/build/* /var/www/html/

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# COPY OVER THE ACCESS POINT START UP SCRIPT + enable services
#
clear
update-rc.d hostapd enable
update-rc.d dnsmasq enable
cp scripts/you_are_here.sh /etc/init.d/you_are_here
chmod 755 /etc/init.d/you_are_here
update-rc.d you_are_here defaults

read -p "Do you wish to reboot now? [N] " yn
	case $yn in
		[Yy]* )
			reboot;;
		Nn]* ) exit 0;;
	esac