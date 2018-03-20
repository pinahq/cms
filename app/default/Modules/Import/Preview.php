<?php

namespace Pina\Modules\Import;

class Preview {

    private $importId = 0;

    private $file = ''; //Загруженный файл
    private $userSchema = array(); //Пользовательская схема столбцов
    private $fields = array(); //Поля для импорта
    private $fileFormat = ''; //Формат файла
    private $headerRow = 0; //Строка с заголовками
    private $startRow =  1; //Начальная строка данных
    private $replaces = array(); //Замены

    private $reader;
    private $schema;
    private $header;

    private $productKeyFields = array();
    private $productVariantKeyFields = array();

    const INSERTION_CHUNK_SIZE = 300;

    public function __construct($importId)
    {
        $this->importId = $importId;
    }

    public function build() {
        
        $this->begin();
        
        $this->loadOptions();

        $this->initReader();
        
        $this->setSchema();

        $this->clearLastImportData();
        
        $this->saveOptions();
        
        $this->read();

        $this->complete();
    }
    
    public function begin()
    {
        ImportGateway::instance()->whereId($this->importId)->update(array(
            'status' => 'read',
            'last_row' => 0,
        ));
    }

    private function loadOptions() {
        $import = ImportGateway::instance()->find($this->importId);

        if (!$import) {
            throw new \Exception("Import task not found " . $this->importId);
        }

        $this->file = !empty($import['path']) ? $import['path'] : '';

        $this->userSchema =  !empty($import['schema']) ? json_decode($import['schema']) : array();

        $this->fields = !empty($import['fields']) ? explode(',', $import['fields']) : array();

        $this->fileFormat = !empty($import['format']) ? $import['format'] : '';

        $this->headerRow = !empty($import['header_row']) ? $import['header_row'] : 0;

        $this->startRow = !empty($import['start_row']) ? $import['start_row'] : 1;

        $this->replaces = !empty($import['replaces']) ? json_decode($import['replaces']) : array();

    }

    private function initReader() {
        $this->reader = Reader::getReader($this->fileFormat, $this->file, $this->headerRow, $this->startRow);
        if (!$this->reader instanceof AbstractReader) {
            throw new \Exception("Can't determine reader for file format ". $this->fileFormat);
        }
    }

    private function setSchema() {
        $fileHeader = $this->reader->header();
        
        $this->schema = new Schema($this->fields, $this->replaces, $this->userSchema, $fileHeader);
    }
    
    private function updateLastRow($index)
    {
        ImportGateway::instance()->whereId($this->importId)->update([
            'last_row' => intval($index)
        ]);
    }
    
    private function saveOptions()
    {
        ImportGateway::instance()->whereId($this->importId)->update(array(
            "schema" => json_encode($this->schema->getUserSchema(), JSON_UNESCAPED_UNICODE),
            "file_header" => json_encode($this->schema->getFileHeader(), JSON_UNESCAPED_UNICODE),
            "header" => json_encode($this->schema->getHeader(), JSON_UNESCAPED_UNICODE),
        ));
    }

    private function clearLastImportData() {
        ImportPreviewGateway::instance()->whereBy('import_id', $this->importId)->delete();
        ImportErrorGateway::instance()->whereBy('import_id', $this->importId)->delete();
    }

    private function read() {
        $insertCounter = 0;
        $preview = [];
        $errors = [];
        $index = 0;
        foreach ($this->reader as $index => $row) {
            
            list($data, $error) = $this->schema->processRow($row);
            if (empty($data)) {
                continue;
            }
            
            $preview [] = [
                'import_id' => $this->importId,
                'row' => $index,
                'error' => !empty($error)?'Y':'N',
                'preview' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ];
            
            if (!empty($error)) {
                $errors[] = array(
                    'import_id' => $this->importId,
                    'row' => $index,
                    'error' => json_encode($error, JSON_UNESCAPED_UNICODE)
                );
            }

            $insertCounter++;

            if ($insertCounter >= self::INSERTION_CHUNK_SIZE) {
                $this->saveImportData($preview, $errors);
                $this->updateLastRow($index);
                $preview = [];
                $errors = [];
                $insertCounter = 0;
            }
        }

        $this->saveImportData($preview, $errors);
        $this->updateLastRow($index);
    }

    private function saveImportData($rows, $errors) {
        if (count($rows) > 0) {
            ImportPreviewGateway::instance()->insert($rows);
        }

        if (count($errors) > 0) {
            ImportErrorGateway::instance()->insert($errors);
        }
        
        
    }

    private function complete()
    {
        ImportGateway::instance()->whereId($this->importId)->update(array('status' => 'confirm'));
    }
}