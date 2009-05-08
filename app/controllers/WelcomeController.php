<?php

/**
 *  Welcome Controller
 *
 */
class WelcomeController extends Controller
{
    function index()
    {
        
        $this->display('welcome/index');
    }
}
