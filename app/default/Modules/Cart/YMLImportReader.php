<?php

namespace Pina\Modules\Cart;

use XMLReader;
use Pina\Modules\CMS\AbstractImportReader;

class YMLImportReader implements AbstractImportReader
{
    private $file = '';
    private $xmlReader;
    private $headers = array();
    private $latestRow = 0;
    
    private $iteration = 1;

    private $categories = array(); //Категории из файла, как есть
    private $orderedCategories = array(); //Категории со всей цепочкой родителей (строка с | разделитялями)

    private $readBuffer = array();
    private $rowIndexes = array();
    private $rowCounter = 0;

    private $ignoreParamElements = false; //Игнорировать доп. поля

    const CHUNK_SIZE = 200; //Размер буфера в строках
    const ITEM_ELEMENT = 'offer'; //Название поля с товаром
    const ITEM_ELEMENT_DEPTH = 3; //Вложенность поля с товаром
    const PARAM_ELEMENT = 'param'; //Название дополнительного поля
    const CATEGORIES_ELEMENT = 'categories'; //Название поля с категориями (родительский контейнер)
    const CATEGORY_ELEMENT = 'category'; //Название поля с категорией
    const CATEGORY_ELEMENT_DEPTH = 3; //Вложенность поля с категорией

    const PICTURE_MAX_COUNT = 5; //Максимальное кол-во сохраняемых изображений
    const PICTURE_FIELD_NAME = 'Изображение'; //Название поля для хранения изображений

    const CATEGORY_ID_FIELD_NAME = 'categoryId'; //Название поля с id категории
    const CATEGORY_FIELD_NAME = 'Категория'; //Название поля для хранения категорий

    const UNIT_PARAM_POSTFIX = ', единицы'; //Постфикс для поля единиц измерения

    private static $headerReplaces = [
        'price' => 'Цена',
        'currencyId' => 'Валюта',
        'picture' => 'Изображение',
        'name' => 'Наименование',
        'vendor' => 'Бренд',
        'description' => 'Описание',
        'vendorCode' => 'Артикул',
        'barcode' => 'Штрихкод',
        'categoryName' => 'Категория',
    ];

    public function __construct($file, $headerRow, $startRow)
    {
        $this->file = $file;
    }
    
    //Инициализирует xml читалку
    private function initXmlReader()
    {
        if (!file_exists($this->file)) {
            return false;
        }
        
        $this->xmlReader = new XMLReader();
        $this->xmlReader->open($this->file);
        $this->rowIndexes = [];
        $this->readBuffer = [];
        
        $this->latestRow = 0;
        $this->rowCounter = 1;
    }

    //Отдаёт заголовки из файла
    public function header()
    {
        $this->initXmlReader();

        //Читаем заголовки
        $headers = [];
        $inItem = false;
        $this->xmlReader->read();

        while ($this->xmlReader->read()) {
            if ($this->xmlReader->nodeType == XMLREADER::ELEMENT) {
                //Начало блока offer, включаем флаг
                if ($this->xmlReader->name == self::ITEM_ELEMENT &&
                    $this->xmlReader->depth === self::ITEM_ELEMENT_DEPTH
                ) {
                    $inItem = true;
                    continue;
                }

                //Если не внутри offer продолжаем
                if ($inItem === false ||
                    $this->xmlReader->depth !== self::ITEM_ELEMENT_DEPTH + 1
                ) {
                    continue;
                }

                //Игнорирование param элементов
                if ($this->xmlReader->name == self::PARAM_ELEMENT &&
                    $this->ignoreParamElements === true
                ) {
                    continue;
                }

                //Если элемент param, забираем заголовок из свойства name
                if ($this->xmlReader->name == self::PARAM_ELEMENT) {
                    $header = $this->xmlReader->getAttribute('name');
                    $unit = $this->xmlReader->getAttribute('unit');
                }
                else {
                    $header = $this->xmlReader->name;
                    $unit = '';
                }

                $header = self::replaceHeader($header);

                if (!$header) {
                    continue;
                }

                $headers[$header] = '';

                if ($unit) {
                    $unitHeader = $header . self::UNIT_PARAM_POSTFIX;
                    $unitHeader = self::replaceHeader($unitHeader);
                    $headers[$unitHeader] = '';
                }
            }
            //Блок offer закончился, убираем флаг
            elseif ($this->xmlReader->nodeType == XMLREADER::END_ELEMENT &&
                $this->xmlReader->name == self::ITEM_ELEMENT &&
                $this->xmlReader->depth === self::ITEM_ELEMENT_DEPTH
            ) {
                $inItem = false;
            }
        }

        //Закрываем файл (нет другой возможности сбросить курсор на начало)
        $this->xmlReader->close();
        $this->xmlReader = null;

        $headers = self::fillPicturesHeaders($headers);
        $headers = self::fillCategoriesHeaders($headers);
        $this->headers = array_keys($headers);

        return $this->headers;
    }
    
    
    public function current()
    {
        return $this->rowBuffer;
    }

