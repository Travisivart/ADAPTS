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

	update = "insert into suspiciousness_scores (select g.name, " + trace_id + ", g.score from("
	update += "select a.name, c.bytes_total, c.bytes_min, c.bytes_max, b.flows_total, b.flows_min, b.flows_max, a.connections_total, a.connections_min, a.connections_max,"
	update += "(connections_total - connections_min) / (connections_max - connections_min) as 'connections_normalized',"
	update += "(flows_total - flows_min) / (flows_max - flows_min) as 'flows_normalized',"
	update += "(bytes_total - bytes_min) / (bytes_max - bytes_min) as 'bytes_normalized',"
	update += "POWER((("
	update += "POWER(((connections_total - connections_min) / (connections_max - connections_min)),2) + "
	update += "POWER(((flows_total - flows_min) / (flows_max - flows_min)),2) +"
	update += "POWER(((bytes_total - bytes_min) / (bytes_max - bytes_min)),2)"
	update += ")/3),(1.0/2.0)) 'score'"
	update += "from ("
	update += "select d.name, COUNT(DISTINCT ip_dst) AS 'connections_total', case when d.name like 'server%' then 10 else 1 end as 'connections_min',"
	update += "case when d.name like 'server%' then 1000 else 10 end as 'connections_max'"
	update += "FROM mtd.packet_logs l, mtd.devices d "
	update += "WHERE d.ipv4 = l.ip_src and ip_src <> ' ' and ip_dst <> ' ' and trace_id = " + trace_id + " "
	update += "group by d.name) a,"
	update += "(SELECT d.name, count(*) as 'flows_total', case when d.name like 'server%' then 1000 else 100 end as 'flows_min', case when d.name like 'server%' then 10000 else 1000 end as 'flows_max'"
	update += "FROM mtd.packet_logs l, mtd.devices d "
	update += "WHERE d.ipv4 = l.ip_src and ip_src <> ' ' and ip_dst <> ' ' and trace_id = " + trace_id + " "
	update += "group by d.name) b,"
	update += "(SELECT d.name , SUM(frame_len) as 'bytes_total', case when d.name like 'server%' then 100000 else 100 end as 'bytes_min', case when d.name like 'server%' then 100000000 else 100000 end as 'bytes_max'"
	update += "FROM mtd.packet_logs l, mtd.devices d WHERE d.ipv4 = l.ip_src and ip_src <> ' ' and ip_dst <> ' ' "
	update += "and (trace_id = " + trace_id + ") "
	update += "group by d.name) c WHERE a.name = b.name and a.name = c.name) g);"

	#print(update)

	cursor.execute(update)
	db.commit()

if __name__ == '__main__':
	main()