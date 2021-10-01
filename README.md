### PHPChain redux 

This is an update to what is a pretty ancient password management application. PHPChain was developed against PHP v4, using the PHP mcrypt library, and mySQL.  The original documentation (a README file) [is available as original_README.txt](original_README.txt)

### Updates

* converted for use on PHP 7.4 and 8.0
    * Minor deprecations (e.g. each()) and updated tag style (e.g. `<?` to `<?php`)

* converted from mcrypt (deprecated in PHP 7.2) to use the openssl_encrypt.

    * Unfortunately this is NOT compatible encryption - old passwords need some hand processing to convert.
    * I do have a process for upgrading passwords, but haven't had time to document.

* Cosmetics and UI.
    * This started with some css lipstick on a pig, but went to an almost entire (and I think it is entire) replacement and upate to use w3.css.
    * Filters are over tables to allow searching of large pages, as well as the category column.
    * Click on the password or login to copy it to your clipboard.
    * I'm not going to claim this is a responsive or mobile app, but it is a lot more friendly than the original.
* Upgraded to more contemporary security practices:
    * CSRF tokens are used
    * Sanitization of form and query string parameter data against injection and XSS attacks
    * Moved key paramenters from cookies to session data.
    * session cookie is HTTPonly, Secure, (and samesite strict)
* Database changes:
    * Added database support for sqlite3.
    * date formats now implemented using epoch time in PHP and not database specific
    * SQL statements are more portable
    * the create and last modified date is added for each entry. Create date is not used in the app but is available for forensics.
* Notes can be added to entries, allowing more detailed data. This data is not encrypted.  The size is currently set at upt to 2048 bytes per entry.
* HTML in the original was done by concatenating HTML and echoing out a huge string at the end.  Most/all of this has been redone to be flat HTML with php additions - kind of the reverse.  I would have moved to a template engine but I'm not super into the PHP ecosystem to know the best way forward.
* The form part of views is now seperated from the action part. i.e. login.php presents a login form and loginexec.php processes the actual login. This has eliminated massive case statements or if/else structures in the original code.
