<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class PoleType extends AbstractEnumType
{

    public const MENUISERIE = 'Menuiserie';
    public const ELECTRICITE = 'Electricite';
    public const MACONNERIE = 'Maconnerie';
    public const CLIMATISATION = 'Climatisation';
    public const PLOMBERIE = 'Plomberie';

    protected static $choices = [
        self::MACONNERIE => 'Maçonnerie',
        self::MENUISERIE => 'Menuiserie',
        self::CLIMATISATION => 'Climatisation',
        self::ELECTRICITE => 'Electricité',
        self::PLOMBERIE => 'Plomberie'
    ];
}
