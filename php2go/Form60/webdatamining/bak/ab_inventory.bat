rem get Hillside data


rem login
wget --post-file=login.txt --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/names.nsf?Login

rem get report selector page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm 

rem get report selector2 page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/fed7d0eb7a72f7db87256b8700744385?EditDocument&Seq=1&val=8647592"


rem finally get the report
wget --post-file=ab_inventory_hillside.txt --referer="http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/fed7d0eb7a72f7db87256b8700744385?EditDocument&Seq=1&val=8647592" --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/fed7d0eb7a72f7db87256b8700744385?EditDocument&Seq=1&val=8647592"


del ab_inventory_hillside.html
ren "fed7d0eb7a72f7db87256b8700744385?EditDocument&Seq=1&val=8647592" ab_inventory_hillside.html
del LCAGTP.NSF
del cookie.txt

copy ab_inventory_hillside.html ..\reports\ab_inventory_hillside.html /Y



rem get enotecca data


rem login
wget --post-file=login.txt --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/names.nsf?Login

rem get report selector page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm 

rem get report selector2 page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/fed7d0eb7a72f7db87256b8700744385?EditDocument&Seq=1&val=8647592"


rem finally get the report
wget --post-file=ab_inventory_enotecca.txt --referer="http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/fed7d0eb7a72f7db87256b8700744385?EditDocument&Seq=1&val=8647592" --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/fed7d0eb7a72f7db87256b8700744385?EditDocument&Seq=1&val=8647592"

del ab_inventory_enotecca.html
ren "fed7d0eb7a72f7db87256b8700744385?EditDocument&Seq=1&val=8647592" ab_inventory_enotecca.html
del LCAGTP.NSF
del cookie.txt

copy ab_inventory_enotecca.html ..\reports\ab_inventory_enotecca.html /Y

