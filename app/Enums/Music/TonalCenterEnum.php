<?php

namespace App\Enums\Music;

enum TonalCenterEnum: string
{
    case C = 'C';
    case C_SHARP = 'C#';
    case D = 'D';
    case D_SHARP = 'D#';
    case E = 'E';
    case F = 'F';
    case F_SHARP = 'F#';
    case G = 'G';
    case G_SHARP = 'G#';
    case A = 'A';
    case A_SHARP = 'A#';
    case B = 'B';

    public function label(): string
    {
        return match ($this) {
            self::C => 'C',
            self::C_SHARP => 'C#/Db',
            self::D => 'D',
            self::D_SHARP => 'D#/Eb',
            self::E => 'E',
            self::F => 'F',
            self::F_SHARP => 'F#/Gb',
            self::G => 'G',
            self::G_SHARP => 'G#/Ab',
            self::A => 'A',
            self::A_SHARP => 'A#/Bb',
            self::B => 'B',
        };
    }

    /**
     * @return array<string, string> value => label, in pitch order
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
