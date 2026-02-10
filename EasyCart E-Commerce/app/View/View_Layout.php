<?php

namespace EasyCart\View;

/**
 * View_Layout — Full Page Layout with Header + Content + Footer
 * 
 * Wraps any content view with header and footer.
 */
class View_Layout extends View_Abstract
{
    protected $template = '';

    /** @var View_Abstract|null The content view */
    private $contentView = null;

    /** @var string Pre-rendered header HTML */
    private $headerHtml = '';

    /** @var string Pre-rendered footer HTML */
    private $footerHtml = '';

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $base = __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR;
        $this->headerHtml = $this->renderTemplate($base . 'header.php', $data);
        $this->footerHtml = $this->renderTemplate($base . 'footer.php', $data);
    }

    /**
     * Set the content view to wrap
     */
    public function setContentView(View_Abstract $view): self
    {
        $this->contentView = $view;
        return $this;
    }

    /**
     * Render header + content + footer
     */
    public function toHtml(): string
    {
        $html = $this->headerHtml;

        if ($this->contentView) {
            $html .= $this->contentView->toHtml();
        }

        $html .= $this->footerHtml;
        return $html;
    }

    /**
     * Render a template file with data
     */
    private function renderTemplate(string $path, array $data): string
    {
        // Normalize path
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        if (!file_exists($path)) {
            error_log("View_Layout: Template not found at: " . $path);
            return '';
        }
        extract($data);
        ob_start();
        include $path;
        return ob_get_clean();
    }
}
