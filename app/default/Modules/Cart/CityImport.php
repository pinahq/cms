<?php

namespace Pina\Modules\Cart;

class CityImport extends CSVImport
{

    protected $existedIds = [];

    public function getSchema()
    {
        return [
                ['country_key', 'Country'],
                ['region_key', 'Region'],
                ['id', 'ID'],
                ['city', 'City'],
        ];
    }

    public function start()
    {
        parent::start();

        $this->existedIds = CityGateway::instance()->column('id');
    }

    public function read()
    {
        $item = parent::read();

        if (empty($item)) {
            return null;
        }

        return $item;
    }

    public function finalize(&$data)
    {
        parent::finalize($data);
        
        foreach ($data as $k => $v) {
            if (empty($v['id'])) {
                unset($data[$k]);
            }
        }

        $updatedIds = array_column($data, 'id');
        CityGateway::instance()->put($data);

        $deletedIds = array_diff($this->existedIds, $updatedIds);
        CityGateway::instance()->whereId($deletedIds)->delete();
    }

}
