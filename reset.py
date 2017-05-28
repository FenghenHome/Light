#!/usr/bin/env python
#coding=utf-8

import os
import sys
import json
import time
import cymysql

def import_env():
    environ = {}
    if os.path.exists(sys.path[0] + '/.env'):
        for line in open(sys.path[0] + '/.env'):
            var = line.strip().split('=')
            if len(var) == 2:
                key, value = var[0].strip(), var[1].strip()
                environ[key] = value
        return environ
    else:
        return {}

environ = import_env()

connection = cymysql.connect(host=environ["DB_HOST"], user=environ["DB_USERNAME"], passwd=environ["DB_PASSWORD"], db=environ["DB_DATABASE"], charset='utf8')
cursor = connection.cursor()
cursor.execute("SELECT * FROM regions WHERE deleted_at IS NULL")
regions = cursor.fetchall()
for region in regions:
    connection_string = region[2]
    connection_object = json.loads(connection_string)
    region_connection = cymysql.connect(host=connection_object["host"], user=connection_object["username"], passwd=connection_object["password"], db=connection_object["database"], charset='utf8')
    region_cursor = region_connection.cursor()
    region_cursor.execute("UPDATE user SET transfer_enable=10737418240+u+d WHERE expired_time > {time};".format(time=time.time()))