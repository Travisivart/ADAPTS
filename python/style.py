class Style(object):
    """This class is used for the text formatting."""
    def __init__(self):
        self.__purple = '\033[95m'
        self.__cyan = '\033[96m'
        self.__darkCyan = '\033[36m'
        self.__blue = '\033[94m'
        self.__green = '\033[92m'
        self.__yellow = '\033[93m'
        self.__red = '\033[91m'
        self.__bold = '\033[1m'
        self.__underline = '\033[4m'
        self.__terminate = '\033[0m'

    def setColor(self,color):

        if color == 'purple':
            return self.__purple

        elif color == 'cyan':
            return self.__cyan

        elif color == 'darkcyan':
            return self.__darkCyan

        elif color == 'blue':
            return self.__blue

        elif color == 'green':
            return self.__green

        elif color == 'yellow':
            return self.__yellow

        elif color == 'red':
            return self.__red

        else:
            return ''

    def setBold(self):
        return self.__bold

    def setUnderline(self):
        return self.__underline

    def end(self):
        return self.__terminate
