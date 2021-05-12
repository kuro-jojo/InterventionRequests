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
        self::UsureNormal => 'Usure normale',
        self::DefautUtilisateur => 'Defaut utilisateur',
        self::DefautProduit => 'Defaut de produit',
        self::Autres => 'Autres'
    ];
}