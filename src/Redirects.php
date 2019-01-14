<?php

namespace Frozzare\Redirects;

class Redirects
{
    /**
     * Redirects content.
     *
     * @var string
     */
    protected $content = '';

    /**
     * Parsed redirects rules.
     *
     * @var array
     */
    protected $rules;

    /**
     * Redirects constructor.
     *
     * @param string $file
     */
    public function __construct($file = null)
    {
        if (file_exists($file)) {
            $this->content = file_get_contents($file);
            $this->parse();
        }
    }

    /**
     * Match url against redirect rule and
     * replaces `:splat` with actual value if any.
     *
     * @param  string $url
     *
     * @return \Frozzare\Redirects\Rule|null
     */
    public function match($url)
    {
        foreach ($this->rules as $rule) {
            $from = str_replace('/', '\/', $rule->from);
            $reg = sprintf('/%s/', $from);

            if (!preg_match($reg, $url)) {
                continue;
            }

            $rule = clone $rule;

            if (strpos($rule->to, ':splat') !== false) {
                $reg = sprintf('/%s\/(.*)/', $from);
                if (preg_match($reg, $url, $matches)) {
                    $rule->to = str_replace(':splat', $matches[1], $rule->to);
                }
            }

            return $rule;
        }
    }

    /**
     * Get redirects rules.
     *
     * @return array
     */
    public function rules()
    {
        return $this->rules;
    }

    /**
     * Parse redirects content.
     *
     * @param  string $content
     *
     * @return \Frozzare\Redirects\Redirects
     */
    public function parse($content = null)
    {
        if (!empty($content)) {
            $this->content = $content;
        }

        $rules = [];
        $lines = preg_split('/\n/', $this->content);

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            if ($line[0] === '#') {
                continue;
            }

            $fields = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
            if (count($fields) < 1) {
                continue;
            }

            $rule = new Rule([
                'from' => $fields[0],
            ]);

            $fields = array_splice($fields, 1);

            // Parse params values.
            foreach ($fields as $field) {
                if (strpos($field, '=') === false) {
                    break;
                }

                $parts = explode('=', $field);

                if (empty($parts)) {
                    continue;
                }

                if (count($parts) > 1) {
                    $rule->params[$parts[0]] = $parts[1];
                } else {
                    $rule->params[$parts[0]] = true;
                }
            }

            $fields = array_splice($fields, count($rule->params));

            if (empty($fields)) {
                continue;
            }

            $rule->to = $fields[0];

            // Set status and force values.
            if (count($fields) > 1 && is_numeric(str_replace('!', '', $fields[1]))) {
                $status = $fields[1];
                $rule->status = intval($status);
                $rule->force = $status[strlen($status)-1] === '!';
                $fields = array_splice($fields, count($fields) > 1 ? 2 : 1);
            } else {
                $fields = array_splice($fields, 1);
            }

            // Parse language and country values.
            foreach ($fields as $field) {
                $parts = explode('=', $field);

                if (empty($parts) || count($parts) < 2) {
                    continue;
                }

                $key = strtolower($parts[0]);
                $rule->$key = explode(',', $parts[1]);
            }

            $rules[] = $rule;
        }

        $this->rules = $rules;

        return $this;
    }
}
