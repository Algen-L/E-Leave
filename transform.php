<?php
$file = 'c:\\xampp\\htdocs\\E-Leave\\resources\\views\\layouts\\sdo.blade.php';
$content = file_get_contents($file);

// Replace span.nav-icon with div.nav-icon
$content = preg_replace('/<span\s+class="nav-icon"\s*>(.*?)<\/span>/s', '<div class="nav-icon">$1</div>', $content);

// Add data-tooltip to nav-item links by extracting the nav-text
$content = preg_replace_callback('/(<a[^>]+class="nav-item[^>]*>)\s*<div\s+class="nav-icon"\s*>(.*?)<\/div>\s*<span\s+class="nav-text"\s*>(.*?)<\/span>\s*<\/a>/s', function($matches) {
    $a_tag = $matches[1];
    $icon_div = $matches[2];
    $text_content = trim($matches[3]);
    
    // Inject data-tooltip into the a_tag
    $a_tag = str_replace('<a ', '<a data-tooltip="' . htmlspecialchars($text_content, ENT_QUOTES) . '" ', $a_tag);
    
    return $a_tag . "\n                        <div class=\"nav-icon\">" . $icon_div . "</div>\n                        <span class=\"nav-text\">" . $text_content . "</span>\n                    </a>";
}, $content);

file_put_contents($file, $content);
echo "Transformation complete.\n";
