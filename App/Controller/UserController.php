<?php 
namespace App\Controller;

use App\Model\{
  User
};

class UserController {

    public static function index() {
        return User::all();
    }

    public static function store() {
        return User::create();
    }

    public static function show($id) {
        return User::find($id);
    }

    public static function update($id) {
        return User::update($id);
    }

    public static function upsert() {
        return User::upsert($_POST);
    }

    public static function delete($id) {
        return User::delete($id);
    }

}