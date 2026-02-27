# Test code for a photoQueue System

This photoQueue system is going to be implemented on a wordpress website, so this is a test enviorment. Also don't use it as the url's are hardcoded, sends almost direct querys to the database (they are filtered a little) and is generally a mess.

## How and what

The wordpress page is run locally with localWP, with the plugins "Elementor" and "Akismet" as they are used on the live site.

The display pages are made using elementors "html" widget and the "backend" stuff is contained in the "../wp-content/themes/hello-elementor/functions.php"

Also th it is not good and is currently being made into a plugin instead.

## How to use
First don't.
But if you really need too, then you need to make a new table in your database with
```
CREATE TABLE Queue (
    class VARCHAR(7) NOT NULL, 
    gradYear INT NOT NULL ,
    time TIME NOT NULL, 
    PRIMARY KEY (time(8))) ENGINE = InnoDB; 
```
and then import the code in "../hello-elementor/functions.php" to your local functions.php (hope you're not using another theme :)))) and also copy the html/css/javaScript code inside "queueadmin" and "photo-queue" (hope you like digging around in SQL). So yeah... don't use this.