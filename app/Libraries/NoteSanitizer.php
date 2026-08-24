<?php

namespace App\Libraries;

class NoteSanitizer
{
    public static function clean(string $html): string
    {
        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html) ?? '';
        $html = preg_replace('/\son\w+="[^"]*"/i', '', $html) ?? '';
        $html = preg_replace("/\son\w+='[^']*'/i", '', $html) ?? '';
        $html = strip_tags($html, '<p><br><div><span><b><strong><i><em><u><ul><ol><li><a><h3><blockquote>');
        $html = preg_replace_callback('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>/i', static function ($m) {
            $href = $m[1];
            if (!preg_match('#^https?://#i', $href)) {
                return '<a>';
            }

            return '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener">';
        }, $html) ?? $html;

        return trim($html);
    }
}
