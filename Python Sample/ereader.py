#! /Library/Frameworks/Python.framework/Versions/2.7/bin/python
# -*- coding: utf-8 -*-
"""
ereader.py is a rudimentary plain-text eBook reader.

This program should be run with a command from terminal in one of two ways:

For analyzing a single Cluster.txt file: 
'python duplex.py the_republic.txt' will launch the script with default paging of
40 lines per page beginning where the user last left off.

For working through each Cluster.txt file in a directory:
'python duplex.py -n 80 therepublic.txt' will launch the script with paging set to
80 lines per page.

Once the ereader has opened, users can press 'n' for the next page, 'p' for the
previous page, and 'q' to quit. 
"""

import os
import sys
import hashlib
import re

if (not os.path.isfile('/Users/nawap24/Documents/CS_2043/A4/.reader_rc')):
    os.system('touch /Users/nawap24/Documents/CS_2043/A4/.reader_rc')

"""Given a numeric value, x, returns x if x > 0 and 0 if x < 0. """
def negToZero(x):
    if x > 0:
        return x
    else:
        return 0

filename = ""
number = ""
text = ""

"""______________________________________________________________"""

"""Check to see if two arguments or four arguments. Get filename."""
if (len(sys.argv) == 2):
    filename = sys.argv[1]
    number = 40
else:
    filename = sys.argv[3]
    number = sys.argv[2]
    
"""Extract content of file to 'text'."""
with open (filename, "r") as myfile:
    text=myfile.read()
    myfile.close()

"""Set hashfile equal to the hash value of the content"""
hashfile = hashlib.md5()
hashfile.update(text)
hashfile = str(hashfile.hexdigest())

"""Extract the bookmarked page"""
expression = hashfile + ',[0-9]+'
regex = re.compile(expression)
result = ""
with open (".reader_rc", "r") as myfile:
    for line in myfile:
        if regex.search(line) is not None:
            result = regex.search(line)
            result = result.group(0)
    myfile.close()
with open (".reader_rc", "a") as myfile:
    if result == "":
        argument = hashfile + "," + "1"
        myfile.write(argument)
        bookmark = 1
    else:
        bookmark = result[result.find(",") + 1:] 
        bookmark = int(bookmark)
    myfile.close()

"""Print current page according to bookmark and page number specification."""
book = open(filename, 'r+')
with open (filename) as f:
    count = 0
    for i, x in enumerate(f):
        if i < bookmark:
            i += 1
        elif i < (bookmark + int(number)):
                i += 1 
                print x[:-1]
                count = count + 1

"""Bash command to update .reader with new bookmark of book."""
a = raw_input()

if a == "n":
    cmd = "sed 's/" + hashfile + "," + str(bookmark) + "/" + hashfile + "," + str(negToZero(int(bookmark) + int(number))) + "/g' .reader_rc > .reader_rc.temp"
    cmd2 = "mv .reader_rc.temp .reader_rc"
    cmd3 = "./ereader.py -f " + number + " " + filename
    os.system(cmd)
    os.system(cmd2)
    os.system(cmd3)

if a == "p":
    cmd = "sed 's/" + hashfile + "," + str(bookmark) + "/" + hashfile + "," + str(negToZero(int(bookmark) - int(number))) + "/g' .reader_rc > .reader_rc.temp"
    cmd2 = "mv .reader_rc.temp .reader_rc"
    cmd3 = "./ereader.py -f " + number + " " + filename
    os.system(cmd)
    os.system(cmd2)
    os.system(cmd3)

if a == "q":
    book.close()





