<?php

namespace App\Model;

use The\Model;

class User extends Model
{

      public $model = ["name", "email", "phone", "google_id", "facebook_id", "password", "enable", "id", "created_at", "updated_at"];
      public $name = "user";
      public $nullable = ["phone", "google_id", "facebook_id", "password", "id"];
      protected $table = "users";
      protected $relations = ['active_role' => ['table' => 'active_roles', 'name' => 'id', 'key' => 'user_id', 'callback' => Active_role::class]];
      protected $fillable = ['name', 'email', 'phone', 'google_id', 'facebook_id', 'password', 'enable'];
}
