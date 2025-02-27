<? 

namespace MyTestApp;

Class RenderArray implements iRenderData {

    public $data = [];

    public function renderData($data) {
        $this->data[] = $data;
    }

}