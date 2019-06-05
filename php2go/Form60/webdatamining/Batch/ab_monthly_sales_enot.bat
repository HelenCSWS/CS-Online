
rem login
wget --post-file=login.txt --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/names.nsf?Login

rem get report selector page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm 

rem get report selector2 page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://www.liquorconnect.ca/lc/liis.nsf/Report_Selector/409d5234c9f4b577872578480076cd2b?EditDocument&Seq=1&val=6637369"

rem finally get the report
wget --post-file=ab_sales_enot.txt --referer="http://www.liquorconnect.ca/lc/liis.nsf/Report_Selector/409d5234c9f4b577872578480076cd2b?EditDocument&Seq=1&val=6637369" --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://www.liquorconnect.ca/lc/liis.nsf/Report_Selector/409d5234c9f4b577872578480076cd2b?EditDocument&Seq=1&val=6637369"

del ab_sales_enot.html
ren "409d5234c9f4b577872578480076cd2b?EditDocument&Seq=1&val=6637369" ab_sales_enot.html
del LCAGTP.NSF
del cookie.txt


copy ab_sales_enot.html ..\reports\ab_sales_enot.html /Y
copy ab_sales_enot.html ..\reports\ab_sales_enot.html /Y