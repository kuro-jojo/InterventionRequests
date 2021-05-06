<?php
namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class Priorite extends AbstractEnumType
{
    public const Urgent = 'Urgent';
    public const PeuUrgent = 'PeuUrgent';
    public const PasUrgent = 'PasUrgent';

    protected static $choices = [
        self::Urgent => 'Urgent',
        self::PeuUrgent => 'PeuUrgent',
        self::PasUrgent => 'PasUrgent'
    ];

}