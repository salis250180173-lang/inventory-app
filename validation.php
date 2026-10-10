<?php
// Validasi angka positif (untuk jumlah stok/harga)
function validatePositiveInt($value) {
    $val = filter_var($value, FILTER_VALIDATE_INT);
    return ($val !== false && $val > 0) ? $val : false;
}
