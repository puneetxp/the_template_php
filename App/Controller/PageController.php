<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller;

use App\Compiled\Component;

/**
 * Description of Page
 *
 * @author puneetxp
 */
class PageController
{

    public static function Home()
    {
        return Component::guest_layout(['body' => ["call" => [Component::class, 'home'], "value" => ""]]);
    }

    public static function About_Us()
    {
        return Component::guest_layout(['body' => ["call" => [Component::class, 'aboutus'], "value" => ""]]);
    }

    public static function Privacy_Policy()
    {
        return Component::guest_layout(['body' => ["call" => [Component::class, 'privacypolicy'], "value" => ""]]);
    }

    public static function Contact_Us()
    {
        return Component::guest_layout(['body' => ["call" => [Component::class, 'contactus'], "value" => ""]]);
    }

    public static function Services($slug)
    {
        return Component::guest_layout(
            [
                'body' =>
                [
                    "call" =>
                    [Component::class, 'service'],
                    "value" => ""
                ]
            ]
        );
    }

    //put your code here
}
