<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

$templates = [
    'LEAVECARD.docx',
    'LeaveIndividualtable.docx',
    'LeaveSummarytable.docx'
];

foreach ($templates as $file) {
    $path = "public/assets/$file";
    if (file_exists($path)) {
        try {
            $tp = new TemplateProcessor($path);
            echo "--- $file ---\n";
            print_r($tp->getVariables());
            echo "\n";
        } catch (\Exception $e) {
            echo "Error processing $file: " . $e->getMessage() . "\n";
        }
    } else {
        echo "$file not found at $path\n";
    }
}
