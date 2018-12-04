<?php

namespace Pina\Modules\CMS;

use Pina\Arr;

abstract class ImportSchema
{

    const FIELD_TARGET = 0;
    const FIELD_TITLE = 1;
    const FIELD_KEY_INFO = 2;
    const FIELD_FUNC = 3;

    abstract public function getDefaultSchema();

    abstract public function schemaTagInfo();

    public function schemaFields()
    {
        $schema = $this->getDefaultSchema();
        return Arr::column($schema, self::FIELD_TITLE, self::FIELD_TARGET);
    }

    public function schemaKeyInfo()
    {
        $schema = $this->getDefaultSchema();
        return Arr::column($schema, self::FIELD_KEY_INFO, self::FIELD_TARGET);
    }

    public function format($schema)
    {
        if (!is_array($schema)) {
            return [];
        }
        $schemaFields = $this->schemaFields();
        foreach ($schema as $k => $item) {
            if (isset($schemaFields[$item])) {
                $schema[$k] = $schemaFields[$item];
                continue;
            }

            $parts = explode(' ', $item);
            $first = array_shift($parts);
            if (isset($schemaFields[$first])) {
                $schema[$k] = $schemaFields[$first];
                $schema[$k] .= "\n" . implode(' ', $parts);
                continue;
            }
        }

        return $schema;
    }

    public function constructUserSchema($header)
    {
        $schema = [];
        foreach ($header as $title) {
            $recognized = $this->recognizeHeader($title);
            $schema[] = $recognized ? $recognized : ('tag ' . $title);
        }
        return $schema;
    }

    protected function recognizeHeader($title)
    {
        $defaultSchema = $this->getDefaultSchema();
        foreach ($defaultSchema as $v) {
            if ($title == $v[self::FIELD_TITLE]) {
                return $v[self::FIELD_TARGET];
            }
        }

        foreach ($defaultSchema as $v) {
            if (strncasecmp($title, $v[self::FIELD_TITLE], strlen($v[self::FIELD_TITLE])) == 0) {
                return $v[self::FIELD_TARGET];
            }
        }

        return null;
    }

    public function validate($name, $cell)
    {
        $schema = Arr::groupUnique($this->getDefaultSchema(), static::FIELD_TARGET);

        if (empty($schema[$name][static::FIELD_FUNC])) {
            return false;
        }

        $validators = $schema[$name][static::FIELD_FUNC];
        if (!is_array($validators)) {
            $validators = [$validators];
        }
        foreach ($validators as $validator) {
            $params = array_merge(array($cell), array_slice($schema[$name], static::FIELD_FUNC + 1));
            $error = call_user_func_array(
                array($this, $validator), $params
            );
            if ($error) {
                return $error;
            }
        }
        return false;
    }

    public function fields()
    {
        return Arr::column($this->getDefaultSchema(), self::FIELD_TITLE, self::FIELD_TARGET);
    }

    protected function notEmpty($v)
    {
        return empty($v) ? 'Укажите значение' : '';
    }

    protected function isInteger($v)
    {
        $isError = intval($v) != $v;
        return $isError ? 'Введите целочисленное значение' : '';
    }

    protected function isAmount($v)
    {
        return $this->isInteger($v);
    }

    protected function isFloat($v)
    {
        $isError = floatval($v) != $v;
        return $isError ? 'Введите число' : '';
    }

    protected function isString($v)
    {
        return false;
    }

    protected function inArray($v, $arr, $message)
    {
        $isError = !in_array($v, $arr);
        return $isError ? $message : '';
    }

}
