rem run the bat file to get data from website

call ab_inventory.bat
call ab_monthly_sales.bat
call ab_monthly_sales_bc.bat


rem generate data from database and send excel file by email

wget -qO - http://csonline.christopherstewart.com/ABVenderData.php



