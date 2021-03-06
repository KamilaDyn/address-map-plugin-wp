<?php
class AddressMapPluginDeactivate
{
    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}
