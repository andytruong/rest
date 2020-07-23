<?php

namespace go1\rest\struct\request;

use go1\rest\struct\Model;

/**
 * @property \go1\rest\struct\request\JwtHeader  $header
 * @property \go1\rest\struct\request\JwtPayload $payload
 * @property string                              $signature
 */
class Jwt extends Model
{
    protected $header;
    protected $payload;
    protected $signature;
}

/**
 * @property string $type
 * @property string $algorithm
 */
class JwtHeader extends Model
{
    /**
     * @json type
     * @json alg
     */
    protected $type;
    protected $algorithm;
}

/**
 * @property string                                    $issuedBy
 * @property string                                    $version
 * @property int                                       $expiredAt
 * @property \go1\rest\struct\request\JwtPayloadObject $object
 */
class JwtPayload extends Model
{
    /**
     * @json iss
     */
    protected $issuedBy = 'go1.user';

    /**
     * @json ver
     */
    protected $version = 'v18.12.03.0';
    protected $expiredAt;
    protected $object;
}

/**
 * @property string                               $type
 * @property \go1\rest\struct\request\ContextUser $content
 */
class JwtPayloadObject extends Model
{
    protected $type = 'user';
    protected $content;
}
