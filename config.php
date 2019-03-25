<?php
/**
 * Metadata that will be publicly visible on the site
 */
const org_name = "";        // String, Name of organization. Will show up in drawer under app logo
const app_icon = "";        // String, the absolute web path to the app logo
const web_title = "";       // String, Metadata title of app
const web_description = ""; // String, Metadata description for app
const web_favicon = "";     // String, The absolute web path to the app favicon
const web_domain = "";      // String, The domain on which the app is hosted, will be used for cookies
const web_ssl = false;      // Boolean, Whether or not the site uses ssl and whether or not cookies should be made secure only
/**
 * Google Analytics Configuration -- Use of Google analytics is optional, but google_analytics_use must be false if not being used
 */
const google_analytics_use = true;      // Boolean, Whether or not to use Google Analytics on site
const google_analytics_tag_id = "";     // String, The Tag ID of the Google Analytics property if Google Analytics is being used
/**
 * Database configuration information -- Mandatory, Must be a MySQL Database
 */
const db_host = "";         // String, Database Host
const db_name = "";         // String, Database Name
const db_username = "";     // String, Database username
const db_password = "";     // String, Database password
/**
 * App Configuration Information -- Mandatory
 */
const session_id_cookie = "";       // String, Name of cookie referencing ID information -- Cannot be the same as session_voting_cookie
const session_voting_cookie = "";   // String, Name of cookie referencing voting information -- Cannot be the same as session_id_cookie
const google_auth_client_id = "";   // String, Client ID for Google Identity Platform,
                                        // Read more here: https://developers.google.com/identity/sign-in/web/sign-in
const auth_allowed_org = "";        // String, Only allow users of a certain organization to be able to sign in
                                        // Or leave as "*" to allow all users
                                        // Accepted Format: "stuy.edu, bxsci.edu" or "*"
const app_time_zone = "";           // String, Primary time zone for the users of the app
                                        // App will automatically adjust times on client's end to match server
                                        // Time Zone must be a supported PHP Time Zone:
                                        // https://www.php.net/manual/en/timezones.php
const app_root = __dir__;           // DO NOT EDIT UNLESS APP ROOT IS DIFFERENT FROM LOCATION OF CONFIG FILE
/**
 * Email configuration -- Mandatory!
 */
const smtp_host = "";           // String, The SMTP endpoint
const smtp_auth = true;         // Boolean, True if the endpoint requires authentication
const smtp_username = "";       // String, The username to authenticate with SMTP endpoint
const smtp_password = "";       // String, The password to authenticate with SMTP endpoint
const smtp_secure_method = "";  // String, The method of which to communicate with SMTP endpoint, "ssl" and "tls" are accepted
const smtp_port = 465;          // Int, The port to be used for the SMTP endpoint
const smtp_from_email = "";     // String, The email with which outgoing emails will be sent from
const smtp_from_name = "";      // String, The name that will be used for outgoing emails