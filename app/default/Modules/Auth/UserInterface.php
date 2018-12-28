<?php

namespace Pina\Modules\Auth;

interface UserInterface
{

    /**
     * Проверяет, существует ли пользователь с данным идентификатором
     * @param int $id идентификатор пользователя
     * @return boolean
     */
    public function exists($id);

    /**
     * Возвращает данные пользователя без пароля по его идентификатору
     * @param int $id идентификатор пользователя
     * @return array Данные пользователя (без пароля)
     */
    public function find($id);
    
    /**
     * @param arrray $formData Данные формы авторизации
     * @return mixed ID пользователя, если авторизация прошла (пользователь найден, пароль совпадает и т.д.)
     */
    public function auth($formData);
}
