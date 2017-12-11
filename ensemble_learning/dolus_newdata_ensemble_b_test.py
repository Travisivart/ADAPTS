from collections import OrderedDict

import xgboost as xgb
import numpy as np
from sklearn.preprocessing import LabelEncoder, OneHotEncoder
from sklearn import model_selection, metrics
import pandas as pd
import operator
import time
from plots import *
from xgboost import plot_tree
from collections import Counter

import itertools

FONT_SIZE = 13
matplotlib.rc('xtick', labelsize=FONT_SIZE)
matplotlib.rc('ytick', labelsize=FONT_SIZE)

TRAIN_DATA = ['root-switch-test1-part1.csv', 'root-switch-test1-part2.csv', 'slave-switch-test1-part1.csv', 'slave-switch-test1-part2.csv']
TEST_DATA = ['root-switch-test2-part1.csv', 'root-switch-test2-part2.csv', 'slave-switch-test2-part1.csv', 'slave-switch-test2-part2.csv']
CLASS = ['slow', 'undefined', 'vlc', 'ping', 'iperf', 'wget', 'scp']


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
                             ('tcp_srcport', 'continuous'),
                             ('tcp_dstport', 'continuous'),
                             ('udp_srcport', 'continuous'),
                             ('udp_dstport', 'continuous'),
                             ('tcp_flags_syn', 'symbolic'),
                             ('ws_col_Length', 'continuous'),
                             ('cusom_test', 'label'),
                             ('custom_attack', 'label')])


class Packet:
    def __init__(self, obj, obj_header, isTest):
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
        self.tcp_srcport = obj[obj_header['tcp_srcport']]
        self.tcp_dstport = obj[obj_header['tcp_dstport']]
        self.udp_srcport = obj[obj_header['udp_srcport']]
        self.udp_dstport = obj[obj_header['udp_dstport']]
        self.tcp_flags_syn = obj[obj_header['tcp_flags_syn']]
        self.ws_col_Length = obj[obj_header['ws_col_Length']]
        self.cusom_test = obj[obj_header['cusom_test']]
        self.custom_attack = obj[obj_header['custom_attack']]

    def features(self):
        return [self.frame_len, self.eth_src, self.eth_dst, self.eth_type,
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


def select(from_file, where, toObject, header, isTest=False):
    data = []

    header = process_header(header)
    for f in from_file:
        i = 0
        print("... reading file " + str(f))
        for line in open(f):
            i += 1
            # skip the header
            if i == 1:
                continue

            cmp = line.strip().split(",")
            if where(cmp):
                obj = toObject(cmp, header, isTest)
                if obj is not None:
                    data.append(obj)

    return header, data


KEY_MAP = {}


def build(str_obj, header, isTest):
    obj = [None] * (len(header))
    for k in header:
        idx = header[k]
        if 'continuous' == PACKET_HEADER[k]:
            try:
                obj[idx] = float(str_obj[idx])
            except ValueError:
                obj[idx] = -1
            except IndexError:
                continue
        elif 'label' == PACKET_HEADER[k]:
            obj[idx] = str(str_obj[idx])
        else:
            if k not in KEY_MAP:
                KEY_MAP[k] = []

            if str_obj[idx] not in KEY_MAP[k]:
                KEY_MAP[k].append(str_obj[idx])
            obj[idx] = int(KEY_MAP[k].index(str_obj[idx]))
    return Packet(obj, header, isTest)


def create_feature_map(features):
    outfile = open('xgb.fmap', 'w')
    for i, feat in enumerate(features):
        outfile.write('{0}\t{1}\tq\n'.format(i, feat))
    outfile.close()


def plot_confusion_matrix(cm, classes,
                          normalize=False,
                          title='',
                          cmap=plt.cm.Blues, file_name='cm_plot'):
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

    fmt = '.3f' if normalize else 'd'
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


def main():
    """
    1) Parse the data
    """
    (header, data_obj) = select(TRAIN_DATA + TEST_DATA, lambda x: True, build, PACKET_HEADER)

    '''
    2) Build training data, and testing data
    '''
    data    = np.array([obj.features() for obj in data_obj])
    labels  = np.array([obj.label(stage_a=False) for obj in data_obj])

    np.random.seed(10)
    train, test, labels_train, labels_test = model_selection.train_test_split(data, labels, train_size=0.6, test_size=0.4)

    dataset_train = xgb.DMatrix(train, label=labels_train)
    dataset_test = xgb.DMatrix(test, label=labels_test)

    '''
    3) Train XGBoost
    '''
    param = {}
    param['objective'] = 'multi:softmax'
    param['eta'] = 0.1
    param['max_depth'] = 4
    param['nthread']     = 3
    param['num_class'] = len(set(labels))
    param['silent'] = 1
    watchlist = [(dataset_train, 'train')]

    num_round            = 20
    bst = xgb.train(param, dataset_train, num_round, watchlist)
    bst.dump_model('dump.raw.txt')

    '''
    4) Prediction
    '''
    # # Read the test dataset.
    # (header, data_obj) = select(TEST_DATA, lambda x: True, build, PACKET_HEADER)
    # data_test    = np.array([obj.features() for obj in data_obj])
    # labels_test  = np.array([obj.label(stage_a=False) for obj in data_obj])
    #
    # data_test = xgb.DMatrix(data_test)

    startTime = time.time()
    labels_predict = bst.predict(dataset_test)
    print "********************************************************"
    np.set_printoptions(precision=6, suppress=True)
    executionTime = (time.time() - startTime)
    print "The total detection time is " + str(executionTime) + 's'
    print "The overall accuracy is: " + str(metrics.accuracy_score(labels_test, labels_predict))
    cnf_matrix = metrics.confusion_matrix(labels_test, labels_predict)
    plot_confusion_matrix(cnf_matrix, classes=CLASS, file_name='stage_b_confusion_matrix_unnormal.pdf')
    plot_confusion_matrix(cnf_matrix, classes=CLASS, normalize=True, file_name='stage_b_confusion_matrix_normal.pdf')
    print metrics.f1_score(labels_test, labels_predict, average='macro')
    plt.show()
    print "********************************************************"
    print


if __name__ == "__main__":
    main()
