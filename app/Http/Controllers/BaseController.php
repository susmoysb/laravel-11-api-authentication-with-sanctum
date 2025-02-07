<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponse;
use App\Traits\ConstantsTrait;

class BaseController extends ApiResponse
{
    use ConstantsTrait;
}
