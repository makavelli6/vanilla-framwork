<?php

class Template
{
    public $blocks = [];
    public $cache_path = 'cache/';
    public $cache_enabled = true;
    public $file_ext = '.html';

    public $assets = [
        'css' => [],
        'js' => []
    ];

    private $current_scope = null;

    public function view($file, $data = [])
    {
        $cached_file = $this->cache($file);

        extract($data, EXTR_SKIP);

        require $cached_file;
    }

    public function cache($file)
    {
        if (!file_exists($this->cache_path)) {
            mkdir($this->cache_path, 0744, true);
        }

        $cached_file = $this->cache_path .
            str_replace(['/', $this->file_ext], ['_', ''], $file) . '.php';

        $source = ROOT . '/App/templates/' . $file;

        if (!$this->cache_enabled ||
            !file_exists($cached_file) ||
            filemtime($cached_file) < filemtime($source)) {

            $code = $this->includeFiles($file);
            $code = $this->compileCode($code);

            file_put_contents(
                $cached_file,
                '<?php class_exists("' . __CLASS__ . '") or exit; ?>' . PHP_EOL . $code
            );
        }

        return $cached_file;
    }

    public function clearCache()
    {
        foreach (glob($this->cache_path . '*') as $file) {
            unlink($file);
        }
    }

    private function compileCode($code)
    {
        $code = $this->compileComponents($code);
        $code = $this->compileComponentStyles($code);

        $code = $this->compileIf($code);
        $code = $this->compileEach($code);
        $code = $this->compileVariables($code);

        $code = $this->compileBlock($code);
        $code = $this->compileYield($code);

        $code = $this->compileEscapedEchos($code);
        $code = $this->compileEchos($code);
        $code = $this->compilePHP($code);

        return $code;
    }

    private function includeFiles($file)
    {
        $path = ROOT . '/App/templates/' . $file;
        $code = file_get_contents($path);

        if (preg_match('/{% ?extends ?\'?(.*?)\'? ?%}/i', $code, $match)) {

            $parent = file_get_contents(ROOT . '/App/templates/' . $match[1]);

            $code = preg_replace('/{% ?extends ?\'?(.*?)\'? ?%}/i', '', $code);

            $this->compileBlock($code);

            $code = $this->compileYield($parent);
        }

        return $code;
    }

    private function compileVariables($code)
    {
        return preg_replace(
            '/\{([a-zA-Z0-9_\.\[\]]+)\}/',
            '<?php echo htmlspecialchars($$1 ?? "", ENT_QUOTES, "UTF-8"); ?>',
            $code
        );
    }

    private function compileIf($code)
    {
        $code = preg_replace('/\{#if\s+(.*?)\}/', '<?php if($1): ?>', $code);
        $code = preg_replace('/\{\:else\}/', '<?php else: ?>', $code);
        $code = preg_replace('/\{\/if\}/', '<?php endif; ?>', $code);

        return $code;
    }

    private function compileEach($code)
    {
        $code = preg_replace(
            '/\{#each\s+(.*?)\s+as\s+(.*?)\}/',
            '<?php foreach($$1 as $$2): ?>',
            $code
        );

        $code = preg_replace('/\{\/each\}/', '<?php endforeach; ?>', $code);

        return $code;
    }

    private function compileComponents($code)
    {
        return preg_replace_callback(
            '/<([A-Z][A-Za-z0-9]*)\s*(.*?)>(.*?)<\/\1>/s',
            function ($matches) {

                $component = $matches[1];
                $props = $this->parseProps($matches[2]);
                $slot = addslashes(trim($matches[3]));

                return "<?php echo \$this->renderComponent('$component',$props,'$slot'); ?>";

            },
            $code
        );
    }

    private function parseProps($string)
    {
        $props = [];

        preg_match_all('/(\w+)="(.*?)"/', $string, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $props[$match[1]] = $match[2];
        }

        preg_match_all('/(\w+)=\{(.*?)\}/', $string, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $props[$match[1]] = $match[2];
        }

        return var_export($props, true);
    }

    public function renderComponent($name, $props = [], $slot = '')
    {
        $file = ROOT . "/App/templates/components/$name.html";

        if (!file_exists($file)) return "";

        $scope = $this->generateScope($file);
        $this->current_scope = $scope;

        $props['slot'] = $slot;

        ob_start();
        $this->view("components/$name.html", $props);
        $html = ob_get_clean();

        $html = preg_replace(
            '/^<(\w+)/',
            '<$1 data-scope="' . $scope . '"',
            $html
        );

        return $html;
    }

    private function generateScope($file)
    {
        return 's' . substr(md5($file), 0, 8);
    }

    private function compileComponentStyles($code)
    {
        return preg_replace_callback(
            '/<style>(.*?)<\/style>/is',
            function ($matches) {

                $css = trim($matches[1]);

                if (!$this->current_scope) return '';

                $css = $this->scopeCSS($css, $this->current_scope);

                if (!in_array($css, $this->assets['css'])) {
                    $this->assets['css'][] = $css;
                }

                return '';

            },
            $code
        );
    }

    private function scopeCSS($css, $scope)
    {
        return preg_replace_callback(
            '/([^{]+){/',
            function ($matches) use ($scope) {

                $selectors = explode(',', $matches[1]);

                $selectors = array_map(function ($selector) use ($scope) {

                    $selector = trim($selector);

                    if ($selector == 'body' || $selector == 'html') {
                        return $selector;
                    }

                    return $selector . '[data-scope="' . $scope . '"]';

                }, $selectors);

                return implode(',', $selectors) . ' {';

            },
            $css
        );
    }

    public function renderScopedCSS()
    {
        if (empty($this->assets['css'])) return '';

        return "<style>\n" .
            implode("\n", $this->assets['css']) .
            "\n</style>";
    }

    private function compilePHP($code)
    {
        return preg_replace(
            '~\{%\s*(.+?)\s*\%}~is',
            '<?php $1 ?>',
            $code
        );
    }

    private function compileEchos($code)
    {
        return preg_replace(
            '~\{{\s*(.+?)\s*\}}~is',
            '<?php echo $1 ?>',
            $code
        );
    }

    private function compileEscapedEchos($code)
    {
        return preg_replace(
            '~\{{{\s*(.+?)\s*\}}}~is',
            '<?php echo htmlentities($1, ENT_QUOTES, "UTF-8") ?>',
            $code
        );
    }

    private function compileBlock($code)
    {
        preg_match_all(
            '/{% ?block ?(.*?) ?%}(.*?){% ?endblock ?%}/is',
            $code,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $value) {

            if (!array_key_exists($value[1], $this->blocks)) {
                $this->blocks[$value[1]] = '';
            }

            if (strpos($value[2], '@parent') === false) {

                $this->blocks[$value[1]] = $value[2];

            } else {

                $this->blocks[$value[1]] = str_replace(
                    '@parent',
                    $this->blocks[$value[1]],
                    $value[2]
                );
            }

            $code = str_replace($value[0], '', $code);
        }

        return $code;
    }

    private function compileYield($code)
    {
        foreach ($this->blocks as $block => $value) {

            $code = preg_replace(
                '/{% ?yield ?' . $block . ' ?%}/',
                $value,
                $code
            );
        }

        $code = preg_replace('/{% ?yield ?(.*?) ?%}/i', '', $code);

        return $code;
    }
}