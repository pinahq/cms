<?php

namespace Pina\Modules\Cart;

class RegionImport extends CSVImport
{

    public function getSchema()
    {
        return [
                ['country_key', 'Country'],
                ['key', 'Key'],
                ['region', 'Region'],
                ['importance', 'Importance']
        ];
    }

    public function start()
    {
        parent::start();

        $this->existedRegions = RegionGateway::instance()->select('key')->select('country_key')->get();
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

        foreach ($this->existedRegions as $k => $region) {
            if ($region['key'] == $item['key'] && $region['country_key'] == $item['country_key']) {
                unset($this->existedRegions[$k]);
                break;
            }
        }

        return $item;
    }

    public function finalize(&$data)
    {
        parent::finalize($data);

        RegionGateway::instance()->put($data);

        foreach ($this->existedRegions as $region) {
            RegionGateway::instance()->whereBy('key', $region['key'])->whereBy('country_key', $region['country_key'])->delete();
        }
    }

}
