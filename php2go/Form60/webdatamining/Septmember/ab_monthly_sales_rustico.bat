
rem login
wget --post-file=login.txt --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/names.nsf?Login

rem get report selector page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm 

rem get report selector2 page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/7b48148dc3cf0abb87256f49006145a5?EditDocument&Seq=1&val=5621576"

rem finally get the report
wget --post-file=ab_sales_rustico.txt --referer="http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/7b48148dc3cf0abb87256f49006145a5?EditDocument&Seq=1&val=5621576" --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/7b48148dc3cf0abb87256f49006145a5?EditDocument&Seq=1&val=5621576"

del ab_sales_rustico.html
ren "7b48148dc3cf0abb87256f49006145a5?EditDocument&Seq=1&val=5621576" ab_sales_rustico.html
del LCAGTP.NSF
del cookie.txt


copy ab_sales_rustico.html ..\reports\ab_sales_rustico.html /Y
