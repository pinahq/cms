<?php

namespace Pina\Modules\CMS;

use NXP\MathExecutor;

class ImportEquation {
    protected $equation;
    
    public function __construct($equation)
    {
        $this->equation = $equation;
    }
    public function calculate($params)
    {
        $r = 0;
        $params = array_map("floatval", $params);
        try {
            $calculator = new MathExecutor();
            $calculator->setVars($params, true);
            $r = round($calculator->execute($this->equation), 2);
        } catch (\Exception $e) {
            return 0;
        }
        return $r;
    }
}