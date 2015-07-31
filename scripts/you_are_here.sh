#!/bin/sh
# /etc/init.d/you_are_here
# starts up ppp0, wlan0 interface, hostapd, and dnsmasq for broadcasting a wireless network

NAME=you_are_here
DESC="Brings up PPP, wireless access point for connecting to web server running on the device."
PIDFILE=/var/run/$NAME.pid
SCRIPTNAME=/etc/init.d/$NAME

	case "$1" in
		start)
			echo "Starting $NAME access point..."
			# associate the wlan0 interface to a physical devices
			# check to see if wlan1 exists; use that radio, if so.
			#FOUND=`iw dev | grep phy#0`
			#if  [ -n "$FOUND" ] ; then
				#PHY="phy0"
				# assign wlan0 to the hardware device found
				#iw phy $PHY interface add wlan0 type __ap
			#fi

			# bring up WLAN0 + PPP interface
			ifup wlan0
			ifup gprs

			# set gateway to PPP IP
			#route del default
			#route add default gw 10.64.64.64

			# start the hostapd and dnsmasq services
			service hostapd start
			service dnsmasq start
		;;

		status)
		;;

		stop)
			ifdown wlan0
			ifdown gprs

			service hostapd stop
            service dnsmasq stop
		;;

		restart)
			$0 stop
			$0 start
		;;

*)
		echo "Usage: $0 {status|start|stop|restart}"
		exit 1
esac
