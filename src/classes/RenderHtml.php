<? 

namespace MyTestApp;

Class RenderHtml implements iRenderData {

    public $data = "";

    public function renderData($data) {
        $this->data .= $data;
    }

}