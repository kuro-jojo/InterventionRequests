<?php
namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class Priorite extends AbstractEnumType
{
    public const Urgent = 'Urgent';
    public const PeuUrgent = 'Peu urgente';
    public const PasUrgent = 'Pas urgente';

    protected static $choices = [
        self::Urgent => 'Elle est urgente',
        self::PeuUrgent => 'Elle est peu urgente',
        self::PasUrgent => 'Elle n\'est pas urgente'
    ];

}