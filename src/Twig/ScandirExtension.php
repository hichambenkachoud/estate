<?php


namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ScandirExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('scandir', [$this, 'getImages']),
        ];
    }

    public function getImages(string $url, $file)
    {
        return array_diff(scandir(dirname($url)), ['.', '..']);
    }
}
