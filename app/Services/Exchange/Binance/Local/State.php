<?php
/*
 * Binance API Database Layer
 * Control flow in real time
 * */
namespace App\Services\Exchange\Binance\Local;

use App\Models\Exchange\Local\State as DbState;

class State
{
    const COLUMN_NAMES_API_STATE = ['param_name', 'value', 'decimal_value', 'bigint_value'];

    public function getLastRequestTime()
    {
        return $this->getValue('last_request_time', 0);
    }

    public function setLastRequestTime(int $requestTime): void
    {
        $this->setValue('last_request_time', $requestTime);
    }

    public function getValue(string $name, mixed $defaultValue = null): mixed
    {
        $exist = DbState::query()->select()->where('param_name', $name)->exists();
        if (!$exist) {
            return $defaultValue;
        }
        $res = DbState::query()->select(['value', 'bigint_value', 'decimal_value'])->where('param_name', $name)->first();
        if (!empty($res->value)) {
            return $res->value;
        }
        if (!empty((int)$res->bigint_value)) {
            return (int)$res->bigint_value;
        }
        if (!empty((int)$res->decimal_value)) {
            return (int)$res->decimal_value;
        }
        return $defaultValue;
    }

    public function setValue(string $name, mixed $value): void
    {
        $stringValue = null;
        $decValue = null;
        $intValue = null;
        if (is_string($value)) {
            $stringValue = $value;
        }
        if (is_float($value)) {
            $decValue = $value;
        }
        if (is_int($value)) {
            $intValue = $value;
        }
        if (is_null($stringValue) && is_null($decValue) && is_null($intValue)) {
            return;
        }
        DbState::query()->upsert(
            [
                [
                    'param_name' => $name,
                    'value' => $stringValue,
                    'decimal_value' => $decValue,
                    'bigint_value' => $intValue
                ]
            ],
            ['param_name'],
            self::COLUMN_NAMES_API_STATE
        );
    }
}
