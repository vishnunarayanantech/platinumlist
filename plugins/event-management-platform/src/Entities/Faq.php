<?php

namespace EventManagementPlatform\Entities;

class Faq {
    public ?int $id = null;
    public int $event_id = 0;
    public string $question = '';
    public string $answer = '';
    public int $sort_order = 0;

    public function __construct( array $data = [] ) {
        if ( ! empty( $data ) ) {
            $this->fromArray( $data );
        }
    }

    public function fromArray( array $data ): void {
        $this->id = isset( $data['id'] ) ? (int) $data['id'] : null;
        $this->event_id = isset( $data['event_id'] ) ? (int) $data['event_id'] : 0;
        $this->question = $data['question'] ?? '';
        $this->answer = $data['answer'] ?? '';
        $this->sort_order = isset( $data['sort_order'] ) ? (int) $data['sort_order'] : 0;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'question' => $this->question,
            'answer' => $this->answer,
            'sort_order' => $this->sort_order,
        ];
    }
}
