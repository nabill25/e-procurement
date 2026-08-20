<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * App Configuration
 *
 * @generated
 */
class App extends BaseConfig
{
    /**
     * Base Site URL
     */
    public string $baseURL = 'http://localhost:8080/';

    /**
     * Allowed Hostnames in the Site URL other than the hostname in the baseURL.
     */
    public array $allowedHostnames = [];

    /**
     * Index File (Leave blank if using Apache mod_rewrite or nginx)
     */
    public string $indexPage = '';

    /**
     * URI Protocol
     */
    public string $uriProtocol = 'REQUEST_URI';

    /**
     * Allowed URI Characters
     */
    public string $permittedURIChars = 'a-z 0-9~%.:_\-@!$&\'()*+,;=';

    /**
     * Default Locale
     */
    public string $defaultLocale = 'id';

    /**
     * Negotiate Locale
     */
    public bool $negotiateLocale = false;

    /**
     * Supported Locales
     */
    public array $supportedLocales = ['id', 'en'];

    /**
     * Application Timezone
     */
    public string $appTimezone = 'Asia/Jakarta';

    /**
     * Default Character Set
     */
    public string $charset = 'UTF-8';

    /**
     * Force Global Secure Requests
     */
    public bool $forceGlobalSecureRequests = false;

    /**
     * Reverse Proxy IPs
     */
    public string|array $proxyIPs = '';

    /**
     * CSRF Token Name
     */
    public string $CSRFTokenName = 'csrf_token';

    /**
     * CSRF Header Name
     */
    public string $CSRFHeaderName = 'X-CSRF-TOKEN';

    /**
     * CSRF Cookie Name
     */
    public string $CSRFCookieName = 'csrf_cookie';

    /**
     * CSRF Expiration time (seconds)
     */
    public int $CSRFExpire = 7200;

    /**
     * CSRF Regenerate
     */
    public bool $CSRFRegenerate = true;

    /**
     * CSRF Redirect
     */
    public bool $CSRFRedirect = false;

    /**
     * CSRF SameSite
     */
    public string $CSRFSameSite = 'Lax';

    /**
     * Cookie Prefix
     */
    public string $cookiePrefix = '';

    /**
     * Cookie Domain
     */
    public string $cookieDomain = '';

    /**
     * Cookie Path
     */
    public string $cookiePath = '/';

    /**
     * Cookie Secure
     */
    public bool $cookieSecure = false;

    /**
     * Cookie HTTPOnly
     */
    public bool $cookieHTTPOnly = false;

    /**
     * Cookie SameSite
     */
    public string $cookieSameSite = 'Lax';

    /**
     * Reverse Proxy trust header
     */
    public bool $trustProxyHeader = false;

    /**
     * Content Security Policy
     *
     * Enables the Response's Content Secure Policy to restrict the sources that
     * can be used for executable scripts, etc.
     */
    public bool $CSPEnabled = false;
}
