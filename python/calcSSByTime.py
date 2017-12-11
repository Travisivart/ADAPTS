import sys
import MySQLdb
import MySQLdb.cursors
 
def main():
 
	if len(sys.argv) < 2:
		print("No trace_id specified....exiting")
		exit()

	trace_id = sys.argv[1]

	print("Using trace_id = " + trace_id)

	db = MySQLdb.connect(user='root', passwd='1234', host='127.0.0.1', db='mtd',cursorclass=MySQLdb.cursors.DictCursor)
	cursor = db.cursor()
	findMin = "SELECT MIN(frame_time) as minFrameTime, MAX(frame_time) as maxFrameTime FROM mtd.packet_logs where trace_id = " + trace_id + ";"
	cursor.execute(findMin)
	results = cursor.fetchone()

	minFrame = results["minFrameTime"]
	maxFrame = results["maxFrameTime"]

	print results
	print str(minFrame)
	print str(maxFrame)

	#deleteExisting = "DELETE FROM mtd.suspiciousness_scores_by_time;"

	#cursor.execute(deleteExisting)
	#db.commit()

	while minFrame < maxFrame:

		update = "INSERT INTO mtd.suspiciousness_scores_by_time VALUES(" + str(minFrame) + ", (select "
		update += "SUM(POWER((("
		update += "POWER(((connections_total - connections_min) / (connections_max - connections_min)),2) + "
		update += "POWER(((flows_total - flows_min) / (flows_max - flows_min)),2) + "
		update += "POWER(((bytes_total - bytes_min) / (bytes_max - bytes_min)),2) "
		update += ")/3),(1.0/2.0))) 'score' "
		update += "from (SELECT d.name, COUNT(DISTINCT ip_dst) AS 'connections_total', case when d.name like 'server%' then 10 else 1 end as 'connections_min', "
		update += "case when d.name like 'server%' then 1000 else 10 end as 'connections_max' "
		update += "FROM mtd.packet_logs l, mtd.devices d "
		update += "WHERE d.ipv4 = l.ip_src and ip_src <> ' ' and ip_dst <> ' ' "
		update += "and (trace_id = " + trace_id + ") and frame_time >= " + str(minFrame) + " and frame_time < " + str(minFrame) + " + 10 "
		update += "group by d.name) a, "
		update += "(SELECT d.name, count(*) as 'flows_total', case when d.name like 'server%' then 1000 else 100 end as 'flows_min', case when d.name like 'server%' then 10000 else 1000 end as 'flows_max' "
		update += "FROM mtd.packet_logs l, mtd.devices d "
		update += "WHERE d.ipv4 = l.ip_src and ip_src <> ' ' and ip_dst <> ' ' and (trace_id = " + trace_id + ") and frame_time >= " + str(minFrame) + " and frame_time < " + str(minFrame) + " + 10 "
		update += "group by d.name) b, "
		update += "(SELECT d.name , SUM(frame_len) as 'bytes_total', case when d.name like 'server%' then 100000 else 100 end as 'bytes_min', case when d.name like 'server%' then 100000000 else 100000 end as 'bytes_max' "
		update += "FROM mtd.packet_logs l, mtd.devices d where d.ipv4 = l.ip_src and ip_src <> ' ' and ip_dst <> ' ' "
		update += "and (trace_id = " + trace_id + ") and frame_time >= " + str(minFrame) + " and frame_time < " + str(minFrame) + " + 10 "
		update += "group by d.name) c WHERE a.name = b.name and a.name = c.name));"
		#print(update)

		cursor.execute(update)
		db.commit()

		minFrame += 10

if __name__ == '__main__':
	main()