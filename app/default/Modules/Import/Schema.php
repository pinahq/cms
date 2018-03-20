<?php

namespace Pina\Modules\Import;

use Pina\Arr;

use Pina\Modules\CMS\TagTypeGateway;
use Pina\Modules\Cart\OfferTagTypeGateway;

class Schema
{

    const FIELD_TITLE = 1;
    const FIELD_TARGET = 0;
    const FIELD_FUNC = 3;
    const FIELD_KEY_INFO = 2;
    
    private $defaultSchema = [];

    private $productCategories = [];
    private $internalIdHash = [];
    private $fields = [];
    private $userSchema = [];
    private $replaces = [];
    private $fileHeader = [];

    public function __construct($fields = [], $replaces = [], $userSchema = [], $fileHeader = [])
    {
        $this->fields = $fields;
        $this->replaces = $replaces;
        $this->fileHeader = $fileHeader;
        $this->defaultSchema = static::getDefaultSchema();
        $this->userSchema = empty($userSchema) ? $this->constructUserSchema($fileHeader) : $userSchema;
    }
    
    protected static function getDefaultSchema() {
        return [
            /* array('Поле в системе', 'Поле в файле', 'ключевое поле', 'Тип для проверки') */
            ['offer.id', __('Offer ID'), 'offer', 'integer'],
            ['offer.external_id', __('Offer External ID'), 'offer', 'string'],
            ['resource.external_id', __('Product External ID'), 'resource', 'string'],
            ['resource.title', __('Title'), 'resource', 'string'],
            ['resource_text.text', __('Description'), false, 'string'],
            ['resource.enabled', __('Product Enabled'), false, 'inArray', array('Y', 'N'), 'Specify: Y or N'],
            ['offer.enabled', __('Offer Enabled'), false, 'inArray', array('Y', 'N'), 'Specify: Y or N'],
            ['offer.amount', __('Amount'), false, 'amount'],
            ['offer.price', __('Price'), false, 'price'],
            ['offer.sale_price', __('Sale Price'), false, 'price'],
            ['offer.cost_price', __('Cost Price'), false, 'price'],
            ['image', __('Image'), false, 'string'],
            ['resource_parent', __('Section'), 'resource', 'string'],
            ['resource_tag', __('Additional section'), 'resource', 'string'],
            ['tag', __('Product Tag'), 'resource', 'string'],
            ['offer_tag', __('Offer Tag'), 'offer', 'string'],
        ];
    }
    
    public static function schemaFields()
    {
        $defaultSchema = static::getDefaultSchema();
        return Arr::column($defaultSchema, self::FIELD_TITLE, self::FIELD_TARGET);
    }

    public static function schemaKeyInfo()
    {
        $defaultSchema = static::getDefaultSchema();
        return Arr::column($defaultSchema, self::FIELD_KEY_INFO, self::FIELD_TARGET);
    }

    public static function prepareUserSchemaToDisplay($schema)
    {
        $schemaFields = static::schemaFields();
        foreach ($schema as $k => $item)
        {
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
            $key = 'tag ' . $title;
            $found = false;
            foreach ($this->defaultSchema as $v) {
                if ($title == $v[self::FIELD_TITLE]) {
                    $key = $v[self::FIELD_TARGET];
                    $found = true;
                }
            }
            
            if (!$found) {
                foreach ($this->defaultSchema as $v) {
                    if (strncasecmp($title, $v[self::FIELD_TITLE], strlen($v[self::FIELD_TITLE])) == 0) {
                        $key = $v[self::FIELD_TARGET];
                        $found = true;
                    }
                }
            }
            
            if (!$found) {
                if (TagTypeGateway::instance()->whereBy('type', $title)->innerJoin(
                        OfferTagTypeGateway::instance()->on('tag_type_id', 'id')
                    )->exists()
                ) {
                    $key = 'offer_tag '.$title;
                }
            }
            
            $schema[] = $key;
        }
        return $schema;
    }

    public function getUserSchema()
    {
        return $this->userSchema;
    }

    public function getFileHeader()
    {
        return $this->fileHeader;
    }

    public function getHeader()
    {
        //предварительно вычислить заголовок на основе полей и замен
        return $this->fileHeader;
    }

    public function getLinks()
    {
        $tmpCounter = 1;
        $header = array();
        foreach ($this->fileHeader as $title) {
            $key = '';
            if (isset($this->userSchema[$title])) {
                $key = $this->userSchema[$title];
            }

            if (empty($key)) {
                $key = '_' . ($tmpCounter++);
            }
            array_push($header, $key);
        }

        return $header;
    }

    public function processRow($row)
    {
        $header = $this->fileHeader;

        if (empty($row) || !is_array($row)) {
            return array(array(), array());
        }

        if (count($header) < count($row)) {
            $row = array_slice($row, 0, count($header));
        }

        if (count($header) > count($row)) {
            while (count($header) > count($row)) {
                $row[] = '';
            }
        }

        $data = array();
        $errors = array();
        $hasError = false;

        $schema = Arr::groupUnique($this->defaultSchema, self::FIELD_TITLE);

        foreach ($this->fileHeader as $key => $name) {

            $cell = $this->makeReplaces($key, $row);

            $error = '';
            if (!empty($schema[$name][self::FIELD_FUNC])) {
                $type = $schema[$name][self::FIELD_FUNC];
                $params = array_merge(array($cell), array_slice($schema[$name], self::FIELD_FUNC + 1));
                $error = call_user_func_array(
                        array($this, $type), $params
                );
                if (!empty($error)) {
                    $hasError = true;
                }
            }
            $data[] = $cell;
            $errors[] = $error;
        }

        return array($data, $hasError ? $errors : false);
    }

