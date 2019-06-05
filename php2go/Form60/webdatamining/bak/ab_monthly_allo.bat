rem login
wget --post-file=login.txt --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/names.nsf?Login

rem get report selector page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LCAGTP.NSF --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm 

rem get report selector2 page
rem wget --referer=http://lcb.liquorconnect.ca/LC/LIIS.nsf/Report_Selector_Web?OpenForm --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/730d21aa8642fdca87256bb30071f474?EditDocument&Seq=1&val=358434"

rem finally get the report
wget --post-file=ab_monthly_allo.txt --referer="http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/730d21aa8642fdca87256bb30071f474?EditDocument&Seq=1&val=358434" --cookies=on --load-cookies=cookie.txt --keep-session-cookies --save-cookies=cookie.txt "http://lcb.liquorconnect.ca/lc/liis.nsf/Report_Selector/730d21aa8642fdca87256bb30071f474?EditDocument&Seq=1&val=358434"

del ab_monthly_allo.html
ren "730d21aa8642fdca87256bb30071f474?EditDocument&Seq=1&val=358434" ab_monthly_allo.html
del LCAGTP.NSF
del cookie.txt


copy ab_monthly_allo.html ..\reports\ab_monthly_allo.html /Y
