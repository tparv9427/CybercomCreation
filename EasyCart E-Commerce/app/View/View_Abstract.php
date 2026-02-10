<?php

namespace EasyCart\View;

/**
 * View_Abstract — Base View Class
 * 
 * Every View MUST implement toHtml().
 * Views contain HTML only — no business logic.
 * Data is passed in via setData() or constructor.
 * Template files are rendered using output buffering.
 */
abstract class View_Abstract
{
    /** @var array Data available to the template */
    protected $data = [];

    /** @var string Path to the PHP template file */
    protected $template = '';

    /**
     * @param array $data Initial data for the view
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * MANDATORY: Render this view to HTML string
     * 
     * @return string HTML output
     */
    public function toHtml(): string
    {
        if (empty($this->template) || !file_exists($this->template)) {
            error_log("View_Abstract: Template not found: " . $this->template);
            return '';
        }

        // Use a isolated scope for extraction and inclusion
        return (function ($____template____, $____data____) {
            extract($____data____);
            ob_start();
            include $____template____;
            return ob_get_clean();
        })($this->template, $this->data);
    }

    /**
     * Set all data at once
     * 
     * @param array $data
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Set a single variable
     * 
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function assign(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Get a data value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Render the view and echo it directly
     */
    public function render(): void
    {
        echo $this->toHtml();
    }

    /**
     * Set the template path
     * 
     * @param string $path Absolute path to template
     * @return self
     */
    public function setTemplate(string $path): self
    {
        $this->template = $path;
        return $this;
    }
}
