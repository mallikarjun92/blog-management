<?php

namespace Core;

class SimpleTemplate
{
    public function render($template, $data = [])
    {
        $templatePath = __DIR__ . '/../app/views/' . $template;

        if (!file_exists($templatePath)) {
            die("Template file not found: $template");
        }

        // Load the child template
        $content = file_get_contents($templatePath);

        // Handle {% extends "..." %}
        if (preg_match('/\{% extends "(.*?)" %\}/', $content, $matches)) {

            $parentTemplatePath = __DIR__ . '/../app/views/' . $matches[1];

            if (!file_exists($parentTemplatePath)) {
                die("Parent template file not found: {$matches[1]}");
            }

            $parentContent = file_get_contents($parentTemplatePath);

            // Replace blocks in the parent template
            preg_match_all('/\{% block (.*?) %\}(.*?)\{% endblock %\}/s', $parentContent, $childBlocks, PREG_SET_ORDER);

            foreach ($childBlocks as $block) {
                $parentContent = str_replace(
                    "{% block {$block[1]} %}{{ {$block[1]} }}{% endblock %}",
                    $block[2],
                    $parentContent
                );
            }

            // merge child content
            preg_match('/\{% block (.*?) %\}(.*?)\{% endblock %\}/s', $content, $matches);

            $parentContent = preg_replace('/\{% block content %\}.*?\{% endblock %\}/s', $matches[2], $parentContent);

            // Replace unused block markers in parent template
            $parentContent = preg_replace('/\{% block (.*?) %\}.*?\{% endblock %\}/s', '', $parentContent);

            $content = $parentContent;
        }

        // Replace variables and handle foreach loops recursively
        $content = $this->replaceVariablesAndLoops($content, $data);

        echo $content;
    }

    private function replaceVariablesAndLoops($content, $data)
    {
        $content = preg_replace_callback(
            '/\{% foreach (.*?) as (.*?) %\}(.*?)\{% endforeach %\}/s',
            function ($matches) use ($data) {
                $arrayName = trim($matches[1]);
                $itemName = trim($matches[2]);
                $loopContent = $matches[3];

                if (!isset($data[$arrayName]) || !is_array($data[$arrayName])) {
                    return '';
                }

                $renderedContent = '';
                foreach ($data[$arrayName] as $item) {
                    // Create a new data array for the loop iteration, ensuring dot notation works
                    $loopData = array_merge($data, [$itemName => (object)$item]);
                    $renderedContent .= $this->replaceVariablesAndLoops($loopContent, $loopData);
                }
                return $renderedContent;
            },
            $content
        );

        // Replace variables
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $content = str_replace("{{ $key }}", $value, $content);
            } elseif (is_object($value)) {
                foreach ($value as $property => $propertyValue) {
                    $content = str_replace("{{ $key.$property }}", $propertyValue, $content);
                }
            }
        }

        // Remove unused placeholders
        $content = preg_replace('/{{ .*? }}/', '', $content);

        return $content;
    }
}
