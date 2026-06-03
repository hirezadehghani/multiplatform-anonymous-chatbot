<?php 
trait HasOptions

{
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(
                fn ($case) => [
                    $case->value => $case->label()
                ]
            )
            ->toArray();
    }
}

?>