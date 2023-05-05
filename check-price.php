<?php
// Массив URL-адресов страниц с товарами
$urls = array(
    'https://demo.colibriclub.kz/products/rebryshki-fridays-tennessi',
    'https://demo.colibriclub.kz/products/lomot-vyalenoe-myaso-olenina-500-gr-lomot'
);

// Электронная почта для отправки уведомлений
$email = 'hello@danikgeek.com';

// Массив текущих цен товаров
$current_prices = array(
    7901,
    8622
);

// Переменная для хранения уведомлений об изменении цены
$notifications = array();

// Функция для отправки уведомления об изменении цены товара
function send_notification($email, $notifications) {
    $message = '';
    foreach ($notifications as $notification) {
        $message .= "Товар: {$notification['name']}<br>Старая цена: {$notification['current_price']} тенге<br>Новая цена: {$notification['new_price']} тенге<br><br>";
    }
    $subject = "🐦Птичка-Невеличка - Изменение цен на товары";
    $subject = "=?utf-8?b?" .base64_encode($subject) . "?=" ;
    $from = "admin@danikgeek.com";
    $headers = "From: $from\r\nReply-To: $from\r\nContent-type: text/html; charset=utf-8\r\n";
    mail($email, $subject, $message, $headers);
}

// Парсим HTML-страницы с помощью библиотеки Simple HTML DOM Parser
require_once('simple_html_dom.php');

// Обрабатываем каждый URL-адрес и сравниваем цены
for ($i = 0; $i < count($urls); $i++) {
    $url = $urls[$i];
    $current_price = $current_prices[$i];
    $html = file_get_html($url);

    // Находим элемент с ценой товара на странице
    $price_element = $html->find('.fn_price', 0);
    $new_price = (int)preg_replace('/[^0-9]/', '', $price_element->plaintext);

    // Если цена изменилась, добавляем уведомление в массив $notifications
    if ($new_price !== $current_price) {
        $product_name = $html->find('h1', 0)->plaintext;
        $notifications[] = array(
            'name' => $product_name,
            'current_price' => $current_price,
            'new_price' => $new_price
        );
        $current_prices[$i] = $new_price;
    }
    // Освобождаем память, освобождаем ресурсы
    $html->clear();
    unset($html);
}

// Если есть уведомления об изменении цен, отправляем их в одном письме
if (!empty($notifications)) {
    send_notification($email, $notifications);
	echo "Цены изменились";
}
else {
echo "Цены остались прежними";
}
?>
