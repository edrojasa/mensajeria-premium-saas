<?php

namespace App\Organizations;

final class OrganizationRole
{
    public const OWNER = 'owner';

    /** @var list<string> */
    public const ALL = [
        self::OWNER,
    ];
}
