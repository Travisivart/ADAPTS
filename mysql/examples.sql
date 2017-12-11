mysqldump -u root -p mtd packet_logs > dbBackup.sql

mysql -u root -p mtd < dbBackup.sql

select * from packet_logs where from_unixtime(frame_time) between timestamp('2017-11-06 00:05:25') and timestamp('2017-11-06 00:05:26');

select unix_timestamp(now()) from dual;


SELECT s.switchID, s.name, s.totalPorts, d.deviceID, d.name, d.ipv4
FROM mtd.switches s, mtd.switch_devices sd, mtd.devices d
WHERE s.switchID = sd.switchID AND sd.deviceID = d.deviceID;

  LOAD DATA LOCAL INFILE 'root-capture-11-19-2017.csv' INTO TABLE mtd.packet_logs FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 LINES (switch_id,trace_id,frame_number,frame_time_relative,frame_time,frame_protocols,frame_len,eth_src,eth_dst,eth_type,ip_proto,ip_src,ip_dst,tcp_srcport,tcp_dstport,udp_srcport,udp_dstport) SET switch_id = 99947915534402, trace_id = 3;
  
  
  
  select a.name, c.bytes_total, c.bytes_min, c.bytes_max, b.flows_total, b.flows_min, b.flows_max, a.connections_total, a.connections_min, a.connections_max,
(connections_total - connections_min) / (connections_max - connections_min) as 'connections_normalized',
(flows_total - flows_min) / (flows_max - flows_min) as 'flows_normalized',
(bytes_total - bytes_min) / (bytes_max - bytes_min) as 'bytes_normalized',
POWER(((
	POWER(((connections_total - connections_min) / (connections_max - connections_min)),2) + 
	POWER(((flows_total - flows_min) / (flows_max - flows_min)),2) +
	POWER(((bytes_total - bytes_min) / (bytes_max - bytes_min)),2)
    )/3),(1.0/2.0)) 'score'
from (
SELECT d.name, COUNT(DISTINCT ip_dst) AS 'connections_total', case when d.name like 'server%' then 10 else 1 end as 'connections_min',
												case when d.name like 'server%' then 1000 else 10 end as 'connections_max'
										FROM mtd.packet_logs l, mtd.devices d
										WHERE d.ipv4 = l.ip_src and ip_src <> ' ' and ip_dst <> ' ' and frame_time
                                        between 1512224482 and 1512224585
                                        group by d.name) a,
(                      
SELECT d.name, count(*) as 'flows_total', case when d.name like 'server%' then 1000 else 100 end as 'flows_min', case when d.name like 'server%' then 10000 else 1000 end as 'flows_max'
										FROM mtd.packet_logs l, mtd.devices d 
										WHERE d.ipv4 = l.ip_src and ip_src <> ' ' and ip_dst <> ' ' and frame_time
                                        between 1512224482 and 1512224585
                                        group by d.name) b,                                        
(SELECT d.name , SUM(frame_len) as 'bytes_total', case when d.name like 'server%' then 100000 else 100 end as 'bytes_min', case when d.name like 'server%' then 100000000 else 100000 end as 'bytes_max'
										FROM mtd.packet_logs l, mtd.devices d where d.ipv4 = l.ip_src and ip_src <> ' ' and ip_dst <> ' '
                                        and frame_time
                                        between 1512224482 and 1512224585
                                        group by d.name) c WHERE a.name = b.name and a.name = c.name;