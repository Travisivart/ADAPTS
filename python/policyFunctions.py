from datetime import datetime
import pytz
from pytz import timezone
from time import sleep

def getExistingPolicy(cursor):
    existingPolicies = ''
    cursor.execute("SELECT policy FROM policies where loaded = 1")
    policies = cursor.fetchall()

    for policy  in policies:
        existingPolicies = existingPolicies + policy['policy'] + '|'

    return existingPolicies[:-1]

def getCurrentPolicy(cursor):
    cursor.execute("SELECT * FROM policies where loaded = 0")
    policy = cursor.fetchall()
    ids = list()
    current_policy = ''

    for array in policy:
        ids.append(array['policyID'])
        current_policy = current_policy + array['policy'] + ' |'

    return current_policy[:-1], ids


def updatePolicyLog(filename, current_policy, dateFormat="%m/%d/%Y", timeFormat="%H:%M:%S"):
    try:
        log_file = open(filename, 'a')
        print log_file
        now_utc = datetime.now(timezone('UTC'))
        central_time = now_utc.astimezone(timezone('US/Central'))
        log_file.write(central_time.strftime(timeFormat)
                        + '\t'+central_time.strftime(dateFormat)
                        + '\t'
                        + current_policy
                        + '\n'
                        )
        #print now_utc
        #print central_time
        log_file.close()
    except:
        print "Error with: " + filename
