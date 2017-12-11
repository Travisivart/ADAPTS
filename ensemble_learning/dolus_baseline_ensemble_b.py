from collections import OrderedDict

from model import *
import xgboost as xgb
import numpy as np
from sklearn.preprocessing import LabelEncoder, OneHotEncoder
from sklearn import model_selection, metrics
import pandas as pd
import operator
import time
from plots import *
import itertools

FONT_SIZE = 15
matplotlib.rc('xtick', labelsize=FONT_SIZE)
matplotlib.rc('ytick', labelsize=FONT_SIZE)

DATASET = 'tshark_dolus.csv'

PACKET_HEADER = OrderedDict([('frame_number', 'continuous'),
                             ('frame_time_relative', 'continuous'),
                             ('frame_time_delta', 'continuous'),
                             ('frame_time', 'continuous'),
                             ('frame_protocols', 'symbolic'),
                             ('frame_len', 'continuous'),
                             ('eth_src', 'symbolic'),
                             ('eth_dst', 'symbolic'),
                             ('eth_type', 'symbolic'),
                             ('ip_proto', 'symbolic'),
                             ('ip_src', 'symbolic'),
                             ('ip_dst', 'symbolic'),
                             ('ipv6_src', 'symbolic'),
                             ('ipv6_dst' , 'symbolic'),
                             ('tcp_srcport', 'continuous'),
                             ('tcp_dstport', 'continuous'),
                             ('udp_srcport', 'continuous'),
                             ('udp_dstport', 'continuous'),
                             ('tcp_time_relative', 'continuous'),
                             ('tcp_time_delta', 'continuous'),
                             ('ws_col_Length', 'continuous'),
                             ('cusom_test', 'label'),
                             ('custom_attack', 'label')])

CLASS = ['slowhttptest-slowloris', 'iperf', 'vlc', 'ping']
CLASS_NAME = ['slow', 'iperf', 'vlc', 'ping']


class Packet:
    def __init__(self, obj, obj_header):
        self.frame_number = obj[obj_header['frame_number']]
        self.frame_time_relative = obj[obj_header['frame_time_relative']]
        self.frame_time_delta = obj[obj_header['frame_time_delta']]
        self.frame_time = obj[obj_header['frame_time']]
        self.frame_protocols = obj[obj_header['frame_protocols']]
        self.frame_len = obj[obj_header['frame_len']]
        self.eth_src = obj[obj_header['eth_src']]
        self.eth_dst = obj[obj_header['eth_dst']]
        self.eth_type = obj[obj_header['eth_type']]
        self.ip_proto = obj[obj_header['ip_proto']]
        self.ip_src = obj[obj_header['ip_src']]
        self.ip_dst = obj[obj_header['ip_dst']]
        self.ipv6_src = obj[obj_header['ipv6_src']]
        self.ipv6_dst = obj[obj_header['ipv6_dst']]
        self.tcp_srcport = obj[obj_header['tcp_srcport']]
        self.tcp_dstport = obj[obj_header['tcp_dstport']]
        self.udp_srcport = obj[obj_header['udp_srcport']]
        self.udp_dstport = obj[obj_header['udp_dstport']]
        self.tcp_time_relative = obj[obj_header['tcp_time_relative']]
        self.tcp_time_delta = obj[obj_header['tcp_time_delta']]
        self.ws_col_Length = obj[obj_header['ws_col_Length']]
        self.cusom_test = obj[obj_header['cusom_test']]
        self.custom_attack = obj[obj_header['custom_attack']]

    def features(self):
        return [self.frame_protocols, self.frame_len, self.eth_src, self.eth_dst, self.eth_type,
                self.ip_proto, self.ip_src, self.ip_dst, self.tcp_srcport, self.tcp_dstport,
                self.udp_srcport, self.udp_dstport]

    def label(self, stage_a=True):
        if not stage_a:
            return CLASS.index(self.cusom_test)
        else:
            if self.custom_attack == 'TRUE':
                return 1.0
            else:
                return 0.0


def process_header(header):
    x = {}
    pos = 0
    for name in header:
        x[name] = pos
        pos += 1
    return x


def select(from_file, where, toObject, header):
    data = []
    i = 0
    header = process_header(header)

    for line in open(from_file):
        i += 1

        # skip the header
        if i == 1:
            continue
        cmp = line.strip().split(",")
        if where(cmp):
            obj = toObject(cmp, header)
            if obj is not None:
                data.append(obj)

        if i % 10000 == 0:
            print("... reading line " + str(i) + " from file " + from_file)

    return header, data