    private function getVars($row)
    {
        $vars = [];
        foreach ($this->fileHeader as $key => $value) {
            if (empty($value)) {
                continue;
            }

            $newKey = str_replace(' ', '_', $value);
            $val = isset($row[$key]) ? $row[$key] : 0;
            $vars[$newKey] = $val;
        }
        return $vars;
    }

    private function makeReplaces($column, $row)
    {
        if (!isset($row[$column])) {
            return '';
        }
        $cell = $row[$column];
        if (empty($this->replaces) || !is_array($this->replaces)) {
            return $cell;
        }

        $vars = $this->getVars($row);
        foreach ($this->replaces as $replace) {
            if ($column == $replace[0]) {
                //Условия
                $displacement = $replace[1];
                //Заместитель
                $replacement = $replace[2];

                if ((strpos($replacement, '=')) === 0) {
                    $equation = new Equation($replacement);
                    $replacement = $equation->calculate($vars);
                }

                //Если в заместителе <***> - подставляем значение ячейки вместо выражения
                $replacement = str_replace('<***>', $cell, $replacement);
                if (is_array($replacement)) {
                    #print_r($replace);
                    #print_r($replacement);
                    exit;
                }
                //Если в заместителе <$Поле> - подставляем значение поля вместо выражения
                $replacement = $this->replaceWithFields($replacement, $vars);

                if (strpos($displacement, '?:') === 0) {
                    $displacement = $this->conditionReplace($displacement, $vars, $cell);

                    //Если условие не прошло проверку продолжаем
                    if ($displacement === false) {
                        continue;
                    }
                }

                //Если в замене *** - заменяем все значения
                if ($displacement == '***') {
                    $cell = $replacement;
                }
                //Если в замене ??? - заменяем непустые значения
                elseif ($displacement == '???' && !empty($cell)) {
                    $cell = $replacement;
                } elseif (!empty($displacement)) {
                    $pos = mb_stripos($cell, $displacement);
                    if ($pos !== false) {
                        $middleLen = mb_strlen($displacement);
                        $first = mb_substr($cell, 0, $pos);
                        $end = mb_substr($cell, $pos + $middleLen);
                        $cell = $first.$replacement.$end;
                    }
                    #$cell = mb_eregi_replace('#'.$displacement."#iu", $replacement, $cell);
                    #$cell = str_ireplace($displacement, $replacement, $cell);
                }
                //Если в замене пусто, то заменяем только пустые строки
                elseif (empty($cell)) {
                    $cell = $replacement;
                }
            }
        }

        return $cell;
    }

    //Производим подстановку значений полей вмето <$Поле>
    private function replaceWithFields($replacement, $fieldVars)
    {
        $replacementTmp = '';
        $end = 0;
        while (($pos = strpos($replacement, '<$', $end)) !== false) {
            $r = substr($replacement, $end, $pos - $end);
            $replacementTmp .= $r;
            $end = strpos($replacement, '>', $pos) + 1;
            $key = substr($replacement, $pos + 2, $end - $pos - 3);
            $val = isset($fieldVars[$key]) ? $fieldVars[$key] : '<$' . $key . '>';
            $replacementTmp .= $val;
        }
        $replacementTmp .= substr($replacement, $end);

        return $replacementTmp;
    }

    //Производим замену по условию ?:значение?:содержит?:замена
    private function conditionReplace($condition, $fieldVars, $cell)
    {
        $conditions = explode('?:', $condition);

        if (!isset($conditions[1]) ||
                !isset($conditions[2]) ||
                !isset($conditions[3])) {
            return $condition;
        }

        $ifVal = $conditions[1];
        $checkVal = $conditions[2];
        $displacementVal = $conditions[3];

        if ($ifVal == '***') {
            $ifVal = $cell;
        } elseif (strpos($ifVal, '$') === 0) {
            $field = substr($ifVal, 1);
            $ifVal = isset($fieldVars[$field]) ? $fieldVars[$field] : $ifVal;
        }

        if (strpos($ifVal, $checkVal) === false) {
            return false;
        }

        return $displacementVal;
    }

    public function fields()
    {
        $fields = Arr::column($this->defaultSchema, self::FIELD_TITLE, self::FIELD_TARGET);

        if (!empty($this->fields)) {
            $userFields = array_flip($this->fields);
            $fields = array_intersect_key($fields, $userFields);
        }

        return $fields;
    }

    private function getProductCategories()
    {
        $this->productCategories = ProductCategory::getProductCategoriesPaths('//');
    }

    private function integer($v)
    {
        $isError = intval($v) != $v;
        return $isError ? 'Введите целочисленное значение' : '';
    }

    private function amount($v)
    {
        return $this->integer($v);
    }

    private function float($v)
    {
        $isError = floatval($v) != $v;
        return $isError ? 'Введите число' : '';
    }

    private function price($v)
    {
        return $this->float($v);
    }

    private function percent($v)
    {
        return $this->float($v);
    }

    private function string($v)
    {
        return false;
    }

    private function inArray($v, $arr, $message)
    {
        $isError = !in_array($v, $arr);
        return $isError ? $message : '';
    }

    private function checkImageUrl($v)
    {
        if (!$v) {
            return '';
        }

        $pattern = '/^(https?:\/\/)?([\w\.]+)\.([a-z]{2,6}\.?)(\/[\/\w= \.\-\?&]*)*\/?$/i';
        $isError = preg_match($pattern, $v) !== 1;

        return $isError ? 'Не корректная ссылка на изображение' : '';
    }

    private function checkProductCategory($v)
    {
        if (!$v) {
            return '';
        }

        if (!$this->productCategories) {
            $this->getProductCategories();
        }

        $isError = !in_array($v, $this->productCategories);

        return $isError ? 'Укажите доступную в системе категорию' : '';
    }

}