    public function key()
    {
        return $this->iteration;
    }
    
    public function next()
    {
        $this->rowBuffer = $this->read();
        $this->iteration ++;
    }

    public function rewind()
    {
        $this->iteration = 1;
        $this->initXmlReader();
        $this->rowBuffer = $this->read();
    }

    public function valid()
    {
        return !empty($this->rowBuffer) || !empty($this->xmlReader);
    }

    private function start($startRow = 0)
    {
        if (empty($this->xmlReader)) {
            return false;
        }
        
        $row = [];
        $inItem = false;
        $this->xmlReader->read();
        $chunkCounter = 0;

        while ($chunkCounter < self::CHUNK_SIZE && $this->xmlReader->read()) {
            if ($this->xmlReader->nodeType == XMLREADER::ELEMENT) {
                //Начало блока offer, включаем флаг
                if ($this->xmlReader->name == self::ITEM_ELEMENT &&
                    $this->xmlReader->depth === self::ITEM_ELEMENT_DEPTH)
                {
                    //Начало с заданной строки
                    if ($startRow > $this->rowCounter) {
                        $this->xmlReader->next();
                        $this->rowCounter++;
                    }

                    $inItem = true;
                    continue;
                }

                //Парсим категории
                if ($this->xmlReader->name == self::CATEGORY_ELEMENT &&
                    $this->xmlReader->depth === self::CATEGORY_ELEMENT_DEPTH)
                {
                    $category = [
                        'id' => $this->xmlReader->getAttribute('id'),
                        'parentId' => $this->xmlReader->getAttribute('parentId'),
                        'name' => trim($this->xmlReader->readString())
                    ];

                    if ($category['id'] && $category['name']) {
                        $this->categories[$category['id']] = $category;
                    }
                    continue;
                }

                //Если не внутри offer продолжаем
                if ($inItem === false ||
                    $this->xmlReader->depth !== self::ITEM_ELEMENT_DEPTH + 1
                ) {
                    continue;
                }

                //Игнорирование param элементов
                if ($this->xmlReader->name == self::PARAM_ELEMENT &&
                    $this->ignoreParamElements === true
                ) {
                    continue;
                }

                //Если элемент param, забираем заголовок из свойства name
                if ($this->xmlReader->name == self::PARAM_ELEMENT
                ) {
                    $header = $this->xmlReader->getAttribute('name');
                    $unit = $this->xmlReader->getAttribute('unit');
                }
                else {
                    $header = $this->xmlReader->name;
                    $unit = '';
                }

                $value = trim($this->xmlReader->readString());

                list($header, $value) = self::replaceCategory($header, $value);
                $header = self::replaceHeader($header);
                $header = self::splitPictures($header, $row);

                if (!$header) {
                    continue;
                }

                $row[$header] = $value;

                if ($unit) {
                    $unitHeader = $header . self::UNIT_PARAM_POSTFIX;
                    $unitHeader = self::replaceHeader($unitHeader);
                    $row[$unitHeader] = $unit;
                }
            }
            //Блок offer закончился, убираем флаг
            elseif ($this->xmlReader->nodeType == XMLREADER::END_ELEMENT &&
                $this->xmlReader->name == self::ITEM_ELEMENT &&
                $this->xmlReader->depth === self::ITEM_ELEMENT_DEPTH
            ) {
                $inItem = false;
                $this->readBuffer[] = $this->getRowValues($row);
                $this->rowIndexes[] = $this->rowCounter++;
                $row = [];

                $chunkCounter++;
            }
            //Блок categories закончился, обрабатываем категории
            elseif ($this->xmlReader->nodeType == XMLREADER::END_ELEMENT &&
                $this->xmlReader->name == self::CATEGORIES_ELEMENT &&
                $this->xmlReader->depth === self::CATEGORY_ELEMENT_DEPTH - 1
            ) {
                $this->orderCategories();
            }
        }

        //Закрываем файл, если за последнее чтение считалось меньше лимита = достигнут конец файла
        if ($chunkCounter < self::CHUNK_SIZE) {
            $this->xmlReader->close();
            $this->xmlReader = null;
        }
    }

