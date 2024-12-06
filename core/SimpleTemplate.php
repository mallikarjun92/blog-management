<?php

namespace Core;

class SimpleTemplate
{
    public function render($template, $data = [])
    {
        $templatePath = __DIR__ . '/../app/views/' . $template;
        
        if (file_exists($templatePath)) {
            $content = file_get_contents($templatePath);
            
            // Replace regular variables first
            foreach ($data as $key => $value) {
                if (!is_array($value)) {  // Only replace non-array values
                    $content = str_replace("{{ $key }}", $value, $content);
                }
            }
            
            // Handle loops by finding {{#key}} and {{/key}}
            preg_match_all('/\{\{#(.*?)\}\}(.*?)\{\{\/\1\}\}/s', $content, $matches);
            
            // Loop through each match
            foreach ($matches[0] as $index => $match) {
                $loopKey = $matches[1][$index];  // Key in the loop
                $loopContent = $matches[2][$index];  // Content inside the loop
                
                // If the key exists in the data and is an array, loop through it
                if (isset($data[$loopKey]) && is_array($data[$loopKey])) {
                    $repeatedContent = '';
                    foreach ($data[$loopKey] as $item) {
                        $itemContent = $loopContent;
                        foreach ($item as $key => $value) {
                            // Replace each item's variables inside the loop
                            $itemContent = str_replace("{{ $key }}", $value, $itemContent);
                        }
                        $repeatedContent .= $itemContent;
                    }
                    $content = str_replace($match, $repeatedContent, $content);
                } else {
                    // If no data is found, remove the loop (optional)
                    $content = str_replace($match, '', $content);
                }
            }

            // Output the final content
            echo $content;
        } else {
            die("Template file not found.");
        }
    }
}

