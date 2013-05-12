==========
WHAT IS IT
==========

REST application and small framework with AR for simply using.
===============
TASK DESCRIPTON
===============

The applicant must write a small RESTful web service that allows a user to store and retrieve physical addresses.

The web application must meet the following requirements:
- The application must be written in PHP and be compatible with the latest stable version of PHP and must be able to run using a default production configuration.
- The application must accept and output its data in the JSON format.
- The application must offer two URL's (patterns):
    /addresses/                 Returns a list of all available addresses or accepts a new address to be stored.
    /addresses/{addressId}/     Returns or updates an existing address.
- The MVC pattern must be used in the architecture of the application.
- Use of existing frameworks, libraries or abstraction layers (e.g. Symfony, Zend Framework, PDO, etc.) is not permitted.
- No authentication or authorization of the user is required.
- The application will be tested for security vulnerabilities and user input errors, but not configuration errors.
- The address entity is defined by the following create table statement (for MySQL):
    create table ADDRESS (
        ADDRESSID int not null auto_increment primary key comment 'The unique address ID.',
        LABEL varchar(100) not null comment 'The name of the person or organisation to which the address belongs.',
        STREET varchar(100) not null comment 'The name of the street.',
        HOUSENUMBER varchar(10) not null comment 'The house number (and any optional additions).',
        POSTALCODE varchar(6) not null comment 'The postal code for the address.',
        CITY varchar(100) not null comment 'The city in which the address is located.',
        COUNTRY varchar(100) not null comment 'The country in which the address is located.'
    )
    engine 'InnoDB',
    character set utf8,
    collate utf8_general_ci,
    comment 'A physical address belonging to a person or organisation.'
    
Some tips to help you complete this application:
- If you're unfamiliar with RESTful web services, Wikipedia <http://en.wikipedia.org/wiki/Representational_State_Transfer> is a good start.
- Users of Firefox may want to use the Poster extension <https://addons.mozilla.org/en-US/firefox/addon/poster/> to help them test their web service. Similar extensions are available for other browsers.
- Don't waste time on writing needless abstraction layers. Just focus on the requirements. Try to limit the amount of time you spend on this task to roughly 4 - 6 hours.
- We'll be looking for structure, use of best practices and a consistent coding style. Make sure to pay attention to detail.
- If anything is unclear, take your best guess and document this choice (and why you had to make it).
- We'll use the latest stable version of PHP, MySQL and Apache under CentOS 6 to run your application, but you're free to use any other environment. Just make sure not to depend on any specific configuration.

=============
CONFIGURATION
=============

1. Create new database. Install SQL dump from db.sql file.
2. Config located in config.php file. Open this file for edit.
3. Edit Db configuration params, set dsn, user, password properties.
   Used PDO class, more information at http://php.net/manual/en/book.pdo.php.
4. If server return 503 error - please check mod_rewrite Apache module.

Thanks.
