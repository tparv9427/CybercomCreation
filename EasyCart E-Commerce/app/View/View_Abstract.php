<?php

namespace EasyCart\View;

/**
 * View_Abstract
 * 
 * Base class for all View classes.
 * Each View must implement toHtml() to render content.
 */
abstract class View_Abstract
{
    /**
     * Template data
     * @var array
     */
    protected $data = [];

    /**
     * Set template data
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setData(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Get template data
     * @param string $key
     * @return mixed|null
     */
    public function getData(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Set multiple data values
     * @param array $data
     * @return $this
     */
    public function setDataArray(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Render the view to HTML string
     * Must be implemented by child classes
     * @return string
     */
    abstract public function toHtml(): string;

    /**
     * Helper to escape HTML
     * @param string $string
     * @return string
     */
    protected function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Include a template file and return output
     * @param string $templatePath Relative path from Views directory
     * @return string
     */
    protected function renderTemplate(string $templatePath): string
    {
        $fullPath = __DIR__ . '/../Views/' . $templatePath;

        if (!file_exists($fullPath)) {
            return '';
        }

        // Extract data to local scope
        extract($this->data);

        ob_start();
        include $fullPath;
        return ob_get_clean();
    }
}
