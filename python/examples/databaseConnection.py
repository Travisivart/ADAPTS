import MySQLdb
import MySQLdb.cursors
import requests, json
import frenetic
from frenetic.syntax import *
import urllib2

MySQLdb.cursors.DictCursor
db = MySQLdb.connect(user='root', passwd='1234', host='127.0.0.1', db='mtd',cursorclass=MySQLdb.cursors.DictCursor)

cursor = db.cursor()

# Use all the SQL you like
cursor.execute("SELECT * FROM rules")

data = cursor.fetchone()

#print ("Data : %s " % data['rule'])

Pol = eval(data['rule'])
policy = json.dumps(Pol.to_json(),indent=4, sort_keys=False, separators=(',', ': '),ensure_ascii=False)
print policy

url = 'http://localhost:9000/adminPol/update_json'
req = urllib2.Request(url, str(policy), {'Content-Type': 'application/json'})
f = urllib2.urlopen(req)
#print f
#for x in f:
#    print(x)
f.close()
db.close()