<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

class RichTextSanitizer
{
    private const ALLOWED_TAGS = [
        'a', 'b', 'blockquote', 'br', 'div', 'em', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'hr', 'i', 'li', 'ol', 'p', 'strong', 'u', 'ul',
    ];

    private const CONTENT_BLOCKED_TAGS = [
        'iframe', 'object', 'script', 'style', 'svg', 'math', 'video', 'audio', 'source', 'embed',
    ];

    public static function sanitize(?string $content): string
    {
        if ($content === null || $content === '') {
            return '';
        }

        if (! preg_match('/<\s*\/?\s*[a-z][^>]*>/i', $content)) {
            return $content;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previousErrorMode = libxml_use_internal_errors(true);

        try {
            $loaded = $document->loadHTML(
                '<?xml encoding="UTF-8"><div id="job-content-root">'.$content.'</div>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
            );
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrorMode);
        }

        if (! $loaded) {
            return strip_tags($content);
        }

        $root = (new DOMXPath($document))->query('//*[@id="job-content-root"]')->item(0);

        if (! $root) {
            return strip_tags($content);
        }

        foreach (iterator_to_array($root->childNodes) as $child) {
            static::sanitizeNode($child);
        }

        $safeHtml = '';
        foreach ($root->childNodes as $child) {
            $safeHtml .= $document->saveHTML($child);
        }

        return $safeHtml;
    }

    public static function render(?string $content): string
    {
        if ($content === null || $content === '') {
            return '';
        }

        if (! preg_match('/<\s*\/?\s*[a-z][^>]*>/i', $content)) {
            return nl2br(htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        }

        return static::sanitize($content);
    }

    private static function sanitizeNode(DOMNode $node): void
    {
        if ($node->nodeType === XML_COMMENT_NODE) {
            $node->parentNode?->removeChild($node);

            return;
        }

        if (! $node instanceof DOMElement) {
            foreach (iterator_to_array($node->childNodes) as $child) {
                static::sanitizeNode($child);
            }

            return;
        }

        $tag = strtolower($node->tagName);

        if (in_array($tag, self::CONTENT_BLOCKED_TAGS, true)) {
            $node->parentNode?->removeChild($node);

            return;
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            static::sanitizeNode($child);
        }

        if (! in_array($tag, self::ALLOWED_TAGS, true)) {
            $parent = $node->parentNode;

            if ($parent) {
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }

                $parent->removeChild($node);
            }

            return;
        }

        static::sanitizeAttributes($node, $tag);
    }

    private static function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            $value = trim($attribute->value);
            $keep = false;

            if ($tag === 'a' && in_array($name, ['href', 'title'], true)) {
                $keep = $name !== 'href' || static::isSafeLink($value);
            } elseif ($name === 'class' && in_array($tag, ['div', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
                $keep = in_array($value, ['ql-align-center', 'ql-align-right', 'ql-align-justify'], true);
            }

            if (! $keep) {
                $element->removeAttributeNode($attribute);
            }
        }

        if ($tag === 'a' && $element->hasAttribute('href')) {
            $element->setAttribute('rel', 'nofollow noopener noreferrer');
        }
    }

    private static function isSafeLink(string $url): bool
    {
        if ($url === '' || preg_match('/[\x00-\x20\\\\]/', $url)) {
            return false;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        if ($scheme === null) {
            return ! str_starts_with($url, '//');
        }

        return in_array(strtolower($scheme), ['http', 'https', 'mailto', 'tel'], true);
    }
}