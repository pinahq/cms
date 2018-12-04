<?php

namespace Pina\Modules\CMS;

use Pina\Log;

abstract class Import
{

    protected $importId = 0;
    protected $import = [];
    protected $schema = [];
    protected $importKeys = [];
    protected $keyFields = [];
    protected $settings = [];
    
    public function __construct($importId, $importSchema)
    {
        $this->importId = $importId;

        $this->import = ImportGateway::instance()->find($this->importId);

        if (!$this->import) {
            throw new \Exception("Import task not found " . $this->importId);
        }

        $this->schema = json_decode($this->import['schema'], true);
        $this->importKeys = json_decode($this->import['keys'], true);
        $this->settings = json_decode($this->import['settings'], true);
        
        /*
            $keyInfo показывает какие поля могут быть ключами какой сущности
            $keyInfo = ['resource.id' => 'resource', 'resource.title' => 'resource']

            $tagInfo показывает какая сущность обладает каким префиксом для обозначения колонки-тега
            $tagInfo = ['resource' => 'tag', 'offer' => 'offer_tag']
        */
        $keyInfo = $importSchema->schemaKeyInfo();
        $tagInfo = $importSchema->schemaTagInfo();

        foreach ($this->importKeys as $subject => $keys) {
            if (!is_array($keys)) {
                continue;
            }

            $this->keyFields[$subject] = [];

            foreach ($keys as $key) {
                $keyField = $this->schema[$key];
                
                //TODO могу ли я здесть отличать ключи-теги от ключей полей по принципу
                //ключ-тег имеет пробел и перед записью tag указывает сущность, чье поле
                //например offer_tag говорит, что тег принадлежит offer
                //в этом случае не надо прокидывать схему, а так же в этом случае не нужен метод schemaTagInfo

                if (isset($keyInfo[$keyField]) && $keyInfo[$keyField] == $subject) {
                    $this->keyFields[$subject][$keyField] = $keyField;
                    continue;
                }


                if (!empty($tagInfo[$subject])) {
                    if ($tagType = $this->extractTagType($tagInfo[$subject], $keyField)) {
                        $this->keyFields[$subject]['tag_type'][] = $tagType;
                    }
                }
            }
        }
    }

    protected function getKeys($subject)
    {
        if (!isset($this->keyFields[$subject])) {
            return [];
        }

        return $this->keyFields[$subject];
    }

    protected abstract function importLine($line);

    public function import()
    {
        $this->setStatus('import');
        
        $this->begin();

        $gw = ImportPreviewGateway::instance()->whereBy('import_id', $this->importId)->whereWithoutErrors();

        $step = 100;
        $importCounter = 0;
        $i = 0;

        while ($items = $gw->limit($i, $step)->get()) {
            foreach ($items as $item) {
                $line = json_decode($item['preview'], true);
                try {
                    $this->importLine($line);
                } catch (\Exception $e) {
                    Log::error('import', $e->getMessage());
                    $this->setStatus('error');
                    $this->finalize();
                    return $importCounter;
                }
                $importCounter ++;
            }

            $i += $step;
        }

        $this->finalize();

        $this->setStatus('done');

        return $importCounter;
    }

    protected function setStatus($status)
    {
        if (empty($status)) {
            return;
        }

        ImportGateway::instance()->whereId($this->importId)->update(['status' => $status]);
    }

    protected function extract($prefix, $line)
    {
        $prefixLength = strlen($prefix);

        $newArray = [];
        foreach ($this->schema as $k => $item) {
            if (strncmp($item, $prefix, $prefixLength) === 0) {
                $newKey = trim(substr($item, $prefixLength));
                $newArray[$newKey] = $line[$k];
            }
        }

        return $newArray;
    }

    protected function extractTags($prefix, $line)
    {
        $prefixLength = strlen($prefix);

        $newArray = [];
        foreach ($this->schema as $k => $item) {
            if (!empty($line[$k]) && strncmp($item, $prefix, $prefixLength) === 0) {
                $type = trim(substr($item, $prefixLength));
                $newArray[] = [$type, $line[$k]];
            }
        }

        return $newArray;
    }

    protected function extractTagType($prefix, $keyField)
    {
        $prefixLength = strlen($prefix);

        if (strncmp($keyField, $prefix, $prefixLength) === 0) {
            return trim(substr($keyField, $prefixLength));
        }

        return null;
    }

    protected function combine($line)
    {
        $data = [];
        foreach ($this->schema as $k => $v) {
            if (isset($line[$k])) {
                $data[$v] = $line[$k];
            }
        }
        return $data;
    }

    
    protected function getId($line, $gw, $keySchema, $tags)
    {
        $data = $this->combine($line);

        $tagIds = [];
        foreach ($keySchema as $keyType => $keyFields) {
            if ($keyType === 'tag_type') {
                if (is_array($keyFields)) {
                    foreach ($keyFields as $keyTagType) {
                        if (!isset($tags[$keyTagType]) || !is_array($tags[$keyTagType])) {
                            continue;
                        }
                        $tagIds = array_merge($tagIds, $tags[$keyTagType]);
                    }
                }
                continue;
            }

            if (!is_array($keyFields)) {
                $keyFields = [$keyFields];
            }

            foreach ($keyFields as $keyField) {
                if (isset($data[$keyField])) {
                    $tableField = $keyField;
                    if (strpos($tableField, '.') !== false) {
                        list($table, $field) = explode('.', $tableField, 2);
                        if ($gw->getTable() == $table) {
                            $gw->whereBy($field, $data[$keyField]);
                        }
                    } else {
                        $gw->whereBy($keyField, $data[$keyField]);
                    }
                }
            }
        }

        $tagIds = array_unique($tagIds);
        if (!empty($tagIds)) {
            $gw->whereTagIds($tagIds);
        }

        return $gw->id();
    }
    
    protected function begin()
    {
        
    }
    
    protected function finalize()
    {
        if (!empty($this->importId)) {
            ImportPreviewGateway::instance()->whereBy('import_id', $this->importId)->delete();
            ImportErrorGateway::instance()->whereBy('import_id', $this->importId)->delete();
        }
    }

}
