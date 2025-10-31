<?php declare(strict_types=1);

namespace App\Bankhistory\Sendler;

class TelegramSendler
{
	private string $token;
	private string $apiUrl;

	public function __construct()
	{
		$this->token = $_ENV['TG_BOT_TOKEN'];
		$this->apiUrl = "https://api.telegram.org/bot{$this->token}/";
	}

	public function sendMessage($chatId, $text, $params = [])
	{
		$defaultParams = [
			'chat_id' => $chatId,
			'text' => $text,
			'parse_mode' => 'HTML'
		];

		$params = array_merge($defaultParams, $params);
		return $this->request('sendMessage', $params);
	}

	public function sendPhoto($chatId, $photo, $caption = '')
	{
		$params = [
			'chat_id' => $chatId,
			'photo' => $photo,
			'caption' => $caption
		];

		return $this->request('sendPhoto', $params);
	}

	public function getUpdates(): int
	{
		$chatId = '';

		$url = "https://api.telegram.org/bot{$this->token}/getUpdates";
		$response = file_get_contents($url);
		$data = json_decode($response, true);

		if ($data['ok']) {
			foreach ($data['result'] as $update) {
				if (isset($update['message'])) {
					$chatId = $update['message']['chat']['id'];
				}
			}
		}

		return $chatId;
	}

	public function createTable($headers, $data): string
	{
		// Функция для правильного подсчета ширины с кириллицей
		$mb_str_pad = function($string, $length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT) {
			$string_length = mb_strlen($string);
			$pad_string_length = mb_strlen($pad_string);

			if ($string_length >= $length) {
				return $string;
			}

			$num_pad_chars = $length - $string_length;
			$num_pad_strings = ceil($num_pad_chars / $pad_string_length);
			$padding = str_repeat($pad_string, (int)$num_pad_strings);
			$padding = mb_substr($padding, 0, $num_pad_chars);

			switch ($pad_type) {
				case STR_PAD_LEFT:
					return $padding . $string;
				case STR_PAD_BOTH:
					$left_padding = mb_substr($padding, 0, floor($num_pad_chars / 2));
					$right_padding = mb_substr($padding, 0, ceil($num_pad_chars / 2));
					return $left_padding . $string . $right_padding;
				default: // STR_PAD_RIGHT
					return $string . $padding;
			}
		};

		// Определяем ширину колонок
		$columnWidths = [];
		foreach ($headers as $index => $header) {
			$columnWidths[$index] = mb_strlen($header);
		}

		foreach ($data as $row) {
			foreach ($row as $index => $cell) {
				$width = mb_strlen($cell);
				if ($width > $columnWidths[$index]) {
					$columnWidths[$index] = $width;
				}
			}
		}

		// Добавляем отступы
		foreach ($columnWidths as &$width) {
			$width += 2;
		}

		// Создаем строку таблицы
		$createRow = function($cells) use ($columnWidths, $mb_str_pad) {
			$row = '|';
			foreach ($cells as $index => $cell) {
				$padded = $mb_str_pad(' ' . $cell . ' ', $columnWidths[$index] + 2, ' ');
				$row .= $padded . '|';
			}
			return $row;
		};

		// Создаем разделитель
		$divider = '|';
		foreach ($columnWidths as $width) {
			$divider .= str_repeat('-', $width + 2) . '|';
		}

		// Формируем таблицу
		$table = "<pre>\n";
		$table .= $createRow($headers) . "\n";
		$table .= $divider . "\n";

		foreach ($data as $row) {
			$table .= $createRow($row) . "\n";
		}
		$table .= "</pre>";

		return $table;
	}

	private function request($method, $params): bool
	{
		try {
			$url = $this->apiUrl . $method;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);

			$result = curl_exec($ch);
			curl_close($ch);

			return true;
		} catch (\Exception $e) {
			return false;
		}

	}
}