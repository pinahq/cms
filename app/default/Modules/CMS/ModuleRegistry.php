<?php

namespace Pina\Modules\CMS;

use Pina\ModuleRegistryInterface;
use Pina\App;
use Pina\Access;
use Pina\ModuleInterface;

class ModuleRegistry extends \Pina\ModuleRegistry
{

    public function __construct()
    {
        parent::__construct();

        $table = ModuleGateway::instance()->getTable();
        $db = App::container()->get(\Pina\DatabaseDriverInterface::class);
        $exists = $db->one("SHOW TABLES LIKE '" . $table . "'");
        if (!$exists) {
            return;
        }

        $modules = ModuleGateway::instance()->whereBy('enabled', 'Y')->column('namespace');
        foreach ($modules as $ns) {
            if (isset($this->registry[$ns])) {
                continue;
            }
            $className = $ns . '\\Module';
            if (!class_exists($className)) {
                continue;
            }
            $this->registry[$ns] = new $className;
        }
    }

    public function add(ModuleInterface $module)
    {
        $ns = $module->getNamespace();
        $title = $module->getTitle();

        if (empty($ns) || empty($title)) {
            return false;
        }

        $moduleId = ModuleGateway::instance()->whereBy('namespace', $ns)->value('id');
        if (!$moduleId) {
            $moduleId = ModuleGateway::instance()->insertGetId(array(
                'title' => $title,
                'namespace' => $ns,
                'enabled' => 'Y',
            ));
        }

        $this->registry[$ns] = $module;

        return $moduleId;
    }

    public function remove(ModuleInterface $module)
    {
        $ns = $module->getNamespace();
        ModuleGateway::instance()->whereBy('namespace', $ns)->delete();
        
        unset($this->registry[$ns]);
    }

    public function turnOn($namespace)
    {
        $className = $namespace . '\\Module';
        if (!class_exists($className)) {
            return;
        }
        $module = new $className;
        if (!($module instanceof \Pina\ModuleInterface)) {
            return;
        }

        $this->registry[$namespace] = $module;

        ModuleGateway::instance()->whereBy('namespace', $namespace)->update(['enabled' => 'Y']);
    }

    public function turnOff($namespace)
    {
        if (!isset($this->registry[$namespace])) {
            return;
        }

        unset($this->registry[$namespace]);

        ModuleGateway::instance()->whereBy('namespace', $namespace)->update(['enabled' => 'N']);
    }

}
