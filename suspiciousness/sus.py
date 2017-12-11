import sys
file = sys.argv[1]
with open(file, 'r') as f:
    lines = f.readlines()
num_lines = sum(1 for line in open(file))

bytes = [0] * num_lines
flows = [0] * num_lines
dst = [0] * num_lines
rms = [0] * num_lines
lst = []

for line in lines:
    lst.append(line.split())
ip = [x[0] for x in lst]
numbytes = [int(x[1]) for x in lst]
numbytesMin = [int(x[2]) for x in lst]
numbytesMax = [int(x[3]) for x in lst]
numflows = [int(x[4]) for x in lst]
numflowsMin = [int(x[5]) for x in lst]
numflowsMax = [int(x[6]) for x in lst]
numdst = [int(x[7]) for x in lst]
numdstMin = [int(x[8]) for x in lst]
numdstMax = [int(x[9]) for x in lst]
for i in range(0,num_lines):
    #normalization
    bytes [i] = (numbytes[i] - numbytesMin[i]) / (numbytesMax[i] - numbytesMin[i])
    flows [i]= (numflows[i] - numflowsMin[i]) / (numflowsMax[i] - numflowsMin[i])
    dst [i] = (numdst[i] - numdstMin[i]) / (numdstMax[i] - numdstMin[i])
    #calculate rms
    rms[i] = bytes[i]**2 + flows[i]**2 + dst[i]**2
    rms[i] = rms[i] / 3
    rms[i] = rms[i] ** (1 / 2.0)
    #print sus
    print ("Suspiciousness score for",ip[i], "is" , rms[i])



