<?php

namespace App\Services;

class SolrQueryBuilder
{
    // Converts react-querybuilder JSON → Solr fq string
    public function build(array $group): string
    {
        $combinator = strtoupper($group['combinator'] ?? 'AND');
        $parts      = [];

        foreach ($group['rules'] as $rule) {
            if (isset($rule['rules'])) {
                // Nested group
                $nested = $this->build($rule);
                if ($nested) {
                    $parts[] = "($nested)";
                }
            } else {
                $part = $this->buildRule($rule);
                if ($part) {
                    $parts[] = $part;
                }
            }
        }

        return implode(" $combinator ", array_filter($parts));
    }

    private function buildRule(array $rule): string
    {
        $field    = $rule['field']    ?? '';
        $operator = $rule['operator'] ?? '=';
        $value    = $rule['value']    ?? '';

        if ($field === '') {
            return '';
        }

        // Only check for value if the operator is not a 'null' check
        if (!in_array($operator, ['isNull', 'isNotNull', 'null', 'notNull'])) {
            if ($value === '' || $value === null) {
                return '';
            }
        }

        // Escape special Solr characters for text fields
        $escaped = $this->escape($value);

        return match ($operator) {
            '=', 'equals'              => "$field:\"$escaped\"",
            '!=', 'doesNotEqual'       => "-$field:\"$escaped\"",
            'contains'                 => "$field:*$escaped*",
            'doesNotContain'           => "-$field:*$escaped*",
            'beginsWith'               => "$field:$escaped*",
            'endsWith'                 => "$field:*$escaped",
            '>', 'greaterThan'         => "$field:{{$value} TO *}",
            '<', 'lessThan'            => "$field:{* TO {$value}}",
            '>=', 'greaterThanOrEqual' => "$field:[{$value} TO *]",
            '<=', 'lessThanOrEqual'    => "$field:[* TO {$value}]",
            'between'                  => $this->buildBetween($field, $value),
            'in'                       => $this->buildIn($field, $value),
            'notIn'                    => $this->buildNotIn($field, $value),
            'null', 'isNull'           => "-$field:[* TO *]",
            'notNull', 'isNotNull'     => "$field:[* TO *]",
            default                    => "$field:\"$escaped\""
        };
    }

    private function buildBetween(string $field, string $value): string
    {
        $parts = explode(',', $value);
        if (count($parts) === 2) {
            return "$field:[{$parts[0]} TO {$parts[1]}]";
        }
        return "$field:\"$value\"";
    }

    private function buildIn(string $field, string $value): string
    {
        $values = array_map(
            fn($v) => '"' . $this->escape(trim($v)) . '"',
            explode(',', $value)
        );
        return "$field:(" . implode(' OR ', $values) . ")";
    }

    private function buildNotIn(string $field, string $value): string
    {
        $values = array_map(
            fn($v) => '"' . $this->escape(trim($v)) . '"',
            explode(',', $value)
        );
        return "-$field:(" . implode(' OR ', $values) . ")";
    }

    private function escape(string $value): string
    {
        // Escape Solr special characters
        $special = ['\\', '+', '-', '&&', '||', '!', '(', ')', '{', '}',
                    '[', ']', '^', '"', '~', '*', '?', ':', '/', ' '];
        foreach ($special as $char) {
            $value = str_replace($char, '\\' . $char, $value);
        }
        return $value;
    }
}