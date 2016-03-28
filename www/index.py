#! /usr/bin/env python

import web
import json

urls = (
    '/', 'index',
    '/create', 'create',
    '/user', 'user'
)

class index:
    def GET(self):
        return "Hello, web.py!"

class create:
    def GET(self):
        web.header('Content-Type', 'application/json')
        return "{\"create\":\"444\"}"

class user:
    def GET(self):
        web.header('Content-Type', 'application/json')
        web.header('Access-Control-Allow-Origin', '*')
        data = [ { 'image':'img.jpg', 'headline':'my headline'} ]
        data_string = json.dumps(data)
        return data_string

if __name__ == "__main__":
    app = web.application(urls, globals())
    app.run()

app = web.application(urls, globals(), autoreload=False)
application = app.wsgifunc()
