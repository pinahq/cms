<?php

namespace Pina\Modules\CMS;

class ImportPreview {

    private $importId = 0;

    private $file = ''; //Загруженный файл
    private $userSchema = array(); //Пользовательская схема столбцов
    private $fileFormat = ''; //Формат файла
    private $headerRow = 0; //Строка с заголовками
    private $startRow =  1; //Начальная строка данных

    private $reader;
    private $schema;

    protected $fileHeader = [];
    protected $replacer = [];
    
    const INSERTION_CHUNK_SIZE = 300;

    public function __construct($importId, $schema)
    {
        $this->importId = $importId;
        $this->schema = $schema;
    }

    public function build() {
        
        
        $this->begin();
        
        $this->loadOptions();

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
        $this->fileFormat = !empty($import['format']) ? $import['format'] : '';
        $this->headerRow = !empty($import['header_row']) ? $import['header_row'] : 0;
        $this->startRow = !empty($import['start_row']) ? $import['start_row'] : 1;
        
        $this->reader = ImportReaderRegistry::get($this->fileFormat, $this->file, $this->headerRow, $this->startRow);
        if (!$this->reader instanceof AbstractImportReader) {
            throw new \Exception("Can't determine reader for file format ". $this->fileFormat);
        }
        
        $this->fileHeader = $this->reader->header();
        $this->header = $this->fileHeader;

        $this->userSchema = !empty($import['schema']) ? json_decode($import['schema']) : $this->schema->constructUserSchema($this->fileHeader);
        
        $virtialColumns = count($this->userSchema) - count($this->fileHeader);
        if ($virtialColumns > 0) {
            for ($i = 0; $i < $virtialColumns; $i++) {
                $this->header[] = 'R'.$this->headerRow.'C'.(count($this->fileHeader) + $i + 1);
            }
        }
        
        $replaces = !empty($import['replaces']) ? json_decode($import['replaces']) : [];
        $this->replacer = new ImportPreviewReplacer($this->fileHeader, $replaces);
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
            "schema" => json_encode($this->userSchema, JSON_UNESCAPED_UNICODE),
            "file_header" => json_encode($this->fileHeader, JSON_UNESCAPED_UNICODE),
            "header" => json_encode($this->header, JSON_UNESCAPED_UNICODE),
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
            
            list($data, $error) = $this->processRow($row);
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
    
    
    public function processRow($row)
    {
        $header = $this->header;

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

        foreach ($this->header as $key => $name) {

            $cell = $this->replacer->makeReplaces($key, $row);
            
            $error = $this->schema->validate($this->userSchema[$key], $cell);
            $hasError = $hasError || !empty($error);

            $data[] = $cell;
            $errors[] = $error;
        }

        return array($data, $hasError ? $errors : false);
    }
    
}