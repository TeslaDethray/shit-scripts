#!/usr/bin/python
# -*- coding: utf-8 -*-
#Imports SSA name data into a Django 2.x database

import os
import glob
import sqlite3 as db

db_name = ''
path = ''
extension = '.TXT'

def dictionarize(array):
    dictionary = {}
    for each in array:
        dictionary[each[0]] = each[1]
    return dictionary

def extract_from_filename(string):
    return string.replace(path, '').replace(extension, '').replace('yob', '')

connection = db.connect(db_name)
cursor = connection.cursor()

regions = dictionarize(cursor.execute('SELECT `abbreviation`, `id` FROM `names_region`;').fetchall())
names = dictionarize(cursor.execute('SELECT `namae`, `id` FROM `names_name`;').fetchall())

for filename in glob.glob(os.path.join(path, '*' + extension)):
    with open(filename, encoding = 'utf-8') as a_file:
        print(extract_from_filename(filename))
        count = 0
        for a_line in a_file: 
            a_line = a_line.replace("\n", "")
            a_list = a_line.split(',')

            if 'yob' in filename:
                region = 'USA'
                year = extract_from_filename(filename)
                instances = a_list.pop()
                sex = a_list.pop()
                name = a_list.pop()
            else:
                instances = a_list.pop()
                name = a_list.pop()
                year = a_list.pop()
                sex = a_list.pop()
                region = a_list.pop()

            try:
                names[name]
            except KeyError:
                cursor.execute('INSERT INTO `names_name` (`namae`) VALUES ("' + name + '");')
                connection.commit()
                names[name] = cursor.lastrowid
                
            cursor.execute('INSERT INTO `names_stat` (`year`, `instances`, `sex`, `name_id`, `region_id`) VALUES (?, ?, ?, ?, ?);', (str(year),  str(instances), sex, str(names[name]), str(regions[region])))
            connection.commit()
            count += 1
            print('.', end = "", flush = True)
        print('')

connection.close()
print('All done!')
