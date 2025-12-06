<?php

//-----------CiuisCRM REMOTE CHECK BASIC SETTINGS-----------//

define("APL_SALT", "0c4e8d1cc7b9f74c");
define("APL_ROOT_URL", "https://license.suisesoft.tech");
define("APL_PRODUCT_ID", 6);
define("APL_DAYS", 30);
define("APL_STORAGE", "FILE");
define("APL_DATABASE_TABLE", "user_data");
define("APL_LICENSE_FILE_LOCATION", "license.dat");
define("APL_NOTIFICATION_NO_CONNECTION", "Can't connect to licensing server.");
define("APL_NOTIFICATION_INVALID_RESPONSE", "Invalid server response.");
define("APL_NOTIFICATION_DATABASE_WRITE_ERROR", "Can't write to database.");
define("APL_NOTIFICATION_LICENSE_FILE_WRITE_ERROR", "Can't write to license file.");
define("APL_NOTIFICATION_SCRIPT_ALREADY_INSTALLED", "License is already installed.");
define("APL_NOTIFICATION_LICENSE_CORRUPTED", "License is not installed yet.");
define("APL_NOTIFICATION_BYPASS_VERIFICATION", "No need to verify");
define("APL_INCLUDE_KEY_CONFIG", "ae5e1b5fb36d9c44");
define("APL_ROOT_IP", "");
define("APL_DELETE_CANCELLED", "YES");
define("APL_DELETE_CRACKED", "YES");
define("APL_GOD_MODE", "NO");
define("APL_USER_INPUT_NOTIFICATION_INVALID_ROOT_URL", "User input error: Invalid installation URL (it should have a valid scheme and no / symbol at the end)");
define("APL_USER_INPUT_NOTIFICATION_EMPTY_LICENSE_DATA", "Please enter your license key");
define("APL_USER_INPUT_NOTIFICATION_INVALID_EMAIL", "User input error: invalid licensed email (it should be a valid email address)");
define("APL_USER_INPUT_NOTIFICATION_INVALID_LICENSE_CODE", "User input error: invalid license code (it should be a code in plain text)");
define("APL_CORE_NOTIFICATION_INVALID_SALT", "Configuration error: invalid or default encryption salt");
define("APL_CORE_NOTIFICATION_INVALID_ROOT_URL", "Configuration error: invalid root URL of CiuisCRM License Server");
define("APL_CORE_NOTIFICATION_INVALID_PRODUCT_ID", "Configuration error: invalid product ID");
define("APL_CORE_NOTIFICATION_INVALID_VERIFICATION_PERIOD", "Configuration error: invalid license verification period");
define("APL_CORE_NOTIFICATION_INVALID_STORAGE", "Configuration error: invalid license storage option");
define("APL_CORE_NOTIFICATION_INVALID_TABLE", "Configuration error: invalid MySQL table name to store license signature");
define("APL_CORE_NOTIFICATION_INVALID_LICENSE_FILE", "Configuration error: invalid license file location (or file not writable)");
define("APL_CORE_NOTIFICATION_INVALID_ROOT_IP", "Configuration error: invalid IP address of CiuisCRM License Server");
define("APL_CORE_NOTIFICATION_INVALID_ROOT_NAMESERVERS", "Configuration error: invalid nameservers of CiuisCRM License Server");
define("APL_CORE_NOTIFICATION_INVALID_DNS", "License error: actual IP address and/or nameservers of your CiuisCRM installation don't match specified IP address and/or nameservers");
define("APL_DIRECTORY", __DIR__);

