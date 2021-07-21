[![Codacy Badge](https://app.codacy.com/project/badge/Grade/08d76aaa95b34640bba64b70c123383a)](https://www.codacy.com/gh/nvendeville/BileMo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=nvendeville/BileMo&amp;utm_campaign=Badge_Grade)

# BileMo
Exposer une API REST

# prerequest
Composer https://getcomposer.org/download/

# install and run

- **Step 1** : In your Terminal run ``git clone https://github.com/nvendeville/BileMo.git``

- **Step 2** : In your Terminal run ``cd BileMo``

- **Step 3** : In your Terminal run the command ``composer install``

- **Step 4** : Rename the file **.env.dist** to **.env**

- **Step 5** : Choose a name for your DataBase

- **Step 6** : Update ``###> doctrine/doctrine-bundle ###`` in your file **.env**

  - Uncomment the ligne related to your SGBQ
  
    DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db" **for sqlite**
    DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name" **for mysql**
    DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=13&charset=utf8" **for postgresql**
    DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=mariadb-10.5.8" **for mariadb**
    DATABASE_URL="oci8://db_user:db_password@127.0.0.1:1521/db_name **for oracle**
    
  - Set the db_user and/or db_password and/or db_name (name chosen on step 4)

- **Step 7** : In your Terminal, create and set your database 
  - Run ``php bin/console doctrine:database:create`` give the name chosen on step 4
  - Run ``php bin/console make:migration``
  - Run ``php bin/console doctrine:migrations:migrate``
    
- **Step 8** : In your Terminal, load the available set of data
  - Run ``php bin/console doctrine:fixtures:load``
  - Available data :
    - 1 SUPER_ADMIN for the Company 1
    - 4 ADMIN for the 4 other companies
    - 25 USERS assigned to the 4 companies
    - The 30 created users have "coucou" as password

- **Step 9** : In your Terminal run the command ``symfony serve``

- **Step 10** : From your browser go to http://locahost:8000/api/doc to open the swagguer documentation
  (To manage the different endpoints, you can use Postman's tool)
