<?php

namespace Pina\Modules\Cart;

use Pina\Modules\CMS\TagTypeGateway;
use Pina\Modules\CMS\ImportResourceSchema;

class ImportOfferSchema extends ImportResourceSchema
{

    public function getDefaultSchema()
    {
        $parent = parent::getDefaultSchema();
        $local = [
            /* array('Поле в системе', 'Поле в файле', 'ключевое поле', 'Тип для проверки') */
                ['offer.id', __('Offer ID'), 'offer', 'isInteger'],
                ['offer.external_id', __('Offer External ID'), 'offer', 'isString'],
                ['offer.enabled', __('Offer Enabled'), false, 'inArray', array('Y', 'N'), 'Specify: Y or N'],
                ['offer.amount', __('Amount'), false, 'isAmount'],
                ['offer.price', __('Price'), false, 'isPrice'],
                ['offer.sale_price', __('Sale Price'), false, 'isPrice'],
                ['offer.cost_price', __('Cost Price'), false, 'isPrice'],
                ['offer_tag', __('Offer Tag'), 'offer', 'isString'],
        ];
        return array_merge($parent, $local);
    }

    public function schemaTagInfo()
    {
        return [
            'resource' => 'tag',
            'offer' => 'offer_tag',
        ];
    }

    protected function recognizeHeader($title)
    {
        $key = parent::recognizeHeader($title);
        if ($key) {
            return $key;
        }

        if (TagTypeGateway::instance()->whereBy('type', $title)->innerJoin(
                OfferTagTypeGateway::instance()->on('tag_type_id', 'id')
            )->exists()
        ) {
            return 'offer_tag ' . $title;
        }

        return null;
    }

    protected function isPrice($v)
    {
        return $this->isFloat($v);
    }

    protected function isPercent($v)
    {
        return $this->isFloat($v);
    }

}
