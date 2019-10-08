<?php

namespace Brunty\Cigar;

class JsonWriter implements WriterInterface
{
    /**
     * @return void
     */
    public function writeErrorLine(string $message)
    {
        echo json_encode([
            'type' => 'error',
            'message' => $message,
        ]), PHP_EOL;
    }

    /**
     * @return void
     */
    public function writeResults(int $numberOfPassedResults, int $numberOfResults, bool $passed, float $timeDiff, Result ...$results)
    {
        echo json_encode([
            'type' => 'results',
            'time_taken' => $timeDiff,
            'passed' => $passed,
            'results_count' => $numberOfResults,
            'results_passed_count' => $numberOfPassedResults,
            'results' => array_map([$this, 'line'], $results),
        ]), PHP_EOL;
    }

    /**
     * @return (null|string|int|bool)[]
     *
     * @psalm-return array{passed: bool, url: string, status_code_expected: int, status_code_actual: int, content_type_expected: null|string, content_type_actual: null|string, content_expected: null|string}
     */
    private function line(Result $result): array
    {
        return [
            'passed' => $result->hasPassed(),
            'url' => $result->getUrl()->getUrl(),
            'status_code_expected' => $result->getUrl()->getStatus(),
            'status_code_actual' => $result->getStatusCode(),
            'content_type_expected' => $result->getUrl()->getContentType(),
            'content_type_actual' => $result->getContentType(),
            'content_expected' => $result->getUrl()->getContent(),
        ];
    }
}
