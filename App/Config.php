<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'DB_HOST';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'DB_NAME';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'DB_USER';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'DB_PASSWORD';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;
	
	/**
     * Secret key for hashing
     * @var boolean
     */
    const SECRET_KEY = 'HASHING_SECRET_KEY';
	
		/**
     * Secret key for captcha
     * @var boolean
     */
    const SECRET_KEY_CAPTCHA = 'CAPTCHA_SECRET_KEY';
	
	  /**
     * Set the mail sender
     *
     * @var string
     */
	const EMAIL_FROM = 'YOUR_DOMAIN_EMAIL_ADDRESS';
	const SMTP_HOST = 'YOUR_DOMAIN_NAME';
	const SMTP_USER = 'YOUR_DOMAIN_USER_NAME';
	const SMTP_PASS = 'YOUR_DOMAIN_EMAIL_PASSWORD';
}
