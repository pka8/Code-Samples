#! /Library/Frameworks/Python.framework/Versions/2.7/bin/python
# -*- coding: utf-8 -*-
"""
duplex.py parses a Cluster.txt output file from ShortStack for the suggested
microRNA with the highest number of alignments. duplex.py prints the microRNA
sequence with the greatest number of alignments to the terminal.

This program should be run with a command from terminal in one of two ways:

For analyzing a single Cluster.txt file: 
'python duplex.py Cluster.txt'

For working through each Cluster.txt file in a directory:
'python duplex.py -d [Path to Directory]'

- Pawan K. Angara
"""
import os
import sys
import re
    
"""
Returns the microRNA with highest number of alignments in the form of:
'>[filename]
[Sequence]'

Precondition: filename must be the name of a Cluster.txt output file from ShortStack.
"""
def bestAlignment(filename):
    linemax = 0 #line with largest alignment score
    left = "" 
    right = ""
    
    left = False #boolean storing value of 'left score is greater than right score'
    right = False #boolean storing value of 'right score is greater than left score'
    
    with open (filename, "r") as myfile:
        i = 0
        max_ascore = 0 #maximum alignment score
        lines = myfile.readlines()
        
        for line in lines:
            regex = re.compile("Alignments: [0-9]+ / [0-9]+")
            i += 1
            result = regex.search(line)
            if (result is not None): 
                numbers = re.findall(r"[0-9]+", line)
                left_score = int(numbers[0])
                right_score = int(numbers[1])
                ascore = left_score + right_score
                if (ascore > max_ascore):
                    max_ascore = ascore
                    linemax = i
                    if (left_score > right_score):
                        left = True
                    else:
                        right = True
        alignment = lines[linemax - 5]
        result = re.findall(r"[A-Z]{2,}", alignment)
        if (left):
            alignment = result[0]
        else:
            alignment = result[1]
        
        filename = re.sub(r'/[A-Za-z/0-9]*/', "", filename)
        filename = re.sub(r'.txt', "", filename)
        print ">" + filename + '\n' + alignment + '\n'
        myfile.close()
"_____________________________________________________________________________________"

"""Calling from terminal as 'python duplex.py -d [Directory Name]'"""
if (len(sys.argv) == 3):
    path = sys.argv[2]
    filelist = os.listdir(path)
    for filename in filelist:
        regex = re.compile("Cluster_[0-9]*.txt")
        result = regex.search(filename)
        if (result is not None):
            filename = path + "/" + filename
            bestAlignment(filename)
            
    
else:
    filename = sys.argv[1]
    bestAlignment(filename)