<?php

namespace RY\Tutor\Composer;

use Composer\Script\Event;

class Composer
{
    public static function removeOllyo(Event $event)
    {
        $composer = $event->getComposer();
        $vendorPath = $composer->getConfig()->get('vendor-dir');

        foreach (['autoload_classmap.php', 'autoload_static.php'] as $file) {
            if (is_file($vendorPath . '/composer/' . $file)) {
                $content = file_get_contents($vendorPath . '/composer/' . $file);
                $content = explode("\n", $content);
                $newContent = [];
                foreach ($content as $line) {
                    if (str_contains($line, '../tutor/ecommerce/')) {
                        if (str_contains($line, '.php')) {
                            continue;
                        }
                    }
                    $newContent[] = $line;
                }
                file_put_contents($vendorPath . '/composer/' . $file, implode("\n", $newContent));
            }
        }
    }
}
