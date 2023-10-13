<?php

namespace App\Services;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BaseService{

  protected ?Model $model;

  public function index() : Collection{
    return $this->model::get();
  }

  public function show(int $id) : Model{
    return $this->model::findOrFail($id);
  }

  public function store() : Model{
    return $this->model::create($this->validate());
  }

  public function update(int $id) : Model{
    $model = $this->show($id);
    $model->update($this->validate());
    return $model->refresh();
  }

  public function destroy(int $id) : bool{
    $model = $this->show($id);
    return $model->delete();
  }

  /**
   * Método responsável por setar o model no construtor do controller
   *
   * @param array $value
   * @return self
   */
  public function setModel(string | Model $value) : self{

    $this->model = $value instanceof Model ? $value :  new $value();
    
    return $this;
  }

  public function validate(object | string $requestClass = null, int $currentId = null) : array{
    if(!$requestClass){
      $requestClass = $this->defineClassByRequest();
    }

    if(!$currentId){
      $currentId = request()->isMethod('put') ? request()->route()->id : null;
    }

    $requestClass = is_object($requestClass) ? $requestClass : new $requestClass();

    if(!$requestClass->authorize()){
      throw new AuthorizationException(code: Response::HTTP_UNAUTHORIZED);
    }
   
    return Validator::validate(request()->all(),$requestClass->rules($currentId),$requestClass->messages());
  }

  private function defineClassByRequest() : string{
    $action = Request()->route()->getActionMethod();

    $requestPrefixes = ["App","Http", "Requests"];
    
    foreach(explode("\\",static::class) as $prefix){
      if($prefix !== "App" && $prefix !== "Services" && $prefix !== class_basename(static::class)){
        $requestPrefixes[] = $prefix;
      }
    } 
    $requestPrefixes[] = Str::replace("Service", "", class_basename(static::class));
    $requestPrefixes[] = Str::ucfirst(Str::camel($action))."Request";
    $class = implode("\\", $requestPrefixes);
    if(!class_exists($class)){
      throw new Exception("The Request file $class does not exists.");
    }

    return $class;
  }
}