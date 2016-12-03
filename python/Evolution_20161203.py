import pymysql
import hashlib
import os
from ParseAndAggregate import DB

########################################################################################################################
excel_path = r'c:\xampp\htdocs\upload'
########################################################################################################################

# Connect to DB
conn = pymysql.connect(host='localhost', user='root', passwd='12345', db='eda', charset='utf8')
cur = conn.cursor()

for i in range(1, 1000):

    print(str(i).zfill(3) + ' ' + '#' * 100)

    class Excel:
        id = None
        date_first = None
        date_last = None
        dates = None
        days_count = None
        company = None
        week_number = None
        dishes_count = None
        file_location = None
        original_file_name = None
        checksum = '00000000000000000000000000000000'
        uploaded_by = '2'

    # 1. Get Excel Id
    print('1. Get Excel Id')
    sql = 'SELECT ExcelId, Company, WeekNumber FROM food WHERE ExcelId LIKE "%.%" LIMIT 1'
    print('\t' + sql)
    result = DB.read(sql, cur)
    for row in result:
        Excel.file_location = row[0]
        Excel.company = row[1]
        Excel.week_number = row[2]
        Excel.original_file_name = os.path.basename(row[0])
        tmp_file_location = os.path.join(excel_path, row[0].split('/')[-1])
        if os.path.isfile(tmp_file_location):
            Excel.checksum = hashlib.md5(open(tmp_file_location, 'rb').read()).hexdigest()

    if not Excel.file_location:
        break

    # 2. Get Dates
    print('2. Get Dates')
    sql = 'SELECT DISTINCT Date FROM food WHERE ExcelId = "' + Excel.file_location + '"'
    print('\t' + sql)
    result = DB.read(sql, cur)
    dates = []
    for row in result:
        if row[0]:
            date = row[0].strftime('%Y-%m-%d')
            dates.append(date)

    Excel.date_first = dates[0]
    Excel.date_last = dates[-1]
    Excel.days_count = str(len(dates))
    Excel.dates = ','.join(dates)

    # 3. Get Dishes Count
    print('3. Get Dishes Count')
    sql = 'SELECT Count(Id) FROM food WHERE ExcelId = "' + Excel.file_location + '"'
    print('\t' + sql)
    result = DB.read(sql, cur)
    for row in result:
        Excel.dishes_count = str(row[0])

    # 4. Write data to "excel" table
    print('4. Write data to "excel" table')
    sql = 'INSERT INTO excel (DateFirst, Datelast, Dates, DaysCount, Company, WeekNumber, DishesCount, OriginalFileName, FileLocation, Checksum, UploadedBy) VALUES ("' +\
          str(Excel.date_first) + '", "' + \
          str(Excel.date_last) + '", "' + \
          str(Excel.dates) + '", ' + \
          str(Excel.days_count) + ', "' + \
          str(Excel.company) + '", ' + \
          str(Excel.week_number) + ', ' + \
          str(Excel.dishes_count) + ', "' + \
          str(Excel.original_file_name) + '", "' + \
          str(Excel.file_location) + '", "' + \
          str(Excel.checksum) + '", ' + \
          str(Excel.uploaded_by) + ')'
    print('\t' + sql)
    DB.write(sql, cur, conn)

    # 5. Get Id from "excel" table
    print('5. Get Id from "excel" table')
    sql = 'SELECT Id FROM excel WHERE FileLocation = "' + Excel.file_location + '"'
    print('\t' + sql)
    result = DB.read(sql, cur)
    for row in result:
        Excel.id = str(row[0])

    # 6. Update "food" table with new ExcelId
    print('6. Update "food" table with new ExcelId')
    sql = 'UPDATE food SET ExcelId = ' + Excel.id + ' WHERE ExcelId = "' + Excel.file_location + '"'
    print('\t' + sql)
    DB.write(sql, cur, conn)

# Close DB connection
conn.close()
