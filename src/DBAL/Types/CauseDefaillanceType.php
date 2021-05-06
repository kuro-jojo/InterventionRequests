<?php


namespace App\DBAL\Types;
use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class CauseDefaillanceType extends AbstractEnumType
{
    public const UsureNormal = 'UsureNormal';
    public const DefautUtilisateur = 'DefautUtilisateur';
    public const DefautProduit = 'DefautProduit';
    public const Autres = 'Autres';

    protected static $choices = [
        self::UsureNormal => 'UsureNormal',
        self::DefautUtilisateur => 'DefautUtilisateur',
        self::DefautProduit => 'DefautProduit',
        self::Autres => 'Autres'
    ];
}