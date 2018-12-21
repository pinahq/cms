<?php

namespace Pina\Modules\CMS;

use Pina\InstallationInterface;
use Pina\Hash;

class Installation implements InstallationInterface
{

    public static function install()
    {
        self::createAdminUser();
        self::createResourceType();
        self::createContentType();
        self::createConfig();
        self::setHomepageImage();
    }

    public static function createAdminUser()
    {
        $adminExists = UserGateway::instance()->whereBy('group', 'root')->exists();
        if (empty($adminExists)) {
            $adminExists = UserGateway::instance()->whereBy('email', 'admin')->exists();
        }
        if (empty($adminExists)) {
            UserGateway::instance()->insert(array(
                'email' => 'admin',
                'password' => Hash::make('admin'),
                'group' => 'root',
                'status' => 'active'
            ));
        }
    }

    public static function createResourceType()
    {
        $data = array(
            'type' => 'pages',
            'title' => 'Страница',
            'tree' => 'Y',
        );
        if (!ResourceTypeGateway::instance()->whereFields($data)->exists()) {
            ResourceTypeGateway::instance()->insert($data);
        }
    }

    public static function createContentType()
    {
        $data = array(
            'type' => 'heading-content',
            'title' => 'Заголовок',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }

        $data = array(
            'type' => 'text-content',
            'title' => 'Текст',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }

        $data = array(
            'type' => 'image-content',
            'title' => 'Изображение',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }

        $data = array(
            'type' => 'list-content',
            'title' => 'Список',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }

        $data = array(
            'type' => 'gallery-content',
            'title' => 'Галерея',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }

        $data = array(
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
    
    public static function setHomepageImage()
    {
        if (!ContentGateway::instance()->exists()) {
            $textConentTypeId = ContentTypeGateway::instance()->whereBy('type', 'text-content')->id();
            ContentGateway::instance()->insert([
                "resource_id" => 0,
                "slot" => "hometop",
                "text" => '<p><img style="width: 100%;" src="static/default/images/homepage.jpg" /></p>',
                "params" => '{}',
                "content_type_id" => $textConentTypeId,
                "order" => 0,
            ]);
            $headingConentTypeId = ContentTypeGateway::instance()->whereBy('type', 'heading-content')->id();
            ContentGateway::instance()->insert([
                "resource_id" => 0,
                "slot" => "home",
                "text" => 'PinaCMS',
                "params" => '{"h":"h2"}',
                "content_type_id" => $headingConentTypeId,
                "order" => 0,
            ]);
            ContentGateway::instance()->insert([
                "resource_id" => 0,
                "slot" => "home",
                "text" => '<p>Не забудьте установить и настроить модули в разделе панели администратора: Настройки -&gt; Модули.</p>',
                "params" => '{}',
                "content_type_id" => $textConentTypeId,
                "order" => 1,
            ]);
        }
        
    }

    public static function remove()
    {
        
    }

}
