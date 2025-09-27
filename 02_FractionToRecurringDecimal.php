<?php declare(strict_types=1);

class Solution {

	/**
	 * @param Integer $numerator
	 * @param Integer $denominator
	 * @return String
	 */
	function fractionToDecimal($numerator, $denominator) {
		// Обработка нулевого числителя
		if ($numerator == 0) return "0";

		// Определение знака
		$sign = (($numerator < 0) ^ ($denominator < 0)) ? "-" : "";

		// Работа с абсолютными значениями
		$numeratorABS = abs($numerator);
		$denominatorABS = abs($denominator);

		// Выделение целой части
		$integerPart = intdiv($numerator, $denominator);
		$remainder = $numerator % $denominator;

		if ($remainder === 0) return $sign . $integerPart;

		// Работа с дробной частью
		$fractionPart = "";
		$remainderMap = []; // Хранит остатки и их позиции
		$position = 0;

		while ($remainder != 0) {
			// Если остаток уже встречался - найден период
			if (isset($remainderMap[$remainder])) {
				$startIndex = $remainderMap[$remainder];
				$nonRepeating = substr($fractionPart, 0, $startIndex);
				$repeating = substr($fractionPart, $startIndex);
				return $sign . $integerPart . "." . $nonRepeating . "(" . $repeating . ")";
			}

			// Запоминаем позицию текущего остатка
			$remainderMap[$remainder] = $position++;

			// Вычисляем следующую цифру
			$remainder *= 10;
			$digit = intval($remainder / $denominator);
			$fractionPart .= $digit;
			$remainder %= $denominator;
		}

		// Если дошли до конца без повторений
		return $sign . $integerPart . "." . $fractionPart;
	}
}