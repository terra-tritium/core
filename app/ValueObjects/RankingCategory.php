<?php

namespace App\ValueObjects;

class RankingCategory
{
    public const SCORE = 'score';
    public const BUILD_SCORE = 'buildScore';
    public const LAB_SCORE = 'labScore';
    public const TRADE_SCORE = 'tradeScore';
    public const ATTACK_SCORE = 'attackScore';
    public const DEFENSE_SCORE = 'defenseScore';
    public const WAR_SCORE = 'warScore';
    public const RESEARCH_SCORE = 'researchScore';
    public const MILITARY_SCORE = 'militaryScore';
    public const ENERGY = 'energy';

    private string $name;
    private string $displayName;

    private function __construct(string $name, string $displayName)
    {
        $this->name = $name;
        $this->displayName = $displayName;
    }

    public static function score(): self
    {
        return new self(self::SCORE, 'Total Score');
    }

    public static function buildScore(): self
    {
        return new self(self::BUILD_SCORE, 'Build Score');
    }

    public static function labScore(): self
    {
        return new self(self::LAB_SCORE, 'Lab Score');
    }

    public static function tradeScore(): self
    {
        return new self(self::TRADE_SCORE, 'Trade Score');
    }

    public static function attackScore(): self
    {
        return new self(self::ATTACK_SCORE, 'Attack Score');
    }

    public static function defenseScore(): self
    {
        return new self(self::DEFENSE_SCORE, 'Defense Score');
    }

    public static function warScore(): self
    {
        return new self(self::WAR_SCORE, 'War Score');
    }

    public static function researchScore(): self
    {
        return new self(self::RESEARCH_SCORE, 'Research Score');
    }

    public static function militaryScore(): self
    {
        return new self(self::MILITARY_SCORE, 'Military Score');
    }

    public static function energy(): self
    {
        return new self(self::ENERGY, 'Energy');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }
}
