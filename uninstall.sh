#! /bin/bash
#
# YouAreHere uninstall script. Removes dnsmasq, hostapd, iw, ppp. Deletes YouAreHere folder and files within.
# Sarah Grant
# Updated 30 July 2015
#
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# CHECK USER PRIVILEGES
(( `id -u` )) && echo "This script *must* be ran with root privileges, try prefixing with sudo. i.e sudo $0" && exit 1


# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# Uninstall YouAreHere
#
read -p "Do you wish to uninstall YouAreHere from your Raspberry Pi? [N] " yn
case $yn in
	[Yy]* )
		clear

		echo -en "Disabling hostapd and dnsmasq on boot... 			"
		update-rc.d hostapd disable
		update-rc.d dnsmasq disable

		# remove hostapd init file
		echo -en "Deleting default hostapd and configuration files...			"
		rm /etc/default/hostapd
		rm /etc/hostapd/hostapd.conf
		echo -en "[OK]\n"

		# remove dnsmasq
		echo -en "Deleting dnsmasq configuration file... 			"
		rm /etc/dnsmasq.conf
		echo -en "[OK]\n"

		# remove usb_modeswitch.conf
		echo -en "Deleting usb_modeswitch configuration file... 			"
		rm /etc/usb_modeswitch.conf
		echo -en "[OK]\n"

		# remove gprs file
		echo -en "Deleting gprs configuration file... 			"
		rm /etc/ppp/peers/gprs
		echo -en "[OK]\n"

		echo ""
		echo -en "Purging iw, ppp, usb-modeswitch, usb-modeswitch-data, lighttpd, hostapd and dnsmasq... 			"
		# how do i uninstall with apt-get
		apt-get purge -y hostapd dnsmasq iw ppp usb-modeswitch usb-modeswitch-data lighttpd mysql-server php5-common php5-cgi php5 php5-mysql
		apt-get autoremove
		echo -en "[OK]\n"

		# restore the previous interfaces file
		echo -en "Restoring previous network interfaces configuration file... 			"
		rm /etc/network/interfaces
		mv /etc/network/interfaces.bak /etc/network/interfaces
		echo -en "[OK]\n"

		# Remove startup scripts and delete
		echo -en "Disabling and deleting startup YouAreHere startup scripts... 			"
		update-rc.d -f you_are_here remove
		rm /etc/init.d/you_are_here

		echo "Deleting YouAreHere folder			"
		cd /home/pi/
		rm -rf /home/pi/YouAreHere
		echo -en "[OK]\n"
		read -p "Do you wish to reboot now? [N] " yn
		case $yn in
			[Yy]* )
				reboot;;
			[Nn]* ) exit 0;;
		esac

	;;
	[Nn]* ) exit 0;;
esac

exit 0
