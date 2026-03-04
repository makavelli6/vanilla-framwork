<?php 

/**
 * 
 */
class View
{
	public $path = ROOT.VIEWS;

    /**
     * Explicit data store — populated via with(). Extracted into view scope.
     */
    private array $_viewData = [];
	
	function __construct()
	{
		//echo "We have a view <br>";
	}

    /**
     * Bind data explicitly to the view for use inside templates.
     * Fluent — can be chained: $this->view->with(['user' => $user])->render('profile/show')
     *
     * @param  array  $data  Assoc array of variables to expose in the template.
     * @return $this
     */
    public function with(array $data): static
    {
        $this->_viewData = array_merge($this->_viewData, $data);
        return $this;
    }

	public function render_mutli($pages = array(), $noInclude = false)
	{
		if($noInclude == true){
			$this->render_array($pages);
		}else{
			$this->safe_require('./App/Head.php');
			$this->render_array($pages);
			$this->safe_require('./App/Tail.php');
		}
	}

    /**
     * Render multiple view partials in sequence, wrapped in Head/Tail by default.
     * Usage: $this->view->renderMany(['sidebar', 'content/feed', 'widgets/recent'])
     *
     * @param  array  $pages     Array of view paths (relative to App/views), no extension needed.
     * @param  bool   $noInclude Skip wrapping with Head.php and Tail.php if true.
     */
    public function renderMany(array $pages, bool $noInclude = false)
    {
        if ($noInclude) {
            $this->render_array($pages);
        } else {
            $this->safe_require('./App/Head.php');
            $this->render_array($pages);
            $this->safe_require('./App/Tail.php');
        }
    }

    /**
     * Render a layout template that wraps specific content views.
     * Example usage in controller:
     *   $this->view->content = ['content/feed', 'widgets/recent'];
     *   $this->view->layout('layouts/dashboard');
     *
     * Inside the layout template, call: $this->renderMany($content);
     */
    public function layout(string $layoutName)
    {
        $this->safe_require('./' . $layoutName . '.php');
    }

    public function render_array($pages = array()) {
        foreach ($pages as $page) {
            $this->safe_require_fresh($page . '.php');
        }
    }

	public function render($name, $noInclude = false){
		if($noInclude == true){
			$this->safe_require($name.'.php');
		}else{
			$this->safe_require('./App/Head.php');
			$this->safe_require('./'.$name.'.php');
			$this->safe_require('./App/Tail.php');
		}
	}
	
    private function safe_require($file) {
        $fullPath = $this->path . $file;
        if (file_exists($fullPath)) {
            // Merge public view properties and explicit with() data, strip internal props
            $vars = get_object_vars($this);
            unset($vars['path'], $vars['_viewData']);
            $vars = array_merge($vars, $this->_viewData);
            extract($vars);

            require_once $fullPath;
        } else {
            Logger::Error("View Not Found: Attempted to render $fullPath but it does not exist.");
            echo "<b>Application Error:</b> The requested view could not be rendered.";
        }
    }

    // -------------------------------------------------------------------------
    // XSS PROTECTION HELPERS
    // -------------------------------------------------------------------------

    /**
     * HTML context escaping — use inside element content and attribute values.
     * Example: echo $this->view->escape($user['name']);
     */
    public function escape($string): string {
        return htmlspecialchars((string) $string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * URL context escaping — sanitizes values placed inside href/src/action attributes.
     * Example: echo $this->view->escapeUrl($redirect);
     */
    public function escapeUrl($url): string {
        $url = (string) $url;
        // Block javascript: and data: URIs
        if (preg_match('/^\s*(javascript|data|vbscript):/i', $url)) {
            return '#';
        }
        return htmlspecialchars($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * HTML attribute context escaping — for dynamic attribute values.
     * Example: <div class="<?= $this->view->escapeAttr($class) ?>">
     */
    public function escapeAttr($value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * JavaScript context escaping — for values embedded inside <script> blocks.
     * Example: var name = "<?= $this->view->escapeJs($name) ?>";
     */
    public function escapeJs($value): string {
        return json_encode((string) $value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
    }

    // Uses `require` (not require_once) so the same partial can be included
    // multiple times — e.g. a card partial rendered per item in a list.
    private function safe_require_fresh($file) {
        $fullPath = $this->path . $file;
        if (file_exists($fullPath)) {
            $vars = get_object_vars($this);
            unset($vars['path']);
            extract($vars);
            require $fullPath;
        } else {
            Logger::Error("View Not Found: $fullPath does not exist.");
            echo "<b>Application Error:</b> The requested view partial could not be rendered.";
        }
    }

	public function Json($value, $status = 200)
	{
        http_response_code($status);
		header('Content-type: application/json');
		echo json_encode($value);
	}
}

?>