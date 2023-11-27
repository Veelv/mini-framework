<?php
namespace Config;

class EnvLoader
{
    private array $data = [];
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->load();
    }

    public function load(): void
    {
        $content = file_get_contents($this->path);

        foreach (explode("\n", $content) as $line) {
            $line = trim($line);

            if (!$line || strpos($line, '#') === 0) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $this->data[$key] = $value;
        }
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return $this->data[$key] ?? $default;
    }
}