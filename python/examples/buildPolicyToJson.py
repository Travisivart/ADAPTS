import frenetic
from frenetic.syntax import *

file_object  = open("json","w")

#     s1 Rules:
#Switch 183318352734019 s2 is Port 1, s3 is Port 2, h1 is Port 3, h3 is Port 4

#While on s1 send everything meant for h1 to h1
pol = Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.1")) >> SetPort(3)

#While on s1 send everything meant for h3 to h3
pol = pol | Filter(SwitchEq(51570677359425) & IP4DstEq("10.0.0.3")) >> SetPort(4)

#h1 to s1 to h3
#pol = pol | Filter(SwitchEq(51570677359425) & IP4SrcEq("10.0.0.1") & IP4DstEq("10.0.0.3")) >> SetPort(4)

#Send everything from h1 not meant for h3 to s2
pol = pol | Filter(SwitchEq(51570677359425) & IP4SrcEq("10.0.0.1") & IP4DstNotEq("10.0.0.3")) >> SetPort(1)

#Send everything from h3 not meant for h1 to s3
pol = pol | Filter(SwitchEq(51570677359425) & IP4SrcEq("10.0.0.3") & IP4DstNotEq("10.0.0.1")) >> SetPort(2)

#     #s2 Rules:
#Switch 258241292470349, s1 is Port 1, s3 is Port 2, h2 is Port 3

#WHile on s2 send everything meant for h2 to h2
pol = pol | Filter(SwitchEq(258241292470349) & IP4DstEq("10.0.0.2")) >> SetPort(3)

#Send everything from h2 meant for h1 to s1
pol = pol | Filter(SwitchEq(258241292470349) & IP4SrcEq("10.0.0.2") & IP4DstEq("10.0.0.1")) >> SetPort(1)

#Send everything from h2 meant for h3 to s3
pol = pol | Filter(SwitchEq(258241292470349) & IP4SrcEq("10.0.0.2") & IP4DstNotEq("10.0.0.1")) >> SetPort(2)

#     s3 Rules (these rules can be very basic and just say everything in one port send out the other since there are only two connections):
#Switch 86435678487366, is Port 1, is Port 2
pol = pol | Filter(SwitchEq(86435678487366) & PortEq(1)) >> SetPort(2)
pol = pol | Filter(SwitchEq(86435678487366) & PortEq(2)) >> SetPort(1)

file_object.write(str(pol.to_json()))
file_object.close()