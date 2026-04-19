<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Department;
use App\Models\Organization;
use App\Models\Shipment;
use App\Models\User;
use App\Shipments\ShipmentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_shipment_store_creates_initial_history(): void
    {
        $this->authenticateWithTenant();

        $originCity = City::factory()->create();
        $destinationCity = City::factory()->create();

        $payload = [
            'sender_name' => 'Ana López',
            'sender_phone' => '3001234567',
            'sender_email' => null,
            'recipient_name' => 'Carlos Ruiz',
            'recipient_phone' => '3109876543',
            'recipient_email' => null,
            'origin_address_line' => 'Calle 10 #20-30',
            'origin_department_id' => $originCity->department_id,
            'origin_city_id' => $originCity->id,
            'origin_postal_code' => null,
            'destination_address_line' => 'Carrera 15 #45',
            'destination_department_id' => $destinationCity->department_id,
            'destination_city_id' => $destinationCity->id,
            'destination_postal_code' => null,
            'reference_internal' => 'REF-001',
            'notes_internal' => null,
            'weight_kg' => '2.5',
            'declared_value' => null,
        ];

        $response = $this->post(route('shipments.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('shipments', [
            'sender_name' => 'Ana López',
            'status' => ShipmentStatus::RECEIVED,
        ]);

        $shipment = Shipment::first();
        $this->assertNotNull($shipment);
        $this->assertMatchesRegularExpression('/^RT-\d{4}-\d{6}$/', $shipment->tracking_number);

        $this->assertDatabaseHas('shipment_status_histories', [
            'shipment_id' => $shipment->id,
            'from_status' => null,
            'to_status' => ShipmentStatus::RECEIVED,
        ]);
    }

    public function test_user_cannot_access_shipment_from_other_organization(): void
    {
        $this->authenticateWithTenant();

        $otherOrg = Organization::factory()->create();
        $originCity = City::factory()->create();
        $destinationCity = City::factory()->create();

        $foreign = Shipment::withoutGlobalScopes()->create([
            'organization_id' => $otherOrg->id,
            'tracking_number' => 'FOREIGNTRACK01',
            'sender_name' => 'X',
            'sender_phone' => '300',
            'recipient_name' => 'Y',
            'recipient_phone' => '301',
            'origin_address_line' => 'A',
            'origin_city' => $originCity->name,
            'origin_region' => $originCity->department->name,
            'origin_department_id' => $originCity->department_id,
            'origin_city_id' => $originCity->id,
            'destination_address_line' => 'C',
            'destination_city' => $destinationCity->name,
            'destination_region' => $destinationCity->department->name,
            'destination_department_id' => $destinationCity->department_id,
            'destination_city_id' => $destinationCity->id,
            'status' => ShipmentStatus::RECEIVED,
        ]);

        $this->get(route('shipments.show', $foreign))->assertNotFound();
    }

    public function test_status_change_appends_history_and_updates_shipment(): void
    {
        $user = $this->authenticateWithTenant();
        $orgId = $user->organizations()->first()->id;

        $shipment = Shipment::factory()->create([
            'organization_id' => $orgId,
            'created_by_user_id' => $user->id,
        ]);

        \App\Models\ShipmentStatusHistory::create([
            'organization_id' => $orgId,
            'shipment_id' => $shipment->id,
            'from_status' => null,
            'to_status' => ShipmentStatus::RECEIVED,
            'notes' => null,
            'changed_by_user_id' => $user->id,
        ]);

        $response = $this->post(route('shipments.status.update', $shipment), [
            'status' => ShipmentStatus::IN_TRANSIT,
            'notes' => 'Salió de bodega',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('shipments.show', $shipment));

        $shipment->refresh();
        $this->assertSame(ShipmentStatus::IN_TRANSIT, $shipment->status);

        $this->assertDatabaseHas('shipment_status_histories', [
            'shipment_id' => $shipment->id,
            'from_status' => ShipmentStatus::RECEIVED,
            'to_status' => ShipmentStatus::IN_TRANSIT,
            'notes' => 'Salió de bodega',
        ]);
    }

    public function test_invalid_status_transition_is_rejected(): void
    {
        $user = $this->authenticateWithTenant();
        $orgId = $user->organizations()->first()->id;

        $shipment = Shipment::factory()->create([
            'organization_id' => $orgId,
            'created_by_user_id' => $user->id,
            'status' => ShipmentStatus::RECEIVED,
        ]);

        \App\Models\ShipmentStatusHistory::create([
            'organization_id' => $orgId,
            'shipment_id' => $shipment->id,
            'from_status' => null,
            'to_status' => ShipmentStatus::RECEIVED,
            'notes' => null,
            'changed_by_user_id' => $user->id,
        ]);

        $response = $this->from(route('shipments.show', $shipment))->post(route('shipments.status.update', $shipment), [
            'status' => ShipmentStatus::DELIVERED,
            'notes' => null,
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertSame(ShipmentStatus::RECEIVED, $shipment->fresh()->status);
    }

    public function test_public_tracking_page_displays_basic_info(): void
    {
        $user = User::factory()->withOrganization()->create();
        $org = $user->organizations()->first();

        $originCity = City::factory()->create();
        $destinationCity = City::factory()->create();

        $shipment = Shipment::withoutGlobalScopes()->create([
            'organization_id' => $org->id,
            'tracking_number' => 'RT-2026-000099',
            'sender_name' => 'X',
            'sender_phone' => '300',
            'recipient_name' => 'Y',
            'recipient_phone' => '301',
            'origin_address_line' => 'A',
            'origin_city' => $originCity->name,
            'origin_region' => $originCity->department->name,
            'origin_department_id' => $originCity->department_id,
            'origin_city_id' => $originCity->id,
            'destination_address_line' => 'C',
            'destination_city' => $destinationCity->name,
            'destination_region' => $destinationCity->department->name,
            'destination_department_id' => $destinationCity->department_id,
            'destination_city_id' => $destinationCity->id,
            'status' => ShipmentStatus::RECEIVED,
        ]);

        \App\Models\ShipmentStatusHistory::create([
            'organization_id' => $org->id,
            'shipment_id' => $shipment->id,
            'from_status' => null,
            'to_status' => ShipmentStatus::RECEIVED,
            'notes' => null,
            'changed_by_user_id' => null,
        ]);

        $url = route('tracking.public', [
            'organization_slug' => $org->slug,
            'tracking_number' => $shipment->tracking_number,
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee($shipment->tracking_number, false)
            ->assertSee($org->name, false);
    }

    public function test_dashboard_displays_shipment_metrics(): void
    {
        $this->authenticateWithTenant();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('dashboard.metric_registered_today'), false)
            ->assertSee(__('dashboard.metric_in_transit'), false)
            ->assertSee(__('dashboard.metric_delivered_today'), false)
            ->assertSee(__('dashboard.metric_incidents'), false);
    }

    public function test_public_tracking_search_form_renders(): void
    {
        $this->get(route('tracking.search'))
            ->assertOk()
            ->assertSee(__('tracking.search_heading'), false);
    }

    public function test_public_tracking_lookup_redirects_when_single_match(): void
    {
        $user = User::factory()->withOrganization()->create();
        $org = $user->organizations()->first();

        $originCity = City::factory()->create();
        $destinationCity = City::factory()->create();

        $shipment = Shipment::withoutGlobalScopes()->create([
            'organization_id' => $org->id,
            'tracking_number' => 'RT-2026-555555',
            'sender_name' => 'X',
            'sender_phone' => '300',
            'recipient_name' => 'Y',
            'recipient_phone' => '301',
            'origin_address_line' => 'A',
            'origin_city' => $originCity->name,
            'origin_region' => $originCity->department->name,
            'origin_department_id' => $originCity->department_id,
            'origin_city_id' => $originCity->id,
            'destination_address_line' => 'C',
            'destination_city' => $destinationCity->name,
            'destination_region' => $destinationCity->department->name,
            'destination_department_id' => $destinationCity->department_id,
            'destination_city_id' => $destinationCity->id,
            'status' => ShipmentStatus::RECEIVED,
        ]);

        $this->post(route('tracking.lookup'), [
            'tracking_number' => $shipment->tracking_number,
        ])->assertRedirect(route('tracking.public', [
            'organization_slug' => $org->slug,
            'tracking_number' => $shipment->tracking_number,
        ]));
    }

    public function test_public_tracking_lookup_not_found(): void
    {
        $this->from(route('tracking.search'))
            ->post(route('tracking.lookup'), [
                'tracking_number' => 'RT-2026-000000',
            ])
            ->assertRedirect(route('tracking.search'))
            ->assertSessionHasErrors('tracking_number');
    }

    public function test_public_tracking_lookup_ambiguous_requires_organization_slug(): void
    {
        $originCity = City::factory()->create();
        $destinationCity = City::factory()->create();

        $orgAlpha = Organization::factory()->create(['slug' => 'tenant-alpha']);
        $orgBeta = Organization::factory()->create(['slug' => 'tenant-beta']);

        foreach ([$orgAlpha, $orgBeta] as $organization) {
            Shipment::withoutGlobalScopes()->create([
                'organization_id' => $organization->id,
                'tracking_number' => 'DUP-TRACK-001',
                'sender_name' => 'X',
                'sender_phone' => '300',
                'recipient_name' => 'Y',
                'recipient_phone' => '301',
                'origin_address_line' => 'A',
                'origin_city' => $originCity->name,
                'origin_region' => $originCity->department->name,
                'origin_department_id' => $originCity->department_id,
                'origin_city_id' => $originCity->id,
                'destination_address_line' => 'C',
                'destination_city' => $destinationCity->name,
                'destination_region' => $destinationCity->department->name,
                'destination_department_id' => $destinationCity->department_id,
                'destination_city_id' => $destinationCity->id,
                'status' => ShipmentStatus::RECEIVED,
            ]);
        }

        $this->from(route('tracking.search'))
            ->post(route('tracking.lookup'), [
                'tracking_number' => 'DUP-TRACK-001',
            ])
            ->assertRedirect(route('tracking.search'))
            ->assertSessionHasErrors('tracking_number');

        $this->post(route('tracking.lookup'), [
            'tracking_number' => 'DUP-TRACK-001',
            'organization_slug' => $orgBeta->slug,
        ])->assertRedirect(route('tracking.public', [
            'organization_slug' => $orgBeta->slug,
            'tracking_number' => 'DUP-TRACK-001',
        ]));
    }

    public function test_geo_cities_endpoint_requires_authentication(): void
    {
        $department = Department::factory()->create(['country_code' => 'CO']);

        $this->get(route('geo.cities', ['department_id' => $department->id]))
            ->assertRedirect();
    }

    public function test_geo_cities_returns_city_list_for_authenticated_user(): void
    {
        $this->authenticateWithTenant();

        $department = Department::factory()->create(['country_code' => 'CO']);
        City::factory()->create([
            'department_id' => $department->id,
            'name' => 'Ciudad Geo Prueba',
        ]);

        $this->get(route('geo.cities', ['department_id' => $department->id]))
            ->assertOk()
            ->assertJsonFragment(['name' => 'Ciudad Geo Prueba']);
    }

    public function test_shipment_guide_page_displays_tracking_and_brand(): void
    {
        $user = $this->authenticateWithTenant();
        $orgId = $user->organizations()->first()->id;

        $shipment = Shipment::factory()->create([
            'organization_id' => $orgId,
            'created_by_user_id' => $user->id,
        ]);

        $this->get(route('shipments.guide', $shipment))
            ->assertOk()
            ->assertSee($shipment->tracking_number, false)
            ->assertSee(__('brand.name'), false);
    }

    public function test_shipment_guide_pdf_returns_pdf_response(): void
    {
        $user = $this->authenticateWithTenant();
        $orgId = $user->organizations()->first()->id;

        $shipment = Shipment::factory()->create([
            'organization_id' => $orgId,
            'created_by_user_id' => $user->id,
        ]);

        $response = $this->get(route('shipments.guide.pdf', $shipment));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }
}
