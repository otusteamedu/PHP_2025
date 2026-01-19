<?php

namespace Fatnev\Price\Services;

use Dompdf\Dompdf;
use Fatnev\Price\Config\Config;

class PdfGenerator
{
    private Config $config;
    
    public function __construct(Config $config)
    {
        $this->config = $config;
    }
    
    public function createFromProducts(array $products): array
    {
        $html = $this->generateHtml($products);
        $pdfContent = $this->createPdf($html);
        
        $filename = "price-list-" . date('Y-m-d-His') . ".pdf";
        $filePath = $this->saveFile($pdfContent, $filename);
        
        return [
            'file_path' => $filePath,
            'file_url' => $this->config->getBaseUrl() . '/upload/managers/price/' . $filename,
            'file_name' => $filename
        ];
    }
    
    private function generateHtml(array $products): string
    {
        $logoUrl = $this->getLogoForPdf();
        $date = date('d.m.Y');
        $count = count($products);
        
        $rows = '';
        if (empty($products)) {
            $rows = '<tr><td colspan="4" style="text-align:center;color:red;padding:40px;">
                    <strong>Товары в наличии не найдены!</strong></td></tr>';
        } else {
            foreach ($products as $index => $product) {
                $rows .= sprintf(
                    '<tr><td>%d</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                    $index + 1,
                    htmlspecialchars($product['name']),
                    number_format($product['quantity'], 0, '', ' '),
                    number_format($product['price'], 2, '.', ' ')
                );
            }
        }
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, Arial; font-size: 9px; margin: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background: #f2f2f2; }
        .header { text-align: center; margin-bottom: 10px; }
        .footer { margin-top: 10px; text-align: center; font-size: 8px; color: #666; }
        .logo { height: 35px; }
    </style>
</head>
<body>
    <div class="header">
        <p><strong>ООО "Название компании"</strong></p>
        {$logoUrl}
        <p>г. Ставрополь, ул. Промышленная, 7</p>
        <h4>Прайс-лист от {$date}</h4>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="30">№</th>
                <th>Наименование</th>
                <th width="60">Кол-во</th>
                <th width="70">Цена, руб.</th>
            </tr>
        </thead>
        <tbody>{$rows}</tbody>
    </table>
    
    <div class="footer">
        <p>Всего товаров: {$count}</p>
        <p>Сгенерировано: {$this->getCurrentDateTime()}</p>
    </div>
</body>
</html>
HTML;
    }
    
    private function getLogoForPdf(): string
    {
        $logoUrl = $this->config->getLogoUrl();
        $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/upload/managers/logo/logo.png';
        
        // Вариант 1: Используем base64 если файл существует локально
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            return '<img src="data:image/png;base64,' . $logoData . '" class="logo">';
        }
        
        // Вариант 2: Используем абсолютный URL (может не работать в DomPDF)
        return '<img src="' . $logoUrl . '" class="logo">';
    }
    
    private function createPdf(string $html): string
    {
        $dompdf = new Dompdf();
        $dompdf->set_option('defaultFont', 'DejaVu Sans');
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('chroot', $_SERVER['DOCUMENT_ROOT']);
        $dompdf->set_option('isPhpEnabled', true);
        
        // Настройки SSL для загрузки внешних ресурсов
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        $dompdf->setHttpContext($context);
        
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->output();
    }
    
    private function saveFile(string $content, string $filename): string
    {
        $path = $this->config->getPriceListPath() . $filename;
        file_put_contents($path, $content);
        return $path;
    }
    
    private function getCurrentDateTime(): string
    {
        return date('d.m.Y H:i:s');
    }
}