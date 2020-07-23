<?php

namespace go1\rest\struct\request;

use go1\rest\struct\Model;

/**
 * @property int      $id
 * @property string   $portalName
 * @property int      $portalId
 * @property int      $profileId
 * @property string[] $roles
 */
class ContextAccount extends Model
{
    const ROLE_ADMIN_CONTENT = 'content administrator';
    const ROLE_ADMIN         = 'administrator';
    const ROLE_MANAGER       = 'manager';

    protected $id;

    /**
     * @json instance
     */
    protected $portalName;

    /**
     * @json portal_id
     */
    protected $portalId;

    /**
     * @json profile_id
     */
    protected $profileId;
    protected $roles = [];

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isManager(): bool
    {
        return $this->hasRole(self::ROLE_MANAGER);
    }

    public function isContentAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN_CONTENT);
    }
}
