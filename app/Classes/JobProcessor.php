<?php
namespace App\Classes;

class JobProcessor {
    public function handle(array $data) {
        $requestId = $data['requestId'] ?? null;
        if (!$requestId) {
            throw new \InvalidArgumentException("В пакете отсутствует requestId");
        }

        // эмуляция генерации отчета
        sleep(10);

        error_log("Processed: " . json_encode($data));

        // отправка уведомлений на email и телеграмм
        (new MailService())->send($data['email']);
        (new TelegramService())->notify($data['phone']);

        // меняем занчение в StatusStore на сделано, чтобы обновить статус и на фронте тоже
        $statusStore = new StatusStore();
        $statusStore->set($requestId, 'done');
    }
}
