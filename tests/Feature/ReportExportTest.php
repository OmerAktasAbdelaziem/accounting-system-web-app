<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_report_pdf_export_returns_pdf_download(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('reports.generate-pdf'), [
            'report' => 'sales',
            'format' => 'pdf',
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('.pdf', (string) $response->headers->get('content-disposition'));
    }

    public function test_inventory_report_csv_export_returns_csv_download(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('reports.generate-pdf'), [
            'report' => 'inventory',
            'format' => 'csv',
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('.csv', (string) $response->headers->get('content-disposition'));
    }
}
