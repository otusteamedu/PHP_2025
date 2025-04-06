<? 

namespace MyTestApp;

Class RenderHtml {

    public function __construct(string $tpl, array $data) {
        $data = $data;
        include($tpl);
    }

}