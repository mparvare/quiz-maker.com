<?php
namespace Core;

class Response {
    public function json($data, $status = 200) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function error($message, $status = 400) {
        $this->json([
            'status' => 'error',
            'message' => $message
        ], $status);
    }

    public function success($message, $data = null, $status = 200) {
        $response = [
            'status' => 'success',
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        $this->json($response, $status);
    }
}
?>