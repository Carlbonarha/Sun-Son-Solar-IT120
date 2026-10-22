<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefreshSolarCatalogMockContent extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');
        foreach ($this->productContent() as $name => $product) {
            $this->db->table('catalog_products')
                ->where('name', $name)
                ->update([
                    'price' => $product['price'],
                    'description' => $product['description'],
                    'photo' => $product['photo'],
                    'updated_at' => $now,
                ]);
        }

        foreach ($this->serviceContent() as $name => $service) {
            $this->db->table('catalog_services')
                ->where('name', $name)
                ->update([
                    'description' => $service['description'],
                    'photo' => $service['photo'],
                    'updated_at' => $now,
                ]);
        }

        $newProducts = [
            [
                'name' => 'Smart Energy Monitor',
                'category' => 'Inverters',
                'price' => '₱8,900 / kit',
                'description' => 'Household energy monitor for viewing compatible system production and usage. Compatibility, app features, connectivity, and included sensors must be checked against the selected equipment. Installation and network setup are not included.',
                'photo' => 'images/catalog/product-smart-energy-monitor.jpg',
                'created_at' => $now,
            ],
            [
                'name' => 'DC Isolator & Surge Protection Kit',
                'category' => 'Wires',
                'price' => '₱7,500 / kit',
                'description' => 'DC-side isolation and surge-protection components for a professionally designed PV system. Ratings and component selection must match the system design and applicable electrical requirements. Wiring and installation are not included.',
                'photo' => 'images/catalog/product-dc-isolator-surge-protection-kit.jpg',
                'created_at' => $now,
            ],
        ];
        foreach ($newProducts as $product) {
            $existingProduct = $this->db->table('catalog_products')
                ->where('name', $product['name'])
                ->countAllResults() > 0;
            if ($existingProduct) {
                unset($product['created_at']);
                $product['updated_at'] = $now;
                $this->db->table('catalog_products')
                    ->where('name', $product['name'])
                    ->update($product);
            } else {
                $this->db->table('catalog_products')->insert($product);
            }
        }

        $newServices = [
            [
                'name' => 'Battery Backup Planning',
                'service_type' => 'Consultation',
                'description' => 'Discuss which essential loads matter during an outage, potential battery placement, and questions for a backup-power design. Backup availability depends on compatible equipment, system configuration, and site assessment; no runtime or outage coverage is guaranteed.',
                'photo' => 'images/catalog/solar-battery.svg',
                'created_at' => $now,
            ],
            [
                'name' => 'Post-Installation Walkthrough',
                'service_type' => 'Installation',
                'description' => 'Handover session covering basic system components, monitoring access, and where to find equipment documentation. Scope and availability are confirmed with the final project plan; it is not a substitute for manufacturer instructions or electrical work.',
                'photo' => 'images/catalog/installation.svg',
                'created_at' => $now,
            ],
        ];
        foreach ($newServices as $service) {
            $existingService = $this->db->table('catalog_services')
                ->where('name', $service['name'])
                ->countAllResults() > 0;
            if ($existingService) {
                unset($service['created_at']);
                $service['updated_at'] = $now;
                $this->db->table('catalog_services')
                    ->where('name', $service['name'])
                    ->update($service);
            } else {
                $this->db->table('catalog_services')->insert($service);
            }
        }
    }

    public function down()
    {
        foreach (['Smart Energy Monitor', 'DC Isolator & Surge Protection Kit'] as $name) {
            $this->db->table('catalog_products')->where('name', $name)->delete();
        }
        foreach (['Battery Backup Planning', 'Post-Installation Walkthrough'] as $name) {
            $this->db->table('catalog_services')->where('name', $name)->delete();
        }

        foreach ($this->originalProducts() as $name => $product) {
            $this->db->table('catalog_products')->where('name', $name)->update($product);
        }
        foreach ($this->productContent() as $name => $product) {
            $this->db->table('catalog_products')
                ->where('name', $name)
                ->where('photo', $product['photo'])
                ->update(['photo' => null]);
        }
        foreach ($this->originalServices() as $name => $service) {
            $this->db->table('catalog_services')->where('name', $name)->update($service);
        }
        foreach ($this->serviceContent() as $name => $service) {
            $this->db->table('catalog_services')
                ->where('name', $name)
                ->where('photo', $service['photo'])
                ->update(['photo' => null]);
        }
    }

    private function productContent(): array
    {
        return [
            'High-Efficiency Solar Panels' => [
                'price' => '₱12,500 / panel',
                'description' => '450 W monocrystalline PV module for residential systems. Final output depends on the exact model, orientation, shading, temperature, and system design. Check the manufacturer datasheet and warranty terms before selection. Installation and balance-of-system equipment are not included.',
                'photo' => 'images/catalog/product-solar-panels.jpg',
            ],
            'Solar Inverters' => [
                'price' => '₱48,000 / unit',
                'description' => '5 kW grid-connected inverter. AC rating, phase, grid requirements, monitoring features, and compatibility vary by model and site. Confirm local approvals and the system design before purchase. Installation and commissioning are not included.',
                'photo' => 'images/catalog/product-solar-inverters.webp',
            ],
            'Energy Storage Batteries' => [
                'price' => '₱78,000 / unit',
                'description' => '5 kWh battery-storage unit for backup-power systems. Usable capacity, chemistry, power rating, controls, and inverter compatibility depend on the selected model. Backup operation requires a compatible design and does not guarantee whole-home or unlimited power.',
                'photo' => 'images/catalog/product-energy-storage-batteries.jpg',
            ],
            'Racking Systems' => [
                'price' => '₱18,000 / set',
                'description' => 'Roof-mount rail and attachment set. The correct mounting approach depends on roof material, structure, wind loading, layout, and site inspection. Structural suitability must be verified by a qualified professional. Roof work and installation are not included.',
                'photo' => 'images/catalog/product-racking-systems.jpg',
            ],
            'Solar Wiring Kit' => [
                'price' => '₱6,500 / kit',
                'description' => 'PV cable and connector bundle. Cable gauge, connector type, route, protection, and electrical ratings must be selected for the actual system and applicable code. Final bill of materials and installation are not included.',
                'photo' => 'images/catalog/product-solar-wiring-kit.jpg',
            ],
        ];
    }

    private function serviceContent(): array
    {
        return [
            'Free Consultation' => [
                'description' => 'Introductory discussion of project goals, recent electricity use, property location, roof or site conditions, and questions to investigate. It is an initial conversation, not an engineering assessment, savings estimate, or binding quotation.',
                'photo' => 'images/catalog/service-free-consultation.jpg',
            ],
            'System Design' => [
                'description' => 'Preliminary review of energy-use information, possible array layout, equipment assumptions, and questions for a site visit. Final design requires verified site conditions, equipment selection, and applicable technical and utility review.',
                'photo' => 'images/catalog/service-permit-handling.jpg',
            ],
            'Permit Handling' => [
                'description' => 'Coordination of local approvals, utility requirements, and project documents. Requirements and responsibility vary by location and project; no approval, timeline, or outcome is guaranteed.',
                'photo' => 'images/catalog/consultation.svg',
            ],
            'Professional Installation' => [
                'description' => 'Installation based on an approved system design, including equipment placement and a handover discussion. Site access, structural suitability, permits, schedule, and final work scope must be confirmed before any agreement.',
                'photo' => 'images/catalog/installation.svg',
            ],
            'Regular Maintenance' => [
                'description' => 'Inspection and maintenance visit that may include a visual system check, accessible module cleaning where appropriate, and a findings summary. Safe access, equipment guidance, weather, and actual scope are assessed before service.',
                'photo' => 'images/catalog/maintenance.svg',
            ],
            'Quick Repairs' => [
                'description' => 'Troubleshooting visit to document reported symptoms and inspect accessible system components. Diagnosis, parts, repair scope, and timing depend on the equipment and site assessment; no repair outcome or response time is guaranteed.',
                'photo' => 'images/catalog/solar-inverter.svg',
            ],
            '24/7 Monitoring' => [
                'description' => 'Remote performance monitoring for compatible connected equipment. Internet access, manufacturer platforms, alert coverage, response procedures, and availability depend on the final setup; monitoring does not guarantee uninterrupted generation.',
                'photo' => 'images/catalog/solar-monitoring.svg',
            ],
        ];
    }

    private function originalProducts(): array
    {
        return [
            'High-Efficiency Solar Panels' => ['price' => '$350', 'description' => 'High-efficiency solar panels with 25-year warranty'],
            'Solar Inverters' => ['price' => '$2,000', 'description' => 'Convert DC to AC power efficiently'],
            'Energy Storage Batteries' => ['price' => '$5,000', 'description' => 'Lithium energy storage solutions for backup power'],
            'Racking Systems' => ['price' => '$1,200', 'description' => 'Durable mounting systems for any roof type'],
            'Solar Wiring Kit' => ['price' => '$300', 'description' => 'Safe and certified solar electrical wiring'],
        ];
    }

    private function originalServices(): array
    {
        return [
            'Free Consultation' => ['description' => 'Free initial consultation to assess your energy needs'],
            'System Design' => ['description' => 'Custom solar system design by our engineers'],
            'Permit Handling' => ['description' => 'Handle all necessary permits and documentation'],
            'Professional Installation' => ['description' => 'Professional installation by certified technicians'],
            'Regular Maintenance' => ['description' => 'Regular system maintenance and cleaning'],
            'Quick Repairs' => ['description' => 'Quick repair services for system issues'],
            '24/7 Monitoring' => ['description' => '24/7 system performance monitoring'],
        ];
    }
}
