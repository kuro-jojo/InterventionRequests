<?php


namespace App\DBAL\Types;
use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class DepartementType extends AbstractEnumType
{
    public const BatimentDirectional = 'BatimentDirectional';
    public const BatimentACP = 'BatimentACP';
    public const GenieCivil = 'GenieCivil';
    public const Gestion = 'Gestion';
    public const GenieChimique = 'GenieChimique';
    public const GenieElec = 'GenieElec';
    public const GenieMeca = 'GenieMeca';
    public const GenieInf = 'GenieInf';
    public const RessourceHumaines = 'RessourceHumaines';
    public const Caisse = 'Caisse';
    public const CIFRES = 'CIFRES';
    public const LPAO = 'LPAO';
    public const LERG = 'LERG';
    public const LMAGI = 'LMAGI';
    public const LER = 'LER';
    public const SID = 'SID';
    public const Scolarite = 'Scolarite';
    public const CRENT = 'CRENT';
    public const LAE = 'LAE';
    public const LIMBI = 'LIMBI';
    public const LIRT = 'LIRT';
    public const Autre = 'Autre';

    protected static $choices = [
        self::BatimentDirectional => 'BatimentDirectional',
        self::BatimentACP => 'BatimentACP',
        self::GenieCivil => 'GenieCivil',
        self::Gestion => 'Gestion',
        self::GenieChimique => 'GenieChimique',
        self::GenieElec => 'GenieElec',
        self::GenieMeca => 'GenieMeca',
        self::GenieInf => 'GenieInf',
        self::RessourceHumaines => 'RessourceHumaines',
        self::Caisse => 'Caisse',
        self::LPAO => 'LPAO',
        self::LERG => 'LERG',
        self::LMAGI => 'LMAGI',
        self::LER => 'LER',
        self::SID => 'SID',
        self::Scolarite => 'Scolarite',
        self::CRENT => 'CRENT',
        self::LAE => 'LAE',
        self::LIMBI => 'LIMBI',
        self::LIRT => 'LIRT',
        self::Autre => 'Autre'

    ];


}