<?php
require __DIR__ . '/vendor/autoload.php';

if (class_exists(\DoctrineMigrations\Version20250525171307::class)) {
    echo "Class found\n";
} else {
    echo "Class NOT found\n";
}
