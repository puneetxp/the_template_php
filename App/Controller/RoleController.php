<?php 
namespace App\Controller;

use App\Model\{
  Role
};

class RoleController {

    public static function index() {
        return Role::all();
    }

    public static function store() {
        return Role::create();
    }

    public static function show($id) {
        return Role::find($id);
    }

    public static function update($id) {
        return Role::update($id);
    }

    public static function upsert() {
        return Role::upsert($_POST);
    }

    public static function delete($id) {
        return Role::delete($id);
    }

}