# web-app-bookshop
## What?
This bookshop web applications enables users to either acquire from or sell to books one another. 

## How?
- Server-side scripting for generating dynamic content for the web pages has been implemented in PHP
- Client-side scripting for form validation has been implemented in JavScript
- Styling of HTML pages has been implemented using CSS
- MariaDB relational DBMS
- Website is hosted on Apache HTTP web server

## How to run it?
Downlaod XAMPP installer at this [link](https://www.apachefriends.org/download.html) (free and open-source cross-platform web server solution stack package developed by Apache consisting of the Apache HTTP Server, MariaDB database and interpreters for scripts written in the PHP and Perl).  
Install XAMPP in default folder (C:\xampp).  
After installation is completed, launch the XAMPP Control Panel and start the Apache and MySQL services and move the project folder 'mybookshop' into C:\xampp\htdocs. 
Go to http://localhost/phpmyadmin/ to access to phpMyAdmin service and create a new database instance called 'compravendo' using the 'compravendo.sql' SQL script in the database folder of the project.  
Now that everything is set, go to http://localhost/home.php in order to start using the website.
