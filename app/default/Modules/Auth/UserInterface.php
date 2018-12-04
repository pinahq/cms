<?php

namespace Pina\Modules\Auth;

interface UserInterface
{

    public function exists($id);

    public function find($id);

    public function findByEmail($email);
}
