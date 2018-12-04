<?php

namespace Pina\Modules\Cart;

class CountryImport extends CSVImport
{

    public function getSchema()
    {
        return [
                ['key', 'Key'],
                ['country', 'Title'],
                ['importance', 'Importance']
        ];
    }

    public function start()
    {
        parent::start();

        $this->existedKeys = CountryGateway::instance()->column('key');
    }

    public function read()
    {
        $item = parent::read();

        if (empty($item)) {
            return null;
        }

        if (empty($item['importance'])) {
            $item['importance'] = 0;
        }

        return $item;
    }

    public function finalize(&$data)
    {
        parent::finalize($data);

        $updatedKeys = array_column($data, 'key');
        CountryGateway::instance()->put($data);

        $deletedKeys = array_diff($this->existedKeys, $updatedKeys);

        CountryGateway::instance()->whereBy('key', $deletedKeys)->delete();
    }

}
