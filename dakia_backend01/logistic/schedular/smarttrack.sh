################################################################
##   MySQL Database Backup Script 
##   Written By: Tahir Akbar
##   Last Update: July 22, 2019
################################################################
TODAY=`date +"%A"`
mysqladmin -h213.246.109.140 -u'super_tahir' -p'2see9lf1EJhycynQ' create smart_${TODAY}
if [ $? -eq 0 ]; then
  echo "Database created successfully"
fi

mysqldump -usuper_tahir -h213.246.109.140 -p'2see9lf1EJhycynQ' smart --quick --skip-lock-tables  | mysql -h213.246.109.140 -usuper_tahir -p'2see9lf1EJhycynQ' smart_${TODAY}
if [ $? -eq 0 ]; then
  echo "Database backup successfully completed"
  echo smart_${TODAY}"successsfully backed up on server 6(213.246.109.140)" | mail -s "Database copied successfullt " mtahir.nusrat@gmail.com
else
  echo "Error found during backup"
fi


