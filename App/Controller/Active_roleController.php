<?php 
namespace App\Controller;

use App\Model\{
  Active_role
};

class Active_roleController {

    public static function index() {
        return Active_role::all();
    }

    public static function store() {
        return Active_role::create();
    }

    public static function show($id) {
        return Active_role::find($id);
    }

    public static function update($id) {
        return Active_role::update($id);
    }

    public static function upsert() {
        return Active_role::upsert($_POST);
    }

    public static function delete($id) {
        return Active_role::delete($id);
    }

}