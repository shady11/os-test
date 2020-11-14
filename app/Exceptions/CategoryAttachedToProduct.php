<?php

namespace App\Exceptions;

use Exception;

class CategoryAttachedToProduct extends Exception
{
    public function render()
    {
        return ['errors' => 'Категория прикреплена к товару.'];
    }
}
