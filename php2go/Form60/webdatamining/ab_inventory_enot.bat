rem get enotecca data


rem login
wget --post-file=login.txt --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/names.nsf?Login

rem get report selector page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm 

rem get report selector2 page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/728C72B522EDB732872578480076CD27?OpenDocument&val=6783245"


rem finally get the report
wget --post-file=ab_inventory_enotecca.txt --referer="http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/728C72B522EDB732872578480076CD27?OpenDocument&val=6783245" --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/728C72B522EDB732872578480076CD27?OpenDocument&val=6783245"

del ab_inventory_enotecca.html
ren "728C72B522EDB732872578480076CD27@OpenDocument&val=6783245" ab_inventory_enotecca.html
del LCAGTP.NSF
del cookie.txt

copy ab_inventory_enotecca.html ..\reports\ab_inventory_enotecca.html /Y

