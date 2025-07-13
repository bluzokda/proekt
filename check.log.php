<?php
if (file_exists('auth_errors.log')) {
    echo "✅ Файл auth_errors.log найден!<br>";
    echo "<pre>" . file_get_contents('auth_errors.log') . "</pre>";
} else {
    echo "❌ Файл auth_errors.log НЕ найден.";
}
?>