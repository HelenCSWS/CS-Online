rem login
wget --post-file=login.txt --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/names.nsf?Login

rem get report selector page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm 

rem get report selector2 page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/409D5234C9F4B577872578480076CD2B?OpenDocument&val=8618295"

rem finally get the report
wget --post-file=ab_sales.txt --referer="http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/409D5234C9F4B577872578480076CD2B?OpenDocument&val=8618295" --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/409D5234C9F4B577872578480076CD2B?OpenDocument&val=8618295"



del ab_sales_report.html
ren "409D5234C9F4B577872578480076CD2B?OpenDocument&val=8618295" ab_sales_report.html
del LCAGTP.NSF
del cookie.txt

copy ab_sales_report.html ..\reports\ab_sales_report.html /Y
