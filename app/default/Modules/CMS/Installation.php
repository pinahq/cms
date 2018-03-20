<?php

namespace Pina\Modules\CMS;

use Pina\InstallationInterface;
use Pina\ModuleRegistry;

class Installation implements InstallationInterface
{

    public static function install()
    {
        $moduleId = ModuleRegistry::add(new Module());

        if (empty($moduleId)) {
            throw new \Exception('can not install module ' . __NAMESPACE__);
            return;
        }

        self::createResourceType($moduleId);
        self::createContentType($moduleId);
        self::createConfig();
    }

    public static function createResourceType($moduleId)
    {
        $data = array(
            'module_id' => $moduleId,
            'type' => 'pages',
            'title' => 'Страница',
            'tree' => 'Y',
        );
        if (!ResourceTypeGateway::instance()->whereFields($data)->exists()) {
            ResourceTypeGateway::instance()->insert($data);
        }
    }

    public static function createContentType($moduleId)
    {
        $data = array(
            'module_id' => $moduleId,
            'type' => 'heading-content',
            'title' => 'Заголовок',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }

        $data = array(
            'module_id' => $moduleId,
            'type' => 'text-content',
            'title' => 'Текст',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }

        $data = array(
            'module_id' => $moduleId,
            'type' => 'image-content',
            'title' => 'Изображение',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }

        $data = array(
            'module_id' => $moduleId,
            'type' => 'list-content',
            'title' => 'Список',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }

        $data = array(
            'module_id' => $moduleId,
            'type' => 'gallery-content',
            'title' => 'Галерея',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }

        $data = array(
            'module_id' => $moduleId,
            'type' => 'resource-list-content',
            'title' => 'Список страниц',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }
    }

    public static function createConfig()
    {
        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'Уведомления')->insertIgnore([
            'key' => 'submission_emails',
            'value' => '',
            'title' => 'Emails для уведомлений',
            'order' => "0"
        ]);

        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'Компания')->insertIgnore([
                [
                'key' => 'company_title',
                'value' => '',
                'title' => 'Наименование',
                'order' => "1"
            ],
                [
                'key' => 'company_phone',
                'value' => '',
                'title' => 'Телефон',
                'order' => "2"
            ],
                [
                'key' => 'company_email',
                'value' => '',
                'title' => 'email',
                'order' => "3"
            ]
        ]);

        $timeFormats = [
            'H:i' => '23:59',
            'H:i:s' => '23:59:59',
            'h:i A' => '12:59 AM/PM',
            'h:i:s A' => '12:59:59 AM/PM'
        ];

        $dateFormats = [
            'd-m-Y' => '25-07-' . date('Y'),
            'd/m/Y' => '25/07/' . date('Y'),
            'd.m.Y' => '25.07.' . date('Y'),
            'm-d-Y' => '07-25-' . date('Y'),
            'm/d/Y' => '07/25/' . date('Y'),
            'm.d.Y' => '07.25.' . date('Y'),
            'Y-m-d' => date('Y') . '-07-25',
            'Y/m/d' => date('Y') . '/07/25',
            'Y.m.d' => date('Y') . '.07.25',
        ];

        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'Формат даты и времени')->insertIgnore([
                [
                'key' => 'format_date',
                'type' => 'select',
                'variants' => json_encode($dateFormats),
                'value' => '',
                'title' => 'Формат даты',
                'order' => "4"
            ],
                [
                'key' => 'format_time',
                'type' => 'select',
                'variants' => json_encode($timeFormats),
                'value' => '',
                'title' => 'Формат времени',
                'order' => "5"
            ]
        ]);

        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'favicon')->insertIgnore([
                [
                'key' => 'favicon',
                'type' => 'image',
                'value' => '',
                'title' => 'Favicon',
                'order' => "6"
            ],
        ]);

        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'logo')->insertIgnore([
                [
                'key' => 'logo',
                'type' => 'image',
                'value' => '',
                'title' => 'Logo',
                'order' => "7"
            ],
        ]);

        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'Default Meta Tags')->insertIgnore([
                [
                'key' => 'meta_title',
                'value' => '',
                'title' => 'Meta Title',
                'order' => "8"
            ],
                [
                'key' => 'meta_description',
                'value' => '',
                'title' => 'Meta Description',
                'order' => "9"
            ],
                [
                'key' => 'meta_keywords',
                'value' => '',
                'title' => 'Meta Keywords',
                'order' => "10"
            ],
        ]);

        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'Custom code')->insertIgnore([
                [
                'key' => 'custom_header_code',
                'type' => 'textarea',
                'value' => '',
                'title' => 'Meta tags',
                'order' => "11"
            ],
                [
                'key' => 'custom_footer_code',
                'type' => 'textarea',
                'value' => '',
                'title' => 'Counters',
                'order' => "12"
            ],
        ]);
    }

    public static function remove()
    {
        #echo 'remove';
    }

}
