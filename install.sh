#! /bin/bash
#
# Raspberry Pi YouAreHere Installation script
# Sarah Grant
# took guidance from a script by Paul Miller : https://dl.dropboxusercontent.com/u/1663660/scripts/install-rtl8188cus.sh
# Updated 30 July 2015
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# SOME DEFAULT VALUES
#
# WIRELESS RADIO DRIVER
RADIO_DRIVER=nl80211

# ACCESS POINT
AP_COUNTRY=US
AP_CHAN=1
AP_SSID=you.are.here
AP_IP=10.0.0.1

# DNSMASQ STUFF
DHCP_START=192.168.2.101
DHCP_END=192.168.2.254
DHCP_NETMASK=255.255.255.0
DHCP_LEASE=1h

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# CHECK USER PRIVILEGES
(( `id -u` )) && echo "This script *must* be ran with root privileges, try prefixing with sudo. i.e sudo $0" && exit 1

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# BEGIN INSTALLATION PROCESS
#
echo "●▬▬▬▬▬▬▬▬▬▬▬*'¨'*▬▬▬▬▬▬▬▬▬▬▬●"
echo "| You Are Here Installation |"
echo "●▬▬▬▬▬▬▬▬▬▬▬*,_,*▬▬▬▬▬▬▬▬▬▬▬●"
echo ""

read -p "This installation script will configure a wireless access point, lighttpd web server and ppp 3G data link. Make sure you have a USB wifi radio connected to your Raspberry Pi before proceeding. Press any key to continue..."
echo ""
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# SOFTWARE INSTALL
#
# update the packages
echo "Updating apt-get and installing hostapd, dnsmasq, ppp, lighttpd web server, iw package for network interface configuration..."
apt-get update && apt-get install -y hostapd dnsmasq iw ppp usb-modeswitch usb-modeswitch-data lighttpd
echo ""
echo "Configuring lighttpd web server..."
chown www-data:www-data /var/www
chmod 775 /var/www
usermod -a -G www-data pi

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
read -p "Wifi Country [$AP_COUNTRY]: " -e t1
if [ -n "$t1" ]; then AP_COUNTRY="$t1";fi

read -p "Wifi Channel Name [$AP_CHAN]: " -e t1
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
iface eth0 inet dhcp

auto wlan0
allow-hotplug wlan0
iface wlan0 inet static
	address $AP_IP
	netmask 255.255.255.0

# create ppp
auto gprs
iface gprs inet ppp
provider gprs

iface default inet dhcp
EOF
rc=$?
if [[ $rc != 0 ]] ; then
    	echo -en "[FAIL]\n"
	echo ""
	exit $rc
else
	echo -en "[OK]\n"
fi

# delete wlan0
#ifconfig wlan0 down
#iw wlan0 del

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
country_code=$AP_COUNTRY
ctrl_interface=/var/run/hostapd
ctrl_interface_group=0
ssid=$AP_SSID
hw_mode=g
channel=$AP_CHAN
beacon_int=100
auth_algs=1
wpa=0
macaddr_acl=0
wmm_enabled=1
ap_isolate=1
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
address=/apple.com/0.0.0.0
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

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# COPY OVER THE ACCESS POINT START UP SCRIPT + enable services
#
clear
update-rc.d hostapd enable
update-rc.d dnsmasq enable
cp scripts/you_are_here.sh /etc/init.d/you_are_here
chmod 755 /etc/init.d/you_are_here
update-rc.d you_are_here defaults

# Need to create /etc/usb_modeswitch.conf and prompt for values:
# (First two values are obtainable from lsusb and reading what 
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

read -p "Do you wish to reboot now? [N] " yn
	case $yn in
		[Yy]* )
			reboot;;
		Nn]* ) exit 0;;
	esac