KEY_MAP = {}


def plot_confusion_matrix(cm, classes,
                          normalize=False,
                          title='',
                          cmap=plt.cm.Blues, file_name='plot'):
    """
    This function prints and plots the confusion matrix.
    Normalization can be applied by setting `normalize=True`.
    """
    if normalize:
        cm = cm.astype('float') / cm.sum(axis=1)[:, np.newaxis]
        print("Normalized confusion matrix")
    else:
        print('Confusion matrix, without normalization')

    print(cm)
    plt.rcParams["font.family"] = "Times New Roman"
    plt.rcParams["font.size"] = FONT_SIZE
    plt.imshow(cm, interpolation='nearest', cmap=cmap)
    plt.title(title)
    plt.colorbar()
    tick_marks = np.arange(len(classes))
    plt.xticks(tick_marks, classes, rotation=0)
    plt.yticks(tick_marks, classes)

    fmt = '.4f' if normalize else 'd'
    thresh = cm.max() / 2.
    for i, j in itertools.product(range(cm.shape[0]), range(cm.shape[1])):
        plt.text(j, i, format(cm[i, j], fmt),
                 horizontalalignment="center",
                 color="white" if cm[i, j] > thresh else "black")

    plt.tight_layout()
    plt.ylabel('True label', fontsize=FONT_SIZE)
    plt.xlabel('Predicted label', fontsize=FONT_SIZE)
    plt.subplots_adjust(bottom=0.12)
    with PdfPages(file_name) as pdf:
        pdf.savefig()
        plt.close()


def build(str_obj, header):
    obj = [None] * (len(header))
    for k in header:
        idx = header[k]
        if 'continuous' == PACKET_HEADER[k]:
            try:
                obj[idx] = float(str_obj[idx])
            except ValueError:
                obj[idx] = -1
        elif 'label' == PACKET_HEADER[k]:
            obj[idx] = str(str_obj[idx])
        else:
            if k not in KEY_MAP:
                KEY_MAP[k] = []

            if str_obj[idx] not in KEY_MAP[k]:
                KEY_MAP[k].append(str_obj[idx])
            obj[idx] = int(KEY_MAP[k].index(str_obj[idx]))

    return Packet(obj, header)


def create_feature_map(features):
    outfile = open('xgb.fmap', 'w')
    for i, feat in enumerate(features):
        outfile.write('{0}\t{1}\tq\n'.format(i, feat))
    outfile.close()


def main():
    """
    1) Parse the data
    """
    (header, data_obj) = select(DATASET, lambda x: True, build, PACKET_HEADER)

    '''
    2) Build training data, and testing data
    '''
    data    = np.array([obj.features() for obj in data_obj])
    labels  = np.array([obj.label(stage_a=False) for obj in data_obj])

    np.random.seed(10)
    train, test, labels_train, labels_test = model_selection.train_test_split(data, labels, train_size=0.8, test_size=0.2)

    print train.shape
    print test.shape

    dataset_train = xgb.DMatrix(train, label=labels_train)
    dataset_test = xgb.DMatrix(test, label=labels_test)

    '''
    3) Train XGBoost
    '''
    param = {}
    param['objective'] = 'multi:softmax'
    param['eta'] = 0.1
    param['max_depth'] = 6
    param['nthread']     = 3
    param['num_class'] = len(set(labels))
    param['silent'] = 1
    watchlist = [(dataset_train, 'train')]

    num_round            = 10
    bst = xgb.train(param, dataset_train, num_round, watchlist)

    '''
    4) Prediction
    '''
    startTime = time.time()
    labels_test = dataset_test.get_label()
    labels_predict = bst.predict(dataset_test)
    print metrics.accuracy_score(labels_test, labels_predict)
    np.set_printoptions(precision=6, suppress=True)

    cnf_matrix =  metrics.confusion_matrix(labels_test, labels_predict)

    plot_confusion_matrix(cnf_matrix, classes=CLASS_NAME, file_name='state_b_unnormalized.pdf')

    # Plot normalized confusion matrix
    plt.figure()
    plot_confusion_matrix(cnf_matrix, classes=CLASS_NAME, normalize=True, file_name='state_b_normalized.pdf')

    print metrics.f1_score(labels_test, labels_predict, average='macro')
    # print metrics.recall_score(labels_test, labels_predict, average='macro')
    # print metrics.precision_score(labels_test, labels_predict, average='macro')
    executionTime = (time.time() - startTime)
    print "Prediction time = %.8f" % executionTime
    plt.show()

if __name__ == "__main__":
    main()