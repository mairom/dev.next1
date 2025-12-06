<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Cookie\Exceptions;

use CodeIgniter\Exceptions\FrameworkException;

/**
 * CookieException is thrown for invalid cookies initialization and management.
 */
class CookieException extends FrameworkException
{
    /**
     * Thrown for invalid type given for the "Expires" attribute.
     *
     * @return static
     */
    public static function forInvalidExpiresTime(string $type)
    {
        return new static(lang2('Cookie.invalidExpiresTime', [$type]));
    }

    /**
     * Thrown when the value provided for "Expires" is invalid.
     *
     * @return static
     */
    public static function forInvalidExpiresValue()
    {
        return new static(lang2('Cookie.invalidExpiresValue'));
    }

    /**
     * Thrown when the cookie name contains invalid characters per RFC 2616.
     *
     * @return static
     */
    public static function forInvalidCookieName(string $name)
    {
        return new static(lang2('Cookie.invalidCookieName', [$name]));
    }

    /**
     * Thrown when the cookie name is empty.
     *
     * @return static
     */
    public static function forEmptyCookieName()
    {
        return new static(lang2('Cookie.emptyCookieName'));
    }

    /**
     * Thrown when using the `__Secure-` prefix but the `Secure` attribute
     * is not set to true.
     *
     * @return static
     */
    public static function forInvalidSecurePrefix()
    {
        return new static(lang2('Cookie.invalidSecurePrefix'));
    }

    /**
     * Thrown when using the `__Host-` prefix but the `Secure` flag is not
     * set, the `Domain` is set, and the `Path` is not `/`.
     *
     * @return static
     */
    public static function forInvalidHostPrefix()
    {
        return new static(lang2('Cookie.invalidHostPrefix'));
    }

    /**
     * Thrown when the `SameSite` attribute given is not of the valid types.
     *
     * @return static
     */
    public static function forInvalidSameSite(string $sameSite)
    {
        return new static(lang2('Cookie.invalidSameSite', [$sameSite]));
    }

    /**
     * Thrown when the `SameSite` attribute is set to `None` but the `Secure`
     * attribute is not set.
     *
     * @return static
     */
    public static function forInvalidSameSiteNone()
    {
        return new static(lang2('Cookie.invalidSameSiteNone'));
    }

    /**
     * Thrown when the `CookieStore` class is filled with invalid Cookie objects.
     *
     * @param list<int|string> $data
     *
     * @return static
     */
    public static function forInvalidCookieInstance(array $data)
    {
        return new static(lang2('Cookie.invalidCookieInstance', $data));
    }

    /**
     * Thrown when the queried Cookie object does not exist in the cookie collection.
     *
     * @param list<string> $data
     *
     * @return static
     */
    public static function forUnknownCookieInstance(array $data)
    {
        return new static(lang2('Cookie.unknownCookieInstance', $data));
    }
}
