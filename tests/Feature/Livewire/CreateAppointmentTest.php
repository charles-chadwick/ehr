<?php

use App\Livewire\Forms\AppointmentForm;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Volt\Volt;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('prefills patient and default date/time on mount', function () {
    // Ensure the expected patient exists
    Patient::factory()->create(['id' => 12]);

    // Freeze time to ensure deterministic "nextWeekday at 8:00"
    Carbon::setTestNow(Carbon::parse('2025-01-01 12:00:00')); // Wednesday

    // Replace with your actual view name (e.g., 'appointments.create')\
    actingAs(User::factory()->create())->withSession([
        '_token' => csrf_token() ?: Str::random(40),
    ]);
    $component = Volt::test('appointments.create');
    $expected = Carbon::today()->nextWeekday()->setHour(8)->setMinute(0);

    $component
        ->assertSet('form.date', $expected->format('Y-m-d'))
        ->assertSet('form.time', $expected->format('H:i'));

    // Assert patient bound in the form is the one with id 12
    $patient = $component->get('form.patient');
    expect($patient)->not->toBeNull()
        ->and($patient->id)->toBe(12);

    Carbon::setTestNow();
});

it('submits the form and handles the success path', function () {
    Patient::factory()->create(['id' => 12]);
    Carbon::setTestNow(Carbon::parse('2025-01-01 12:00:00'));

    // Mock the AppointmentForm so that save() returns an object with exists=true
    $formMock = Mockery::mock(AppointmentForm::class)->makePartial();
    $formMock->shouldReceive('save')->andReturn((object)['exists' => true]);

    // Bind the mock into the container so the Volt component resolves it
    app()->instance(AppointmentForm::class, $formMock);

    // Replace with your actual view name (e.g., 'appointments.create')
    $component = Volt::test('appointments.create');

    // Call save; we only assert that it runs without validation errors
    $component->call('save')->assertHasNoErrors();

    Carbon::setTestNow();
});
