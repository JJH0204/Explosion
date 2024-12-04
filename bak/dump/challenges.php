<?php
class ChallengeController {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getChallenge($id) {
        // 보안 검증 후 챌린지 데이터 반환
        $challenge = $this->db->getChallenge($id);
        
        return [
            'id' => $challenge['id'],
            'title' => $challenge['title'],
            'description' => $challenge['description'],
            'type' => $challenge['type'],
            // 정답은 클라이언트에 노출하지 않음
        ];
    }
    
    public function verifyAnswer($id, $answer) {
        // 서버에서 정답 검증
        $result = $this->db->verifyAnswer($id, $answer);
        
        if ($result) {
            $this->updateUserProgress();
            return ['success' => true];
        }
        
        return ['success' => false];
    }
} 