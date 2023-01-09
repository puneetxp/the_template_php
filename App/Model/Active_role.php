<?php 
namespace App\Model;

use The\Model;

class Active_role extends Model {

      public $model = ["updated_at","user_id","role_id"];
      public $name = "active_role";
      public $nullable = [];
      protected $table = "active_roles";       
      protected $relations = ['user'=>['table'=>'users','name'=>'user_id','key'=>'id','callback'=>User::class],'role'=>['table'=>'roles','name'=>'role_id','key'=>'id','callback'=>Role::class]];
      protected $fillable = ['user_id','role_id'];

}
   
   