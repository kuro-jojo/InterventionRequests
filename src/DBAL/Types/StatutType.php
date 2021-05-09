<?php

namespace App\DBAL\Types;
use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class StatutType extends AbstractEnumType
{
    public const EnAttente = 'EnAttente';
    public const EnCours = 'EnCours';
    public const OK = 'OK';

    public static $choices = [
        self::EnAttente => 'En Attente',
        self::EnCours => 'En Cours',
        self::OK => 'OK'
    ];
}