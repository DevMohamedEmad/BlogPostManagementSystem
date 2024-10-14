<?php
namespace App\Services;

class PostFormatterService
{
    public function format(string $content): string
    {
        return strtoupper($content);
    }
}
