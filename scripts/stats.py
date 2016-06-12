#!/usr/bin/env python

import cgi, time, urllib

print("Content-Type: text/html; charset=utf-8")
print("")

args = cgi.FieldStorage()

data = urllib.unquote(args.getvalue('stats'))

#print(data)

#print(str(args.getvalue('stats')))

if 'stats' in args.keys():
        with open('/var/www/stats.txt', 'a') as statsfile:
                statsfile.write(str(time.time()) + ': ' + args['stats'].value + '\n')
        print('OK')

statsfile.write(str(time.time()) + ': ' + str(args) + '\n')