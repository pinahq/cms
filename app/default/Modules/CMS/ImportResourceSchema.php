<?php

namespace Pina\Modules\CMS;

use Pina\Arr;

class ImportResourceSchema extends ImportSchema
{

    public function getDefaultSchema()
    {
        return [
            /* ['Поле в системе', 'Поле в файле', 'ключевое поле', 'Функция для проверки'] */
                ['resource.external_id', __('Resource External ID'), 'resource', 'isString'],
                ['resource.title', __('Title'), 'resource', 'isString'],
                ['resource.resource', __('Slug'), 'resource', ['notEmpty', 'isString']],
                ['resource_text.text', __('Description'), false, 'isString'],
                ['resource.enabled', __('Resource Enabled'), false, 'inArray', array('Y', 'N'), 'Specify: Y or N'],
                ['image', __('Image'), false, 'isString'],
                ['parent', __('Section'), 'resource', 'isString'],
                ['tag', __('Resource Tag'), 'resource', 'isString'],
        ];
    }

    public function schemaTagInfo()
    {
        return [
            'resource' => 'tag',
        ];
    }
}
