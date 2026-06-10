<?php

declare(strict_types=1);

namespace MkdBot\Application\Service;

use MkdBot\Domain\ValueObject\RagSearchResult;

class RagResponseFormatter
{
    private const MAX_ANSWER_LENGTH = 4000;
    private const TRUNCATE_SUFFIX = "\n\n...текст сокращён";

    public function format(RagSearchResult $result): string
    {
        if (!$result->isSuccess()) {
            return $this->formatError($result);
        }

        $answer = $result->getAnswer();
        if ($answer === '') {
            return '🤔 К сожалению, не удалось найти ответ на ваш вопрос. Попробуйте переформулировать.';
        }

        $text = "🤖 {$answer}";

        $sources = $result->getSources();
        if (!empty($sources)) {
            $text .= $this->formatSources($sources);
        }

        return $this->truncateText($text);
    }

    /**
     * @param \MkdBot\Domain\ValueObject\RagSearchSource[] $sources
     */
    private function formatSources(array $sources): string
    {
        // Дедупликация: по каждому файлу — только одна строка с максимальным score
        $uniqueSources = [];
        foreach ($sources as $source) {
            $filename = $source->getFilename();
            $score = $source->getScore();
            if (!array_key_exists($filename, $uniqueSources)) {
                $uniqueSources[$filename] = $score;
            } elseif ($score !== null) {
                $existing = $uniqueSources[$filename];
                if ($existing === null || $score > $existing) {
                    $uniqueSources[$filename] = $score;
                }
            }
        }

        $sourceTexts = [];
        foreach ($uniqueSources as $filename => $score) {
            $entry = '📄 ' . $filename;
            if ($score !== null) {
                $percentage = (int)round($score * 100);
                $entry .= " (соответствие: {$percentage}%)";
            }
            $sourceTexts[] = $entry;
        }

        return "\n\n📚 Источники:\n" . implode("\n", $sourceTexts);
    }

    private function formatError(RagSearchResult $result): string
    {
        $errorCode = $result->getErrorCode();

        // Ошибка авторизации — скорее всего проблема конфигурации
        if ($errorCode === 401) {
            return '⚠️ Сервис временно недоступен. Попробуйте позже.';
        }

        // Ошибка валидации — проблема с вопросом
        if ($errorCode === 400) {
            return '❌ Не удалось обработать вопрос. Попробуйте переформулировать.';
        }

        // Серверные ошибки
        return '⚠️ Произошла ошибка при поиске ответа. Попробуйте позже.';
    }

    private function truncateText(string $text): string
    {
        if (mb_strlen($text) <= self::MAX_ANSWER_LENGTH) {
            return $text;
        }

        $suffixLength = mb_strlen(self::TRUNCATE_SUFFIX);
        $maxContentLength = self::MAX_ANSWER_LENGTH - $suffixLength;

        return mb_substr($text, 0, $maxContentLength) . self::TRUNCATE_SUFFIX;
    }
}
