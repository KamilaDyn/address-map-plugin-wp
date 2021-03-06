<?php
class AddressMapPluginActivate
{
    public static function activate()
    {

        flush_rewrite_rules();
    }
}
