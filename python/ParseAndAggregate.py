#!C:/Python34/python
# -*- coding: cp1251 -*-
import os
import sys
import win32com.client
import pymysql
import xlrd
from datetime import date
from datetime import datetime

upload_folder = 'c:\\xampp\\htdocs\\upload\\'

########################################################################################################################
months = ['янв', 'февр', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сент', 'окт', 'ноя', 'дека']


class CompanyName:
    cimus = 'Цимус'
    adam = 'Адам'


class Dish:
    date = ''
    type = ''
    name = ''
    weight = ''
    price = ''
    contain = ''
    column = ''
    row = ''
    sheet_index = ''
    company = ''
    excel_id = ''
    week_number = ''
########################################################################################################################

########################################################################################################################


def log(text):
    t = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    with open('python.log', 'a') as f:
        f.write(t + '\t' + str(text) + '\n')
########################################################################################################################


########################################################################################################################
class DB:

    @staticmethod
    def read(sql, cur):
        log(sql)
        cur.execute(sql)
        return cur

    @staticmethod
    def write(sql, cur, conn):
        cur.execute(sql)
        conn.commit()

    @staticmethod
    def insert_dish(dish, cur, conn):
        sql = """INSERT INTO eda.food (Date, Type, Name, Weight, Price, Contain, CellColumn, CellRow, SheetIndex, Company, ExcelId, WeekNumber)
        VALUES ('%(Date)s', '%(Type)s', '%(Name)s', '%(Weight)s', '%(Price)s', '%(Contain)s', '%(CellColumn)s', '%(CellRow)s', '%(SheetIndex)s', '%(Company)s', '%(ExcelId)s', '%(WeekNumber)s')
        """%{'Date': dish.date,
             'Type': dish.type,
             'Name': dish.name,
             'Weight': dish.weight,
             'Price': dish.price,
             'Contain': dish.contain,
             'CellColumn': dish.column,
             'CellRow': dish.row,
             'SheetIndex': dish.sheet_index,
             'Company': dish.company,
             'ExcelId': dish.excel_id,
             'WeekNumber': dish.week_number}
        DB.write(sql, cur, conn)

########################################################################################################################


########################################################################################################################
class Parse:

    @staticmethod
    def adam(cur, conn, excel, dish, parse_result):

        log('\tParse Excel "Adam"...')

        # 1. Set default WeekNumber.
        dish.week_number = 0

        # 2. Read all Sheets
        i = 1
        while i < excel._all_sheets_count - 1:

            sheet = excel.sheet_by_index(i)

            if sheet.name.lower().strip() in ['пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс']:

                dish.sheet_index = i
                j = 0  # row

                # 3. Read the Sheet date
                date_parsed = False
                year = None
                month = None
                day = None

                # Date type '23.11.2015' and date located in 'A2' cell
                try:
                    j = 1  # Cell 'A2'
                    date_tmp = sheet.cell_value(rowx=j, colx=0)
                    date_tmp_splt = date_tmp.split('.')
                    year = date_tmp_splt[2]
                    month = date_tmp_splt[1]
                    day = date_tmp_splt[0]
                    date_parsed = True
                except:
                    pass

                # Date type is '25.07.2016' and date located in 'A3' cell
                if date_parsed is False:
                    try:
                        j = 2  # Cell 'A3'
                        date_tmp = sheet.cell_value(rowx=j, colx=0)
                        date_tmp_splt = date_tmp.split('.')
                        year = date_tmp_splt[2]
                        month = date_tmp_splt[1]
                        day = date_tmp_splt[0]
                        date_parsed = True
                    except:
                        pass

                # Date type '24 ноября 2015 г.' and located in 'A3' cell
                if date_parsed is False:
                    try:
                        j = 2  # Cell 'A3'
                        date_tmp = xlrd.xldate_as_tuple(sheet.cell_value(rowx=j, colx=0), 0)
                        year = date_tmp[0]
                        month = date_tmp[1]
                        day = date_tmp[2]
                        date_parsed = True
                    except:
                        pass

                # Date type ... and located in 'A2' cell
                if date_parsed is False:
                    try:
                        j = 1  # Cell 'A2'
                        date_tmp = xlrd.xldate_as_tuple(sheet.cell_value(rowx=j, colx=0), 0)
                        year = date_tmp[0]
                        month = date_tmp[1]
                        day = date_tmp[2]
                        date_parsed = True
                    except:
                        pass

                # Date type is string
                if date_parsed is False:
                    try:
                        date_tmp = sheet.cell_value(rowx=j, colx=0)  # Parse Date as string
                        date_tmp = date_tmp.split(' ')
                        year = date_tmp[2]
                        day = date_tmp[0]
                        month = 0

                        # Find month
                        k = 0
                        while k < len(months):
                            if months[k] in date_tmp[1]:
                                month = k + 1
                            k += 1
                    except:
                        pass

                dish.date = str(year) + '-' + str(month).zfill(2) + '-' + str(day).zfill(2)
                parse_result.dates.append(dish.date)

                # 4. Move to next x lines for reading Menu
                j += 1
                title = sheet.cell_value(rowx=j, colx=0)
                if 'наименование' in title.lower():
                    j += 1
                '''
                # 5. Add offset 1 line (row) if 'Наименование' in 'A4' (see МЕНЮ ГАММА_4 с 25-29 июля.xls)
                tmp = sheet.cell_value(rowx=j+1, colx=0)
                if 'наименование' in tmp.lower():
                    j += 1
                '''
                # 6. Read menu for specified Sheet.
                dish_type_previous = ''
                while j < sheet.nrows - 2:  # offset 2 rows from the bottom of the sheet
                    price_tmp = sheet.cell_value(rowx=j, colx=1)
                    if price_tmp == '':
                        dish.type = sheet.cell_value(rowx=j, colx=0)
                        dish_type_previous = dish.type
                        j += 1
                    else:
                        dish.type = dish_type_previous

                    dish.name = sheet.cell_value(rowx=j, colx=0)
                    dish.price = sheet.cell_value(rowx=j, colx=1)
                    dish.contain = sheet.cell_value(rowx=j, colx=2)
                    dish.column = 4
                    dish.row = j

                    # Check, whether Excel row is not empty.
                    if dish.name:
                        DB.insert_dish(dish, cur, conn)
                        parse_result.dishes_count += 1
                    j += 1
            i += 1

        log('\t...completed')
        return parse_result

    @staticmethod
    def cimus(cur, conn, file_name, excel, dish, parse_result):

        log('\tParse Excel "Cimus"...')

        # 1. Parse filename for Week Number.
        # Assume that filename is '45 Неделя ООО Адалиск.xls' or '05 Неделя ООО Адалиск.xls'
        file_name_wo_extension = os.path.basename(file_name)
        try:
            week_number = int(file_name_wo_extension.split(' ')[0])
            if 0 < week_number < 60:
                pass
            else:
                week_number = 0
        except ValueError:
            week_number = 0

        dish.week_number = week_number
        parse_result.week_number = week_number

        # 2. Parse Excel
        i = 0
        while i < excel._all_sheets_count - 1:
            sheet = excel.sheet_by_index(i)
            if sheet.name[3:].isdigit() and sheet.name[:2].isdigit():
                dish.sheet_index = i
                year = date.today().year
                if week_number < 5 and date.today().month == 12:
                    # exotic situation when you uploading Excel for next year before the New Year
                    year += 1
                dish.date = str(year) + '-' + sheet.name[3:] + '-' + sheet.name[:2]
                parse_result.dates.append(dish.date)
                j = 41
                dish_type_previous = ''

                # Read the comments
                notes = sheet.cell_note_map

                while j < sheet.nrows - 1:  # offset 1 row from the bottom of the sheet
                    dish.name = sheet.cell_value(rowx=j, colx=2)
                    if dish.name == 'ИТОГО':
                        break
                    else:

                        # Parse comments
                        try:
                            note = notes[j, 2].text
                            dish.name += ' (' + note + ')'
                            log('Comment found at cell rowx=' + str(j) + ', colx=2')
                        except Exception as e:
                            pass

                        dish.type = sheet.cell_value(rowx=j, colx=1)
                        if dish.type == '':
                            dish.type = dish_type_previous
                        dish_type_previous = dish.type
                        dish.weight = sheet.cell_value(rowx=j, colx=3)
                        dish.price = sheet.cell_value(rowx=j, colx=4)
                        dish.column = 5
                        dish.row = j
                        DB.insert_dish(dish, cur, conn)
                        parse_result.dishes_count += 1
                        j += 1
            else:
                pass
            i += 1

        log('\t...completed')
        return parse_result

    @staticmethod
    def find_company_name(excel):
        log('\tFind Company name...')
        sheet = excel.sheet_by_index(0)
        try:
            sheet.cell_value(rowx=1, colx=5)
            company_name = CompanyName.cimus
        except:
            company_name = CompanyName.adam

        log('\t...completed')
        return company_name

    @staticmethod
    def main(conn, cur, file_name, excel_id):
        log('Run "Parse/Main" method...')

        class ParseResult:
            dates = []
            dishes_count = 0
            week_number = 0

        Dish.excel_id = excel_id
        excel = xlrd.open_workbook(file_name, formatting_info=True, encoding_override="cp1252")
        Dish.company = Parse.find_company_name(excel)
        if Dish.company == CompanyName.cimus:
            parse_result = Parse.cimus(cur, conn, file_name, excel, Dish, ParseResult)
        else:
            parse_result = Parse.adam(cur, conn, excel, Dish, ParseResult)

        # Write excel metadata to the DB
        log(Dish.company)
        # log(parse_result.dates)
        # log(parse_result.dates[0])
        # log(parse_result.dates[-1])
        log(parse_result.week_number)
        log(parse_result.dishes_count)

        sql = 'UPDATE excel SET ' \
              'Company="' + Dish.company + '", ' \
              'DateFirst="' + parse_result.dates[0] + '", ' \
              'DateLast="' + parse_result.dates[-1] + '", ' \
              'Dates="' + ','.join(parse_result.dates)+ '", ' \
              'DaysCount="' + str(len(parse_result.dates)) + '", ' \
              'WeekNumber=' + str(parse_result.week_number) + ', ' \
              'DishesCount=' + str(parse_result.dishes_count) +\
              ' WHERE Id = ' + str(excel_id)

        log(sql)
        DB.write(sql, cur, conn)
        log('...completed')

########################################################################################################################


########################################################################################################################
class Aggregate:

    @staticmethod
    def for_one_user(cur, excel_id, user_id, new_file_name):

        log('Aggregate "For One User"...')

        # 1. Get Excel File location
        excel_file_location = None
        sql = 'SELECT FileLocation from excel WHERE Id = ' + excel_id
        result = DB.read(sql, cur)
        for row in result:
            excel_file_location = row[0]
        if excel_file_location is None:
            print('Unable to get information about Excel file location')
            exit()

        # 2. Get Dishes ordered by User.
        menu_item_ids = []
        counts = []
        # sql = 'SELECT orders.MenuItemId, orders.Count FROM eda.orders WHERE orders.UserId = ' + str(user_id)

        sql = """SELECT orders.MenuItemId, orders.Count FROM orders
                 JOIN food ON food.Id = orders.MenuItemId
                 WHERE orders.UserId = """ + str(user_id) + """ AND food.ExcelId = '""" + excel_id + """'"""

        result = DB.read(sql, cur)
        for row in result:
            menu_item_ids.append(row[0])
            counts.append(row[1])

        # 3. Calculate 'column_iterator' parameter.
        column_iterator = 0
        excel = xlrd.open_workbook(excel_file_location, formatting_info=True)
        if Parse.find_company_name(excel) == CompanyName.cimus:
            column_iterator = 1

        # 4. Open Excel File
        os.system("taskkill /f /im EXCEL.EXE")  # In case if previous process not finished.
        excel = win32com.client.dynamic.Dispatch("Excel.Application")
        excel_file_name = os.path.basename(new_file_name)
        wb = excel.Workbooks.Open(upload_folder + excel_file_name)

        # 5. Write Users order to Excel file.
        i = 0
        while i < len(menu_item_ids):
            sql = 'SELECT SheetIndex, CellRow, CellColumn FROM eda.food WHERE Id = ' + str(menu_item_ids[i])
            result = DB.read(sql, cur)
            for row in result:
                sheet_index = row[0] + 1  # '+1' for converting format from xlrd to win32com.
                cell_row = row[1] + 1  # '+1' for converting format from xlrd to win32com.
                cell_column = row[2] + column_iterator  # '+1' for converting format from xlrd to win32com.
                # print('Sheet Index: ', sheet_index, 'Row: ', cell_row, 'Column: ', cell_column)

                # 5. Write to Excel File.
                ws = wb.Worksheets(sheet_index)
                ws.Cells(cell_row, cell_column).Value = str(counts[i])
            i += 1

        # 6. Save and Close Excel file.
        Aggregate.save_excel(wb, excel_file_name, excel)
        log('...completed')

    @staticmethod
    def for_all_users(cur, excel_id, new_file):

        log('Aggregate "For All Users"...')

        # 1. Get distinct MenuItemId's
        log('1. Get Distinct MenuItemIds')
        menu_item_ids = []
        sql = """SELECT DISTINCT orders.MenuItemId FROM orders
                    LEFT JOIN food
                    ON food.Id = orders.MenuItemId
                    WHERE food.ExcelId = \"""" + excel_id + """\""""
        result = DB.read(sql, cur)
        for row in result:
            for menu_item_id in row:
                log('Got MenuItemId: ' + str(menu_item_id))
                menu_item_ids.append(menu_item_id)

        # 2. Compute Total Count of Orders for each MenuItemId
        log('2. Compute Total Count of Orders for each MenuItemId')
        total_count = []
        for menu_item_id in menu_item_ids:
            sql = 'SELECT SUM(Count) FROM orders WHERE MenuItemId = ' + str(menu_item_id)
            result = DB.read(sql, cur)
            for row in result:
                for count in row:
                    log('MenuItemId: ' + str(menu_item_id) + '. Count: ' + str(count))
                    total_count.append(count)

        # 3. Get the Company Name (required for 'column_iterator' parameter)
        log('3. Get the Company Name (required for column_iterator parameter)')
        column_iterator = 0
        excel = None
        try:
            excel = xlrd.open_workbook(new_file, formatting_info=True)
        except Exception as e:
            log('Error. Unable to open Excel file: ' + new_file)
            log('Error arguments: ' + e.args)
        if Parse.find_company_name(excel) == CompanyName.cimus:
            column_iterator = 1

        # 4. Kill EXCEL.EXE process (in case if previous process not finished)
        log('4. Kill EXCEL.EXE process (in case if previous process not finished)')
        os.system("taskkill /f /im EXCEL.EXE")

        # 5. Launch win32com instance
        log('5. Launch win32com instance')
        try:
            excel = win32com.client.dynamic.Dispatch("Excel.Application")
        except Exception as e:
            log('Error. Unable to launch win32com instance')
            log('Error arguments: ' + e.args)

        # 6. Open Excel File
        log('6. Open Excel file: ' + new_file)
        new_file_name_without_path = os.path.basename(new_file)
        new_file_win_path = upload_folder + new_file_name_without_path
        wb = None
        try:
            wb = excel.Workbooks.Open(new_file_win_path)
        except Exception as e:
            log('Error. Unable to open file: ' + new_file_win_path)
            log('Error arguments: ' + e.args)

        # 7. Get the cell locations and values from DB
        log('7. Get the cell locations and values from DB')
        i = 0
        while i < len(menu_item_ids):
            sql = 'SELECT SheetIndex, CellRow, CellColumn FROM eda.food WHERE Id = ' + str(menu_item_ids[i]) + ' AND ExcelId = "' + excel_id + '"'
            result = DB.read(sql, cur)
            for row in result:
                sheet_index = row[0] + 1  # '+1' for converting format from xlrd to win32com.
                cell_row = row[1] + 1  # '+1' for converting format from xlrd to win32com.
                cell_column = row[2] + column_iterator  # '+1' for converting format from xlrd to win32com.
                # print('Sheet Index: ', sheet_index, 'Row: ', cell_row, 'Column: ', cell_column)
                log('Sheet Index: ' + str(sheet_index) + '. Row: ' + str(cell_row) + '. Column: ' + str(cell_column))

                # 8. Write to Excel
                log('8. Write to Excel')
                try:
                    ws = wb.Worksheets(sheet_index)
                    ws.Cells(cell_row, cell_column).Value = str(total_count[i])
                except Exception as e:
                    log('Error. Unable to write to Excel')
                    log('Error arguments: ' + e.args[0])
            i += 1

        # 9. Disable check compatibility
        log('9. Disable check compatibility')
        wb.CheckCompatibility = False

        try:
            wb.Save()
        except Exception as e:
            log('Error. Unable to save file: ' + new_file_win_path)
            log('Error arguments: ' + e.args)

        # 10. Close Workbook
        log('10. Close Workbook')
        try:
            wb.Close(True)
        except Exception as e:
            log('Error. Unable to close Workbook')
            log('Error arguments: ' + e.args)

        # 11. Quit Excel
        try:
            excel.Application.Quit()
        except Exception as e:
            log('Error. Unable to quit excel')
            log('Error arguments: ' + e.args)

        os.system("taskkill /f /im EXCEL.EXE")  # In case if previous process not finished
        log('=> Excel saving completed')
        log('...completed')
        print('OK')

    @staticmethod
    def save_excel(wb, excel_file_name, excel):

        log('Save Excel. Started. Excel file name: ' + excel_file_name)

        # 1. Delete Previous Excel file (if exist)
        # wb.DisplayAlerts = False # not working for some reasons, instead we just delete this file:
        wb.CheckCompatibility = False  # Disable check compatibility

        # 2. Save Excel File
        wb.Save()
        wb.Close(True)
        excel.Application.Quit()
        os.system("taskkill /f /im EXCEL.EXE")  # In case if previous process not finished
        log('Save Excel. Completed')
        print('OK')

    @staticmethod
    def save_as_excel(wb, excel_file_name, excel):

        log('Save Excel. Started. Excel file name: ' + excel_file_name)

        # 1. Delete Previous Excel file (if exist).
        # wb.DisplayAlerts = False # not working for some reasons, instead we just delete this file:
        wb.CheckCompatibility = False  # Disable check compatibility.

        try:
            os.remove(upload_folder + excel_file_name)
        except:
            pass

        # 2. Save Excel File.
        wb.SaveAs(upload_folder + excel_file_name)
        wb.Close(True)
        excel.Application.Quit()
        os.system("taskkill /f /im EXCEL.EXE")  # In case if previous process not finished
        log('Save Excel. Completed')
        print('OK')

########################################################################################################################


########################################################################################################################
def main():
    
    # conn = pymysql.connect(host='eda', user='eda_admin', passwd='eda12345', db='eda', charset='utf8')
    conn = pymysql.connect(host='localhost', user='root', passwd='12345', db='eda', charset='utf8')
    cur = conn.cursor()

    log('#' * 100)
    log('Script Launched. Arguments count: ' + str(len(sys.argv)))
    
    # Write all Args to log
    i = 0
    while i < len(sys.argv):
        log('Argument ' + str(i) + ': ' + str(sys.argv[i]))
        i += 1
     
    # Call methods according to the Args
    if len(sys.argv) == 4 and sys.argv[1] == 'parse':
        Parse.main(conn, cur, sys.argv[2], sys.argv[3])
    elif len(sys.argv) == 5 and sys.argv[1] == 'aggregate':
        if sys.argv[2] == 'all':
            excel_id = sys.argv[3]
            new_file_name = sys.argv[4]
            Aggregate.for_all_users(cur, excel_id, new_file_name)
        else:
            excel_id = sys.argv[2]
            user_id = sys.argv[3]
            new_file_name = sys.argv[4]
            Aggregate.for_one_user(cur, excel_id, user_id, new_file_name)
    else:
        log('Warning! Arguments not fit')

    conn.close()
    
    conn.close()
########################################################################################################################


if __name__ == '__main__':
    main()
