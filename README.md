### PHPChain redux 

This is an update to what is a pretty ancient password management application. PHPChain was developed against PHP v4, using the PHP mcrypt library, and mySQL.  The original documentation (a README file) [is available as original_README.txt](original_README.txt)

### Updates

* converted for use on PHP 7.4 and 8.0
    * Minor deprecations (e.g. each()) and updated tag style (e.g. `<?` to `<?php`)
    * Truthfully most my testing has been a combination of 7.1 (the last version to provide mcrypt) and 7.4.

* converted from mcrypt (deprecated in PHP 7.2) to use the openssl_encrypt.

    * Unfortunately this is NOT compatible encryption - old passwords need some hand processing to convert.
    * I do have a process for upgradingencrypted parts, but haven't had time to document.
    * The encryption, none-the-less is pretty weak, and I know some of the ciphertext is double base64 encoded, but I'm not too concerned at this point. If I had time I'd have it only store encrypted passwords (not sites, urls, logins) and only encrypt/decrypt passwords in the browser.

* Cosmetics and UI.
    * This started with some css lipstick on a pig, but went to an almost entire (and I think it is entire) replacement and upate to use w3.css.
    * Filters are over tables to allow searching of large pages, as well as the category column.
    * Click on the password or login to copy it to your clipboard.
    * I'm not going to claim this is a responsive or mobile app, but it is a lot more friendly than the original.
    * I'm hardly a convert to material design, but it looks fresh compared to the original code, and w3.css makes it so easy.
    * I've checked this on Firefox, Chrome, Edge, and Safari.
    * Quick eval on iPhone - it's not pretty, but usable, but it does work (D+).
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
    * Sqlite3 handles multiple users just fine. I don't know if it would go web-scale, but it works fine with a few dozen users and does not appear to be close to top.
* Notes can be added to entries, allowing more detailed data. This data is not encrypted.  The size is currently set at upt to 2048 bytes per entry.  The idea here is it gives a way to attach ssh keys, etc.  but keep in mind these are clear text in the database.
* HTML in the original was done by concatenating HTML and echoing out a huge string at the end.  Most/all of this has been redone to be flat HTML with php additions - kind of the reverse.  I would have moved to a template engine but I'm not super into the PHP ecosystem to know the best way forward.
* The form part of views is now seperated from the action part. i.e. login.php presents a login form and loginexec.php processes the actual login. This has eliminated massive case statements or if/else structures in the original code.
* Some logic is pushed to the browser, especially on the catview page. This requests confirmation for delete and a detail pane for entries.
