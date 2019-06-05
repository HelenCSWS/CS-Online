rem delete all txt files

del ab_inventory_enotecca.txt
del ab_inventory_hillside.txt
del ab_sales.txt
del ab_sales_enot.txt
del ab_sales_hill.txt

rem create txt file with new date

wget -qO - http://csonline.christopherstewart.com/ABWebMiningDateReplace.php


rem run the bat file to get data from website

call ab_inventory.bat
call ab_monthly_sales.bat
call ab_monthly_sales_bc.bat


rem generate data from database and send excel file by email

wget -qO - http://csonline.christopherstewart.com/ABVenderData.php



