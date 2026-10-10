<?php
// Fungsi untuk mencegah serangan XSS (Cross-Site Scripting)
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
