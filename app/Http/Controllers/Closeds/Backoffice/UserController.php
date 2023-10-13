<?php

namespace App\Http\Controllers\Closeds\Backoffice;

use App\Http\Controllers\BaseController;
use App\Models\Closeds\Backoffice\User;
use App\Services\Closeds\Backoffice\UserService;
use Illuminate\Http\Request;

class UserController extends BaseController{

  public function __construct(Request $request){
    $this->service = (new UserService())
      ->setModel(User::class);
  }   
}
