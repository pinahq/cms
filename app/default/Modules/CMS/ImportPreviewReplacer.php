<?php

namespace Pina\Modules\CMS;

class ImportPreviewReplacer {

    
    protected $fileHeader = [];
    protected $replaces = [];

    public function __construct($fileHeader, $replaces)
    {
        $this->fileHeader = $fileHeader;
        $this->replaces = $replaces;
    }
    
    public function makeReplaces($column, $row)
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
                    $equation = new ImportEquation($replacement);
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
                        $cell = $first . $replacement . $end;
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
}