<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Webkul\Contact\Repositories\PersonRepository;

beforeEach(function () {
    $this->admin = getDefaultAdmin();
    $this->personRepository = app(PersonRepository::class);
});

it('creates a primary contact record', function () {
    $person = $this->personRepository->create([
        'entity_type'     => 'persons',
        'name'            => 'QA Contact',
        'emails'          => [
            ['value' => 'qa@example.com', 'label' => 'work', 'primary' => 1],
        ],
        'contact_numbers' => [
            ['value' => '5551112222', 'label' => 'mobile', 'primary' => 1],
        ],
        'user_id'         => $this->admin->id,
    ]);

    expect($person->exists())->toBeTrue();
    expect($person->emails)->toBeJson();
});

it('updates an existing contact', function () {
    $person = $this->personRepository->create([
        'entity_type' => 'persons',
        'name'        => 'Editable Contact',
        'emails'      => [
            ['value' => 'editable@example.com', 'label' => 'work', 'primary' => 1],
        ],
    ]);

    $updated = $this->personRepository->update([
        'entity_type' => 'persons',
        'name'        => 'Editable Contact Updated',
        'emails'      => [
            ['value' => 'editable@example.com', 'label' => 'work', 'primary' => 1],
        ],
        'contact_numbers' => [
            ['value' => '5553334444', 'label' => 'office', 'primary' => 1],
        ],
        'user_id' => $this->admin->id,
    ], $person->id);

    expect($updated->name)->toBe('Editable Contact Updated');
    expect(collect(json_decode($updated->contact_numbers, true))->pluck('value'))->toContain('5553334444');
});

it('deletes a contact safely', function () {
    $person = $this->personRepository->create([
        'entity_type' => 'persons',
        'name'        => 'Disposable Contact',
        'emails'      => [
            ['value' => 'disposable@example.com', 'label' => 'work', 'primary' => 1],
        ],
    ]);

    $this->personRepository->delete($person->id);

    expect($this->personRepository->find($person->id))->toBeNull();
});
