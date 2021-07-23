TIME=`date +%d-%b-%y`
cd /var/www/vhosts/pointestudio.fr/bk/gecps1/
# plesk db dump gecps1 > gecps1-$TIME.sql
mysqldump --defaults-extra-file=/var/www/vhosts/pointestudio.fr/gecps1.pointestudio.fr/tools/bk_script/conf.txt gecps1 > gecps1-$TIME.sql
zip gecps1-$TIME.zip gecps1-$TIME.sql
rm -f gecps1-$TIME.sql
find "./" -type f -mtime +10 -delete
