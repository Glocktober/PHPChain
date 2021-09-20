### PHPChain redux 

This is an update to what is a pretty ancient password management application. PHPChain was developed against PHP v4, using the PHP mcrypt library, and mySQL.  The original documentation (a README file) [is available as original_README.txt](original_README.txt)

### Updates

* converted for use on PHP 7.4 and 8.0
    * Minor deprecations (e.g. each()) and updated tag style (e.g. `<?` to `<?php`)

* converted from mcrypt (deprecated in PHP 7.2) to use the openssl_encrypt.

    * Unfortunately this is NOT compatible encryption.
    * I do have a process for upgrading passwords.

* Cosmetics (css lipstick on a pig)
* Upgraded to more contemporary security practices:
    * CSRF tokens are used
    * Sanitization of form and query string parameter data against injection attacks
    * Moved key paramenters from cookies to session data.
    * session cookie is HTTPonly, Secure, (and samesite strict)
* Added database support for sqlite3.
    * date formats now implemented using epoch time in PHP and not database specific
    * SQL statements are more portable
* 