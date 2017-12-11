import MySQLdb
import MySQLdb.cursors
import requests, json
import frenetic
from frenetic.syntax import *
import urllib2
from datetime import datetime
import pytz
from pytz import timezone
from time import sleep

from style import Style
import policyFunctions as pf


def main():
    myStyle = Style()
    current_rule = ''
    print (myStyle.setColor('yellow') + 'Policy updater initialized. \nNow Listening...' + myStyle.end())

    while True:
        #MySQLdb.cursors.DictCursor
        #connecting to the mysql DB


        db = MySQLdb.connect(user='root', passwd='1234', host='127.0.0.1', db='mtd',cursorclass=MySQLdb.cursors.DictCursor)
        cursor = db.cursor()

        existing_policies = pf.getExistingPolicy(cursor)

        current_policy,ids = pf.getCurrentPolicy(cursor)

        temp = current_policy

        if existing_policies == '':
            current_policy = current_policy
        else:
            current_policy = existing_policies + '|' + current_policy


        if current_rule == temp:
			#print ()
            sleep(5)
            cursor.close()
            if db.open:
                db.close()
            continue
        else:
			# Simple data check.
            if len(temp) == 0:
                if db.open:
                    db.close()
                continue
            elif ("Filter(SwitchEq(" not in temp or "SetPort(" not in temp):
                print myStyle.setBold() + myStyle.setColor('red') + 'WOAH BAD POLICY ENTERED!!!' + myStyle.end()
                print myStyle.setBold() + myStyle.setColor('red') + "Policy: " + temp + myStyle.end()
				#print type(current_policy)
                print myStyle.setColor('yellow') + 'This is going into the logs.' + myStyle.end()
				#current_rule = temp
                for id in ids:
                    cursor = db.cursor()
                    cursor.execute("DELETE FROM policies where loaded = 0")
                    #policy = cursor.fetchall()
                    db.commit()

                print myStyle.setBold() + myStyle.setColor('yellow') + 'The bad Policy Has been Logged and removed from the working database' + myStyle.end()

                pf.updatePolicyLog('logs/bad_policy_logs.txt',temp)


            else:
                try:
                    #print current_policy
                    Pol = eval(current_policy)
                    policy = json.dumps(Pol.to_json(),indent=4, sort_keys=False, separators=(',', ': '),ensure_ascii=False)

                    current_rule = temp
                    url = 'http://localhost:9000/adminPol/update_json'
                    req = urllib2.Request(url, str(policy), {'Content-Type': 'application/json'})
                    f = urllib2.urlopen(req)
                    f.close()
					#cursor = db.cursor()
                    print myStyle.setBold() + myStyle.setColor('green') + 'STATUS: SUCCESS' + myStyle.end()
                    print myStyle.setBold() + myStyle.setColor('yellow') + 'CURRENT POLICY: '+ current_policy + myStyle.end()
                    for id in ids:
                        query = "UPDATE policies SET loaded = 1 where PolicyID = '" + id + "'"
						#print query
                        cursor.execute(query)
                        db.commit()
				#db.close()

                    pf.updatePolicyLog('logs/policy_logs.txt', current_policy)
						#sleep(5)

                except:
                    current_rule = ''
                    print( myStyle.setBold() + myStyle.setColor('red') + 'INCORRECT POLICY INPUT' + myStyle.end())
                    print(myStyle.setColor('yellow') + current_policy + myStyle.end())

                    for id in ids:
                        cursor = db.cursor()
                        cursor.execute("DELETE FROM policies where policyID = '" + id + "'")
						#policy = cursor.fetchall()
                        db.commit()
                        print id
        cursor = db.cursor()

        db.close()

if __name__ == '__main__':
    main()
