<? 

namespace MyTestApp;

Class RenderHtml {

    public $html = "";
    public $data = [];

    public function __construct(string $tpl, array $data) {
        $this->html .= include($tpl);
    }

    public function renderHtml($html) {
        $this->html .= $html;
    }

}