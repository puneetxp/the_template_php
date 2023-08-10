<?php

namespace App\Controller\Web;

use view\pages\{
    index,
    aboutus,
    contactus,
    products,
    product,
    page,
    pages
};

class guestController {

    public static function homepage() {
        return (new index())->view();
    }

    public static function aboutus() {
        return (new aboutus())->view();
    }

    public static function page() {
        return (new page())->view();
    }

    public static function pages() {
        return (new pages())->view();
    }

    public static function contactus() {
        return (new contactus())->view();
    }

    public static function product() {
        return (new product())->view();
    }

    public static function products() {
        return (new products())->view();
    }

}
