<?php
require 'vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;

$templatePath = 'public/assets/LEAVECARD.docx';
if (!file_exists($templatePath)) {
    echo "Template file not found at $templatePath\n";
    exit(1);
}

try {
    $templateProcessor = new TemplateProcessor($templatePath);
    $variables = $templateProcessor->getVariables();
    echo "Variables found in $templatePath:\n";
    foreach ($variables as $variable) {
        echo "- $variable\n";
    }
} catch (\Exception $e) {
    echo "Error processing template: " . $e->getMessage() . "\n";
}
