<?php

namespace App\Models\Variable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class Base extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['name', 'value'];

    public function getValue(string $name, mixed $defaultValue = null): mixed
    {
        $res = parent::query()->select(['value'])->where('name', $name)->first();
        if (!empty($res->value)) {
            return $res->value;
        }
        return $defaultValue;
    }

    public function setValue(string $name, mixed $value): void
    {
        if (is_null($value)) {
            return;
        }
        parent::query()->upsert(
            [
                [
                    'name' => $name,
                    'value' => $value
                ]
            ],
            ['name'],
            ['value']
        );
    }
}
