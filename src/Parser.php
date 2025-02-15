<?php

namespace Brunty\Cigar;

/**
 * @psalm-type ParseOutput = array{
 *   url: string,
 *   status: int,
 *   content: ?string,
 *   content-type: ?string,
 *   connect-timeout: ?int,
 *   timeout: ?int
 * }
 */
class Parser
{
    /**
     * @var string|null
     */
    private $baseUrl;

    /**
     * @var null|int
     */
    private $connectTimeout;

    /**
     * @var null|int
     */
    private $timeout;

    public function __construct(string $baseUrl = null, int $connectTimeout = null, int $timeout = null)
    {
        $this->baseUrl = $baseUrl ? rtrim($baseUrl, '/') : null;
        $this->connectTimeout = $connectTimeout;
        $this->timeout = $timeout;
    }

    /**
     * @param string $filename
     *
     * @return Url[]
     * @throws \ParseError
     */
    public function parse(string $filename): array
    {
        /**
         * @var array<ParseOutput>|null
         */
        $urls = json_decode(file_get_contents($filename), true);

        if($urls === null) {
            throw new \ParseError('Could not parse ' . $filename);
        }

        return array_map(
            /**
             * @param ParseOutput $value
             */
            function(array $value) {
                $url = $this->getUrl($value['url']);

                return new Url(
                    $url,
                    $value['status'],
                    $value['content'] ?? null,
                    $value['content-type'] ?? null,
                    $value['connect-timeout'] ?? $this->connectTimeout,
                    $value['timeout'] ?? $this->timeout
                );
            },
            $urls
        );
    }

    /**
     * @param string $url
     *
     * @return string
     * @throws \ParseError
     */
    private function getUrl(string $url): string
    {
        $urlParts = parse_url($url);

        if ($urlParts === false) {
            throw new \ParseError("Could not parse URL: $url");
        }

        if ($this->baseUrl !== null && ! isset($urlParts['host'])) {
            $url = $this->baseUrl . '/' . ltrim($url, '/');
        }

        return $url;
    }
}