    //Отдаёт следующую строку данных из файла
    private function read()
    {
        if (count($this->readBuffer) < 1) {
            $this->start($this->latestRow + 1);
        }
        
        $this->latestRow = array_shift($this->rowIndexes);
        return array_shift($this->readBuffer);
    }

    //Расставляет значения по положению заголовков
    private function getRowValues($row) {
        $result = [];

        foreach ($this->headers as $key => $value) {
            if (!isset($row[$value])) {
                $result[$key] = '';
            }
            else {
                $result[$key] = $row[$value];
            }
        }

        return $result;
    }

    //Подготавливает категории
    private function orderCategories() {
        $this->orderedCategories = [];

        foreach ($this->categories as $category) {
            $this->orderedCategories[$category['id']] = $this->getSequence($category['id']);
        }
    }

    //Отдаёт строку с вложенностью категорий, начиная от текущей
    private function getSequence($categoryId, $sequence = []) {
        $parentId = '';
        if (isset($this->categories[$categoryId])) {
            $sequence[] = $this->categories[$categoryId]['name'];
            $parentId = $this->categories[$categoryId]['parentId'];
        }

        if (!$parentId) {
            return join('/', array_reverse($sequence));
        }

        return $this->getSequence($parentId, $sequence);
    }

    //Производит автозамену заголовков
    private static function replaceHeader($originalHeader) {
        if (isset(self::$headerReplaces[$originalHeader])) {
            return self::$headerReplaces[$originalHeader];
        }

        return $originalHeader;
    }

    //Заменяет id категории на название со структурой родителей
    private function replaceCategory($header, $value) {
        if ($header == self::CATEGORY_ID_FIELD_NAME) {
            $header = self::CATEGORY_FIELD_NAME;

            if (isset($this->orderedCategories[$value])) {
                $value = $this->orderedCategories[$value];
            }
        }

        return [$header, $value];
    }

    //Дополняет заголовки зарезервированными заголовками изображений
    private static function fillPicturesHeaders($headers) {
        for ($i = 1; $i <= 5; $i++) {
            $headers[self::PICTURE_FIELD_NAME . ' ' . $i] = '';
        }

        return $headers;
    }

    //Дополняет заголовки зарезервированными заголовками категорий
    private static function fillCategoriesHeaders($headers) {
        $headers[self::CATEGORY_FIELD_NAME] = '';

        //Убираем старый заголовок
        unset($headers[self::CATEGORY_ID_FIELD_NAME]);

        return $headers;
    }

    //Разделяет одинаковый заголовок изображений на несколько
    private static function splitPictures($header, $row) {
        if ($header != self::PICTURE_FIELD_NAME) {
            return $header;
        }

        for ($i = 0; $i <= 5; $i++) {
            if ($i == 0) {
                $name = self::PICTURE_FIELD_NAME;
            }
            else {
                $name = self::PICTURE_FIELD_NAME . ' ' . $i;
            }

            if (!isset($row[$name])) {
                return $name;
            }
        }

        return '';
    }
}
