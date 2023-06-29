How to install site to user folder(adopt it i=to the normal vhost installation) 0. if you have admin rights please set the next PHP8 settings:
Open php.ini file and add next options:
zend_extension=opcache
extension=intl
realpath_cache_size = 5M
opcache.enable=On
opcache.enable_cli=On

1. In the user's home folder or subdirectory (not in public_html ! ) run: git clone https://github.com/Olha-Sereda/qa_aplication.git
2. cd to the qa_aplication
3. In qa_aplication folder make 2 folders var and vendor
4. In qa_aplication folder run: php bin/composer install
5. In file .env set the right connection string to database if you have already crearted(ex: DATABASE_URL="mysql://username:password@localhost:3306/dbname" )
6. In file qa_appliation/config/packages/doctrine.yaml set the right version of database (ex: server_version: "8.0.25" )
   7: To prepare data on database install
   symfony console make:migration
   symfony console doctrine:migrations:migrate
   #check the data migrated successfully
   symfony console doctrine:migrations:status

7. Load test data:
   symfony console doctrine:fixtures:load
